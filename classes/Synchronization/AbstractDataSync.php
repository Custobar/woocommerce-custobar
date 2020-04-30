<?php

namespace WooCommerceCustobar\Synchronization;

defined('ABSPATH') or exit;

use WooCommerceCustobar\DataUpload;

/**
 * Class AbstractDataSync
 *
 * @package WooCommerceCustobar\Synchronization
 */
abstract class AbstractDataSync
{
    abstract public static function singleUpdate($item_id);
    abstract public static function batchUpdate();
    abstract protected static function formatSingleItem($item);
    abstract protected static function uploadDataTypeData($data);

    protected static function uploadCustobarData($data) {

      $endpoint = static::$endpoint;

      $cds = new DataSource\CustobarDataSource();
      $integrationId = $cds->getIntegrationId();
      if( !$integrationId ) {
        $integrationId = $cds->createIntegration();
      }

      if( $integrationId ) {

        switch( $endpoint ) {
          case '/customers/upload/':
            $dataSourceId = $cds->getCustomerDataSourceId();
            if( !$dataSourceId ) {
              $dataSourceId = $cds->createDataSource( 'WooCommerce customers', 'customers' );
            }
            break;
          case '/products/upload/':
            $dataSourceId = $cds->getProductDataSourceId();
            if( !$dataSourceId ) {
              $dataSourceId = $cds->createDataSource( 'WooCommerce products', 'products' );
            }
            break;
          case '/sales/upload/':
            $dataSourceId = $cds->getSaleDataSourceId();
            if( !$dataSourceId ) {
              $dataSourceId = $cds->createDataSource( 'WooCommerce sales', 'sales' );
            }
            break;
        }

        if( $dataSourceId ) {
          $endpoint = '/api/datasources/' . $dataSourceId . '/import/';
        }

      }

      return DataUpload::uploadCustobarData($endpoint, $data);

    }
}
