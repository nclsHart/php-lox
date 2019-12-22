<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

use Lox\Token;

final class Assign extends Expr
{
    private $name;

    private $value;

    public function __construct(Token $name, Expr $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function name(): Token
    {
        return $this->name;
    }

    public function accept(Visitor $visitor)
    {
        return $visitor->visitAssignExpr($this);
    }

    public function value(): Expr
    {
        return $this->value;
    }
}
