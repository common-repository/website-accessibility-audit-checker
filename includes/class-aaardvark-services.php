<?php
/**
 * The API Services functionality of the plugin.
 *
 * @see       https://aaardvarkaccessibility.com
 * @since      1.0.0
 * @package AAArdvark
 */

/**
 * The API Services functionality of the plugin.
 *
 * @author     N Squared <support@aaardvarkaccessibility.com>
 */
class AAArdvark_Services {

	/**
	 * The API Base URL
	 *
	 * @var string
	 */
	private $api_base_url;

	/**
	 * The API version
	 *
	 * @var string
	 */
	private $api_version;


	/**
	 * The Site ID
	 *
	 * @var string
	 */
	private $site_id;

	/**
	 * The API key
	 *
	 * @var string
	 */
	private $api_key;


	/**
	 * Constructor for the service class.
	 *
	 * @since    1.0.0
	 *
	 * @param string $api_config
	 * @param string $api_base_url
	 * @param string $api_version
	 * @return void
	 */
	public function __construct( $api_config, $api_base_url, $api_version ) {

		// Split out the site ID from the Bearer token.
		preg_match( '/^(\w+)\|(.*)/', $api_config, $matches );
		$site_id            = isset( $matches[1] ) ? $matches[1] : null;
		$api_key            = isset( $matches[2] ) ? $matches[2] : null;
		$this->site_id      = $site_id;
		$this->api_key      = $api_key;
		$this->api_base_url = $api_base_url;
		$this->api_version  = $api_version;
	}

	/**
	 * Helper function to build the API URL from the injected configuration.
	 *
	 * @param mixed $endpoint
	 * @return string
	 */
	protected function get_url( $endpoint ) {
		return "{$this->api_base_url}/{$this->api_version}/{$endpoint}";
	}

	/**
	 * Check that the service is responding for the configured
	 * API key.
	 *
	 * @return bool
	 */
	public function ping() {
		$request = wp_remote_get(
			$this->get_url( "site/{$this->site_id}/scans" ),
			array(
				'headers' => array(
					'Authorization' => "Bearer {$this->api_key}",
					'Accept'        => 'application/json',
				),
			)
		);

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get reporting data for the site associated with the
	 * configured API key.
	 *
	 * @return array
	 */
	public function scans() {
		$request       = wp_remote_get(
			$this->get_url( "site/{$this->site_id}/scans" ),
			array(
				'headers' => array(
					'Authorization' => "Bearer {$this->api_key}",
					'Accept'        => 'application/json',
				),
			)
		);
		$response_code = wp_remote_retrieve_response_code( $request );
		if ( is_wp_error( $request ) || $response_code !== 200 ) {
			$response_code = ! empty( $response_code ) ? $response_code : 500;
			$e             = new Exception( 'Failed to connect to API.', $response_code );
			throw $e;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		$chart_data = array(
			'labels' => array(),
			'series' => array( array() ),
		);
		$table_data = array(
			'caption' => 'Results',
			'headers' => array(
				'Date',
				'# Pages',
				'# Issues',
				'Total Active Issues',
			),
			'rows'    => array(),
		);

		$num_rows = count( $response->data );

		foreach ( $response->data as $index => $scan ) {
			$date                      = DateTime::createFromFormat( 'Y-m-d', substr( $scan->date, 0, 10 ) )->format( 'm/d/y' );
			$chart_data['labels'][]    = $scan->human_date;
			$table_data['rows'][]      = array(
				$date,
				$scan->statistics->total_pages,
				"{$scan->statistics->warnings} Warnings / {$scan->statistics->errors} Errors",
				$scan->statistics->active_issues,
			);

			if ( $num_rows === 1 ) {
				$start_date = $date;
			}
			if ( $index === 0 ) {
				$end_date = $date;
			}
			if ( ! isset( $start_date ) && $index === $num_rows - 1 ) {
				$start_date = $date;
			}
		}
		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			$table_data['caption'] .= " {$start_date} - {$end_date}";
		}

		$chart_data['labels']    = array_reverse( $chart_data['labels'] );
		$chart_data['series'][0] = array_reverse( $chart_data['series'][0] );

		return array(
			'site'      => $response->meta->site->data[0],
			'team'      => $response->meta->team->data[0],
			'scan'      => $response->data[0],
			'chartData' => $chart_data,
			'tableData' => $table_data,
		);
	}

	/**
	 * Get page data for the site associated with the
	 * configured API key.
	 *
	 * @param int $page The page to pass to the API request.
	 *
	 * @return array
	 */
	public function pages( $page = 1 ) {
		$request       = wp_remote_get(
			$this->get_url( "site/{$this->site_id}/pages?" ) . http_build_query( array( 'page' => $page ) ),
			array(
				'headers' => array(
					'Authorization' => "Bearer {$this->api_key}",
					'Accept'        => 'application/json',
				),
			)
		);
		$response_code = wp_remote_retrieve_response_code( $request );
		if ( is_wp_error( $request ) || $response_code !== 200 ) {
			$response_code = ! empty( $response_code ) ? $response_code : 500;
			$e             = new Exception( 'Failed to connect to API.', $response_code );
			throw $e;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		$table_data = array(
			'caption' => 'Pages Scanned',
			'headers' => array(
				'Path',
				'# Instances',
				'Scan Status',
			),
			'rows'    => array(),
		);

		foreach ( $response->data as $index => $page ) {
			$table_data['rows'][] = array(
				$page->path,
				"{$page->warning_count} Warnings / {$page->error_count} Errors",
				$page->status ? 'OK' : $page->last_scan_error,
			);
		}

		return array(
			'site'       => $response->meta->site->data[0],
			'tableData'  => $table_data,
			'pagination' => $response->meta->pagination,
		);
	}

	/**
	 * Download the requested report from the API.
	 *
	 * @param string $type The report type, either csv or pdf.
	 *
	 * @return mixed
	 */
	public function download_report( $type ) {
		if ( ! in_array( $type, array( 'csv', 'pdf' ), true ) ) {
			return new WP_Error( 'Invalid type.' );
		}

		$url      = $this->get_url( "site/{$this->site_id}/report" ) . '?' . http_build_query( array( 'type' => $type ) );
		$filename = "accessibility-report.{$type}";


		return $this->download_from_memory( $url, $filename );
	}

	/**
	 * Alternate method of download
	 *
	 * @param string $url The endpoint to download from.
	 * @param string $filename The download filename.
	 *
	 * @return never
	 */
	protected function download_from_memory( $url, $filename ) {
		$response      = wp_remote_get(
			$url,
			array(
				'blocking' => true,
				'redirection' => 0,
				'headers'  => array(
					'Authorization' => "Bearer {$this->api_key}",
				),
			)
		);
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			status_header( $response_code );
			echo esc_html__( 'Error downloading report. Please check your API Key.', 'website-accessibility-audit-checker' );
		} else {
			$this->send_download_headers( $filename );
			echo esc_html( wp_remote_retrieve_body( $response ) );
		}
		exit();
	}

	/**
	 * Helper method to send download headers.
	 *
	 * @param string $filename Filename for content disposition header.
	 *
	 * @return void
	 */
	protected function send_download_headers( $filename ) {
		header( "Content-Disposition: attachment; filename=\"{$filename}\"" );
		header( 'Pragma: public' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	}
}
