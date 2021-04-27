<?php

namespace App\Services;

use App\Repositories\StockPortfolioRepository;

class GetLatestPurchaseService
{
    private StockPortfolioRepository $repository;

    public function __construct(StockPortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $symbol): int
    {
        return $this->repository->getLatestPurchase($symbol);
    }
}