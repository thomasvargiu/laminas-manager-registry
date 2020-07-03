<?php

namespace TMV\Laminas\ManagerRegistry\Test;

use function class_alias;

if (! trait_exists(\Prophecy\PhpUnit\ProphecyTrait::class)) {
    trait ProphecyTrait {

    }

    class_alias(ProphecyTrait::class, \Prophecy\PhpUnit\ProphecyTrait::class);
}