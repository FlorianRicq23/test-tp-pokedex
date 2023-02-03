<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getFranceData($generation)
    {
        $response = $this->client->request('GET','https://pokeapi.co/api/v2/generation/'.$generation);
        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();
        return $content;
    }
    public function getFranceDetailsData($name): array
    {
        $response = $this->client->request('GET','https://pokeapi.co/api/v2/pokemon/'.$name);
        $statusCode = $response->getStatusCode();
        if ($statusCode ==404) return array();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();
        return $content;
    }

    public function getFranceCsvData($name): array
    {
        $response = $this->client->request('GET','https://pokeapi.co/api/v2/pokemon/'.$name);
        $statusCode = $response->getStatusCode();
        if ($statusCode ==404) return array();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();
        return array($content['id'], $content['name']);
    }
}