<?php

class WP_ntwcwppi
{
  private $plugin;

  public function __construct($plugin)
  {
   $this->plugin = $plugin;
  }

  public function run()
  {
    add_action('rest_api_init', function () {
      register_rest_route('ntwcwppi/v1', '/authorized', array(
        'methods' => 'POST',
        'callback' => array($this, 'ntwcwppi_saveAuthorization'),
      ));
      register_rest_route('ntwcwppi/v1', '/authorize', array(
        'methods' => 'GET',
        'callback' => array($this, 'ntwcwppi_createAuthTokens'),
      ));
    });

    // CRUD Menu for Product Importer UI
    add_action('admin_menu', array($this, 'ntwcwppi_addMenu'));
  }

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

  public function ntwcwppi_saveAuthorization()
  {
    $requiredFields = ['key_id', 'user_id', 'consumer_key', 'consumer_secret', 'key_permissions'];
    $dataToStore = [];

    foreach ($_POST as $key => $value) {
      if (in_array($key, $requiredFields)) {
        $dataToStore[$key] = $value;
      }
    }

    var_dump($_POST);
    die;
    // Skip storing the credentials to avoid clutter.
    if (empty($dataToStore)) {
      exit;
    }

    add_option("ntwcwppi_rest", json_encode($dataToStore));
  }

  public function ntwcwppi_addMenu()
  {
    add_menu_page(
      'NTWCWPPI', 
      'NTWCWPPI', 
      'edit_posts', 
      'ntwcwppi_product_import', 
      array($this, 'ntwcwppi_createView'), 
      'dashicons-products'
    );
  }

  public function ntwcwppi_createView()
  {
    $ntwcwp_current_url = "https://$_SERVER[HTTP_HOST]" ;

    echo '<a href=' .  $ntwcwp_current_url . '/wp-json/ntwcwppi/v1/authorize' .'>CREATE AUTH TOKENS</a>';
    echo get_option('ntwcwppi_rest');
  }
}
