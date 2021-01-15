<?php

namespace WooCommerceCustobar\Synchronization;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\Data_Upload;

/**
 * Class Data_Sync
 *
 * @package WooCommerceCustobar\Synchronization
 */
abstract class Data_Sync {


	abstract public static function single_update( $item_id);
	abstract public static function batch_update();
	abstract protected static function format_single_item( $item);
	abstract protected static function upload_data_type_data( $data);

	protected static function upload_custobar_data( $data ) {

		$endpoint = static::$endpoint;

		$cds           = new \WooCommerceCustobar\DataSource\Custobar_Data_Source();
		$integrationId = $cds->get_integration_id();
		if ( ! $integrationId ) {
			$integrationId = $cds->create_integration();
		}

		if ( $integrationId ) {

			switch ( $endpoint ) {
				case '/customers/upload/':
					$dataSourceId = $cds->get_customer_data_source_id();
					if ( ! $dataSourceId ) {
						$dataSourceId = $cds->create_data_source( 'WooCommerce customers', 'customers' );
					}
					break;
				case '/products/upload/':
					$dataSourceId = $cds->get_product_data_source_id();
					if ( ! $dataSourceId ) {
						$dataSourceId = $cds->create_data_source( 'WooCommerce products', 'products' );
					}
					break;
				case '/sales/upload/':
					$dataSourceId = $cds->get_sale_data_source_id();
					if ( ! $dataSourceId ) {
						$dataSourceId = $cds->create_data_source( 'WooCommerce sales', 'sales' );
					}
					break;
			}

			if ( $dataSourceId ) {
				$endpoint = '/datasources/' . $dataSourceId . '/import/';
			}
		}

		return Data_Upload::upload_custobar_data( $endpoint, $data );

	}
}
