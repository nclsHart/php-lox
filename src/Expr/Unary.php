<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

use Lox\Token;

final class Unary extends Expr
{
    private $operator;

    private $right;

    public function __construct(Token $operator, Expr $right)
    {
        $this->operator = $operator;
        $this->right = $right;
    }

    public function operator(): Token
    {
        return $this->operator;
    }

    public function accept(Visitor $visitor)
    {
        return $visitor->visitUnaryExpr($this);
    }

    public function right(): Expr
    {
        return $this->right;
    }
}
