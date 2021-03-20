<?php

namespace Lox;

final class Keywords
{
    private static ?self $instance = null;

    private static array $keywords;

    private function __construct()
    {
        self::$keywords = [
            'and' => TokenType::AND(),
            'class' => TokenType::TCLASS(),
            'else' => TokenType::ELSE(),
            'false' => TokenType::FALSE(),
            'for' => TokenType::FOR(),
            'fun' =>  TokenType::FUN(),
            'if' => TokenType::IF(),
            'nil' => TokenType::NIL(),
            'or' => TokenType::OR(),
            'print' => TokenType::PRINT(),
            'return' => TokenType::RETURN(),
            'super' => TokenType::SUPER(),
            'this' => TokenType::THIS(),
            'true' => TokenType::TRUE(),
            'var' => TokenType::VAR(),
            'while' => TokenType::WHILE(),
        ];
    }

    public function get(string $keyword): ?TokenType
    {
        if (!isset(self::$keywords[$keyword])) {
            return null;
        }

        return self::$keywords[$keyword];
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
