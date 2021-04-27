<?php

namespace App\Services;

use App\Models\Stock;
use App\Repositories\StockPortfolioRepository;

class BuyStockService
{
    private StockPortfolioRepository $repository;

    public function __construct(StockPortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(Stock $stock): void
    {
        $this->repository->addStock($stock);
    }

}