<?php

namespace Lox;

class Token
{
    /**
     * @var TokenType
     */
    private $type;

    /**
     * @var string
     */
    private $lexeme;

    private $literal;

    /**
     * @var int
     */
    private $line;

    public function __construct(TokenType $type, string $lexeme, $literal, int $line)
    {
        $this->type = $type;
        $this->lexeme = $lexeme;
        $this->literal = $literal;
        $this->line = $line;
    }

    public function __toString(): string
    {
        return sprintf('%s %s %s', $this->type->getKey(), $this->lexeme, null === $this->literal ? 'null' : $this->literal);
    }
}
