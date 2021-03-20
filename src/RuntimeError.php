<?php

namespace Lox;

class RuntimeError extends \RuntimeException
{
    private Token $token;

    public function __construct(Token $token, string $message)
    {
        parent::__construct($message);
        $this->token = $token;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
