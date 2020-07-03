<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use function serialize;
use TMV\Laminas\ManagerRegistry\EntityRepository;
use TMV\Laminas\ManagerRegistry\EntityRepositoryFactory;
use function unserialize;

/**
 * @covers \TMV\Laminas\ManagerRegistry\EntityRepositoryFactory
 */
class EntityRepositoryFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldCreateRepository(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $container->get(ManagerRegistry::class)
            ->willReturn($managerRegistry->reveal());

        $factory = new EntityRepositoryFactory('Foo', 'bar');

        $repository = $factory($container->reveal());
        $this->assertInstanceOf(EntityRepository::class, $repository);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $managerRegistry->getManager('bar')
            ->willReturn($entityManager->reveal());

        $this->assertSame($entityManager->reveal(), $repository->getEntityManager());
    }

    public function testShouldBeSerializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $container->get(ManagerRegistry::class)
            ->willReturn($managerRegistry->reveal());

        $factory = new EntityRepositoryFactory('Foo', 'bar');
        $unserialized = unserialize(serialize($factory));

        /** @var EntityRepository $repository */
        $repository = $unserialized($container->reveal());
        $this->assertInstanceOf(EntityRepository::class, $repository);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $managerRegistry->getManager('bar')
            ->willReturn($entityManager->reveal());

        $this->assertSame($entityManager->reveal(), $repository->getEntityManager());
    }

    public function testSerializedState(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $container->get(ManagerRegistry::class)
            ->willReturn($managerRegistry->reveal());

        $factory = EntityRepositoryFactory::__set_state([
            'entityName' => 'Foo',
            'managerName' => 'bar',
        ]);

        $repository = $factory($container->reveal());
        $this->assertInstanceOf(EntityRepository::class, $repository);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $managerRegistry->getManager('bar')
            ->willReturn($entityManager->reveal());

        $this->assertSame($entityManager->reveal(), $repository->getEntityManager());
    }
}
