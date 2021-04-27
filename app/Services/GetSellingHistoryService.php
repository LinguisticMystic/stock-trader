<?php

namespace App\Services;

use App\Repositories\StockPortfolioRepository;

class GetSellingHistoryService
{
    private StockPortfolioRepository $repository;

    public function __construct(StockPortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->getSellingHistory();
    }
}