<?php

namespace App\Repositories;

class JSONBudgetRepository implements BudgetRepository
{
    private $jsonFileContents;

    public function __construct()
    {
        $this->jsonFileContents = file_get_contents('../budget.json');
        $this->jsonFileContents = json_decode($this->jsonFileContents, true);
    }

    public function getCurrentBudget(): int
    {
        return $this->jsonFileContents['current'];
    }

    public function removeFunds(int $amount): void
    {
        $this->jsonFileContents['current'] -= $amount;
        file_put_contents('../budget.json', json_encode($this->jsonFileContents));
    }

    public function addFunds(int $amount): void
    {
        $this->jsonFileContents['current'] += $amount;
        file_put_contents('../budget.json', json_encode($this->jsonFileContents));
    }
}