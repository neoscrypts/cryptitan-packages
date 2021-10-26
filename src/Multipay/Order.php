<?php

namespace NeoScrypts\Multipay;


use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use BadMethodCallException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

class Order
{
    /**
     * Order currency
     *
     * @var Currency
     */
    protected Currency $currency;

    /**
     * Total amount
     *
     * @var Money
     */
    protected Money $totalAmount;

    /**
     * Shipping
     *
     * @var Money
     */
    protected Money $shipping;

    /**
     * Handling
     *
     * @var Money
     */
    protected Money $handling;

    /**
     * Discount
     *
     * @var Money
     */
    protected Money $discount;

    /**
     * Order items
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Order unique id
     *
     * @var string
     */
    protected string $uuid;

    /**
     * Initialize with currency.
     *
     * @param string $currency
     * @param string|float $totalAmount
     */
    public function __construct(string $currency, $totalAmount = null)
    {
        $this->uuid = Str::uuid()->toString();
        $this->currency = new Currency($currency);

        if (!is_null($totalAmount)) {
            $this->totalAmount = new Money(Validator::validateAmount($totalAmount), $this->currency, true);
        }
    }

    /**
     * Get unique id
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Get total amount
     *
     * @return Money
     */
    public function getTotalAmount(): Money
    {
        if ($this->isFixed()) {
            return $this->totalAmount;
        }

        $aggregate = $this->getSubTotal()->add($this->getTotalTax());

        if ($discount = $this->getDiscount()) {
            $aggregate = $aggregate->subtract($discount);
        }

        if ($shipping = $this->getShipping()) {
            $aggregate = $aggregate->add($shipping);
        }

        if ($handling = $this->getHandling()) {
            $aggregate = $aggregate->add($handling);
        }

        return $aggregate;
    }

    /**
     * Get subTotal
     *
     * @return Money
     */
    public function getSubTotal(): Money
    {
        $this->assertUnfixed();

        return collect($this->items)
            ->reduce(function (Money $aggregate, OrderItem $item) {
                return $aggregate->add($item->getAmount());
            }, new Money(0, $this->currency));
    }

    /**
     * Get tax
     *
     * @return Money
     */
    public function getTotalTax(): Money
    {
        $this->assertUnfixed();

        return collect($this->items)
            ->reduce(function (Money $aggregate, OrderItem $item) {
                return ($tax = $item->getTax()) ? $aggregate->add($tax) : $aggregate;
            }, new Money(0, $this->currency));
    }

    /**
     * Add item to order.
     *
     * @param OrderItem $item
     * @return $this
     */
    public function addItem(OrderItem $item)
    {
        $this->assertUnfixed();

        if (!$item->getCurrency()->equals($this->currency)) {
            throw new InvalidArgumentException("Currency does not match with item");
        }

        $this->items[] = $item;
        return $this;
    }

    /**
     * Collect items
     *
     * @return Collection
     */
    public function collectItems()
    {
        return collect($this->items);
    }

    /**
     * Set shipping fee
     *
     * @param string|float $shipping
     */
    public function setShipping($shipping)
    {
        $this->assertUnfixed();

        $this->shipping = new Money(Validator::validateAmount($shipping), $this->currency, true);
        return $this;
    }

    /**
     * @return Money
     */
    public function getShipping(): Money
    {
        $this->assertUnfixed();

        return $this->shipping;
    }

    /**
     * Set handling fee
     *
     * @param string|float $handling
     */
    public function setHandling($handling)
    {
        $this->assertUnfixed();

        $this->handling = new Money(Validator::validateAmount($handling), $this->currency, true);
        return $this;
    }

    /**
     * @return Money
     */
    public function getHandling(): Money
    {
        $this->assertUnfixed();

        return $this->handling;
    }

    /**
     * Set discount
     *
     * @param string|float $discount
     */
    public function setDiscount($discount)
    {
        $this->assertUnfixed();

        $this->discount = new Money(Validator::validateAmount($discount), $this->currency, true);
        return $this;
    }

    /**
     * @return Money
     */
    public function getDiscount(): Money
    {
        $this->assertUnfixed();

        return $this->discount;
    }

    /**
     * Assert that the order is not fixed
     *
     * @return void
     */
    protected function assertUnfixed()
    {
        if ($this->isFixed()) {
            throw new BadMethodCallException("Order has a fixed amount");
        }
    }

    /**
     * Has fixed amount
     *
     * @return bool
     */
    public function isFixed()
    {
        return isset($this->totalAmount);
    }
}