<?php

namespace ZerosDev\MCPayment;

use ZerosDev\MCPayment\Support\SetterGetter;
use ZerosDev\MCPayment\Support\Validator;

class VirtualAccount
{
    use SetterGetter, Validator;

    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function create()
    {
        $this->requires([
            'external_id',
            'order_id',
            'currency',
            'payment_method',
            'payment_channel',
            'payment_details',
            'customer_details',
            'callback_url'
        ]);

        $payload = [
            'external_id' => $this->external_id,
            'order_id' => $this->order_id,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_channel' => $this->payment_channel,
            'payment_details' => $this->payment_details,
            'customer_details' => $this->customer_details,
            'billing_address' => $this->billing_address,
            'shipping_address' => $this->shipping_address,
            'additional_data' => $this->additional_data,
            'callback_url' => $this->callback_url,
        ];

        return $this->client->send('POST', '/va', $payload);
    }
}
