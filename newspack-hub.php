<?php
/**
 * Plugin Name: Newspack Network Node
 * Description: The Newspack Network Node plugin.
 * Version: 0.1
 * Author: Automattic
 * Author URI: https://newspack.com/
 * License: GPL3
 * Text Domain: newspack-network-node
 * Domain Path: /languages/
 *
 * @package newspack-network-node
 */

defined( 'ABSPATH' ) || exit;

// Define NEWSPACK_NODE_PLUGIN_DIR.
if ( ! defined( 'NEWSPACK_NODE_PLUGIN_DIR' ) ) {
	define( 'NEWSPACK_NODE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Define NEWSPACK_NODE_PLUGIN_FILE.
if ( ! defined( 'NEWSPACK_NODE_PLUGIN_FILE' ) ) {
	define( 'NEWSPACK_NODE_PLUGIN_FILE', __FILE__ );
}

// Load language files.
load_plugin_textdomain( 'newspack-network-node', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

require_once __DIR__ . '/vendor/autoload.php';

Newspack_Network_Node\Initializer::init();
