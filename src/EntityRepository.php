<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\Persistence\ManagerRegistry as DoctrineManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use RuntimeException;
use function sprintf;

/**
 * @template T
 * @implements ObjectRepository<T>
 * @implements Selectable<int, T>
 */
class EntityRepository implements ObjectRepository, Selectable
{
    /** @var DoctrineManagerRegistry */
    private $managerRegistry;

    /**
     * @var string
     * @phpstan-var class-string<T>
     */
    private $entityName;

    /** @var null|string */
    private $managerName;

    /**
     * @param DoctrineManagerRegistry $managerRegistry
     * @param string $entityName
     * @param string|null $managerName
     *
     * @phpstan-param class-string<T> $entityName
     */
    public function __construct(DoctrineManagerRegistry $managerRegistry, string $entityName, ?string $managerName)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityName = $entityName;
        $this->managerName = $managerName;
    }

    /**
     * @return DoctrineEntityRepository
     * @psalm-return DoctrineEntityRepository<T>
     * @phpstan-return DoctrineEntityRepository<T>
     */
    public function getRepository(): DoctrineEntityRepository
    {
        $repo = $this->managerRegistry->getRepository($this->entityName, $this->managerName);

        if (! $repo instanceof DoctrineEntityRepository) {
            throw new RuntimeException(sprintf('Repository is not an instance of "%s"', DoctrineEntityRepository::class));
        }

        return $repo;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->managerRegistry->getManager($this->managerName);

        if (! $entityManager instanceof EntityManagerInterface) {
            throw new RuntimeException(sprintf('ObjectManager is not an instance of "%s"', EntityManagerInterface::class));
        }

        return $entityManager;
    }

    /**
     * @param mixed $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     *
     * @return object|null
     *
     * @psalm-return T|null
     * @phpstan-return T|null
     */
    public function find($id, ?int $lockMode = null, ?int $lockVersion = null)
    {
        return $this->getRepository()->find($id, $lockMode, $lockVersion);
    }

    /**
     * @return array
     *
     * @psalm-return T[]
     * @phpstan-return T[]
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<mixed>|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return object[]
     *
     * @psalm-return T[]
     * @phpstan-return T[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function getClassName(): string
    {
        return $this->getRepository()->getClassName();
    }

    /**
     * @param Criteria $criteria
     *
     * @return Collection
     *
     * @phpstan-return Collection<int, T>
     * @psalm-return Collection<int, T>
     */
    public function matching(Criteria $criteria): Collection
    {
        $repo = $this->getRepository();
        if (! $repo instanceof Selectable) {
            throw new RuntimeException(sprintf('Repository does not implement "%s" interface', Selectable::class));
        }

        return $repo->matching($criteria);
    }
}
