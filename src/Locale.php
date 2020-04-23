<?php

namespace Duit;

use Duit\Contracts\Money as MoneyContract;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;

class Locale implements Contracts\Locale
{
    public static function from(MoneyContract $money, string $locale = 'ms-MY'): MoneyContract
    {
        $money->setFormatter(
            new IntlMoneyFormatter(
                new \NumberFormatter($locale, \NumberFormatter::CURRENCY),
                new ISOCurrencies()
            )
        );

        return $money;
    }
}
