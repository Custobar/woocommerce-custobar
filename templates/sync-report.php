<?php
namespace WooCommerceCustobar\Synchronization;
?>
<div class="custobar-admin-table">
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Data type', 'woocommerce-custobar' ); ?>
	</div>
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Status', 'woocommerce-custobar' ); ?>
	</div>
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Export started', 'woocommerce-custobar' ); ?>
	</div>
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Completed', 'woocommerce-custobar' ); ?>
	</div>
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Uploaded records', 'woocommerce-custobar' ); ?>
	</div>
	<div class="custobar-admin-table__item custobar-admin-table__item--heading">
		<?php _e( 'Action', 'woocommerce-custobar' ); ?>
	</div>
	<!-- Loop through each data type -->
	<?php
	$data_types = Data_Sync::get_data_types();
	foreach ( $data_types as $data_type ) {
		$export_data = Data_Sync::get_data_type_export_data( $data_type );
		$launch_export_url = wp_nonce_url(
			add_query_arg( 'launch_custobar_export', $data_type, admin_url( 'admin.php?page=wc-settings&tab=checkout&tab=custobar' ) ),
			'woocommerce_custobar_' . $data_type . '_export',
			'woocommerce_custobar_' . $data_type . '_export_nonce'
		);
		$launch_export_url = add_query_arg( 'woocommerce_custobar_export_id', uniqid(), $launch_export_url );
		?>
			<div class="custobar-admin-table__item">
				<strong><?php echo ucfirst( $data_type.'s' ); ?></strong>
			</div>
			<div class="custobar-admin-table__item">

				<?php
				$status = $export_data['status'] ?? '';
				switch ( $export_data['status'] ) {
					case 'in_progress':
						_e( 'In progress', 'woommerce-custobar' );
					break;
					case 'completed':
						_e( 'Completed', 'woommerce-custobar' );
					break;
					case 'failed':
						_e( 'Failed', 'woommerce-custobar' );
					break;
					default: 
						_e( 'Not started', 'woommerce-custobar' );
				}
			?>
			</div>
			<div class="custobar-admin-table__item">
				<?php echo !empty( $export_data['start_time'] ) ? date_i18n( get_option('date_format') . ' ' . get_option('time_format'), $export_data['start_time'] ) : ''; ?>
			</div>
			<div class="custobar-admin-table__item">
				<?php echo !empty( $export_data['completed_time'] ) ? date_i18n( get_option('date_format') . ' ' . get_option('time_format'), $export_data['completed_time'] ) : ''; ?>
			</div>
			<div class="custobar-admin-table__item">
				<?php echo $export_data['exported_count'] ?? ''; ?>
			</div>
			<div class="custobar-admin-table__item">
				<a href="<?php echo $launch_export_url; ?>" class="button-primary"><?php _e( 'Start export', 'woocommerce-custobar' ); ?></a>
			</div>
		<?php
	}
	?>
</div>