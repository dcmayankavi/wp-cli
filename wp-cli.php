<?php
/**
 * Plugin Name: WP CLI
 * Plugin URI: http://dineshchouhan.com
 * Description: Will provide wp-cli command action at backend.
 * Version: 0.0.1
 * Author: Dinesh Chouhan
 * Author URI: http://dineshchouhan.com
 * Text Domain: wp-cli
 *
 * @package wp-cli
 * @author Dinesh Chouhan
 */

// exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once 'classes/class-wp-cli-loader.php';
