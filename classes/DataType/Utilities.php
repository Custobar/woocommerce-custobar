<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) or exit;

/**
 * Class Utilities
 *
 * @package WooCommerceCustobar\DataType
 */
class Utilities {

	/**
	 * Returns the price in cents and rounds to total cents.
	 *
	 * @param  string $price
	 *
	 * @return int
	 */
	public static function getPriceInCents( $price ) {
		return (int) round( (float) $price * 100 );
	}

	/**
	 * Returns the time in the format required by Custobar API.
	 *
	 * @param  WC_DateTime|DateTime $datetime
	 *
	 * @return string
	 */
	public static function formatDateTime( $datetime ) {
		if ( ! $datetime || ! is_a( $datetime, 'DateTime' ) ) {
			return null;
		}
		return $datetime->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'Y-m-d\TH:i:s' );
	}
}
