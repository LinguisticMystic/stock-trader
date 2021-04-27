<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../main.css">
    <title>Stock Market</title>
</head>
<body>

<h2 class="text-green-700">Your current budget is ${{ (budget / 10000)|number_format(2, '.', ',') }}.</h2>

<figure class="bg-gray-100 rounded-xl p-8">
    <h1 class="text-3xl font-bold leading-normal mt-0 mb-2 text-gray-900">Make a purchase</h1>

    <form action="/buy" method="post">
        Stock symbol<input class="flex rounded-full border-grey-light border h-8" type="text" name="symbol"
                           placeholder="AAPL">
        Amount<input class="flex rounded-full border-grey-light border h-8" type="number" name="buyAmount"
                     placeholder="10">
        <br>
        <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit"
               value="Purchase">
    </form>

    {% for error in buyErrors %}
    <p class="text-red-700">{{ error|capitalize }}</p>
    {% endfor %}

</figure>

<br>

<figure class="bg-gray-100 rounded-xl p-8">

    <h1 class="text-3xl font-bold leading-normal mt-0 mb-2 text-gray-900">Portfolio</h1>


    <table class="rounded-t-lg m-5 w-full mx-auto bg-blue-500 text-white">

        <tr class="text-left border-b-2 border-gray-300">
            <th class="px-4 py-3">Symbol</th>
            <th class="px-4 py-3">Amount</th>
            <th class="px-4 py-3">Current price (per stock)</th>
            <th class="px-4 py-3">Price change since last purchase</th>
            <th class="px-4 py-3">Sell amount</th>
            <th class="px-4 py-3">Sell</th>
        </tr>

        {% for stock in portfolio %}

        <tr class="bg-blue-100 border-b border-gray-200 text-gray-900">
            <td class="px-4 py-3">{{ stock.symbol }}</td>
            <td class="px-4 py-3">{{ stock.amount }}</td>
            <td class="px-4 py-3">${{ (currentStockPrices[stock.id] / 10000)|number_format(2, '.', ',') }}</td>

            {% if latestPurchases[stock.symbol] == currentStockPrices[stock.id] %}
            <td class="px-4 py-3 text-gray-600">- {{ 0|number_format(2, '.', ',') }} (0%)</td>

            {% elseif latestPurchases[stock.symbol] < currentStockPrices[stock.id] %}
            <td class="px-4 py-3 text-green-600">⬆ {{ ((currentStockPrices[stock.id] -
                latestPurchases[stock.symbol]) / 10000)|number_format(2, '.', ',') }}
                ({{ (100 - (latestPurchases[stock.symbol] * 100 / currentStockPrices[stock.id]))|number_format(2, '.')
                }}%)
            </td>

            {% else %}
            <td class="px-4 py-3 text-red-600">⬇ {{ ((latestPurchases[stock.symbol] - currentStockPrices[stock.id])
                / 10000)|number_format(2, '.', ',') }}
                ({{ ((latestPurchases[stock.symbol] * 100 / currentStockPrices[stock.id]) - 100)|number_format(2, '.')
                }}%)
            </td>
            {% endif %}

            <form action="/sell" method="post" id="{{ stock.id }}">
                <td><input type="number" name="sellAmount" size="4" min="0" placeholder="0"></td>
                {% if stock.amount > 0 %}
                <td><input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type='submit'
                           name="sell[{{ stock.id }}]" value='Sell'></td>
                {% else %}
                <td class="px-4 py-3 text-red-700 font-bold">Sold</td>
                {% endif %}
            </form>

        </tr>

        {% endfor %}

    </table>

    {% for error in sellErrors %}
    <p class="text-red-700">{{ error|capitalize }}</p>
    {% endfor %}

</figure>

<br>

<figure class="bg-gray-100 rounded-xl p-8">

    <h1 class="text-3xl font-bold leading-normal mt-0 mb-2 text-gray-900">Purchase history</h1>

    <table class="rounded-t-lg m-5 w-full mx-auto bg-blue-500 text-white">

        <tr class="text-left border-b-2 border-gray-300">
            <th class="px-4 py-3">Symbol</th>
            <th class="px-4 py-3">Amount</th>
            <th class="px-4 py-3">Buy price (per stock)</th>
            <th class="px-4 py-3">Buy price (total)</th>
            <th class="px-4 py-3">Buy date</th>
        </tr>

        {% for stock in purchaseHistory %}

        <tr class="bg-blue-100 border-b border-gray-200 text-gray-900">
            <td class="px-4 py-3">{{ stock.symbol }}</td>
            <td class="px-4 py-3">{{ stock.amount }}</td>
            <td class="px-4 py-3">${{ (stock.buy_price / 10000)|number_format(2, '.', ',') }}</td>
            <td class="px-4 py-3">${{ (stock.buy_price * stock.amount / 10000)|number_format(2, '.', ',') }}</td>
            <td class="px-4 py-3">{{ stock.buy_date|date("m/d/Y") }}</td>
        </tr>

        {% endfor %}

    </table>

</figure>

<br>

<figure class="bg-gray-100 rounded-xl p-8">

    <h1 class="text-3xl font-bold leading-normal mt-0 mb-2 text-gray-900">Selling history</h1>

    <table class="rounded-t-lg m-5 w-full mx-auto bg-blue-500 text-white">

        <tr class="text-left border-b-2 border-gray-300">
            <th class="px-4 py-3">Symbol</th>
            <th class="px-4 py-3">Amount</th>
            <th class="px-4 py-3">Sell price (per stock)</th>
            <th class="px-4 py-3">Sell price (total)</th>
            <th class="px-4 py-3">Sell date</th>
        </tr>

        {% for stock in sellingHistory %}

        <tr class="bg-blue-100 border-b border-gray-200 text-gray-900">
            <td class="px-4 py-3">{{ stock.symbol }}</td>
            <td class="px-4 py-3">{{ stock.amount }}</td>
            <td class="px-4 py-3">${{ (stock.sell_price / 10000)|number_format(2, '.', ',') }}</td>
            <td class="px-4 py-3">${{ (stock.sell_price * stock.amount / 10000)|number_format(2, '.', ',') }}</td>
            <td class="px-4 py-3">{{ stock.sell_date|date("m/d/Y") }}</td>
        </tr>

        {% endfor %}

    </table>

</figure>

</body>
</html>