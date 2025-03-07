<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

use Lox\Token;

final class Binary extends Expr
{
    private Expr $left;
    private Token $operator;
    private Expr $right;

    public function __construct(Expr $left, Token $operator, Expr $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    public function left(): Expr
    {
        return $this->left;
    }

    public function operator(): Token
    {
        return $this->operator;
    }

    public function right(): Expr
    {
        return $this->right;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitBinaryExpr($this);
    }
}
