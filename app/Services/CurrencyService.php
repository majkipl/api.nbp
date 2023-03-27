<?php

namespace App\Services;

use App\Integrations\NbpApiIntegration;
use App\Models\Currency;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CurrencyService
{
    protected nbpApiIntegration $nbpApiIntegration;


    /**
     * @param NbpApiIntegration $nbpApiIntegration
     */
    public function __construct(NbpApiIntegration $nbpApiIntegration)
    {
        $this->nbpApiIntegration = $nbpApiIntegration;
    }


    /**
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getCurrencies(): JsonResponse
    {
        $currencies = $this->nbpApiIntegration->getExchangeRates();

        if ($currencies) {
            foreach ($currencies[0]->rates as $rate) {
                DB::beginTransaction();

                try {
                    $currency = Currency::where('currency_code', $rate->code)->first();

                    if ($currency) {
                        $currency->exchange_rate = $rate->mid;
                        $currency->save();
                    } else {
                        Currency::create([
                            'id' => Str::uuid(),
                            'name' => $rate->currency,
                            'currency_code' => $rate->code,
                            'exchange_rate' => $rate->mid,
                        ]);
                    }
                    DB::commit();

                } catch (Exception $e) {
                    DB::rollback();
                }
            }
        } else {
            return response()->json(['error' => true]);
        }

        return response()->json(['success' => true]);
    }
}
