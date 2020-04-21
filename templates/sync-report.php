<div id="custobar-export-wrap">

  <h2>Custobar Sync Status</h2>

  <table>
    <tr>
      <th>Record Type</th>
      <th>Total Records</th>
      <th>Synced Records</th>
      <th>Sync %</th>
      <th>Last Export</th>
      <th>&nbsp;</th>
    </tr>
    <tr>
      <td>Products</td>
      <td><?php print $productStat->total; ?></td>
      <td><?php print $productStat->synced; ?></td>
      <td><?php print ($productStat->synced / $productStat->total) * 100 . '%'; ?></td>
      <td>2020-04-10 02:34AM</td>
      <td><button class="custobar-export">Run Exporter</button></td>
    </tr>
    <tr>
      <td>Sales</td>
      <td><?php print $saleStat->total; ?></td>
      <td><?php print $saleStat->synced; ?></td>
      <td><?php print ($saleStat->synced / $saleStat->total) * 100 . '%'; ?></td>
      <td>2020-04-10 02:34AM</td>
      <td><button class="custobar-export">Run Exporter</button></td>
    </tr>
    <tr>
      <td>Customers</td>
      <td><?php print $customerStat->total; ?></td>
      <td><?php print $customerStat->synced; ?></td>
      <td><?php print ($customerStat->synced / $customerStat->total) * 100 . '%'; ?></td>
      <td><?php print $customerStat->updated; ?></td>
      <td><button class="custobar-export">Run Exporter</button></td>
    </tr>
  </table>

</div>

<hr style="margin-bottom:45px;" />
