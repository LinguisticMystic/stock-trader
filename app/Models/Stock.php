<?php

namespace App\Models;

class Stock
{
    private string $symbol;
    private int $amount;
    private int $buyPrice;
    private int $buyDate;

    public function __construct(
        string $symbol,
        int $amount,
        int $buyPrice)
    {
        $this->symbol = strtoupper($symbol);
        $this->amount = $amount;
        $this->buyPrice = $buyPrice;
        $this->buyDate = time();
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function buyPrice(): int
    {
        return $this->buyPrice;
    }

    public function buyDate(): int
    {
        return $this->buyDate;
    }
}