<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

interface Visitor
{
    public function visitExpressionStmtStmt(ExpressionStmt $stmt);

    public function visitPrintStmtStmt(PrintStmt $stmt);
}
