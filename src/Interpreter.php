<?php

namespace Lox;

use Lox\Expr\Assign;
use Lox\Expr\Binary;
use Lox\Expr\Expr;
use Lox\Expr\Grouping;
use Lox\Expr\Literal;
use Lox\Expr\Unary;
use Lox\Expr\Variable;
use Lox\Expr\Visitor as VisitorExpr;
use Lox\Stmt\ExpressionStmt;
use Lox\Stmt\PrintStmt;
use Lox\Stmt\Stmt;
use Lox\Stmt\VarStmt;
use Lox\Stmt\Visitor as VisitorStmt;

class Interpreter implements VisitorExpr, VisitorStmt
{
    private Environment $environment;

    public function __construct()
    {
        $this->environment = new Environment();
    }

    /**
     * @param Stmt[] $statements
     */
    public function interpret(array $statements): void
    {
        try {
            foreach ($statements as $statement) {
                $this->execute($statement);
            }
        } catch (RuntimeError $error) {
            Lox::runtimeError($error);
        }
    }

    private function stringify($object): string
    {
        if (null === $object) {
            return 'nil';
        }

        if (true === $object) {
            return 'true';
        }

        if (false === $object) {
            return 'false';
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
                    return $left + $right;
                }

                if (is_string($left) && is_string($right)) {
                    return $left . $right;
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

    public function visitVariableExpr(Variable $expr)
    {
        return $this->environment->get($expr->name());
    }

    public function visitExpressionStmt(ExpressionStmt $stmt): void
    {
        $this->evaluate($stmt->expression());
    }

    public function visitPrintStmt(PrintStmt $stmt): void
    {
        $value = $this->evaluate($stmt->expression());
        print $this->stringify($value) . "\n";
    }

    public function visitVarStmt(VarStmt $stmt): void
    {
        $value = null;
        if ($stmt->initializer() !== null) {
            $value = $this->evaluate($stmt->initializer());
        }

        $this->environment->define($stmt->name()->lexeme(), $value);
    }

    public function visitAssignExpr(Assign $expr)
    {
        $value = $this->evaluate($expr->value());

        $this->environment->assign($expr->name(), $value);

        return $value;
    }

    private function evaluate(Expr $expr)
    {
        return $expr->accept($this);
    }

    private function execute(Stmt $stmt): void
    {
        $stmt->accept($this);
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

        return $a === $b;
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
