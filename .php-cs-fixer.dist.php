<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$directories = ['vendor'];

$rules = [
    '@PSR2' => true,
];

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude($directories);
    
return (new Config())
    ->setRiskyAllowed(false)
    ->setRules($rules)
    ->setUsingCache(false)
    ->setFinder($finder);