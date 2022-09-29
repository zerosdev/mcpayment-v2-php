<?php

namespace ZerosDev\MCPayment;

use ZerosDev\MCPayment\Support\SetterGetter;
use ZerosDev\MCPayment\Support\Validator;

class Ewallet
{
    use SetterGetter, Validator;

    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function create(array $fields = [])
    {
        foreach ($fields as $key => $value) {
            $this->{$key} = $value;
        }

        $this->requires([
            'external_id',
            'order_id',
            'currency',
            'payment_method',
            'payment_channel',
            'payment_details',
            'customer_details',
            'wallet_details',
            'callback_url',
            'return_url',
        ]);

        $payload = [
            'external_id' => $this->external_id,
            'order_id' => $this->order_id,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_channel' => $this->payment_channel,
            'payment_details' => $this->payment_details,
            'customer_details' => $this->customer_details,
            'item_details' => $this->item_details,
            'wallet_details' => $this->wallet_details,
            'billing_address' => $this->billing_address,
            'shipping_address' => $this->shipping_address,
            'payment_options' => $this->payment_options,
            'additional_data' => $this->additional_data,
            'callback_url' => $this->callback_url,
            'return_url' => $this->return_url,
        ];

        $payload = array_filter($payload, function ($p) {
            return !is_null($p);
        });

        return $this->client->send('POST', '/ewallet/v2/create-payment', $payload, [
            'X-Req-Signature' => hash('sha256', $this->client->configs['hash_key'].$payload['external_id'].$payload['order_id'])
        ]);
    }
}
