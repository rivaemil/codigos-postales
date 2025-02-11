<?php

namespace App\Services;

use GuzzleHttp\Client;

class CopomexService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('COPOMEX_API_KEY');
    }

    public function obtenerEstados()
    {
        $response = $this->client->get('https://api.copomex.com/query/get_estados?token=39bae8e2-ac8f-4ade-8cac-e7697e281aa4', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function obtenerMunicipios($estado)
    {
        $response = $this->client->get('https://api.copomex.com/query/get_municipio_por_estado/' . $estado, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function obtenerColonias($codigoPostal)
    {
        $response = $this->client->get('https://api.copomex.com/query/get_colonia_por_cp/' . $codigoPostal, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}