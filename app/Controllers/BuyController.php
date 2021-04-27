<?php

namespace App\Controllers;

use App\Models\Stock;
use App\Services\BuyStockService;
use App\Services\RemoveFundsFromBudgetService;
use App\Services\Validators\BuyValidator;
use Finnhub\Api\DefaultApi;

class BuyController
{
    private RemoveFundsFromBudgetService $removeFundsFromBudgetService;
    private BuyStockService $buyStockService;
    private DefaultApi $client;

    public function __construct(
        RemoveFundsFromBudgetService $removeFundsFromBudgetService,
        BuyStockService $buyStockService,
        DefaultApi $client
    )
    {
        $this->removeFundsFromBudgetService = $removeFundsFromBudgetService;
        $this->buyStockService = $buyStockService;
        $this->client = $client;
    }

    public function buy()
    {
        $validation = new BuyValidator($_POST, $this->client);
        $_SESSION['errors']['buyErrors'] = $validation->validateForm();

        if (empty($_SESSION['errors']['buyErrors'])) {

            $currentStockPrice = $this->client->quote(strtoupper($_POST['symbol']))['c'] * 10000;
            $stock = new Stock($_POST['symbol'], $_POST['buyAmount'], $currentStockPrice);

            $this->buyStockService->execute($stock);
            $this->removeFundsFromBudgetService->execute($currentStockPrice * $_POST['buyAmount']);
        }

        header('Location: /');
    }

}