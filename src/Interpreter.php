<?php

namespace Lox;

use Lox\Expr\Binary;
use Lox\Expr\Expr;
use Lox\Expr\Grouping;
use Lox\Expr\Literal;
use Lox\Expr\Unary;
use Lox\Expr\Visitor;

class Interpreter implements Visitor
{
    public function interpret(Expr $expression): void
    {
        try {
            $value = $this->evaluate($expression);
            print $this->stringify($value) . "\n";
        } catch (RuntimeError $error) {
            Lox::runtimeError($error);
        }
    }

    private function stringify($object): string
    {
        if ($object == null) {
            return 'nil';
        }

        return (string)$object;
    }

    public function visitBinaryExpr(Binary $expr)
    {
        $left = $this->evaluate($expr->left());
        $right = $this->evaluate($expr->right());

        switch ($expr->operator()->type()) {
            case TokenType::BANG_EQUAL():
                return !$this->isEqual($left, $right);
            case TokenType::EQUAL_EQUAL():
                return $this->isEqual($left, $right);
            case TokenType::GREATER():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left > (float)$right;
            case TokenType::GREATER_EQUAL():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left >= (float)$right;
            case TokenType::LESS():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left < (float)$right;
            case TokenType::LESS_EQUAL():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left <= (float)$right;
            case TokenType::MINUS():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left - (float)$right;
            case TokenType::PLUS():
                if (is_float($left) && is_float($right)) {
                    return (float)$left + (float)$right;
                }

                if (is_string($left) && is_string($right)) {
                    return (string)$left . (string)$right;
                }

                throw new RuntimeError($expr->operator(), 'Operands must be two numbers or two strings.');
            case TokenType::SLASH():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left / (float)$right;
            case TokenType::STAR():
                $this->checkNumberOperands($expr->operator(), $left, $right);
                return (float)$left * (float)$right;
        }

        // Unreachable.
        return null;
    }

    public function visitGroupingExpr(Grouping $expr)
    {
        return $this->evaluate($expr->expression());
    }

    public function visitLiteralExpr(Literal $expr)
    {
        return $expr->value();
    }

    public function visitUnaryExpr(Unary $expr)
    {
        $right = $this->evaluate($expr->right());

        switch ($expr->operator()->type()) {
            case TokenType::BANG():
                return !$this->isTruthy($right);
            case TokenType::MINUS():
                $this->checkNumberOperand($expr->operator(), $right);
                return -(float)$right;
        }

        // Unreachable.
        return null;
    }

    private function evaluate(Expr $expr)
    {
        return $expr->accept($this);
    }

    private function isTruthy($object): bool
    {
        if (null === $object) {
            return false;
        }

        if (is_bool($object)) {
            return $object;
        }

        return true;
    }

    private function isEqual($a, $b): bool
    {
        // nil is only equal to nil.
        if (null === $a && null === $b) {
            return true;
        }

        if (null === $a) {
            return false;
        }

        return $a == $b;
    }

    private function checkNumberOperand(Token $operator, $operand): void
    {
        if (is_float($operand)) {
            return;
        }

        throw new RuntimeError($operator, 'Operand must be a number.');
    }

    private function checkNumberOperands(Token $operator, $left, $right): void
    {
        if (is_float($left) && is_float($right)) {
            return;
        }

        throw new RuntimeError($operator, 'Operands must be numbers.');
    }
}
