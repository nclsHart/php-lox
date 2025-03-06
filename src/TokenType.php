<?php

namespace Lox;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @extends Enum<int>
 *
 * @method static TokenType LEFT_PAREN()
 * @method static TokenType RIGHT_PAREN()
 * @method static TokenType LEFT_BRACE()
 * @method static TokenType RIGHT_BRACE()
 * @method static TokenType COMMA()
 * @method static TokenType DOT()
 * @method static TokenType MINUS()
 * @method static TokenType PLUS()
 * @method static TokenType SEMICOLON()
 * @method static TokenType SLASH()
 * @method static TokenType STAR()
 * @method static TokenType BANG()
 * @method static TokenType BANG_EQUAL()
 * @method static TokenType EQUAL()
 * @method static TokenType EQUAL_EQUAL()
 * @method static TokenType GREATER()
 * @method static TokenType GREATER_EQUAL()
 * @method static TokenType LESS()
 * @method static TokenType LESS_EQUAL()
 * @method static TokenType IDENTIFIER()
 * @method static TokenType STRING()
 * @method static TokenType NUMBER()
 * @method static TokenType AND()
 * @method static TokenType ELSE()
 * @method static TokenType FALSE()
 * @method static TokenType FUN()
 * @method static TokenType FOR()
 * @method static TokenType IF()
 * @method static TokenType NIL()
 * @method static TokenType OR()
 * @method static TokenType PRINT()
 * @method static TokenType RETURN()
 * @method static TokenType SUPER()
 * @method static TokenType TCLASS()
 * @method static TokenType THIS()
 * @method static TokenType TRUE()
 * @method static TokenType VAR()
 * @method static TokenType WHILE()
 * @method static TokenType EOF()
 */
class TokenType extends Enum
{
    // Single-character tokens.
    private const LEFT_PAREN = 1;
    private const RIGHT_PAREN = 2;
    private const LEFT_BRACE = 3;
    private const RIGHT_BRACE = 4;
    private const COMMA = 5;
    private const DOT = 6;
    private const MINUS = 7;
    private const PLUS = 8;
    private const SEMICOLON = 9;
    private const SLASH = 10;
    private const STAR = 11;

    // One or two character tokens.
    private const BANG = 12;
    private const BANG_EQUAL = 13;
    private const EQUAL = 14;
    private const EQUAL_EQUAL = 15;
    private const GREATER = 16;
    private const GREATER_EQUAL = 17;
    private const LESS = 18;
    private const LESS_EQUAL = 19;

    // Literals.
    private const IDENTIFIER = 20;
    private const STRING = 21;
    private const NUMBER = 22;

    // Keywords.
    private const AND = 23;
    private const ELSE = 24;
    private const FALSE = 25;
    private const FUN = 26;
    private const FOR = 27;
    private const IF = 28;
    private const NIL = 29;
    private const OR = 30;
    private const PRINT = 31;
    private const RETURN = 32;
    private const SUPER = 33;
    private const TCLASS = 34;
    private const THIS = 35;
    private const TRUE = 36;
    private const VAR = 37;
    private const WHILE = 38;

    private const EOF = 39;

    /**
     * @psalm-pure
     */
    public function __toString(): string
    {
        return (string) $this->getKey();
    }
}
