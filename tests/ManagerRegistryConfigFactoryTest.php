<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use TMV\Laminas\ManagerRegistry\ManagerRegistryConfigFactory;

/**
 * @covers \TMV\Laminas\ManagerRegistry\ManagerRegistryConfigFactory
 */
class ManagerRegistryConfigFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldCreateManagerRegistry(): void
    {
        $config = [
            ManagerRegistryConfigFactory::class => [
                'connections' => [
                    'orm_default' => 'doctrine.connection.orm_default',
                    'orm_slave' => 'doctrine.connection.orm_slave',
                ],
                'managers' => [
                    'orm_default' => 'doctrine.entitymanager.orm_default',
                    'orm_slave' => 'doctrine.entitymanager.orm_slave',
                ],
            ],
        ];
        $container = $this->prophesize(ServiceManager::class);
        $container->get('config')->willReturn($config);
        $container->get('doctrine.connection.orm_default')->willReturn('connection1');
        $container->get('doctrine.connection.orm_slave')->willReturn('connection2');
        $container->get('doctrine.entitymanager.orm_default')->willReturn('manager1');
        $container->get('doctrine.entitymanager.orm_slave')->willReturn('manager2');

        $factory = new ManagerRegistryConfigFactory();
        $managerRegistry = $factory($container->reveal());

        $this->assertSame('default', $managerRegistry->getName());
        $this->assertSame(['orm_default' => 'connection1', 'orm_slave' => 'connection2'], $managerRegistry->getConnections());
        $this->assertSame(['orm_default' => 'manager1', 'orm_slave' => 'manager2'], $managerRegistry->getManagers());
        $this->assertSame('connection1', $managerRegistry->getConnection());
        $this->assertSame('manager1', $managerRegistry->getManager());
        $this->assertSame('connection1', $managerRegistry->getConnection('orm_default'));
        $this->assertSame('connection2', $managerRegistry->getConnection('orm_slave'));
        $this->assertSame('manager1', $managerRegistry->getManager('orm_default'));
        $this->assertSame('manager2', $managerRegistry->getManager('orm_slave'));
    }

    public function testShouldCreateManagerRegistryWithAllParams(): void
    {
        $config = [
            ManagerRegistryConfigFactory::class => [
                'name' => 'foo',
                'connections' => [
                    'orm_default' => 'doctrine.connection.orm_default',
                    'orm_slave' => 'doctrine.connection.orm_slave',
                ],
                'managers' => [
                    'orm_default' => 'doctrine.entitymanager.orm_default',
                    'orm_slave' => 'doctrine.entitymanager.orm_slave',
                ],
                'default_connection' => 'orm_slave',
                'default_manager' => 'orm_slave',
            ],
        ];
        $container = $this->prophesize(ServiceManager::class);
        $container->get('config')->willReturn($config);
        $container->get('doctrine.connection.orm_default')->willReturn('connection1');
        $container->get('doctrine.connection.orm_slave')->willReturn('connection2');
        $container->get('doctrine.entitymanager.orm_default')->willReturn('manager1');
        $container->get('doctrine.entitymanager.orm_slave')->willReturn('manager2');

        $factory = new ManagerRegistryConfigFactory();
        $managerRegistry = $factory($container->reveal());

        $this->assertSame('foo', $managerRegistry->getName());
        $this->assertSame(['orm_default' => 'connection1', 'orm_slave' => 'connection2'], $managerRegistry->getConnections());
        $this->assertSame(['orm_default' => 'manager1', 'orm_slave' => 'manager2'], $managerRegistry->getManagers());
        $this->assertSame('connection2', $managerRegistry->getConnection());
        $this->assertSame('manager2', $managerRegistry->getManager());
    }
}
