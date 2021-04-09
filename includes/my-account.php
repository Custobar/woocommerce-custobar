<?php
namespace WooCommerceCustobar;

// Add Marketing Permissions fields
function woocommerce_edit_account_form_marketing_permissions() {
	$allow_email_marketing_checked = get_user_meta( get_current_user_id(), '_woocommerce_custobar_can_email', true ) ? true : '';
	$allow_sms_marketing_checked   = get_user_meta( get_current_user_id(), '_woocommerce_custobar_can_sms', true ) ? true : '';
	if ( apply_filters( 'woocommerce_custobar_show_email_permission_setting_my_account', true ) ) : ?>
	<p class="form-row form-row-wide">
		<span class="woocommerce-input-wrapper">
			<label for="custobar_can_email" class="checkbox">
				<input type="checkbox" class="input-checkbox" name="custobar_can_email" id="custobar_can_email" <?php checked( $allow_email_marketing_checked, true, true ); ?>>
				<?php esc_html_e( 'I would like to receive marketing messages via Email', 'woocommerce-custobar' ); ?>
			</label>
		</span> 
	</p>
	<?php endif; ?>
	<?php if ( apply_filters( 'woocommerce_custobar_show_sms_permission_setting_my_account', true ) ) : ?>
	<p class="form-row form-row-wide">
		<span class="woocommerce-input-wrapper">
			<label for="custobar_can_sms" class="checkbox">
				<input type="checkbox" class="input-checkbox" name="custobar_can_sms" id="custobar_can_sms" <?php checked( $allow_sms_marketing_checked, true, true ); ?>>
				<?php esc_html_e( 'I would like to receive marketing messages via SMS', 'woocommerce-custobar' ); ?>
			</label>
		</span> 
	</p>
		<?php
	endif;
}
add_action( 'woocommerce_edit_account_form', __NAMESPACE__ . '\\woocommerce_edit_account_form_marketing_permissions' );

// Save marketing permission fields as user meta
function woocommerce_save_account_details( $customer_id ) {
	if ( apply_filters( 'woocommerce_custobar_show_email_permission_setting_my_account', true ) ) {
		$can_email = ( isset( $_POST['custobar_can_email'] ) && $_POST['custobar_can_email'] ) ? true : false; // @codingStandardsIgnoreLine
		update_user_meta( $customer_id, '_woocommerce_custobar_can_email', $can_email );
	}
	if ( apply_filters( 'woocommerce_custobar_show_sms_permission_setting_my_account', true ) ) {
		$can_sms = ( isset( $_POST['custobar_can_sms'] ) && $_POST['custobar_can_sms'] ) ? true : false; // @codingStandardsIgnoreLine
		update_user_meta( $customer_id, '_woocommerce_custobar_can_sms', $can_sms );
	}
}
add_action( 'woocommerce_save_account_details', __NAMESPACE__ . '\\woocommerce_save_account_details', 10, 1 );

