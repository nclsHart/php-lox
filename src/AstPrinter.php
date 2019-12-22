<?php

namespace Lox;

use Lox\Expr\Assign;
use Lox\Expr\Binary;
use Lox\Expr\Expr;
use Lox\Expr\Grouping;
use Lox\Expr\Literal;
use Lox\Expr\Unary;
use Lox\Expr\Variable;
use Lox\Expr\Visitor;
use Lox\Stmt\Stmt;

class AstPrinter implements Visitor
{
    public function print(Expr $expr): string
    {
        return $expr->accept($this);
    }

    public function visitBinaryExpr(Binary $expr): string
    {
        return $this->parenthesize($expr->operator()->lexeme(), $expr->left(), $expr->right());
    }

    public function visitGroupingExpr(Grouping $expr): string
    {
        return $this->parenthesize('group', $expr->expression());
    }

    public function visitLiteralExpr(Literal $expr): string
    {
        if (null === $expr->value()) {
            return 'nil';
        }

        return (string) $expr->value();
    }

    public function visitUnaryExpr(Unary $expr): string
    {
        return $this->parenthesize($expr->operator()->lexeme(), $expr->right());
    }

    public function visitVariableExpr(Variable $expr)
    {
        return $expr->name()->lexeme();
    }

    public function visitAssignExpr(Assign $expr)
    {
        return $this->parenthesize('=', $expr->name()->lexeme(), $expr->value());
    }

    private function parenthesize(string $name, ...$parts): string
    {
        $result = '(' . $name;
        foreach ($parts as $part) {
            $result .= ' ';

            if ($part instanceof Expr || $part instanceof Stmt) {
                $result .= $part->accept($this);
            } elseif ($part instanceof Token) {
                $result .= $part->lexeme();
            } else {
                $result .= $part;
            }
        }
        $result .= ')';

        return $result;
    }
}
