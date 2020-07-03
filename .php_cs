<?php

/** @var \PhpCsFixer\Config $config */
$config = include __DIR__ . '/.php_cs.dist';

$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\RiskyRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider([
        //'@PhpCsFixer',
        'self_accessor' => false,
        'void_return' => true,
        'ternary_to_null_coalescing' => true,
        'visibility_required' => ['property', 'method', 'const'],
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'is_null' => true,
        'modernize_types_casting' => true,
        'dir_constant' => true,
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'no_alias_functions' => true,
        'ereg_to_preg' => true,
        'implode_call' => true,
        'include' => true,
        'no_unset_on_property' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'compact_nullable_typehint' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ]),
]);

$config->setRules($rulesProvider->getRules());
$config->setRiskyAllowed(true);

$ignorePaths = [
    __DIR__ . '/module/Blacklist/tests/data',
    __DIR__ . '/module/CougarWS/tests/data',
    __DIR__ . '/data/DoctrineORMModule/Proxy',
    __DIR__ . '/module/AppAoT/gen',
];
$config->getFinder()
    ->filter(static function (\Symfony\Component\Finder\SplFileInfo $fileinfo) use ($ignorePaths) {
        foreach ($ignorePaths as $path) {
            if (false !== strpos($fileinfo->getRealPath(), $path)) {
                return false;
            }
        }

        return true;
    })
    ;

return $config;
