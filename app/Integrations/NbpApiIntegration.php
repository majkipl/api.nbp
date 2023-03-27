<?php

namespace App\Integrations;

use GuzzleHttp\Client;

class NbpApiIntegration
{
    public function getExchangeRates()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://api.nbp.pl/api/exchangerates/tables/a/' );

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody());
        }

        return null;
    }
}
