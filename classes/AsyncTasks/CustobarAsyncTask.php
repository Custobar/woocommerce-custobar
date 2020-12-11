<?php

namespace WooCommerceCustobar\AsyncTasks;

defined( 'ABSPATH' ) or exit;

/**
 * Class CustobarAsyncTask
 *
 * @package WooCommerceCustobar\AsyncTasks
 */
class CustobarAsyncTask extends \WP_Async_Task {

	protected $action;

	public function __construct( $action ) {
		$this->action = $action;
		parent::__construct();
	}

    protected function prepare_data($data) {  // @codingStandardsIgnoreLine
		return array( 'data' => $data );
	}

    protected function run_action() {  // @codingStandardsIgnoreLine
		do_action( 'wp_async_' . $this->action, $_POST['data'] );
	}
}
