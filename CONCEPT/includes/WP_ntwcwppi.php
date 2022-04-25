<?php
require 'Services/WooCommerceService.php';

use Services\WooCommerceService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class WP_ntwcwppi
{
  private $plugin;

  private $usedKeys = [
    'ntwcwppi_rest'
  ];

  private $routesFile = 'Config/routes.json';

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

      register_rest_route(
        'ntwcwppi/v1',
        'tester',
        [
          'methods' => 'GET',
          'callback' => ['Classes\\ntwcwppiAuthorization', 'test']
        ]
      );

      // $routes = json_decode(file_get_contents($this->routesFile, true), true);

      // foreach($routes as $route) {
      //   register_rest_route(
      //     'ntwcwppi/v1',
      //     $route['route'],
      //     [
      //       'methods' => $route['methods'],
      //       'callback' => [$this, $route['callback']]
      //     ]
      //   );
      // }
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
    echo "<div id='ntwcwppi_app'> </div>";
    wp_enqueue_script('ntwcwppi_app-vue', plugin_dir_url( __FILE__ ) . 'Frontend/App.js', [], '1.0', true);
    wp_enqueue_style('ntwcwppi_app-styling', plugin_dir_url( __FILE__ ) . 'Frontend/App.css', [], '1.0', 'all');
    wp_enqueue_style('ntwcwppi_font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '1.0', 'all');
  }
  
  public function ntwcwppi_cleanUp()
  {
    foreach ($this->usedKeys as $key) {
      delete_option($key);
    }
  }

  public function ntwcwppi_createDatabaseTables()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . "ntwcwppi_datasources";

    // $wpdb->insert($table_name, [
    //   'data_source_id' => 1,
    //   'data' => json_encode([
    //     "test" => "hallo"
    //   ])
    //   ]);

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      basic varchar(255) NOT NULL,
      url varchar(255) NOT NULL,
      PRIMARY KEY  (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // return $wpdb->prefix . "ntwcwppi_products"; 
  }

  // -----------------DATASOURCES-----------------------------------------------
  public function ntwcwppi_createDataSource()
  {
    $client = new Client();
    $response = $client->request('GET', 'https://deeekhoorn.com/nl/feed/products/json', ['auth' => ['info@mooijdesigns.nl', 'Karveel33!']]);

    return json_decode($response->getBody()->getContents(), true);
    // return $response->getBody();
    // var_dump($response->getBody());
    // die;
    // $requiredFields = ['name', 'username', 'password', 'url'];
    // $input = json_decode(json_decode(file_get_contents('php://input'), true)['body']);
    // $dataToStore = [];

    // // TODO: HANDLE ERROR
    // if(!$input) {
    //   var_dump('NO INPUT');
    //   die;
    // }

    // foreach ($input as $key => $value) {
    //   if (in_array($key, $requiredFields)) {
    //     $dataToStore[$key] = $value;
    //   }
    // }

    // return [
    //   "status" => "OK",
    //   "message" => "Created data source: ${dataToStore['name']}",
    //   "payload" => json_encode($dataToStore)
    // ];

    // // Skip storing the credentials to avoid clutter.
    // // if (empty($dataToStore)) {
    // //   exit;
    // // }

    // // add_option("ntwcwppi_rest", json_encode($dataToStore));
  }

  public function ntwcwppi_listDataSources()
  {
    global $wpdb;
    $dataSources = $wpdb->get_results("SELECT * FROM wp_ntwcwppi_datasources;");

    if (!$dataSources) {
      return [
        "status" => "OK",
        "message" => "No data sources found",
        "payload" => []
      ];
    }

    return $dataSources;
  }

  // -----------------PRODUCTS-----------------------------------------------
  public function ntwcwppi_addProduct()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . "ntwcwppi_products";

    // GET INPUT
    $input = json_decode(file_get_contents('php://input'), true);

    // CHECK IF PAYLOAD INCLUDED REQUIRED DATA_SOURCE_ID
    if(!$input['data_source_id']){
      return 'NO DATA SOURCE INSERTED, EXITING';
    }

    if(!$input['data']) {
      return 'NO DATA BEING INSERTED, SKIPPING';
    }

    $wpdb->insert($table_name, [
        'data_source_id' => $input['data_source_id'],
        'data' => json_encode($input['data'])
      ]);

    return $wpdb->insert_id;
  }

  public function ntwcpwppi_listProducts()
  {
    global $wpdb;

    if(!isset($_GET['productListLimit'])) {
      $productListLimit = 10;
    } else {
      $productListLimit = $_GET['productListLimit'];
    }

    if (isset($_GET['page'])) {
      $pageno = $_GET['page'];
    } else {
      $pageno = 1;
    }

    $offset = ($pageno-1) * $productListLimit; 
    $totalOfProducts = $wpdb->get_var("SELECT COUNT(*) FROM wp_ntwcwppi_products");
    $totalOfPages = ceil($totalOfProducts/$productListLimit);

    return [
      "totalPages" => $totalOfPages,
      "products" => $wpdb->get_results("SELECT * FROM wp_ntwcwppi_products LIMIT $offset, $productListLimit")
    ];
  }

  public function ntwcwppi_listWCProductAttributes()
  {
    $service = new WooCommerceService([
      "consumer_key" => 'ck_017633af71b89b7f9f4cacf5785255b735762cd1',
      "consumer_secret" => 'cs_c285af7ea01bfa61e7bc11931342c318e0ea100b'
    ]);

    return $service->listProductAttributes();
  }
}
