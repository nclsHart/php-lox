<?php

namespace Lox\Tests;

readonly class OutputExpectation
{
    public function __construct(public string $expectedOutput, public int $lineNum)
    {
    }
}
