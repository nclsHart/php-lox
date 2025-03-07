<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

final class Grouping extends Expr
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
        return $visitor->visitGroupingExpr($this);
    }
}
