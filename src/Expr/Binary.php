<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

use Lox\Token;

final class Binary extends Expr
{
    private $left;

    private $operator;

    private $right;

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

    public function accept(Visitor $visitor): Visitor
    {
        return $visitor->visitBinaryExpr($this);
    }

    public function operator(): Token
    {
        return $this->operator;
    }

    public function right(): Expr
    {
        return $this->right;
    }
}
