<?php

namespace App\Services;

use App\Integrations\NbpApiIntegration;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    protected nbpApiIntegration $nbpApiIntegration;


    public function __construct(NbpApiIntegration $nbpApiIntegration)
    {
        $this->nbpApiIntegration = $nbpApiIntegration;
    }

    public function getCurrencies()
    {
        // 1. get data from api : example using integration
        $currencies = $this->nbpApiIntegration->getExchangeRates();

        if ($currencies) {
            foreach ($currencies[0]->rates as $rate) {
                DB::beginTransaction();

                try {
                    $currency = Currency::where('currency_code', $rate->code)->first();

                    // 2. check if currency exists in db
                    if ($currency) {
                        // 3. then update
                        $currency->exchange_rate = $rate->mid;
                        $currency->save();
                    } else {
                        // 4. else save
                        Currency::create([
                            'id' => \Illuminate\Support\Str::uuid(),
                            'name' => $rate->currency,
                            'currency_code' => $rate->code,
                            'exchange_rate' => $rate->mid,
                        ]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
        }

    }
}
