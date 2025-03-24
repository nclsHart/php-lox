<?php

namespace Lox;

class Environment
{
    /**
     * @var array<string, mixed>
     */
    private array $values = [];

    private ?Environment $enclosing = null;

    public function __construct(Environment $enclosing = null)
    {
        $this->enclosing = $enclosing;
    }

    public function define(string $name, $value): void
    {
        $this->values[$name] = $value;
    }

    public function get(Token $name)
    {
        if (array_key_exists($name->lexeme(), $this->values)) {
            return $this->values[$name->lexeme()];
        }

        if (null !== $this->enclosing) {
            return $this->enclosing->get($name);
        }

        throw new RuntimeError($name, sprintf('Undefined variable "%s"', $name->lexeme()));
    }

    public function assign(Token $name, $value): void
    {
        if (isset($this->values[$name->lexeme()])) {
            $this->values[$name->lexeme()] = $value;

            return;
        }

        if (null !== $this->enclosing) {
            $this->enclosing->assign($name, $value);

            return;
        }

        throw new RuntimeError($name, sprintf('Undefined variable "%s"', $name->lexeme()));
    }
}
