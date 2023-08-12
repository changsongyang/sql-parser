<?php

declare(strict_types=1);

namespace PhpMyAdmin\SqlParser\Tests\Parser;

use PhpMyAdmin\SqlParser\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ReplaceStatementTest extends TestCase
{
    #[DataProvider('replaceProvider')]
    public function testReplace(string $test): void
    {
        $this->runParserTest($test);
    }

    /**
     * @return string[][]
     */
    public static function replaceProvider(): array
    {
        return [
            ['parser/parseReplace'],
            ['parser/parseReplace2'],
            ['parser/parseReplaceValues'],
            ['parser/parseReplaceSet'],
            ['parser/parseReplaceSelect'],
            ['parser/parseReplaceErr'],
            ['parser/parseReplaceErr2'],
            ['parser/parseReplaceErr3'],
            ['parser/parseReplaceIntoErr'],
        ];
    }
}
