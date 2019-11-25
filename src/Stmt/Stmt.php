<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

abstract class Stmt
{
    abstract public function accept(Visitor $visitor);
}
