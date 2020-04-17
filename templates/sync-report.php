<div id="custobar-export-wrap" style="margin:25px 0 45px;">

  <h2>Export Existing Data</h2>
  <p>Export your existing WooCommerce data to Custobar.</p>
  <button id="custobar-export">Run Exporter</button>

  <h3>Custobar Export Sync Status</h3>
  <table>
    <tr>
      <th>Record Type</th>
      <th>Total Count</th>
      <th>Synced Count</th>
      <th>Sync %</th>
    </tr>
    <tr>
      <td>Products</td>
      <td><?php print $productStat->total; ?></td>
      <td><?php print $productStat->synced; ?></td>
      <td><?php print ($productStat->synced / $productStat->total) * 100 . '%'; ?></td>
    </tr>
    <tr>
      <td>Sales</td>
      <td><?php print $saleStat->total; ?></td>
      <td><?php print $saleStat->synced; ?></td>
      <td><?php print ($saleStat->synced / $saleStat->total) * 100 . '%'; ?></td>
    </tr>
    <tr>
      <td>Customers</td>
      <td><?php print $customerStat->total; ?></td>
      <td><?php print $customerStat->synced; ?></td>
      <td><?php print ($customerStat->synced / $customerStat->total) * 100 . '%'; ?></td>
    </tr>
  </table>

</div>

<hr style="margin-bottom:45px;" />
