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

define( 'WP_CLI_FILE', trailingslashit( dirname( __FILE__ ) ) . 'wp-cli.php' );
define( 'WP_CLI_DIR', plugin_dir_path( WP_CLI_FILE ) );
define( 'WP_CLI_URL', plugins_url( '/', WP_CLI_FILE ) );

// chdir( WP_CLI_FILE );

/**
 * WP CLI Scripts
 */
function wp_cli_scripts(){
	wp_enqueue_style( 'wp-cli-style', WP_CLI_URL . 'assets/style.css' );
	wp_enqueue_script( 'wp-cli-script', WP_CLI_URL . 'assets/script.js', array( 'jquery' ), '0.0.1', true );
}
add_action( 'admin_enqueue_scripts', 'wp_cli_scripts' );

/**
 * WP CLI Console Markup
 */
function wp_cli_console_markup() {
	require_once WP_CLI_DIR . 'template/wp-cli-console.php';
}
add_action( 'admin_footer', 'wp_cli_console_markup' );

/**
 * Ajax callback
 */
function get_cmd_result_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die();
	}

	$response = array(
		'status' 	=> false,
		'result'	=> '',
	);
	$cmd = $_POST['cmd'];
	if( 0 == strpos( $cmd, 'wp ' ) ) {
		$cmd = str_replace('wp', 'php wp-cli.phar', $cmd);
	}
	$output = shell_exec( $cmd );
	
	if( ! empty($output ) ) {
		$response['status'] = true;
		$response['result'] = '<pre>' . $output . '</pre>';
	}
	
	wp_send_json( $response );
}
add_action( 'wp_ajax_get_cmd_result', 'get_cmd_result_callback' );

/**
 * Add WP CLI button
 */
function wp_cli_admin_link() {
	global $wp_admin_bar;

	if ( !is_super_admin() || !is_admin_bar_showing() ) {
		return;
	}

	/* Add the main siteadmin menu item */
	$wp_admin_bar->add_menu( array( 'id' => 'wp_cli_link', 'title' => __( 'WP CLI', 'wp-cli' ), 'href' => '#' ) );
}
add_action( 'admin_bar_menu', 'wp_cli_admin_link', 1000 );

/**
 * Add WP CLI file
 */
function wp_cli_activation(){
	$destination = shell_exec('cd');
	$source      = str_replace( "/", "\\", WP_CLI_DIR) .'wp-cli.phar';

	$cmd = 'copy ' . $source .' '.$destination;
	shell_exec($cmd);
}
register_activation_hook( __FILE__, 'wp_cli_activation' );