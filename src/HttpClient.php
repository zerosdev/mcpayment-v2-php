<?php

namespace ZerosDev\MCPayment;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use stdClass;

class HttpClient
{
    public $configs = [];
    public $client;
    public $baseUrl;
    public $xVersion;
    public $merchantId;
    public $secretUnboundId;
    public $hashKey;

    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
        $this->baseUrl = isset($this->configs['base_url']) ? $this->configs['base_url'] : null;
        $this->xVersion = isset($this->configs['x_version']) ? $this->configs['x_version'] : null;
        $this->merchantId = isset($this->configs['merchant_id']) ? $this->configs['merchant_id'] : null;
        $this->secretUnboundId = isset($this->configs['secret_unbound_id']) ? $this->configs['secret_unbound_id'] : null;
        $this->hashKey = isset($this->configs['hash_key']) ? $this->configs['hash_key'] : null;

        $this->createClient();
    }

    public function createClient()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'allow_redirects' => false,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->merchantId . ':' . $this->secretUnboundId),
            ],
        ]);
    }

    public function send($method, $endpoint, array $payload, array $headers = [])
    {
        $endpoint = ltrim($endpoint, '/');
        $localMethod = strtolower($method);

        $return = new stdClass();
        $return->config = $this->configs;
        $return->request = new stdClass();
        $return->response = new stdClass();

        $return->request->url = null;
        $return->request->method = $method;
        $return->request->body = json_decode(json_encode($payload));

        $return->response->status = 0;
        $return->response->body = null;
        $return->response->headers = [];
        $return->response->decoded = null;
        $return->response->error = null;

        try {
            $response = $this->client->{$localMethod}($endpoint, [
                'json' => $payload,
                'headers' => $headers,
                'on_stats' => function (TransferStats $stats) use (&$return) {
                    $return->request->url = (string) $stats->getEffectiveUri();
                }
            ]);
            $return->request->headers = array_merge(
                (array) $this->client->getConfig('headers'),
                $headers
            );
            $return->response->status = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $return->response->body = $body;
            $data = json_decode($body);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $return->response->error = 'Unable to decode body';
            }
            $return->response->decoded = $data;
        } catch (Exception $e) {
            $return->response->error = $e->getMessage();
        }

        return $return;
    }
}
