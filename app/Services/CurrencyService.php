<?php

namespace App\Services;

use App\Integrations\NbpApiIntegration;

class CurrencyService
{
    public function getCurrencies()
    {
        // 1. get data from api : example using integration
        $nbpApi = new NbpApiIntegration();
        $currencies = $nbpApi->getExchangeRates();

        dd($currencies);

        // 2. check if currency exists in db
        // 3. then update
        // 4. else save
    }
}
