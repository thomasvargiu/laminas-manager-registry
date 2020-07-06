<?php

namespace TMV\Laminas\ManagerRegistry\Test;

use function class_alias;
use function trait_exists;

if (! trait_exists(\Prophecy\PhpUnit\ProphecyTrait::class)) {
    trait ProphecyTrait
    {
    }

    class_alias(ProphecyTrait::class, \Prophecy\PhpUnit\ProphecyTrait::class);
}
