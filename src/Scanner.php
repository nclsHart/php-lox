<?php

namespace Lox;

class Scanner
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var Token[]
     */
    private $tokens = [];

    /**
     * @var int
     */
    private $start = 0;

    /**
     * @var int
     */
    private $current = 0;

    /**
     * @var int
     */
    private $line = 1;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function scanTokens(): array
    {
        while (!$this->isAtEnd()) {
            $this->start = $this->current;
            $this->scanToken();
        }

        $this->tokens[] = new Token(TokenType::EOF(), '', null, $this->line);

        return $this->tokens;
    }

    private function scanToken(): void
    {
        $char = $this->advance();

        switch ($char) {
            case '(':
                $this->addToken(TokenType::LEFT_PAREN());
                break;
            case ')':
                $this->addToken(TokenType::RIGHT_PAREN());
                break;
            case '{':
                $this->addToken(TokenType::LEFT_BRACE());
                break;
            case '}':
                $this->addToken(TokenType::RIGHT_BRACE());
                break;
            case ',':
                $this->addToken(TokenType::COMMA());
                break;
            case '.':
                $this->addToken(TokenType::DOT());
                break;
            case '-':
                $this->addToken(TokenType::MINUS());
                break;
            case '+':
                $this->addToken(TokenType::PLUS());
                break;
            case ';':
                $this->addToken(TokenType::SEMICOLON());
                break;
            case '*':
                $this->addToken(TokenType::STAR());
                break;
            case '!':
                $this->addToken($this->match('=') ? TokenType::BANG_EQUAL() : TokenType::BANG());
                break;
            case '=':
                $this->addToken($this->match('=') ? TokenType::EQUAL_EQUAL() : TokenType::EQUAL());
                break;
            case '<':
                $this->addToken($this->match('=') ? TokenType::LESS_EQUAL() : TokenType::LESS());
                break;
            case '>':
                $this->addToken($this->match('=') ? TokenType::GREATER_EQUAL() : TokenType::GREATER());
                break;
            case '/':
                if ($this->match('/')) {
                    while ($this->peek() !== '\n' && !$this->isAtEnd()) {
                        $this->advance();
                    }
                } else {
                    $this->addToken(TokenType::SLASH());
                }
                break;
            case ' ':
            case "\r":
            case "\t":
                // Ignore whitespace.
                break;
            case "\n":
                $this->line++;
                break;
            case '"':
                $this->string();
                break;
            default:
                if ($this->isDigit($char)) {
                    $this->number();

                    return;
                }

                if ($this->isAlpha($char)) {
                    $this->identifier();

                    return;
                }

                Lox::error($this->line, 'Unexpected character.');
                break;
        }
    }

    private function isAtEnd(): bool
    {
        return $this->current >= strlen($this->source);
    }

    private function advance(): string
    {
        $this->current++;

        return $this->charAt($this->current - 1);
    }

    private function addToken(TokenType $type, $literal = null): void
    {
        $text = substr($this->source, $this->start, $this->current - $this->start);
        $this->tokens[] = new Token($type, $text, $literal, $this->line);
    }

    private function match(string $expected): bool
    {
        if ($this->isAtEnd()) {
            return false;
        }

        if ($this->charAt($this->current) !== $expected) {
            return false;
        }

        $this->current++;

        return true;
    }

    private function peek(): string
    {
        if ($this->isAtEnd()) {
            return '\0';
        }

        return $this->charAt($this->current);
    }

    private function string(): void
    {
        while ($this->peek() !== '"' && !$this->isAtEnd()) {
            if ($this->peek() === "\n") {
                $this->line++;
            }
            $this->advance();
        }

        // Unterminated string.
        if ($this->isAtEnd()) {
            Lox::error($this->line, 'Unterminated string.');

            return;
        }

        // The closing ".
        $this->advance();

        // Trim the surrounding quotes.
        $value = substr($this->source, $this->start + 1, ($this->current - $this->start - 2));
        $this->addToken(TokenType::STRING(), $value);
    }

    private function charAt(int $position): string
    {
        return substr($this->source, $position, 1);
    }

    private function isDigit(string $char): bool
    {
        return $char >= '0' && $char <= '9';
    }

    private function number(): void
    {
        while ($this->isDigit($this->peek())) {
            $this->advance();
        }

        // Look for a fractional part.
        if ($this->peek() === '.' && $this->isDigit($this->peekNext())) {
            // Consume the "."
            $this->advance();

            while ($this->isDigit($this->peek())) {
                $this->advance();
            }
        }

        $this->addToken(TokenType::NUMBER(), (float) substr($this->source, $this->start, $this->current - $this->start));
    }

    private function peekNext(): string
    {
        if ($this->current + 1 >= strlen($this->source)) {
            return '\0';
        }

        return $this->charAt($this->current + 1);
    }

    private function identifier(): void
    {
        while ($this->isAlphaNumeric($this->peek())) {
            $this->advance();
        }

        $text = substr($this->source, $this->start, $this->current - $this->start);

        $type = Keywords::getInstance()->get($text);
        if (null === $type) {
            $type = TokenType::IDENTIFIER();
        }
        $this->addToken($type);
    }

    private function isAlpha(string $char): bool
    {
        return ($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || $char == '_';
    }

    private function isAlphaNumeric(string $char): bool
    {
        return $this->isAlpha($char) || $this->isDigit($char);
    }
}
