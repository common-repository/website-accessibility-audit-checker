<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options = get_option( 'aaardvark_options' );
?>

<?php if ( ! empty( $options['api_key'] ) ) { ?>
	<div id="aaardvark-app"></div>
<?php } else { ?>
	<div>
		<?php 
			$site_url = site_url( '/wp-admin/admin.php?page=aaardvark-settings' ); 
			$setup_api_key_str = sprintf( 
				__( 'Please set up your %1$s API Key %2$s to access statistics and reports.', 'website-accessibility-audit-checker' ),
				'<a href="' . esc_url( $site_url ) . '">',
				'</a>',
			);
			echo wp_kses( $setup_api_key_str, array(
				'a' => array(
					'href' => array(),
				),
			) );
		?>
	</div>
<?php } ?>
