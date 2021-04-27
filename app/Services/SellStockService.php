<?php

namespace App\Services;

use App\Repositories\StockPortfolioRepository;

class SellStockService
{
    private StockPortfolioRepository $repository;

    public function __construct(StockPortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $key, int $currentStockPrice, int $postAmount): void
    {
        $this->repository->sellStock($key, $currentStockPrice, $postAmount);
    }

}