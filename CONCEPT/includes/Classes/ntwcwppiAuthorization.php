<?php

namespace Classes;

class ntwcwppiAuthorization {

  public function __construct()
  {
    var_dump('ntwcwppiAuthorization');
    die;
  }

  /**
   * Create Authorization Tokens through the WooCommerce REST API,
   * And send data to saveAuthorization Callback JSON Rest URL.
   * 
   * @return void
   */
  public function ntwcwppi_createAuthTokens()
  {
    $ntwcwp_current_url = "https://$_SERVER[HTTP_HOST]";

    $endpoint = '/wc-auth/v1/authorize';

    $params = [
      'app_name' => 'NT Woocommerce Product Importer', // Auto Generated name for package.
      'scope' => 'read_write',
      'user_id' => 1, // Current Logged in Userid
      'return_url' => $ntwcwp_current_url,
      'callback_url' => $ntwcwp_current_url . '/wp-json/ntwcwppi/v1/authorized' //Callback URL for storing data.
    ];

    $authentication_url = $ntwcwp_current_url . $endpoint . '?' . http_build_query($params);

    exit(wp_redirect($authentication_url));
  }

  public function test() {
      var_dump("HIER");
      die;
  }

}