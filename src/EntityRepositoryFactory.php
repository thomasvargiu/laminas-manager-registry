<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry;

use Doctrine\Persistence\ManagerRegistry as DoctrineManagerRegistry;
use Psr\Container\ContainerInterface;

/**
 * @template T
 */
final class EntityRepositoryFactory
{
    /**
     * @var string
     * @phpstan-var class-string<mixed>
     */
    private $entityName;

    /** @var string|null */
    private $managerName;

    /**
     * @param string $entityName
     * @param string|null $managerName
     *
     * @phpstan-param class-string<T> $entityName
     */
    public function __construct(string $entityName, ?string $managerName = null)
    {
        $this->entityName = $entityName;
        $this->managerName = $managerName;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EntityRepository
     *
     * @phpstan-return EntityRepository<T>
     * @psalm-return EntityRepository<T>
     */
    public function __invoke(ContainerInterface $container): EntityRepository
    {
        return new EntityRepository(
            $container->get(DoctrineManagerRegistry::class),
            $this->entityName,
            $this->managerName
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return EntityRepositoryFactory<mixed>
     */
    public static function __set_state(array $data): self
    {
        return new self(
            $data['entityName'],
            $data['managerName'] ?? null
        );
    }
}
