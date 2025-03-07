<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

use Lox\Expr\Expr;

final class PrintStmt extends Stmt
{
    private Expr $expression;

    public function __construct(Expr $expression)
    {
        $this->expression = $expression;
    }

    public function expression(): Expr
    {
        return $this->expression;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitPrintStmt($this);
    }
}
