<?php
require 'Services/WooCommerceService.php';

use Services\WooCommerceService;

class WP_ntwcwppi
{
  private $plugin;

  private $usedKeys = [
    'ntwcwppi_rest'
  ];

  public function __construct($plugin)
  {
   $this->plugin = $plugin;
  }

  /**
   * Bootstrap / Init Product Importer Plugin.
   * 
   * @return void
   */
  public function run()
  {
    add_action('rest_api_init', function () {
      /**
       * Authorization Callback URL for storing auth token.
       *
       * @TODO: permission_callback setup
       */
      register_rest_route('ntwcwppi/v1', '/authorized', array(
        'methods' => 'POST',
        'callback' => array($this, 'ntwcwppi_saveAuthorization'),
      ));

      /**
       * Authorize Rest API URL through WooCommerce.
       *
       * @TODO: permission_callback setup
       */
      register_rest_route('ntwcwppi/v1', '/authorize', array(
        'methods' => 'GET',
        'callback' => array($this, 'ntwcwppi_createAuthTokens'),
      ));
    });

    // CRUD Menu for Product Importer UI
    add_action('admin_menu', array($this, 'ntwcwppi_addMenu'));

    // Wordpress deactivation hook for cleanup.
    register_deactivation_hook($this->plugin, array($this, 'ntwcwppi_cleanUp'));
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

  /**
   * Function for Saving Authorization data from WooCommerce Rest API.
   * 
   * @return void
   */
  public function ntwcwppi_saveAuthorization()
  {
    $requiredFields = ['key_id', 'user_id', 'consumer_key', 'consumer_secret', 'key_permissions'];
    $input = json_decode(file_get_contents('php://input'), true);
    $dataToStore = [];

    foreach ($input as $key => $value) {
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

  /**
   * Initialize NTWCWPPI Menu.
   * 
   * @return void
   */
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

  /**
   * Create View
   * 
   * @return void
   */
  public function ntwcwppi_createView()
  {
    echo plugin_dir_url( __FILE__ );
    // echo '<div id="product_import_dashboard"></div>'

    // add_action('admin_enqueue_scripts', function ($hook) {
    //   // only load scripts on dashboard
    //   // if ($hook != 'index.php') {
    //   //   return;
    //   // }
    //   if (in_array($_SERVER['REMOTE_ADDR'], array('10.255.0.2', '::1'))) {
    //     // DEV React dynamic loading
    //     $js_to_load = 'http://localhost:3000/static/js/bundle.js';
    //   } else {
    //     $js_to_load = plugin_dir_url( __FILE__ ) . 'ghost-inspector.js';
    //     $css_to_load = plugin_dir_url( __FILE__ ) . 'ghost-inspector.css';
    //   }
    //   wp_enqueue_style('ghost_inspector_css', $css_to_load);
    //   wp_enqueue_script('ghost_inspector_js', $js_to_load, '', mt_rand(10,1000), true);
    // });
    // $ntwcwp_current_url = "https://$_SERVER[HTTP_HOST]" ;
    // echo '<a href=' .  $ntwcwp_current_url . '/wp-json/ntwcwppi/v1/authorize' .'>CREATE AUTH TOKENS</a>';

    // $service = new WooCommerceService([
    //   "consumer_key" => 'ck_017633af71b89b7f9f4cacf5785255b735762cd1',
    //   "consumer_secret" => 'cs_c285af7ea01bfa61e7bc11931342c318e0ea100b'
    // ]);

    // echo '
    //   <h1>Add Data Feed</h1>
    //   <form id="addDataFeed">
    //     <input type="text" name="data-url" /><br/>
    //     <input type="text" name="username" /><br/>
    //     <input type="text" name="password" /><br/>
    //     <input type="submit" value="Add Datafeed" />
    //   </form>
    //   <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    //   <script>
    //     $("#addDataFeed").submit(function(event){
    //       event.preventDefault();

    //       var $form = $(this);
          
    //       var data = JSON.parse(JSON.stringify($form.serializeArray()))
    //       console.log(data);
    //     });
    //   </script>
    // ';

    // echo '<textarea>' . get_option('ntwcwppi_rest') . '</textarea>';

    // echo "<pre>";
    // var_dump($service->listAllProducts());
    // echo "</pre>";
  }
  
  public function ntwcwppi_cleanUp()
  {
    foreach ($this->usedKeys as $key) {
      delete_option($key);
    }
  }
}
