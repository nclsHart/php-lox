<?php

namespace Lox\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\FileIterator\Factory;
use Symfony\Component\Process\Process;

class LoxTest extends TestCase
{
    /**
     * @var list<ExpectedOutput>
     */
    private array $expectedOutputs = [];

    /**
     * @var list<string>
     */
    private array $expectedErrors = [];

    private ?string $expectedRuntimeError = null;

    private int $expectedExitCode = 0;

    public const string FIXTURES_DIR = __DIR__ . '/lox';

    #[DataProvider('provideFiles')]
    public function test_lox(\SplFileInfo $file): void
    {
        $process = Process::fromShellCommandline(sprintf(__DIR__ . '/../bin/php-lox %s', $file->getPathname()));
        $process->run();

        $resource = fopen($file->getPathname(), 'r');
        if (false === $resource) {
            throw new \RuntimeException(sprintf('Unable to open file: %s', $file->getPathname()));
        }

        $lineNum = 1;
        while ($line = fgets($resource)) {
            $this->collectExpectedOutputs($line, $lineNum);
            $this->collectExpectedErrors($line);
            $this->collectExpectedRuntimeError($line, $lineNum);

            $lineNum++;
        }
        fclose($resource);

        $errorLines = $this->splitOutput($process->getErrorOutput());
        if (null !== $this->expectedRuntimeError) {
            $this->assertRuntimeError($errorLines);
        } else {
            $this->assertExpectedErrors($errorLines);
        }

        $this->assertExitCode((int) $process->getExitCode());
        $this->assertExpectedOutputs($process->getOutput());
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function provideFiles(): iterable
    {
        /**
         * @psalm-suppress InternalClass
         * @psalm-suppress InternalMethod
         */
        $files = (new Factory())->getFileIterator(__DIR__ . '/lox', '.lox');

        foreach ($files as $file) {
            yield LoxTest::extractDataSetName($file) => [$file];
        }
    }

    private static function extractDataSetName(\SplFileInfo $file): string
    {
        return trim(
            str_replace(self::FIXTURES_DIR, '', $file->getPathname()),
            '/'
        );
    }

    private function collectExpectedOutputs(string $line, int $lineNum): void
    {
        $matches = [];
        preg_match('#// expect: ?(?P<output>.*)#', $line, $matches);

        if (!empty($matches)) {
            $this->expectedOutputs[] = new ExpectedOutput($matches['output'], $lineNum);
        }
    }

    private function collectExpectedErrors(string $line): void
    {
        $matches = [];
        preg_match('#// \[line (?P<line>\d+)] (?P<message>Error.*)#', $line, $matches);

        if (!empty($matches)) {
            $this->expectedErrors[] = sprintf('[%s] %s', $matches['line'], $matches['message']);
            $this->expectedExitCode = 65;
        }
    }

    private function collectExpectedRuntimeError(string $line, int $lineNum): void
    {
        $matches = [];
        preg_match('#// expect runtime error: (?P<message>.+)#', $line, $matches);

        if (!empty($matches)) {
            $this->expectedRuntimeError = sprintf('%s [line %s]', $matches['message'], $lineNum);
            $this->expectedExitCode = 70;
        }
    }

    private function assertExpectedOutputs(string $output): void
    {
        $index = 0;
        foreach ($this->splitOutput($output) as $outputLine) {
            if ($index >= count($this->expectedOutputs)) {
                $this->fail(sprintf('Got output "%s" when none was expected.', $outputLine));
            }

            $this->assertSame(
                $this->expectedOutputs[$index]->output,
                $outputLine,
                sprintf('Expected output "%s" on line %s and got "%s".', $this->expectedOutputs[$index]->output, $this->expectedOutputs[$index]->line, $outputLine)
            );

            $index++;
        }

        if ($index < count($this->expectedOutputs)) {
            $this->fail(sprintf('Missing expected output "%s" on line %s.', $this->expectedOutputs[$index]->output, $this->expectedOutputs[$index]->line));
        }
    }

    private function assertExpectedErrors(array $errorLines): void
    {
        /**
         * @var list<string> $foundErrors
         */
        $foundErrors = [];
        foreach ($errorLines as $errorLine) {
            $matches = [];
            preg_match('#\[line (?P<line>\d+)] (?P<message>Error.*)#', $errorLine, $matches);

            if (!empty($matches)) {
                $error = sprintf('[%s] %s', $matches['line'], $matches['message']);

                $contains = in_array($error, $this->expectedErrors, true);
                if ($contains) {
                    $foundErrors[] = $error;
                }

                $this->assertTrue($contains, sprintf('Unexpected error: %s', $errorLine));
            } elseif ($errorLine != "") {
                $this->fail(sprintf('Unexpected output on stderr: %s', $errorLine));
            }
        }

        $missingErrors = array_diff($this->expectedErrors, $foundErrors);
        $this->assertEmpty($missingErrors, sprintf('Missing expected errors: %s', implode(', ', $missingErrors)));
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    private function assertRuntimeError(array $errorLines): void
    {
        $this->assertNotEmpty($errorLines, sprintf('Expected runtime error "%s" and got none', $this->expectedRuntimeError));
        $this->assertSame($this->expectedRuntimeError, reset($errorLines), sprintf('Expected runtime error "%s" and got: %s', $this->expectedRuntimeError, reset($errorLines)));
    }

    private function assertExitCode(int $exitCode): void
    {
        $this->assertSame($this->expectedExitCode, $exitCode, sprintf('Expected return code %s and got %s', $this->expectedExitCode, $exitCode));
    }

    /**
     * @return list<string>
     */
    private function splitOutput(string $output): array
    {
        $lines = explode("\n", $output);
        array_pop($lines);

        return $lines;
    }
}
