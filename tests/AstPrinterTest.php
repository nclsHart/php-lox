<?php

use Lox\AstPrinter;
use Lox\Expr;
use Lox\Token;
use Lox\TokenType;
use PHPUnit\Framework\TestCase;

class AstPrinterTest extends TestCase
{
    public function test_print(): void
    {
        $expr = new Expr\Binary(
            new Expr\Unary(
                new Token(TokenType::MINUS(), '-', null, 1),
                new Expr\Literal(123)
            ),
            new Token(TokenType::STAR(), '*', null, 1),
            new Expr\Grouping(
                new Expr\Literal(45.67)
            )
        );

        $this->assertSame('(* (- 123) (group 45.67))', (new AstPrinter())->print($expr));
    }
}
