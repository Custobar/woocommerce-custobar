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
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Products</td>
        <td class="custobar-center"><?php print $productStat->total; ?></td>
        <td class="custobar-center"><?php print $productStat->synced; ?></td>
        <td class="custobar-center"><?php print $productStat->synced_percent; ?></td>
        <td><?php print date('Y-m-d g:i:sA', $productStat->updated); ?></td>
        <td><button class="custobar-export" data-record-type="product">Run Exporter</button></td>
      </tr>
      <tr>
        <td>Sales</td>
        <td class="custobar-center"><?php print $saleStat->total; ?></td>
        <td class="custobar-center"><?php print $saleStat->synced; ?></td>
        <td class="custobar-center"><?php print $saleStat->synced_percent; ?></td>
        <td><?php print date('Y-m-d g:i:sA', $saleStat->updated); ?></td>
        <td><button class="custobar-export" data-record-type="sale">Run Exporter</button></td>
      </tr>
      <tr>
        <td>Customers</td>
        <td class="custobar-center"><?php print $customerStat->total; ?></td>
        <td class="custobar-center"><?php print $customerStat->synced; ?></td>
        <td class="custobar-center"><?php print $customerStat->synced_percent; ?></td>
        <td><?php print date('Y-m-d g:i:sA', $customerStat->updated); ?></td>
        <td><button class="custobar-export" data-record-type="customer">Run Exporter</button></td>
      </tr>
    <tbody>
  </table>

</div>

<hr />
