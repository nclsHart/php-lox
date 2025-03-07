<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->path(['bin', 'src', 'tests', 'tools'])
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
