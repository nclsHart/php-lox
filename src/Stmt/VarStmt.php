<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

use Lox\Expr\Expr;
use Lox\Token;

final class VarStmt extends Stmt
{
    private Token $name;
    private ?Expr $initializer;

    public function __construct(Token $name, ?Expr $initializer)
    {
        $this->name = $name;
        $this->initializer = $initializer;
    }

    public function name(): Token
    {
        return $this->name;
    }

    public function initializer(): ?Expr
    {
        return $this->initializer;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitVarStmt($this);
    }
}
