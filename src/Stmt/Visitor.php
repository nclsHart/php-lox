<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

interface Visitor
{
    public function visitBlockStmt(BlockStmt $stmt): void;

    public function visitExpressionStmt(ExpressionStmt $stmt): void;

    public function visitIfStmt(IfStmt $stmt): void;

    public function visitPrintStmt(PrintStmt $stmt): void;

    public function visitVarStmt(VarStmt $stmt): void;
}
