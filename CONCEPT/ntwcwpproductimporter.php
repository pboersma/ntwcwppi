<?php
/**
 * NT Woocommerce Wordpress Product Importer
 *
 * @package           wcwppi
 * @author            Peter Boersma
 * @copyright         2021 Peter Boersma
 *
 * @wordpress-plugin
 * Plugin Name:       NT Woocommerce Wordpress Product Importer
 * Description:       Product Import Plugin for Woocommerce to allow auto-importation of products.
 * Version:           1.0.0
 **/

include_once  __DIR__ . '/vendor/autoload.php';
require 'includes/WP_ntwcwppi.php';

$plugin = new WP_ntwcwppi( __FILE__ );
$plugin->run();
