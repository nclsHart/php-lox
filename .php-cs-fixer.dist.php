<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->path(['src', 'tests', 'tools'])
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR2' => true,
    ])
    ->setFinder($finder)
;
