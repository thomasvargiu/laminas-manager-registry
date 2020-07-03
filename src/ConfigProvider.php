<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry;

class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                \Doctrine\Persistence\ManagerRegistry::class => ManagerRegistry::class,
            ],
            'factories' => [
                ManagerRegistry::class => ManagerRegistryConfigFactory::class,
            ],
        ];
    }
}
