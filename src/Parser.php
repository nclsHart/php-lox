<?php

namespace Lox;

use Lox\Expr\Assign;
use Lox\Expr\Binary;
use Lox\Expr\Expr;
use Lox\Expr\Grouping;
use Lox\Expr\Literal;
use Lox\Expr\Unary;
use Lox\Expr\Variable;
use Lox\Stmt\ExpressionStmt;
use Lox\Stmt\PrintStmt;
use Lox\Stmt\Stmt;
use Lox\Stmt\VarStmt;

class Parser
{
    /**
     * @var Token[]
     */
    private array $tokens;

    private int $current = 0;

    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return Stmt[]
     */
    public function parse(): array
    {
        $statements = [];
        while (!$this->isAtEnd()) {
            $statement = $this->declaration();

            if (null !== $statement) {
                $statements[] = $statement;
            }
        }

        return $statements;
    }

    private function declaration(): ?Stmt
    {
        try {
            if ($this->match(TokenType::VAR())) {
                return $this->varDeclaration();
            }

            return $this->statement();
        } catch (ParseError $error) {
            $this->synchronize();

            return null;
        }
    }

    private function statement(): Stmt
    {
        if ($this->match(TokenType::PRINT())) {
            return $this->printStatement();
        }

        return $this->expressionStatement();
    }

    private function printStatement(): Stmt
    {
        $value = $this->expression();
        $this->consume(TokenType::SEMICOLON(), 'Expect ";" after value.');

        return new PrintStmt($value);
    }

    private function varDeclaration(): Stmt
    {
        $name = $this->consume(TokenType::IDENTIFIER(), 'Expect variable name.');

        $initializer = null;
        if ($this->match(TokenType::EQUAL())) {
            $initializer = $this->expression();
        }

        $this->consume(TokenType::SEMICOLON(), 'Expect ";" after variable declaration.');

        return new VarStmt($name, $initializer);
    }

    private function expressionStatement(): Stmt
    {
        $expr = $this->expression();
        $this->consume(TokenType::SEMICOLON(), 'Expect ";" after expression.');

        return new ExpressionStmt($expr);
    }

    private function assignment(): Expr
    {
        $expr = $this->equality();

        if ($this->match(TokenType::EQUAL())) {
            $equals = $this->previous();
            $value = $this->assignment();

            if ($expr instanceof Variable) {
                return new Assign($expr->name(), $value);
            }

            $this->error($equals, 'Invalid assignment target.');
        }

        return $expr;
    }

    private function expression(): Expr
    {
        return $this->assignment();
    }

    private function equality(): Expr
    {
        $expr = $this->comparison();

        while ($this->match(TokenType::BANG_EQUAL(), TokenType::EQUAL_EQUAL())) {
            $operator = $this->previous();
            $right = $this->comparison();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function comparison(): Expr
    {
        $expr = $this->addition();

        while ($this->match(TokenType::GREATER(), TokenType::GREATER_EQUAL(), TokenType::LESS(), TokenType::LESS_EQUAL())) {
            $operator = $this->previous();
            $right = $this->addition();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function addition(): Expr
    {
        $expr = $this->multiplication();

        while ($this->match(TokenType::MINUS(), TokenType::PLUS())) {
            $operator = $this->previous();
            $right = $this->multiplication();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function multiplication(): Expr
    {
        $expr = $this->unary();

        while ($this->match(TokenType::SLASH(), TokenType::STAR())) {
            $operator = $this->previous();
            $right = $this->unary();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function unary(): Expr
    {
        if ($this->match(TokenType::BANG(), TokenType::MINUS())) {
            $operator = $this->previous();
            $right = $this->unary();

            return new Unary($operator, $right);
        }

        return $this->primary();
    }

    private function primary(): Expr
    {
        if ($this->match(TokenType::FALSE())) {
            return new Literal(false);
        }
        if ($this->match(TokenType::TRUE())) {
            return new Literal(true);
        }
        if ($this->match(TokenType::NIL())) {
            return new Literal(null);
        }

        if ($this->match(TokenType::NUMBER(), TokenType::STRING())) {
            return new Literal($this->previous()->literal());
        }

        if ($this->match(TokenType::IDENTIFIER())) {
            return new Variable($this->previous());
        }

        if ($this->match(TokenType::LEFT_PAREN())) {
            $expr = $this->expression();
            $this->consume(TokenType::RIGHT_PAREN(), 'Expect ")" after expression.');

            return new Grouping($expr);
        }

        throw $this->error($this->peek(), 'Expect expression.');
    }

    private function match(TokenType ...$types): bool
    {
        foreach ($types as $type) {
            if ($this->check($type)) {
                $this->advance();

                return true;
            }
        }

        return false;
    }

    private function check(TokenType $type): bool
    {
        if ($this->isAtEnd()) {
            return false;
        }

        return $this->peek()->type() == $type;
    }

    private function advance(): Token
    {
        if (!$this->isAtEnd()) {
            $this->current++;
        }

        return $this->previous();
    }

    private function isAtEnd(): bool
    {
        return $this->peek()->type() == TokenType::EOF();
    }

    private function peek(): Token
    {
        return $this->tokens[$this->current];
    }

    private function previous(): Token
    {
        return $this->tokens[$this->current - 1];
    }

    private function consume(TokenType $type, string $message): Token
    {
        if ($this->check($type)) {
            return $this->advance();
        }

        throw $this->error($this->peek(), $message);
    }

    private function error(Token $token, string $message): ParseError
    {
        Lox::parseError($token, $message);

        return new ParseError();
    }

    private function synchronize(): void
    {
        $this->advance();

        while (!$this->isAtEnd()) {
            if (TokenType::SEMICOLON() === $this->previous()->type()) {
                return;
            }

            switch ($this->peek()->type()) {
                case TokenType::TCLASS():
                case TokenType::FUN():
                case TokenType::VAR():
                case TokenType::FOR():
                case TokenType::IF():
                case TokenType::WHILE():
                case TokenType::PRINT():
                case TokenType::RETURN():
                    return;
            }

            $this->advance();
        }
    }
}
