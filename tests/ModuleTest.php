<?php

declare(strict_types=1);

namespace TMV\Laminas\ManagerRegistry\Test;

use PHPUnit\Framework\TestCase;
use TMV\Laminas\ManagerRegistry\Module;

/**
 * @covers \TMV\Laminas\ManagerRegistry\Module
 */
class ModuleTest extends TestCase
{
    public function testShouldRetrieveConfig(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('service_manager', $config);
    }
}
