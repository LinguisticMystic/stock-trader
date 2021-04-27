<?php

namespace App\Repositories;

use App\Models\Stock;
use Medoo\Medoo;

class MySQLStockPortfolioRepository implements StockPortfolioRepository
{
    private Medoo $database;

    public function __construct()
    {
        $this->database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'stocks',
            'server' => 'localhost',
            'username' => $_ENV['MYSQL_USERNAME'],
            'password' => $_ENV['MYSQL_PASSWORD']
        ]);
    }

    public function getPortfolio(): array
    {
        return $this->database->select('portfolio', ['id', 'symbol', 'amount']);
    }

    public function addStock(Stock $stock): void
    {
        if (empty($this->database->select('portfolio', 'symbol', ['symbol' => $stock->symbol()]))) {
            $this->database->insert('portfolio', [
                'symbol' => $stock->symbol(),
                'amount' => $stock->amount()]);
        } else {
            $this->database->update('portfolio', ['amount[+]' => $stock->amount()], ['symbol' => $stock->symbol()]);
        }

        $this->database->insert('purchase_history', [
            'symbol' => $stock->symbol(),
            'amount' => $stock->amount(),
            'buy_price' => $stock->buyPrice(),
            'buy_date' => $stock->buyDate()
        ]);
    }

    public function getPurchaseHistory(): array
    {
        return $this->database->select('purchase_history', '*');
    }

    public function getSellingHistory(): array
    {
        return $this->database->select('selling_history', '*');
    }

    public function getSymbol(int $id): array
    {
        return $this->database->select('portfolio', 'symbol', ['id' => $id]);
    }

    public function getAmount(int $id): array
    {
        return $this->database->select('portfolio', 'amount', ['id' => $id]);
    }

    public function sellStock(int $id, int $price, string $amount): void
    {
        $this->database->update('portfolio', ['amount[-]' => $amount], ['id' => $id]);

        $symbolByID = $this->database->select('portfolio', 'symbol', ['id' => $id]);

        $this->database->insert('selling_history', [
            'symbol' => $symbolByID[0],
            'amount' => $amount,
            'sell_price' => $price,
            'sell_date' => time()
        ]);
    }

    public function getLatestPurchase(string $symbol): int
    {
        $lastPurchasePrice = $this->database->select('purchase_history', 'buy_price', ['symbol' => $symbol]);
        return end($lastPurchasePrice);
    }

}