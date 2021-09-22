<?php

namespace Services;


use Automattic\WooCommerce\Client;


class WooCommerceService {

    private $credentials;
    private $client;

    public function __construct($credentials)
    {
        $this->credentials = $credentials;

        $this->client = new Client(
            "https://$_SERVER[HTTP_HOST]",
            $credentials['consumer_key'],
            $credentials['consumer_secret'],
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );
    }

    public function listAllProducts()
    {
        $this->client->get('products');
    }

}