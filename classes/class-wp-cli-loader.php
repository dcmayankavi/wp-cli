<?php
/**
 * WP CLI Class
 *
 * @package wp-cli
 * @author Dinesh Chouhan
 */

// exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WP_CLI_Loader' ) ) :

	/**
	 * Create class WP_CLI_Loader
	 */
	class WP_CLI_Loader {

		/**
		 * Declare a static variable instance.
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Initiate class
		 *
		 * @since 1.0.0
		 * @return object
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = new WP_CLI_Loader();
				self::$instance->constants();
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Declare constants
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function constants() {

			define( 'WP_CLI_VER', '0.0.1' );
			define( 'WP_CLI_FILE', trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'wp-cli.php' );
			define( 'WP_CLI_DIR', plugin_dir_path( WP_CLI_FILE ) );
			define( 'WP_CLI_URL', plugins_url( '/', WP_CLI_FILE ) );
		}

		/**
		 * Include files required to plugin
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {

			if ( function_exists( 'shell_exec' ) && ! in_array( 'shell_exec', array_map( 'trim', explode( ', ', ini_get( 'disable_functions' ) ) ) ) && strtolower( ini_get( 'safe_mode' ) ) != 1 ) {
				add_action( 'admin_enqueue_scripts',     array( $this, 'wp_cli_scripts' ) );
				add_action( 'wp_ajax_get_wp_cli_result', array( $this, 'get_wp_cli_result_callback' ) );
				add_action( 'admin_footer',              array( $this, 'wp_cli_console_markup' ) );
				add_action( 'admin_bar_menu',            array( $this, 'wp_cli_admin_link' ), 1000 );
			} else {
				add_action( 'admin_notices',             array( $this, 'shell_exec_disabled' ) );
			}
		}

		/**
		 * Notice for disabled shell_exec function.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function shell_exec_disabled() {
			?>
			<div class="notice notice-error">
				<p><?php _e( 'Enable `shell_exec` from Server to work with WP CLI.', 'wp-cli' ); ?></p>
			</div>
			<?php
		}

		/**
		 * WP CLI Scripts
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function wp_cli_scripts() {
			wp_enqueue_style( 'wp-cli-style', WP_CLI_URL . 'assets/style.css' );
			wp_enqueue_script( 'wp-cli-script', WP_CLI_URL . 'assets/script.js', array( 'jquery' ), '0.0.1', true );
		}

		/**
		 * WP CLI Console Markup
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function wp_cli_console_markup() {
			require_once WP_CLI_DIR . 'template/wp-cli-console.php';
		}

		/**
		 * Ajax callback
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function get_wp_cli_result_callback() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die();
			}

			$response = array(
				'status' 	=> false,
				'result'	=> '',
			);
			$cmd = $_POST['cmd'];
			if ( 0 == strpos( $cmd, 'wp ' ) ) {
				$cmd = str_replace( 'wp ', 'php ' . WP_CLI_DIR . 'wp-cli.phar ', $cmd );
			}

			if ( function_exists( 'shell_exec' ) ) {
				$output = shell_exec( $cmd );
			} else {
				$output = 'Error: shell_exec not available';
			}

			if ( ! empty( $output ) ) {
				$response['status'] = true;
				$response['result'] = '<pre>' . $output . '</pre>';
			}

			wp_send_json( $response );
		}

		/**
		 * Add WP CLI button
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function wp_cli_admin_link() {
			global $wp_admin_bar;

			if ( ! is_super_admin() || ! is_admin_bar_showing() || ! is_admin() ) {
				return;
			}

			/* Add the main siteadmin menu item */
			$wp_admin_bar->add_menu( array(
				'id' => 'wp_cli_link',
				'title' => __( 'WP CLI', 'wp-cli' ),
				'href' => '#',
			) );
		}
	}

	WP_CLI_Loader::instance();
endif;
