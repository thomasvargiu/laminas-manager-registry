<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry;

use function array_key_first;
use function count;
use InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class ManagerRegistryConfigFactory
{
    public function __invoke(ContainerInterface $container): ManagerRegistry
    {
        if (! $container instanceof ServiceManager) {
            throw new InvalidArgumentException('ManagerRegistry needs a ServiceManager container');
        }

        $config = $container->get('config')[self::class] ?? [];

        $name = $config['name'] ?? 'default';
        $connections = $config['connections'] ?? [];
        $managers = $config['managers'] ?? [];
        $defaultConnection = $config['default_connection'] ?? null;
        $defaultManager = $config['default_manager'] ?? null;
        $proxyInterface = $config['proxy_interface'] ?? \Doctrine\Persistence\Proxy::class;

        if (0 === count($connections)) {
            throw new RuntimeException('No connections provided');
        }

        if (0 === count($managers)) {
            throw new RuntimeException('No managers provided');
        }

        $defaultConnection = $defaultConnection ?? array_key_first($connections);
        $defaultManager = $defaultManager ?? array_key_first($managers);

        return new ManagerRegistry(
            $container,
            $name,
            $connections,
            $managers,
            $defaultConnection,
            $defaultManager,
            $proxyInterface
        );
    }
}
