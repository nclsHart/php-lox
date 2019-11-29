<?php

use Lox\Scanner;
use PHPUnit\Framework\TestCase;

class ScannerTest extends TestCase
{
    /**
     * @dataProvider provideSource
     */
    public function test_scan_tokens(string $source, array $expectedTokens): void
    {
        $scanner = new Scanner($source);
        $tokens = $scanner->scanTokens();

        $this->assertSameSize($expectedTokens, $tokens);
        foreach ($expectedTokens as $expectedToken) {
            $this->assertSame($expectedToken, (string) array_shift($tokens));
        }
    }

    public function provideSource(): array
    {
        return [
            'single_character_tokens' => [
                '(){},.-+;*', [
                    'LEFT_PAREN ( null',
                    'RIGHT_PAREN ) null',
                    'LEFT_BRACE { null',
                    'RIGHT_BRACE } null',
                    'COMMA , null',
                    'DOT . null',
                    'MINUS - null',
                    'PLUS + null',
                    'SEMICOLON ; null',
                    'STAR * null',
                    'EOF  null'
                ]
            ],
            'one_or_two_characters_tokens' => [
                '!!====<=<>=>', [
                    'BANG ! null',
                    'BANG_EQUAL != null',
                    'EQUAL_EQUAL == null',
                    'EQUAL = null',
                    'LESS_EQUAL <= null',
                    'LESS < null',
                    'GREATER_EQUAL >= null',
                    'GREATER > null',
                    'EOF  null'
                ]
            ],
            'whitespace' => [
                " \r\t\n", [
                    'EOF  null'
                ]
            ],
            'strings' => [
                '"" "string"', [
                    'STRING "" ',
                    'STRING "string" string',
                    'EOF  null',
                ]
            ],
            'numbers' => [
                '123 123.456 .456 123.', [
                    'NUMBER 123 123',
                    'NUMBER 123.456 123.456',
                    'DOT . null',
                    'NUMBER 456 456',
                    'NUMBER 123 123',
                    'DOT . null',
                    'EOF  null',
                ]
            ],
            'identifiers' => [
                'andy formless fo _ _123 _abc ab123 abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_', [
                    'IDENTIFIER andy null',
                    'IDENTIFIER formless null',
                    'IDENTIFIER fo null',
                    'IDENTIFIER _ null',
                    'IDENTIFIER _123 null',
                    'IDENTIFIER _abc null',
                    'IDENTIFIER ab123 null',
                    'IDENTIFIER abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_ null',
                    'EOF  null',
                ]
            ],
            'keywords' => [
                'and class else false for fun if nil or return super this true var while', [
                    'AND and null',
                    'TCLASS class null',
                    'ELSE else null',
                    'FALSE false null',
                    'FOR for null',
                    'FUN fun null',
                    'IF if null',
                    'NIL nil null',
                    'OR or null',
                    'RETURN return null',
                    'SUPER super null',
                    'THIS this null',
                    'TRUE true null',
                    'VAR var null',
                    'WHILE while null',
                    'EOF  null',
                ]
            ]
        ];
    }

    public function test_scan_complete_file(): void
    {
        $scanner = new Scanner(file_get_contents(__DIR__ . '/lox/operator/add.lox'));
        $tokens = $scanner->scanTokens();

        $this->assertCount(11, $tokens);
        $this->assertSame('PRINT print null', (string) array_shift($tokens));
        $this->assertSame('NUMBER 123 123', (string) array_shift($tokens));
        $this->assertSame('PLUS + null', (string) array_shift($tokens));
        $this->assertSame('NUMBER 456 456', (string) array_shift($tokens));
        $this->assertSame('SEMICOLON ; null', (string) array_shift($tokens));
        $this->assertSame('PRINT print null', (string) array_shift($tokens));
        $this->assertSame('STRING "str" str', (string) array_shift($tokens));
        $this->assertSame('PLUS + null', (string) array_shift($tokens));
        $this->assertSame('STRING "ing" ing', (string) array_shift($tokens));
        $this->assertSame('SEMICOLON ; null', (string) array_shift($tokens));
        $this->assertSame('EOF  null', (string) array_shift($tokens));
    }
}
