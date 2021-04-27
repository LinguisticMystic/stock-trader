<?php

namespace App\Services;

use App\Repositories\StockPortfolioRepository;

class GetSymbolService
{
    private StockPortfolioRepository $repository;

    public function __construct(StockPortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): string
    {
        return $this->repository->getSymbol($id)[0];
    }
}