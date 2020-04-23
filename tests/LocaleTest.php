<?php

namespace Duit\Tests;

use Duit\Locale;
use Duit\MYR;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    /**
     * @dataProvider duitExamples
     * @test
     * @param $amount
     * @param $expected
     */
    public function it_formats_money($amount, $expected)
    {
        $this->assertSame($expected, Locale::from(new MYR($amount))->amount());
    }

    public static function duitExamples()
    {
        return [
            [5, "RM\xc2\xa00.05"],
            [35, "RM\xc2\xa00.35"],
            [135, "RM\xc2\xa01.35"],
            [5005, "RM\xc2\xa050.05"],
            [52225, "RM\xc2\xa0522.25"],
            [123456, "RM\xc2\xa01,234.56"],
            [-5, "-RM\xc2\xa00.05"],
            [-35, "-RM\xc2\xa00.35"],
            [-135, "-RM\xc2\xa01.35"],
            [-5005, "-RM\xc2\xa050.05"],
            [-52225, "-RM\xc2\xa0522.25"],
            [-123456, "-RM\xc2\xa01,234.56"],
        ];
    }
}
