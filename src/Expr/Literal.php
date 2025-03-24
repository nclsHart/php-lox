<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

final class Literal extends Expr
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitLiteralExpr($this);
    }
}
