<?php

namespace App\Services\Validators;

use App\Repositories\JSONBudgetRepository;
use Finnhub\Api\DefaultApi;

class BuyValidator
{
    private array $data;
    private array $errors = [];
    private static array $fields = ['symbol', 'buyAmount'];
    private DefaultApi $client;

    public function __construct(array $postData, DefaultApi $client)
    {
        $this->data = $postData;
        $this->client = $client;
    }

    public function validateForm(): array
    {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                $this->addError('field', $field . ' field is missing');
                return $this->errors;
            }
        }
        $this->validateSymbol();
        $this->validateBuyAmount();
        $this->validateFunds();
        return $this->errors;
    }

    private function validateSymbol(): void
    {
        $value = trim($this->data['symbol']);

        if (empty($value)) {
            $this->addError('symbol', 'symbol field cannot be empty');
        } elseif ($this->client->quote(strtoupper($_POST['symbol']))['c'] == 0) {
            $this->addError('symbol', 'stock symbol does not exist');
        }
    }

    private function validateBuyAmount(): void
    {
        $value = $this->data['buyAmount'];

        if (empty($value)) {
            $this->addError('buyAmount', 'amount field cannot be empty');
        } elseif (!is_numeric($value)) {
            $this->addError('buyAmount', 'amount must be numeric');
        } elseif ($value < 1) {
            $this->addError('buyAmount', 'amount must be at least 1');
        }
    }

    private function validateFunds(): void
    {
        $funds = new JSONBudgetRepository();

        $currentStockPrice = $this->client->quote(strtoupper($_POST['symbol']))['c'] * 10000;
        $totalStockPrice = $currentStockPrice * $_POST['buyAmount'];

        if ($funds->getCurrentBudget() < $totalStockPrice) {
            $this->addError('funds', 'not enough funds');
        }
    }

    private function addError(string $key, string $errorMessage): void
    {
        $this->errors[$key] = $errorMessage;
    }
}