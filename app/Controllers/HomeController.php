<?php

namespace App\Controllers;

use App\Services\GetBudgetService;
use App\Services\GetLatestPurchaseService;
use App\Services\GetPortfolioService;
use App\Services\GetPurchaseHistoryService;
use App\Services\GetSellingHistoryService;
use App\Services\GetSymbolService;
use Finnhub\Api\DefaultApi;
use Twig\Environment;

class HomeController
{
    private Environment $environment;
    private GetBudgetService $getBudgetService;
    private GetPortfolioService $getPortfolioService;
    private GetPurchaseHistoryService $getPurchaseHistoryService;
    private GetSellingHistoryService $getSellingHistoryService;
    private GetLatestPurchaseService $getLatestPurchaseService;
    private GetSymbolService $getSymbolService;
    private DefaultApi $client;

    public function __construct(
        Environment $environment,
        GetBudgetService $getBudgetService,
        GetPortfolioService $getPortfolioService,
        GetPurchaseHistoryService $getPurchaseHistoryService,
        GetSellingHistoryService $getSellingHistoryService,
        GetLatestPurchaseService $getLatestPurchaseService,
        GetSymbolService $getSymbolService,
        DefaultApi $client
    )
    {
        $this->environment = $environment;
        $this->getBudgetService = $getBudgetService;
        $this->getPortfolioService = $getPortfolioService;
        $this->getPurchaseHistoryService = $getPurchaseHistoryService;
        $this->getSellingHistoryService = $getSellingHistoryService;
        $this->getLatestPurchaseService = $getLatestPurchaseService;
        $this->getSymbolService = $getSymbolService;
        $this->client = $client;
    }

    public function index()
    {
        $budget = $this->getBudgetService->execute();
        $portfolio = $this->getPortfolioService->execute();
        $purchaseHistory = $this->getPurchaseHistoryService->execute();
        $sellingHistory = $this->getSellingHistoryService->execute();

        $currentStockPrices = [];
        foreach ($portfolio as $stock => ['id' => $id]) {
            $currentStockPrices[$id] = $this->client->quote($this->getSymbolService->execute($id))['c'] * 10000;
        }

        $latestPurchases = [];
        foreach ($portfolio as $stock => ['symbol' => $symbol]) {
            $latestPurchases[$symbol] = $this->getLatestPurchaseService->execute($symbol);
        }

        echo $this->environment->render('indexView.php', [
            'budget' => $budget,
            'portfolio' => $portfolio,
            'purchaseHistory' => $purchaseHistory,
            'sellingHistory' => $sellingHistory,
            'currentStockPrices' => $currentStockPrices,
            'latestPurchases' => $latestPurchases,
            'buyErrors' => $_SESSION['errors']['buyErrors'],
            'sellErrors' => $_SESSION['errors']['sellErrors']
        ]);
    }
}