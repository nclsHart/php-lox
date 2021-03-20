<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

use Lox\Expr\Expr;

final class ExpressionStmt extends Stmt
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

    public function accept(Visitor $visitor)
    {
        return $visitor->visitExpressionStmt($this);
    }
}
