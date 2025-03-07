<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

use Lox\Token;

final class Variable extends Expr
{
    private Token $name;

    public function __construct(Token $name)
    {
        $this->name = $name;
    }

    public function name(): Token
    {
        return $this->name;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitVariableExpr($this);
    }
}
