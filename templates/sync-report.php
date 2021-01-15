<div id="custobar-export-wrap">
	<h2>Custobar Sync Status</h2>
	<table>
	<thead>
	<tr>
		<th>Record Type</th>
		<th class="custobar-center">Total Records</th>
		<th class="custobar-center">Synced Records</th>
		<th class="custobar-center">Sync %</th>
		<th>Last Export</th>
		<th>Reset</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		<tr class="sync-report-product">
			<td>Products</td>
			<td class="custobar-center"><?php echo $product_stat->total; ?> / <?php echo $product_stat->variant_total; ?></td>
			<td class="custobar-center"><?php echo $product_stat->synced; ?> / <?php echo $product_stat->variant_synced; ?></td>
			<td class="custobar-center"><?php echo $product_stat->synced_percent; ?></td>
			<td><?php echo $product_stat->last_updated; ?></td>
			<td><input name="reset-product" type="checkbox" value="1"></td>
			<td><button class="custobar-export" data-record-type="product">Run Exporter</button></td>
		</tr>
		<tr class="sync-report-customer">
			<td>Customers</td>
			<td class="custobar-center"><?php echo $customer_stat->total; ?></td>
			<td class="custobar-center"><?php echo $customer_stat->synced; ?></td>
			<td class="custobar-center"><?php echo $customer_stat->synced_percent; ?></td>
			<td><?php echo $customer_stat->last_updated; ?></td>
			<td><input name="reset-customer" type="checkbox" value="1"></td>
			<td><button class="custobar-export" data-record-type="customer">Run Exporter</button></td>
		</tr>
		<tr class="sync-report-sale">
			<td>Sales</td>
			<td class="custobar-center"><?php echo $sale_stat->total; ?></td>
			<td class="custobar-center"><?php echo $sale_stat->synced; ?></td>
			<td class="custobar-center"><?php echo $sale_stat->synced_percent; ?></td>
			<td><?php echo $sale_stat->last_updated; ?></td>
			<td><input name="reset-sale" type="checkbox" value="1"></td>
			<td><button class="custobar-export" data-record-type="sale">Run Exporter</button></td>
		</tr>
	<tbody>
	</table>

</div>

<hr />
