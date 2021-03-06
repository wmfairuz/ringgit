<?php

namespace Duit\Concerns;

use Money\Money;
use Money\MoneyFormatter;
use Duit\Contracts\Taxable;

trait Tax
{
    use Cash;

    /**
     * Enable Tax calculation.
     *
     * @var \Duit\Contracts\Taxable|null
     */
    protected $taxable;

    /**
     * Make object with Tax.
     *
     * @param int|string $amount
     * @param \Duit\Concerns\Taxable $taxable
     *
     * @return static
     */
    public static function afterTax($amount, Taxable $taxable)
    {
        return static::beforeTax(
            $taxable->getAmountWithoutTax(static::asMoney($amount)), $taxable
        );
    }

    /**
     * Make object before applying Tax.
     *
     * @param int|string $amount
     * @param \Duit\Concerns\Taxable $taxable
     *
     * @return static
     */
    public static function beforeTax($amount, Taxable $taxable)
    {
        return (new static($amount))->enableTax($taxable);
    }

    /**
     * Make object without Tax.
     *
     * @param int|string $amount
     *
     * @return static
     */
    public static function withoutTax($amount)
    {
        return (new static($amount))->disableTax();
    }

    /**
     * Get formatted amount with GST.
     *
     * @return string
     */
    public function amountWithTax(): string
    {
        return $this->getFormatter()->format(
            static::asMoney($this->getAmountWithTax())
        );
    }

    /**
     * Get formatted cash with GST.
     *
     * @return string
     */
    public function cashAmountWithTax(): string
    {
        return $this->getFormatter()->format(
            static::asMoney($this->getCashAmountWithTax())
        );
    }

    /**
     * Enable Tax for calculation.
     *
     * @return $this
     */
    final public function enableTax(Taxable $taxable): self
    {
        $this->taxable = $taxable;

        return $this;
    }

    /**
     * Disable Tax for calculation.
     *
     * @return $this
     */
    final public function disableTax(): self
    {
        $this->taxable = null;

        return $this;
    }

    /**
     * Check if the object has Tax.
     *
     * @return bool
     */
    final public function hasTax(): bool
    {
        return $this->getTax() instanceof Taxable;
    }

    /**
     * Get GST amount.
     *
     * @return string
     */
    public function getTaxAmount(): string
    {
        if (! $this->hasTax()) {
            return '0';
        }

        return $this->getTax()->getTaxAmount($this->getMoney());
    }

    /**
     * Returns the value represented by this object with Tax.
     *
     * @return string
     */
    public function getAmountWithTax(): string
    {
        if (! $this->hasTax()) {
            return $this->getMoney()->getAmount();
        }

        return $this->getTax()->getAmountWithTax($this->getMoney());
    }

    /**
     * Get amount for cash with Tax.
     *
     * @return string
     */
    public function getCashAmountWithTax(): string
    {
        return (string) $this->getClosestAcceptedCashAmount(
            $this->getAmountWithTax()
        );
    }

    /**
     * Allocate the money according to a list of ratios with Tax.
     *
     * @param  array  $ratios
     *
     * @return Money[]
     */
    public function allocateWithTax(array $ratios): array
    {
        $method = $this->hasTax() ? 'afterTax' : 'withoutTax';

        $results = [];
        $allocates = static::asMoney($this->getAmountWithTax())->allocate($ratios);

        foreach ($allocates as $allocate) {
            $results[] = static::{$method}($allocate->getAmount(), $this->getTax());
        }

        return $results;
    }

    /**
     * Allocate the money among N targets with GST.
     *
     * @param  int  $n
     *
     * @throws \InvalidArgumentException If number of targets is not an integer
     *
     * @return Money[]
     */
    public function allocateWithTaxTo(int $n): array
    {
        $method = $this->hasTax() ? 'afterTax' : 'withoutTax';

        $results = [];
        $allocates = static::asMoney($this->getAmountWithTax())->allocateTo($n);

        foreach ($allocates as $allocate) {
            $results[] = static::{$method}($allocate->getAmount(), $this->getTax());
        }

        return $results;
    }

    /**
     * Get applied tax.
     *
     * @return \Duit\Contracts\Taxable|null
     */
    abstract public function getTax(): ?Contracts\Taxable;

    /**
     * Get the money object.
     *
     * @return \Money\Money
     */
    abstract public function getMoney(): Money;

    /**
     * Build money object.
     *
     * @param  int|string  $amount
     *
     * @return \Money\Money
     */
    abstract protected static function asMoney($amount): Money;

    /**
     * Get money formatter.
     *
     * @return \Money\MoneyFormatter
     */
    abstract protected function getFormatter(): MoneyFormatter;
}
