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
    register_activation_hook($this->plugin, array($this, 'ntwcwppi_createAuthTokens'));

    // Register API Route for saving Authorization Token.
    add_action('rest_api_init', function () {
      register_rest_route('ntwcwppi/v1', '/authorized', array(
        'methods' => 'POST',
        'callback' => $this->ntwcwppi_saveAuthorization(),
      ));
    });

    // // CRUD Menu for Product Importer UI
    // //add_action( 'admin_menu', 'extra_post_info_menu' );
  }

  public function ntwcwppi_createAuthTokens()
  {
    $ntwcwp_current_url = "https://$_SERVER[HTTP_HOST]";

    $endpoint = '/wc-auth/v1/authorize';

    $params = [
      'app_name' => 'NT Woocommerce Product Importer', // Auto Generated name for package.
      'scope' => 'read_write',
      'user_id' => get_current_user_id(), // Current Logged in Userid
      'return_url' => $ntwcwp_current_url,
      'callback_url' => $ntwcwp_current_url . '/wp-json/ntwcwppi/v1/authorized' //Callback URL for storing data.
    ];

    $authentication_url = $ntwcwp_current_url . $endpoint . '?' . http_build_query($params);

    exit(wp_redirect($authentication_url));
  }

  public function ntwcwppi_saveAuthorization()
  {
    var_dump('HIEROPOOO');
    die;
    $requiredFields = ['key_id', 'user_id', 'consumer_key', 'consumer_secret', 'key_permissions'];
    $dataToStore = [];

    foreach ($_POST as $key => $value) {
      if (in_array($key, $requiredFields)) {
        $dataToStore[$key] = $value;
      }
    }

    // Skip storing the credentials to avoid clutter.
    if (empty($dataToStore)) {
      exit;
    }

    add_option("ntwcwppi_rest", json_encode($dataToStore));
  }
}
