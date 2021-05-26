<div id="custobar-export-wrap">
	<h2>Custobar Sync Status</h2>
	<table class="custobar-sync-report">
	<thead>
	<tr>
		<th>Record Type</th>
		<th class="custobar-center">Total Records</th>
		<th class="custobar-center">Synced Records</th>
		<th class="custobar-center">Sync %</th>
		<th>Last Export</th>
		<th>Reset</th>
		<th colspan="2">Set marketing permissions</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		<tr class="sync-report-product">
			<td>Products</td>
			<td class="custobar-center"><?php echo esc_html($product_stat->total); ?> / <?php echo esc_html($product_stat->variant_total); ?></td>
			<td class="custobar-center"><?php echo esc_html($product_stat->synced); ?> / <?php echo esc_html($product_stat->variant_synced); ?></td>
			<td class="custobar-center"><?php echo esc_html($product_stat->synced_percent); ?></td>
			<td><?php echo esc_html($product_stat->last_updated); ?></td>
			<td><input name="reset-product" type="checkbox" value="1"></td>
			<td></td>
			<td></td>
			<td><button class="custobar-export button button-alt" data-record-type="product">Run Exporter</button></td>
		</tr>
		<tr class="sync-report-customer">
			<td>Customers</td>
			<td class="custobar-center"><?php echo esc_html($customer_stat->total); ?></td>
			<td class="custobar-center"><?php echo esc_html($customer_stat->synced); ?></td>
			<td class="custobar-center"><?php echo esc_html($customer_stat->synced_percent); ?></td>
			<td><?php echo esc_html($customer_stat->last_updated); ?></td>
			<td><input name="reset-customer" type="checkbox" value="1"></td>
			<td><input name="can-email-customer" type="checkbox" value="1">can_email</td>
			<td><input name="can-sms-customer" type="checkbox" value="1">can_sms</td>
			<td><button class="custobar-export button button-alt" data-record-type="customer">Run Exporter</button></td>
		</tr>
		<tr class="sync-report-sale">
			<td>Sales</td>
			<td class="custobar-center"><?php echo esc_html($sale_stat->total); ?></td>
			<td class="custobar-center"><?php echo esc_html($sale_stat->synced); ?></td>
			<td class="custobar-center"><?php echo esc_html($sale_stat->synced_percent); ?></td>
			<td><?php echo esc_html($sale_stat->last_updated); ?></td>
			<td><input name="reset-sale" type="checkbox" value="1"></td>
			<td></td>
			<td></td>
			<td><button class="custobar-export button button-alt" data-record-type="sale">Run Exporter</button></td>
		</tr>
	<tbody>
	</table>
</div>
<hr />
