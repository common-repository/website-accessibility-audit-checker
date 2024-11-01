<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options = get_option( 'aaardvark_options' );
?>
<div style="position:relative;">
<input
<?php ( empty( $options['api_key'] ) ? 'required' : '' ); ?>
style="width:400px"
<?php if ( ! empty( $options['api_key'] ) ) { ?>
placeholder="********************************"
onfocus="this.placeholder = ''"
onblur="this.placeholder = '********************************'"
<?php } ?>
id="api_key"
name="aaardvark_options[api_key]"
>

<?php if ( ! empty( $options['api_key'] ) ) { ?>
	<?php if ( isset( $options['api_key_valid'] ) && $options['api_key_valid'] ) { ?>
<span style="position:absolute;margin-left:12px;color:white;background-color:green;padding:4px 6px;border-radius:12px;font-size:12px;">✓</span>
<p>
<strong><?php echo esc_html__( 'Your API Key has been set. It is not displayed here for security purposes.', 'website-accessibility-audit-checker' ); ?></strong>
</p>
<?php } else { ?>
	<span style="position:absolute;margin-left:12px;color:white;background-color:red;padding:4px 8px;border-radius:12px;font-size:12px;">x</span>
<p>
<strong><?php echo esc_html__( 'There was an error connecting to AAArdvark with your API key.', 'website-accessibility-audit-checker' ); ?></strong>
</p>
<?php } ?>
<?php } ?>
</div>
