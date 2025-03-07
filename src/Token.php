<?php

namespace Lox;

class Token
{
    private TokenType $type;

    private string $lexeme;

    private mixed $literal;

    private int $line;

    public function __construct(TokenType $type, string $lexeme, mixed $literal, int $line)
    {
        $this->type = $type;
        $this->lexeme = $lexeme;
        $this->literal = $literal;
        $this->line = $line;
    }

    public function type(): TokenType
    {
        return $this->type;
    }

    public function lexeme(): string
    {
        return $this->lexeme;
    }

    public function literal(): mixed
    {
        return $this->literal;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function __toString(): string
    {
        return sprintf('%s %s %s', $this->type->name, $this->lexeme, null === $this->literal ? 'null' : $this->literal);
    }
}
