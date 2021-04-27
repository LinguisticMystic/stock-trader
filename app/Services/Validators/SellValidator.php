<?php

namespace App\Services\Validators;

use App\Repositories\MySQLStockPortfolioRepository;

class SellValidator
{
    private array $data;
    private array $errors = [];
    private static array $fields = ['sellAmount'];

    public function __construct(array $postData)
    {
        $this->data = $postData;
    }

    public function validateForm(): array
    {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                $this->addError('field', 'amount field is missing');
                return $this->errors;
            }
        }
        $this->validateSellAmount();
        return $this->errors;
    }

    private function validateSellAmount(): void
    {
        $stockRepository = new MySQLStockPortfolioRepository();
        $stockID = array_search('Sell', $this->data['sell']);

        $value = $this->data['sellAmount'];

        if ($value == 0) {
            $this->addError('sellAmount', 'amount cannot be 0');
        } elseif (!is_numeric($value)) {
            $this->addError('sellAmount', 'amount must be numeric');
        } elseif ($_POST['sellAmount'] > $stockRepository->getAmount($stockID)[0]) {
            $this->addError('sellAmount', 'you cannot sell more than what you have');
        }
    }

    private function addError(string $key, string $errorMessage): void
    {
        $this->errors[$key] = $errorMessage;
    }
}