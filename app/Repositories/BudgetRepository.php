<?php

namespace App\Repositories;

interface BudgetRepository
{
    public function getCurrentBudget(): int;
    public function removeFunds(int $amount): void;
    public function addFunds(int $amount): void;
}