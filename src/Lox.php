<?php

namespace Lox;

class Lox
{
    private static $hadError = false;

    public function runFile(string $file): void
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException('File does not exist');
        }

        $this->run(file_get_contents($file));

        if (self::$hadError) {
            exit(1);
        }
    }

    public function runPrompt(): void
    {
        while (true) {
            $line = readline('> ');
            $this->run($line);

            self::$hadError = false;
        }
    }

    public static function scanError(int $line, string $message): void
    {
        self::report($line, '', $message);
    }

    public static function parseError(Token $token, string $message): void
    {
        if (TokenType::EOF() === $token->type()) {
            self::report($token->line(), ' at end', $message);

            return;
        }

        self::report($token->line(), sprintf(' at "%s"', $token->lexeme()), $message);
    }

    private function run(string $source): void
    {
        $scanner = new Scanner($source);
        $tokens = $scanner->scanTokens();

        $parser = new Parser($tokens);
        $expression = $parser->parse();

        if (self::$hadError) {
            return;
        }

        print (new AstPrinter())->print($expression) . "\n";
    }

    private static function report(int $line, string $where, string $message): void
    {
        fwrite(STDERR, sprintf('[line %s] Error%s: %s', $line, $where, $message) . PHP_EOL);

        self::$hadError = true;
    }
}
