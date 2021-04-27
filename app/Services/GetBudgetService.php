<?php

namespace App\Services;

use App\Repositories\BudgetRepository;

class GetBudgetService
{
    private BudgetRepository $repository;

    public function __construct(BudgetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): int
    {
        return $this->repository->getCurrentBudget();
    }
}