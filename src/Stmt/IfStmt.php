<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

use Lox\Expr\Expr;

final class IfStmt extends Stmt
{
    private Expr $condition;
    private Stmt $thenBranch;
    private ?Stmt $elseBranch;

    public function __construct(Expr $condition, Stmt $thenBranch, ?Stmt $elseBranch)
    {
        $this->condition = $condition;
        $this->thenBranch = $thenBranch;
        $this->elseBranch = $elseBranch;
    }

    public function condition(): Expr
    {
        return $this->condition;
    }

    public function thenBranch(): Stmt
    {
        return $this->thenBranch;
    }

    public function elseBranch(): ?Stmt
    {
        return $this->elseBranch;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitIfStmt($this);
    }
}
