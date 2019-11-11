<?php

namespace Lox;

class Lox
{
    private static $hasError = false;

    public function runFile(string $file): void
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException('File does not exist');
        }

        $this->run(file_get_contents($file));

        if (self::$hasError) {
            exit(1);
        }
    }

    public function runPrompt(): void
    {
        while (true) {
            $line = readline('> ');
            $this->run($line);

            self::$hasError = false;
        }
    }

    public static function error(int $line, string $message): void
    {
        self::report($line, '', $message);
    }

    private function run(string $source): void
    {
        $scanner = new Scanner($source);
        $tokens = $scanner->scanTokens();

        foreach ($tokens as $token) {
            print $token . "\n";
        }
    }

    private static function report(int $line, string $where, string $message): void
    {
        fwrite(STDERR, sprintf('[line %s] Error%s: %s', $line, $where, $message) . PHP_EOL);

        self::$hasError = true;
    }
}
