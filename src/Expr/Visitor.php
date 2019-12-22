<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

interface Visitor
{
    public function visitAssignExpr(Assign $expr);

    public function visitBinaryExpr(Binary $expr);

    public function visitGroupingExpr(Grouping $expr);

    public function visitLiteralExpr(Literal $expr);

    public function visitUnaryExpr(Unary $expr);

    public function visitVariableExpr(Variable $expr);
}
