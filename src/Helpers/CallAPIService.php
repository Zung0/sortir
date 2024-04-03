<?php

namespace App\Helpers;

use App\Entity\Lieu;
use http\Client\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallAPIService
{
    private $client;

    function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getFranceDataLoc(Lieu $location): array
    {
        $queryParams = '?q=' . str_replace(' ', '+', $location->getRue()) . '&postcode=' . $location->getCodePostal();
        try {
            $response = $this->client->request(
                'GET',
                'https://api-adresse.data.gouv.fr/search/' . $queryParams);
        } catch (\Exception $e) {
            $response = [];
            dump($e);
        }
        return $response->toArray();
    }
}