<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use PHPUnit\Framework\TestCase;
use TMV\Laminas\ManagerRegistry\ConfigProvider;

/**
 * @covers \TMV\Laminas\ManagerRegistry\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    public function testShouldRetrieveConfig(): void
    {
        $provider = new ConfigProvider();
        $config = $provider();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('dependencies', $config);
    }
}
