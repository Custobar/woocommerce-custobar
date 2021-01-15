<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

class Template {

	public $data = array();
	public $path;
	public $name;

	public function __construct() {
		$this->path = 'templates/';
	}

	public function get() {
		if ( is_array( $this->data ) && ! empty( $this->data ) ) {
			// extract() is fine with templates
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $this->data );
		}
		ob_start();
		require WOOCOMMERCE_CUSTOBAR_PATH . $this->path . $this->name . '.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function render() {
		print $this->get();
	}

}
