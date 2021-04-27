<?php

namespace App\Repositories;

use App\Models\Stock;

interface StockPortfolioRepository
{
    public function addStock(Stock $stock): void;
    public function getPurchaseHistory(): array;
}