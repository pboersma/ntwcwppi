<?php
namespace Services;

use Automattic\WooCommerce\Client;


class WooCommerceService {

    private $client;

    public function __construct($credentials)
    {
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
        var_dump('HIER');
        $this->client->get('products');
    }

}