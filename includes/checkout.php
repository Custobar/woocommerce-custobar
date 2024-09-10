<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

/**
 * Add Custobar marketing permissions to checkout fields
 *
 * @param array $checkout_fields
 * @return void
 */
function woocommerce_checkout_fields( $checkout_fields = array() ) {

	if ( apply_filters( 'woocommerce_custobar_show_email_permission_setting_checkout', true ) ) {
		$checkout_fields['order']['custobar_can_email'] = array(
			'type'     => 'checkbox',
			'class'    => array( 'my-field-class form-row-wide' ),
			'label'    => __( 'I would like to receive marketing messages via email', 'woocommerce-custobar' ),
			'required' => false,
			'default'  => false,
		);
	}
	if ( apply_filters( 'woocommerce_custobar_show_sms_permission_setting_checkout', true ) ) {
		$checkout_fields['order']['custobar_can_sms'] = array(
			'type'     => 'checkbox',
			'class'    => array( 'my-field-class form-row-wide' ),
			'label'    => __( 'I would like to receive marketing messages via SMS', 'woocommerce-custobar' ),
			'required' => false,
			'default'  => false,
		);
	}

	return $checkout_fields;
}
add_filter( 'woocommerce_checkout_fields', __NAMESPACE__ . '\\woocommerce_checkout_fields' );

function woocommerce_checkout_update_order_meta( $order_id, $posted ) {
	$order = wc_get_order($order_id);

	if ( apply_filters( 'woocommerce_custobar_show_email_permission_setting_checkout', true ) ) {
		$can_email = ( isset( $posted['custobar_can_email'] ) && $posted['custobar_can_email'] ) ? true : false;
		$order->update_meta_data( 'can_email', $can_email );
	}
	if ( apply_filters( 'woocommerce_custobar_show_sms_permission_setting_checkout', true ) ) {
		$can_sms = ( isset( $posted['custobar_can_sms'] ) && $posted['custobar_can_sms'] ) ? true : false;
		$order->update_meta_data( 'can_sms', $can_sms );
	}

	$order->save();
}
add_action( 'woocommerce_checkout_update_order_meta', __NAMESPACE__ . '\\woocommerce_checkout_update_order_meta', 10, 2 );

/**
 * Save Custobar marketing permissions as user data
 *
 * @param integer $customer_id
 * @param array   $posted
 * @return void
 */
function woocommerce_checkout_update_user_meta( $customer_id, $posted ) {
	if ( apply_filters( 'woocommerce_custobar_show_email_permission_setting_checkout', true ) ) {
		$can_email = ( isset( $posted['custobar_can_email'] ) && $posted['custobar_can_email'] ) ? true : false;
		update_user_meta( $customer_id, '_woocommerce_custobar_can_email', $can_email );
	}
	if ( apply_filters( 'woocommerce_custobar_show_sms_permission_setting_checkout', true ) ) {
		$can_sms = ( isset( $posted['custobar_can_sms'] ) && $posted['custobar_can_sms'] ) ? true : false;
		update_user_meta( $customer_id, '_woocommerce_custobar_can_sms', $can_sms );
	}

}
add_action( 'woocommerce_checkout_update_user_meta', __NAMESPACE__ . '\\woocommerce_checkout_update_user_meta', 10, 2 );
