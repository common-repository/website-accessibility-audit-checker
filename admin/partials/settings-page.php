<?php
/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @see       https://aaardvarkaccessibility.com
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>


<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
	<?php
	settings_fields( 'aaardvark' );
	do_settings_sections( 'aaardvark' );
	submit_button( __( 'Save Settings', 'website-accessibility-audit-checker' ) );
	?>
	</form>
</div>

<?php
