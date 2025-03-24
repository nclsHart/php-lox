<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace Lox\Stmt;

final class BlockStmt extends Stmt
{
    /** @var list<Stmt> */
    private array $statements;

    /**
     * @param list<Stmt> $statements
     */
    public function __construct(array $statements)
    {
        $this->statements = $statements;
    }

    /**
     * @return list<Stmt>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    #[\Override]
    public function accept(Visitor $visitor)
    {
        return $visitor->visitBlockStmt($this);
    }
}
