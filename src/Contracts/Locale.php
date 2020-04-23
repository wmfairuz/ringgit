<?php


namespace Duit\Contracts;

use Duit\Contracts\Money as MoneyContract;

interface Locale
{
    /**
     * Get money
     *
     * @param MoneyContract $money
     *
     * @param string $locale
     * @return MoneyContract
     */
    public static function from(MoneyContract $money, string $locale = 'ms-MY'): MoneyContract;
}