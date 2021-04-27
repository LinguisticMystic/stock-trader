<?php

namespace Tests;

use App\Models\Stock;
use PHPUnit\Framework\TestCase;

class StockTest extends TestCase
{
    public function testSymbol()
    {
        $stock = new Stock('AAPL', 2, 1271519);
        $this->assertEquals('AAPL', $stock->symbol());
    }

    public function testAmount()
    {
        $stock = new Stock('AAPL', 2, 1271519);
        $this->assertEquals(2, $stock->amount());
    }

    public function testPrice()
    {
        $stock = new Stock('AAPL', 2, 1271519);
        $this->assertEquals(1271519, $stock->buyPrice());
    }

    public function testDate()
    {
        $stock = new Stock('AAPL', 2, 1271519);
        $this->assertEquals(time(), $stock->buyDate());
    }

}