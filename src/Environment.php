<?php

namespace Lox;

class Environment
{
    /**
     * @var array
     */
    private $values = [];

    public function define(string $name, $value): void
    {
        $this->values[$name] = $value;
    }

    public function get(Token $name)
    {
        if (isset($this->values[$name->lexeme()])) {
            return $this->values[$name->lexeme()];
        }

        throw new RuntimeError($name, sprintf('Undefined variable "%s"', $name->lexeme()));
    }
}
