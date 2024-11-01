<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @see       https://aaardvarkaccessibility.com
 * @since      1.0.0
 *
 * @package    AAArdvark
 * @subpackage AAArdvark/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     N Squared <support@aaardvarkaccessibility.com>
 */
class AAArdvark_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string the ID of this plugin
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string the current version of this plugin
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name the name of this plugin.
	 * @param string $version     the version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->hooks();		
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {}

	public function is_valid_key() {
		$options = get_option( 'aaardvark_options' );
		if ( empty( $options ) || empty( $options['api_key'] ) || empty( $options['api_key_valid'] ) ) {
			return false;
		}
		return true;
	}

	public function aaa_admin_url() {
		return admin_url( 'admin.php?page=aaardvark-accessibility' );
	}

	/**
	 * Check that the current user can access the report page.
	 *
	 * @return bool
	 */
	protected function check_access() {
		$user          = wp_get_current_user();
		$options       = get_option( 'aaardvark_options' );
		$allowed_roles = isset( $options['allowed_roles'] ) ?
			array_unique( array_merge( $options['allowed_roles'], array( 'administrator' ) ) ) :
			array( 'administrator' );

		if ( count( array_intersect( $allowed_roles, (array) $user->roles ) ) > 0 ) {
			return true;
		}

		return false;
	}


	/**
	 * Create the Menu Pages
	 *
	 * @return void
	 */
	public function init_menus() {
		if ( $this->check_access() ) {
			add_menu_page(
				'AAArdvark Accessibility',
				'AAArdvark',
				true,
				'aaardvark-accessibility',
				function() {
					include_once __DIR__ . '/partials/app-page.php';
				}
			);

			add_submenu_page( 
				'aaardvark-accessibility', 
				__('Accessibility Report', 'website-accessibility-audit-checker' ),
				__('Report', 'website-accessibility-audit-checker' ),
				true, 
				'aaardvark-accessibility' 
			);

			$this->init_options_page();
		} else {
			$this->init_options_page( true );
		}

	}


	/**
	 * Create the submenu and callback for the options page.
	 *
	 * @param bool $top_level Whether should be a top level menu item.
	 *
	 * @return void
	 */
	public function init_options_page( $top_level = false ) {
		register_setting( 'aaardvark', 'aaardvark_options', array( $this, 'options_validate' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_settings_section(
			'aaardvark_section_main',
			'',
			function () {
				include_once __DIR__ . '/partials/settings-instructions.php';
			},
			'aaardvark'
		);

		add_settings_field(
			'aaardvark_api_key', // Unique prefixed identifier
			__( 'API Key', 'website-accessibility-audit-checker' ),
			function () {
				include_once __DIR__ . '/partials/field-api-key.php';
			},
			'aaardvark',
			'aaardvark_section_main',
			array(
				'label_for' => 'aaardvark_api_key',
			)
		);

		add_settings_field(
			'aaardvark_allowed_roles', // Unique prefixed identifier
			__( 'Allowed Roles', 'website-accessibility-audit-checker' ),
			function () {
				include_once __DIR__ . '/partials/field-allowed-roles.php';
			},
			'aaardvark',
			'aaardvark_section_main',
			array(
				'label_for' => 'aaardvark_allowed_roles',
			)
		);

		if ( $top_level ) {
			$hook = add_menu_page(
				__('AAArdvark API Settings', 'website-accessibility-audit-checker' ),
				__('Accessibility', 'website-accessibility-audit-checker' ),
				'manage_options',
				'aaardvark-settings',
				array( $this, 'options_html' )
			);
		} else {
			$hook = add_submenu_page( 
				'aaardvark-accessibility', 
				__('AAArdvark API Settings', 'website-accessibility-audit-checker' ),
				__('Settings', 'website-accessibility-audit-checker' ),
				'manage_options', 
				'aaardvark-settings', 
				array( $this, 'options_html' ) 
			);
		}

		add_action( "load-{$hook}", array( $this, 'options_submit' ) );
	}

	/**
	 * Create the callback for the options HTML.
	 */
	public function options_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options      = get_option( 'aaardvark_options' );
		$existing_key = ( ! empty( $options['api_key'] ) ) ? $options['api_key'] : '';

		if ( ! empty( $existing_key ) ) {
			$options['api_key_valid'] = $this->check_api_key( $options['api_key'] );
			update_option( 'aaardvark_options', $options, false );
		}

		include_once __DIR__ . '/partials/settings-page.php';
	}

	/**
	 * Clean & validate the options.
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function options_validate( $input ) {
		$options      = get_option( 'aaardvark_options' );
		$existing_key = ( ! empty( $options['api_key'] ) ) ? $options['api_key'] : '';
		$new_key      = ( ! empty( $input['api_key'] ) ) ? $input['api_key'] : '';

		$new_key_valid = ( ! empty( $input['api_key'] ) ) ? $this->check_api_key( $input['api_key'] ) : false;

		if ( ! $new_key_valid ) {
			$input['api_key'] = $existing_key;
		} elseif ( empty( $new_key ) && empty( $existing_key ) ) {
			add_settings_error( 'api_key', 'api_key', 'api Key is required!' );
		}

		if ( ! $new_key_valid && ! empty( $new_key ) ) {
			add_settings_error( 'api_key', 'api_key', 'Could not connect with the key provided. Please check the key and try again.' );
		}

		if ( ! empty( $input['allowed_roles'] ) ) {
			$configured_roles = array_keys( wp_roles()->roles );

			$input['allowed_roles'] = array_filter(
				$input['allowed_roles'],
				function ( $role ) use ( $configured_roles ) {
					return in_array( $role, $configured_roles, true );
				}
			);
		}

		return $input;
	}

	/**
	 * Submit handler for options.
	 *
	 * @return void
	 */
	public function options_submit() {
	}

	/**
	 * Check that the API Key is valid.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function check_api_key( $key ) {

		$service = new AAArdvark_Services( $key, AAArdvark::API_BASE_URL, AAArdvark::API_VERSION );

		return $service->ping();
	}

	/**
	 * Form handler for the PDF and CSV Report Downloads.
	 *
	 * @return void
	 */
	public function handle_report_download() {
		$options = get_option( 'aaardvark_options' );
		$api_key = $options['api_key'];

		if ( empty( $api_key ) ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit( 'API Key is not set.' );
		}

		if ( ! $this->check_access() ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit( 'Access denied.' );
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

		if ( ! isset( $_POST['download_type'] ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit( 'Access denied.' );
		}

		$service = new AAArdvark_Services( $api_key, AAArdvark::API_BASE_URL, AAArdvark::API_VERSION );
		$service->download_report( sanitize_text_field( $_POST['download_type'] ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		if ( 'toplevel_page_aaardvark-accessibility' !== $hook ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/bundle.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_aaardvark-accessibility' !== $hook ) {
			return;
		}
		
		if ( $this->check_access() ) {
			wp_enqueue_script( $this->plugin_name, dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/main.js', array(), $this->version, true );

			wp_localize_script(
				$this->plugin_name,
				'__aaardvark',
				array(
					'apiRoot'       => esc_url_raw( rest_url() ),
					'adminFormRoot' => esc_url_raw( admin_url( 'admin-post.php' ) ),
					'nonce'         => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}
}
