<?php

namespace App\Controllers;

use App\Services\AddFundsToBudgetService;
use App\Services\GetSymbolService;
use App\Services\SellStockService;
use App\Services\Validators\SellValidator;
use Finnhub\Api\DefaultApi;

class SellController
{
    private AddFundsToBudgetService $addFundsToBudgetService;
    private SellStockService $sellStockService;
    private GetSymbolService $getSymbolService;
    private DefaultApi $client;

    public function __construct(
        AddFundsToBudgetService $addFundsToBudgetService,
        SellStockService $sellStockService,
        GetSymbolService $getSymbolService,
        DefaultApi $client
    )
    {
        $this->addFundsToBudgetService = $addFundsToBudgetService;
        $this->sellStockService = $sellStockService;
        $this->getSymbolService = $getSymbolService;
        $this->client = $client;
    }

    public function sell()
    {
        foreach ($_POST['sell'] as $key => $value) {
            if (isset($_POST['sell'][$key])) {

                $validation = new SellValidator($_POST);
                $_SESSION['errors']['sellErrors'] = $validation->validateForm();

                if (empty($_SESSION['errors']['sellErrors'])) {

                    $currentStockPrice = $this->client->quote($this->getSymbolService->execute($key))['c'] * 10000;
                    $this->sellStockService->execute($key, $currentStockPrice, $_POST['sellAmount']);
                    $this->addFundsToBudgetService->execute($currentStockPrice * $_POST['sellAmount']);
                }
            }
        }
        header('Location: /');
    }
}