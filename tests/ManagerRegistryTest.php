<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \TMV\Laminas\ManagerRegistry\ManagerRegistry
 */
class ManagerRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldCreate(): void
    {
        $connections = [
            'default1' => 'connection1',
            'default2' => 'connection2',
        ];
        $managers = [
            'default1' => 'manager1',
            'default2' => 'manager2',
        ];

        $connection1 = $this->prophesize(Connection::class);
        $connection2 = $this->prophesize(Connection::class);
        $manager1 = $this->prophesize(EntityManagerInterface::class);
        $manager2 = $this->prophesize(ObjectManager::class);

        $container = $this->prophesize(ServiceManager::class);
        $container->get('connection1')->willReturn($connection1->reveal());
        $container->get('connection2')->willReturn($connection2->reveal());
        $container->get('manager1')->willReturn($manager1->reveal());
        $container->get('manager2')->willReturn($manager2->reveal());

        $managerRegistry = new \TMV\Laminas\ManagerRegistry\ManagerRegistry(
            $container->reveal(),
            'foo',
            $connections,
            $managers,
            'default1',
            'default1',
            Proxy::class
        );

        $this->assertSame('foo', $managerRegistry->getName());
        $this->assertSame(['default1' => $connection1->reveal(), 'default2' => $connection2->reveal()], $managerRegistry->getConnections());
        $this->assertSame(['default1' => $manager1->reveal(), 'default2' => $manager2->reveal()], $managerRegistry->getManagers());
        $this->assertSame($connection1->reveal(), $managerRegistry->getConnection());
        $this->assertSame($manager1->reveal(), $managerRegistry->getManager());
        $this->assertSame($connection1->reveal(), $managerRegistry->getConnection('default1'));
        $this->assertSame($connection2->reveal(), $managerRegistry->getConnection('default2'));
        $this->assertSame($manager1->reveal(), $managerRegistry->getManager('default1'));
        $this->assertSame($manager2->reveal(), $managerRegistry->getManager('default2'));
    }

    public function testShouldResetService(): void
    {
        $connections = [
            'default' => 'connection',
        ];
        $managers = [
            'default' => 'manager',
        ];

        $connection1 = $this->prophesize(Connection::class);
        $manager1 = $this->prophesize(EntityManagerInterface::class);
        $manager2 = $this->prophesize(EntityManagerInterface::class);

        $managerInstances = [
            $manager1->reveal(),
            $manager2->reveal(),
        ];
        $calls = 0;

        $container = new ServiceManager([
            'factories' => [
                'manager' => function () use ($managerInstances, &$calls) {
                    return $managerInstances[$calls++];
                },
            ],
            'services' => [
                'connection' => $connection1->reveal(),
            ],
        ]);

        $managerRegistry = new \TMV\Laminas\ManagerRegistry\ManagerRegistry(
            $container,
            'foo',
            $connections,
            $managers,
            'default',
            'default',
            Proxy::class
        );

        $this->assertSame($manager1->reveal(), $managerRegistry->getManager());
        $this->assertSame($manager1->reveal(), $managerRegistry->getManager());

        $managerRegistry->resetManager();

        $this->assertSame($manager2->reveal(), $managerRegistry->getManager());
    }

    public function testShouldResetManagerWithAlias(): void
    {
        $connections = [
            'default' => 'connection',
        ];
        $managers = [
            'default' => 'manager.default',
        ];

        $connection1 = $this->prophesize(Connection::class);
        $manager1 = $this->prophesize(EntityManagerInterface::class);
        $manager2 = $this->prophesize(EntityManagerInterface::class);

        $managerInstances = [
            $manager1->reveal(),
            $manager2->reveal(),
        ];
        $calls = 0;

        $container = new ServiceManager([
            'aliases' => [
                'manager.default' => 'manager',
            ],
            'factories' => [
                'manager' => function () use ($managerInstances, &$calls) {
                    return $managerInstances[$calls++];
                },
            ],
            'services' => [
                'connection' => $connection1->reveal(),
            ],
        ]);

        $managerRegistry = new \TMV\Laminas\ManagerRegistry\ManagerRegistry(
            $container,
            'foo',
            $connections,
            $managers,
            'default',
            'default',
            Proxy::class
        );

        $this->assertSame($manager1->reveal(), $managerRegistry->getManager());
        $this->assertSame($manager1->reveal(), $managerRegistry->getManager());

        $managerRegistry->resetManager();

        $this->assertSame($manager2->reveal(), $managerRegistry->getManager());
    }

    public function testShouldGetAliasNamespace(): void
    {
        $connections = [
            'default' => 'connection',
        ];
        $managers = [
            'default1' => 'manager1',
            'default2' => 'manager2',
        ];

        $connection1 = $this->prophesize(Connection::class);
        $manager1 = $this->prophesize(EntityManagerInterface::class);
        $manager2 = $this->prophesize(EntityManagerInterface::class);

        $container = $this->prophesize(ServiceManager::class);
        $container->get('connection')->willReturn($connection1->reveal());
        $container->get('manager1')->willReturn($manager1->reveal());
        $container->get('manager2')->willReturn($manager2->reveal());

        $managerRegistry = new \TMV\Laminas\ManagerRegistry\ManagerRegistry(
            $container->reveal(),
            'foo',
            $connections,
            $managers,
            'default',
            'default',
            Proxy::class
        );

        $alias = 'Foo';

        $configuration1 = $this->prophesize(Configuration::class);
        $configuration2 = $this->prophesize(Configuration::class);

        $configuration1->getEntityNamespace($alias)->willThrow(new ORMException('Message'));
        $configuration2->getEntityNamespace($alias)->willReturn('Bar');

        $manager1->getConfiguration()
            ->willReturn($configuration1->reveal());
        $manager2->getConfiguration()
            ->willReturn($configuration2->reveal());

        $namespace = $managerRegistry->getAliasNamespace($alias);
        $this->assertSame('Bar', $namespace);
    }
}
