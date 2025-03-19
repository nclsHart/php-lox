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

            $lineNum++;
        }
        fclose($resource);

        $output = $process->getOutput();

        $this->assertExpectedOutputs($output);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function provideFiles(): iterable
    {
        /**
         * @psalm-suppress InternalClass InternalMethod
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

    private function assertExpectedOutputs($output): void
    {
        $outputLines = explode("\n", $output);
        array_pop($outputLines);

        $index = 0;
        foreach ($outputLines as $outputLine) {
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
}
