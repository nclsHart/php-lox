<?php

namespace Lox\Tests;

readonly class ExpectedOutput
{
    public function __construct(public string $output, public int $line)
    {
    }
}
