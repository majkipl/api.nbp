<?php

namespace App\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NbpApiIntegration
{
    private const NBP_API_URL = 'http://api.nbp.pl/api/exchangerates/tables/a/';

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function getExchangeRates(): mixed
    {
        $client = new Client();
        $response = $client->request('GET', self::NBP_API_URL );

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody());
        }

        return null;
    }
}
