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
            ]
        ];
    }
}
