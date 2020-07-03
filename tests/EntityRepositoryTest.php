<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use TMV\Laminas\ManagerRegistry\EntityRepository;

/**
 * @covers \TMV\Laminas\ManagerRegistry\EntityRepository
 */
class EntityRepositoryTest extends TestCase
{
    use ProphecyTrait;

    private $managerRegistry;

    private $entityName;

    private $managerName;

    private $doctrineRepository;

    /** @var EntityRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerRegistry = $this->prophesize(ManagerRegistry::class);
        $this->entityName = 'Foo';
        $this->managerName = 'bar';

        $this->doctrineRepository = $this->prophesize(\Doctrine\ORM\EntityRepository::class);
        $this->managerRegistry->getRepository($this->entityName, $this->managerName)
            ->willReturn($this->doctrineRepository->reveal());

        $this->repository = new EntityRepository(
            $this->managerRegistry->reveal(),
            $this->entityName,
            $this->managerName
        );
    }

    public function testShouldReturnEntityManagerFromProvider(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->managerRegistry->getManager($this->managerName)
            ->willReturn($entityManager->reveal());

        $em = $this->repository->getEntityManager();
        $this->assertSame($entityManager->reveal(), $em);
    }

    public function testShouldReturnEntityRepositoryFromProvider(): void
    {
        $doctrineRepo = $this->repository->getRepository();
        $this->assertSame($this->doctrineRepository->reveal(), $doctrineRepo);
    }

    public function testShouldCallFind(): void
    {
        $entity = new stdClass();
        $this->doctrineRepository->find(5, null, null)
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->assertSame($entity, $this->repository->find(5));
    }

    public function testShouldCallFindWithLock(): void
    {
        $entity = new stdClass();
        $this->doctrineRepository->find(5, LockMode::PESSIMISTIC_WRITE, 2)
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->assertSame($entity, $this->repository->find(5, LockMode::PESSIMISTIC_WRITE, 2));
    }

    public function testShouldCallFindAll(): void
    {
        $entity = new stdClass();
        $this->doctrineRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([$entity]);

        $this->assertSame([$entity], $this->repository->findAll());
    }

    public function testShouldCallFindBy(): void
    {
        $criteria = ['foo' => 'bar'];
        $entity = new stdClass();
        $this->doctrineRepository->findBy($criteria, null, null, null)
            ->shouldBeCalled()
            ->willReturn([$entity]);

        $this->assertSame([$entity], $this->repository->findBy($criteria));
    }

    public function testShouldCallFindByWithAllParams(): void
    {
        $criteria = ['foo' => 'bar'];
        $orderBy = ['foo' => 'ASC'];
        $limit = 5;
        $offset = 2;
        $entity = new stdClass();
        $this->doctrineRepository->findBy($criteria, $orderBy, $limit, $offset)
            ->shouldBeCalled()
            ->willReturn([$entity]);

        $this->assertSame([$entity], $this->repository->findBy($criteria, $orderBy, $limit, $offset));
    }

    public function testShouldCallFindOneBy(): void
    {
        $criteria = ['foo' => 'bar'];
        $entity = new stdClass();
        $this->doctrineRepository->findOneBy($criteria)
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->assertSame($entity, $this->repository->findOneBy($criteria));
    }

    public function testShouldCallGetClassName(): void
    {
        $this->doctrineRepository->getClassName()
            ->shouldBeCalled()
            ->willReturn('foo');

        $this->assertSame('foo', $this->repository->getClassName());
    }

    public function testShouldCallMatching(): void
    {
        $criteria = $this->prophesize(Criteria::class);
        $collection = $this->prophesize(Collection::class);

        $this->doctrineRepository->matching($criteria->reveal())
            ->shouldBeCalled()
            ->willReturn($collection->reveal());

        $this->assertSame($collection->reveal(), $this->repository->matching($criteria->reveal()));
    }
}
