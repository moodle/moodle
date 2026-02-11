<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->name('*.php')
;

$config = new Config();
$config
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_empty_phpdoc' => true,
        'no_unused_imports' => true,
        'no_superfluous_phpdoc_tags' => true,
        'ordered_imports' => true,
        'phpdoc_summary' => false,
        'protected_to_private' => false,
        'get_class_to_class_keyword' => false, // override for PHP < 8.0 (because ::class usage is not allowed there)
        'modernize_strpos' => false, // override for PHP < 8.0 (because str_contains not available in PHP 7.x)
    ])
;

return $config;
