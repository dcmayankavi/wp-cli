<?php
/**
 * WP CLI Console template.
 *
 * @package wp-cli
 * @since 0.0.1
 */

?>
<div class="wp-cli-console wp-ui-primary" style="display: none;">
	<div id="wp-cli-drag-bar" class="wp-ui-highlight"></div>
	<div class="wp-cli-console-title-wrap wp-ui-highlight">
		<span class="wp-cli-console-title"><?php echo esc_html__( 'WP_CLI CONSOLE', 'wp-cli' ) . ' - ' . get_bloginfo( 'name' ); ?></span><!--
		--><span class="wp-cli-console-close dashicons-no-alt dashicons"></span>
	</div>
	<div class="wp-cli-console-output"></div>
	<input id="wp-cli-cmd-input" type="text" class="wp-cli-cmd-input" >
	<img class="wp-cli-input-spinner" src="<?php echo WP_CLI_URL . '/assets/spinner.gif' ?>">
</div>
