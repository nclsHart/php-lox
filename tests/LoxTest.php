<?php

use PHPUnit\Framework\TestCase;
use SebastianBergmann\FileIterator\Factory;
use Symfony\Component\Process\Process;

class LoxTest extends TestCase
{
    const FIXTURES_DIR = __DIR__ . '/lox';

    /**
     * @dataProvider provideFiles
     */
    public function test_lox(SplFileInfo $file): void
    {
        $process = Process::fromShellCommandline(sprintf(__DIR__ . '/../bin/php-lox %s', $file->getPathname()));
        $process->run();

        $lineNum = 1;
        $expectations = [];
        $resource = fopen($file->getPathname(), 'r');
        while ($line = fgets($resource)) {
            $matches = [];
            preg_match('#// expect: ?(?P<output>.*)#', $line, $matches);

            if (!empty($matches)) {
                $expectations[] = [$matches['output'], $lineNum];
            }

            $lineNum++;
        }
        fclose($resource);

        $output = $process->getOutput();
        $outputLines = explode("\n", $output);
        array_pop($outputLines);

        $index = 0;
        foreach ($outputLines as $outputLine) {
            if ($index >= count($expectations)) {
                $this->fail(sprintf('Got output "%s" when none was expected.', $outputLine));
            };

            $this->assertSame(
                $expectations[$index][0],
                $outputLine,
                sprintf('Expected output "%s" on line %s and got "%s".', $expectations[$index][0], $expectations[$index][1], $outputLine)
            );

            $index++;
        }

        if ($index < count($expectations)) {
            $this->fail(sprintf('Missing expected output "%s" on line %s.', $expectations[$index][0], $expectations[$index][1]));
        }
    }

    public function provideFiles(): iterable
    {
        $files = (new Factory())->getFileIterator(__DIR__ . '/lox', '.lox');

        foreach($files as $file) {
            yield $this->extractDataSetName($file) => [$file];
        }
    }

    private function extractDataSetName(SplFileInfo $file): string
    {
        return trim(
            str_replace(self::FIXTURES_DIR, '', $file->getPathname()), '/'
        );
    }
}
