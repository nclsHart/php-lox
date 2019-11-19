<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

final class Literal extends Expr
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function accept(Visitor $visitor)
    {
        return $visitor->visitLiteralExpr($this);
    }
}
