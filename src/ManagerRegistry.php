<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry;

use function array_keys;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\AbstractManagerRegistry;
use function interface_exists;
use Laminas\ServiceManager\ServiceManager;
use ProxyManager\Proxy\LazyLoadingInterface;

class ManagerRegistry extends AbstractManagerRegistry
{
    /** @var ServiceManager */
    private $container;

    /**
     * @param ServiceManager $container
     * @param string $name
     * @param string[] $connections
     * @param string[] $managers
     * @param string $defaultConnection
     * @param string $defaultManager
     * @param string $proxyInterfaceName
     */
    public function __construct(
        ServiceManager $container,
        $name,
        array $connections,
        array $managers,
        $defaultConnection,
        $defaultManager,
        $proxyInterfaceName
    ) {
        parent::__construct($name, $connections, $managers, $defaultConnection, $defaultManager, $proxyInterfaceName);

        $this->container = $container;
    }

    /**
     * Fetches/creates the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name the name of the service
     *
     * @return object the instance of the given service
     */
    protected function getService($name)
    {
        return $this->container->get($name);
    }

    /**
     * Resets the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name the name of the service
     *
     * @return void
     */
    protected function resetService($name): void
    {
        $manager = $this->container->get($name);
        if (interface_exists(LazyLoadingInterface::class) && $manager instanceof LazyLoadingInterface) {
            // not initialized
            return;
        }

        (Closure::bind(
            function () use ($name): void {
                /* @noinspection PhpUndefinedFieldInspection */
                $alias = $this->resolvedAliases[$name] ?? $name;

                if (isset($this->services[$name])) {
                    /* @noinspection PhpUndefinedFieldInspection */
                    unset($this->services[$name]);
                }

                if (isset($this->services[$alias])) {
                    /* @noinspection PhpUndefinedFieldInspection */
                    unset($this->services[$alias]);
                }
            },
            $this->container,
            ServiceManager::class
        ))();
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered object managers.
     *
     * @param string $alias the alias
     *
     * @return string the full namespace
     */
    public function getAliasNamespace($alias): string
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                /** @var EntityManagerInterface $manager */
                $manager = $this->getManager($name);

                return $manager->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }
}
