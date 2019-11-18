<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Expr;

interface Visitor
{
    public function visitBinaryExpr(Binary $expr): Visitor;

    public function visitGroupingExpr(Grouping $expr): Visitor;

    public function visitLiteralExpr(Literal $expr): Visitor;

    public function visitUnaryExpr(Unary $expr): Visitor;
}
