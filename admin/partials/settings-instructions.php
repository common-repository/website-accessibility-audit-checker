<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options = get_option( 'aaardvark_options' );
?>
<div style="padding:16px; background:white;border:1px solid #ccc;border-radius:12px; margin-top: 12px;">
<p>
  <?php 
    $site_url = AAArdvark::AAA_SITE_URL; 
    $connecting_api_key_str = sprintf( 
      __( 'Connecting with your %1$s API Key %2$s allows you to display accessibility statistics and reports from %3$s AAArdvark %4$s directly in your WordPress Dashboard.', 'website-accessibility-audit-checker' ),
      '<strong>',
      '</strong>',
      '<a href="' . esc_url( $site_url ) . '" target="_blank" rel="noopener">',
      '</a>',
    );
    echo wp_kses( $connecting_api_key_str, 'post' );
  ?>
</p>
<?php if ( empty( $options['api_key'] ) ) { ?>
<p>
  <?php 
    $need_account_str = sprintf( 
      __( 'Need an account? %1$s Sign up for free today.%2$s', 'website-accessibility-audit-checker' ),
      '<a href="' . esc_url( $site_url ) . '" target="_blank" rel="noopener">',
      '</a>',
    );
    echo wp_kses( $need_account_str, 'post' );
  ?>
</p>
<?php } ?>
</div>
