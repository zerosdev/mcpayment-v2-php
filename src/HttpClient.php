<?php

namespace ZerosDev\MCPayment;

use Exception;
use GuzzleHttp\Client;

class HttpClient
{
    protected $client;
    protected $baseUrl;
    protected $xVersion;
    protected $merchantId;
    protected $secretUnboundId;
    protected $hashKey;

    public function __construct(array $configs = [])
    {
        $this->baseUrl = isset($configs['base_url']) ? $configs['base_url'] : null;
        $this->xVersion = isset($configs['x_version']) ? $configs['x_version'] : null;
        $this->merchantId = isset($configs['merchant_id']) ? $configs['merchant_id'] : null;
        $this->secretUnboundId = isset($configs['secret_unbound_id']) ? $configs['secret_unbound_id'] : null;
        $this->hashKey = isset($configs['hash_key']) ? $configs['hash_key'] : null;

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
            ]
        ]);
    }

    public function send($method, $endpoint, array $payload)
    {
        $endpoint = ltrim($endpoint, '/');
        $localMethod = strtolower($method);

        $return = new \stdClass();

        $return->request->url = null;
        $return->request->method = $method;
        $return->request->headers = $this->client->getConfig('headers');
        $return->request->body = json_decode(json_encode($payload));

        $return->response->status = 0;
        $return->response->body = null;
        $return->response->headers = [];
        $return->response->decoded = null;
        $return->response->error = null;

        try {
            $response = $this->client->{$localMethod}($endpoint, [
                'json' => $payload,
            ]);
            $return->response->status = $response->getStatusCode();
            $body = $response->getBody()->getContent();
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
