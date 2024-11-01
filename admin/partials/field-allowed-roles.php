<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$options       = get_option( 'aaardvark_options' );
$allowed_roles = isset( $options['allowed_roles'] ) ? $options['allowed_roles'] : array();

$roles = wp_roles()->roles;
?>
<fieldset>
<legend style="padding-bottom:12px;font-weight:500;"><?php echo esc_html__( 'Roles with access to accessibility reports.', 'website-accessibility-audit-checker' ); ?></legend>
<?php foreach ( $roles as $key => $r ) { ?>
<div style="display:block">
	<label>
	<div style="display:flex;flex-direction:row-reverse;align-items:center; justify-content:flex-end;">
		<span><?php echo esc_html( $r['name'] ); ?></span>
		<input
			type="checkbox"
			name="aaardvark_options[allowed_roles][]"
			value="<?php echo esc_attr( $key ); ?>"
			style="margin-top:1px;"
			<?php echo 'administrator' === $key ? 'disabled' : ''; ?>
			<?php echo in_array( $key, $allowed_roles, true ) || 'administrator' === $key ? 'checked' : ''; ?> >
		</div>
	</label>
</div>
<?php } ?>
</fieldset>
