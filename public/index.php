<?php

use App\Controllers\BuyController;
use App\Controllers\HomeController;
use App\Controllers\SellController;
use App\Repositories\BudgetRepository;
use App\Repositories\JSONBudgetRepository;
use App\Repositories\MySQLStockPortfolioRepository;
use App\Repositories\StockPortfolioRepository;
use App\Services\AddFundsToBudgetService;
use App\Services\BuyStockService;
use App\Services\GetBudgetService;
use App\Services\GetLatestPurchaseService;
use App\Services\GetPortfolioService;
use App\Services\GetPurchaseHistoryService;
use App\Services\GetSellingHistoryService;
use App\Services\GetSymbolService;
use App\Services\RemoveFundsFromBudgetService;
use App\Services\SellStockService;
use Finnhub\Api\DefaultApi;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once '../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

//Finnhub
$config = Finnhub\Configuration::getDefaultConfiguration()->setApiKey('token', $_ENV['API_KEY']);
$httpClient = new GuzzleHttp\Client();
//$client = new Finnhub\Api\DefaultApi($httpClient, $config);

//Twig
$loader = new FilesystemLoader('../app/Views');
$twig = new Environment($loader);

//Container
$container = new League\Container\Container;
//Repos
$container->add(StockPortfolioRepository::class, MySQLStockPortfolioRepository::class);
$container->add(BudgetRepository::class, JSONBudgetRepository::class);
//Services
$container->add(GetBudgetService::class, GetBudgetService::class)
    ->addArgument(BudgetRepository::class);
$container->add(RemoveFundsFromBudgetService::class, RemoveFundsFromBudgetService::class)
    ->addArgument(BudgetRepository::class);
$container->add(AddFundsToBudgetService::class, AddFundsToBudgetService::class)
    ->addArgument(BudgetRepository::class);
$container->add(GetPortfolioService::class, GetPortfolioService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(GetPurchaseHistoryService::class, GetPurchaseHistoryService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(GetSellingHistoryService::class, GetSellingHistoryService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(BuyStockService::class, BuyStockService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(SellStockService::class, SellStockService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(GetSymbolService::class, GetSymbolService::class)
    ->addArgument(StockPortfolioRepository::class);
$container->add(GetLatestPurchaseService::class, GetLatestPurchaseService::class)
    ->addArgument(StockPortfolioRepository::class);
//API
$container->add(DefaultApi::class, DefaultApi::class)
    ->addArguments([$httpClient, $config]);
//Controllers
$container->add(HomeController::class, HomeController::class)
    ->addArguments([
        $twig,
        GetBudgetService::class,
        GetPortfolioService::class,
        GetPurchaseHistoryService::class,
        GetSellingHistoryService::class,
        GetLatestPurchaseService::class,
        GetSymbolService::class,
        DefaultApi::class
    ]);
$container->add(BuyController::class, BuyController::class)
    ->addArguments([
        RemoveFundsFromBudgetService::class,
        BuyStockService::class,
        DefaultApi::class
    ]);
$container->add(SellController::class, SellController::class)
    ->addArguments([
        AddFundsToBudgetService::class,
        SellStockService::class,
        GetSymbolService::class,
        DefaultApi::class
    ]);

//Routes
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('POST', '/', [HomeController::class, 'index']);
    $r->addRoute('POST', '/buy', [BuyController::class, 'buy']);
    $r->addRoute('POST', '/sell', [SellController::class, 'sell']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:

        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = $handler;
        ($container->get($controller))->$method($vars);
        break;
}

if ($httpMethod == 'GET' && isset($_SESSION['errors'])) {
    unset ($_SESSION['errors']);
}