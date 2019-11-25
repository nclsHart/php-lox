<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

interface Visitor
{
    public function visitExpressionStmt(ExpressionStmt $stmt);

    public function visitPrintStmt(PrintStmt $stmt);
}
