<?php
include 'global-header.php';
include 'global-menu.php';
require_once 'stats.php';
require_once 'textdb.php';

$_REQUEST['cache'] = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : 5;

$values = unserialize(file_get_contents(FILE_NETWORK_SITES_VALUES));
$cache = unserialize(file_get_contents(FILE_NETWORK_SITES_CACHE));

$db = new NetworkDB();
?>

    <script type="text/javascript" src="js/network-stats.js"></script>

    <div class="centered-header">
      Network Stats: <span id="num-items"><?php echo $db->Count(); ?></span> Total Sites, <span id="shown-items"></span> Shown
    </div>


    <!-- SEARCH OPTIONS START -->
    <div class="ta-center block-center search-fields">
      <form action="index.php" method="post">
        <b style="margin-left: 15px;">Owner:</b>
        <select name="owner">
          <option value="">-- ALL --</option>
          <?php
          if( !empty($values['owners']) )
          {
              echo form_options($values['owners'], $_REQUEST['owner']);
          }
          ?>
        </select>

        <b style="margin-left: 15px;">Category:</b>
        <select name="category">
          <option value="">-- ALL --</option>
          <?php
          if( !empty($values['categories']) )
          {
              echo form_options($values['categories'], $_REQUEST['category']);
          }
          ?>
        </select>

        <input type="submit" value="Submit" style="margin-left: 15px;">
        <input type="hidden" name="r" value="_xNetworkStatsShow">
      </form>
    </div>
    <!-- SEARCH OPTIONS END -->



    <!-- NETWORK STATS START -->
    <table id="network-stats" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 1000px;">
      <?php _stats_network_table_header(); ?>
      <tbody>
      <?php

      $all_cached = true;
      $so_total = new StatsOverall(array('domain' => 'Network Totals'));

      $db->AddFilter('owner', $_REQUEST['owner']);
      $db->AddFilter('category', $_REQUEST['category']);

      foreach( $db->RetrieveAll() as $site )
      {
          if( !$site['flag_stats'] )
          {
              continue;
          }

          if( isset($cache[$site['domain']]) && $cache[$site['domain']]['timestamp'] >= time() - 300 )
          {
              $so = $cache[$site['domain']]['so'];
          }
          else
          {
              $all_cached = false;
              $so = new StatsOverall($site);
              $so->SetUnknown();
          }

          _stats_network_table_row($so, $site);
          $so_total->AddStats($so);
      }
      ?>
      </tbody>
      <tfoot>
      <?php
      if( !$all_cached )
      {
          $so_total->SetUnknown();
      }

      _stats_network_table_row($so_total, null, null);
      ?>
      </tfoot>
    </table>
    <!-- NETWORK STATS END -->


    <form id="form-network-login" action="" method="post" target="_blank">
      <input type="hidden" name="cp_username" value=""/>
      <input type="hidden" name="cp_password" value=""/>
      <input type="hidden" name="r" value="_xIndexShow"/>
    </form>


    <!-- TOOLBAR START -->
    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xNetworkSitesAddShow" class="dialog" title="Add Trade"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/reload-32x32.png" class="action" title="Refresh Stats">
          <img src="images/edit-32x32.png" class="action" title="Bulk Edit">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/network-stats.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>

        <!-- LOADING MESSAGE START -->
        <div id="stats-loading-notice" class="message-notice ta-center va-middle">
          <img src="images/activity-16x16.gif"/>
          <span>Loading stats for <span id="stats-loading-site"></span> (<span id="stats-loading-current"></span> of <span id="stats-loading-total"></span>)</span>
        </div>
        <!-- LOADING MESSAGE END -->
      </div>
    </div>

    <div id="toolbar-vspacer"></div>
    <!-- TOOLBAR END -->


    <!-- SITE MENU START -->
    <div id="site-action-menu" class="icon-menu" style="top: 8px; left: 8px;" tabindex="0">
      <div>
        <div js="refresh"><img src="images/reload-16x16.png"> <span>Refresh</span></div>
        <div fnc="_xNetworkSitesEditShow"><img src="images/edit-16x16.png"> <span>Edit</span></div>
        <div fnc="_xNetworkSitesDelete" confirm="Are you sure you want to delete this network site?"><img src="images/delete-16x16.png"> <span>Delete</span></div>
      </div>
    </div>
    <!-- SITE MENU END -->


    <!-- FLOATING HEADER START -->
    <table id="thead-float" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 1000px; position: fixed; top: 0px; display: none;">
    <?php _stats_network_table_header('Network Site', false); ?>
    </table>
    <!-- FLOATING HEADER END -->

<?php
include 'global-footer.php';

function _stats_network_table_header($item = 'Network Site', $checkbox = true)
{
?>
      <thead>
        <tr class="ta-center">
          <td colspan="2"></td>
          <td colspan="6">60 Minutes</td>
          <td colspan="8">24 Hours</td>
        </tr>
        <tr>
          <th width="25"><?php if( $checkbox ): ?><input type="checkbox" class="check-all"/><?php endif; ?></th>
          <th><div style="width: 200px;"><?php echo $item; ?></div></th>
          <th width="65">In</th>
          <th width="65">Out</th>
          <th width="65">Clks</th>
          <th width="65">Qual</th>
          <th width="40">Prod</th>
          <th width="40">Ret</th>
          <th width="65">In</th>
          <th width="65">Out</th>
          <th width="65">Clks</th>
          <th width="60">Qual</th>
          <th width="65">U.In</th>
          <th width="40">Prod</th>
          <th width="40">Skim</th>
          <th width="40">Ret</th>
        </tr>
      </thead>
<?php
}

function _stats_network_table_row($so, $site, $menu = 'site-action-menu')
{
    $id = $so->trade['domain'];
    $domain = htmlspecialchars($so->trade['domain']);
    $status = $so->trade['status'];
    $status_lc = strtolower($status);
    $site = string_htmlspecialchars($site);
?>
        <tr <?php if( !empty($so->trade['color']) ) echo 'bgcolor="' . $so->trade['color'] . '"'; ?> id="item-<?php echo $id; ?>" class="ta-right<?php if( $so->i_raw_60 === STATS_UNKNOWN ) echo ' unknown'; ?>">
          <?php if( !empty($menu) ): ?>
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $id; ?>"/>
          </td>
          <?php endif; ?>
          <td class="va-middle" style="padding-right: 4px;"<?php if( empty($menu) ): ?> colspan="2"<?php endif; ?>>
            <?php if( !empty($menu) ): ?>
            <a href="<?php echo $site['url']; ?>" class="cp-login fw-bold" cpurl="<?php echo $site['url']; ?>" cppass="<?php echo $site['password']; ?>" cpuser="<?php echo $site['username']; ?>" target="_blank"><?php echo $domain; ?></a>
            <span class="icon-menu-container" menu="#<?php echo $menu; ?>" style="position: relative;"><img src="images/actions-16x16.png"/></span>
            <?php else: ?>
            <?php echo $domain; ?>
            <?php endif; ?>
          </td>
          <td class="i_raw_60"><?php echo format_int_to_string($so->i_raw_60); ?></td>
          <td><?php echo format_int_to_string($so->o_raw_60); ?></td>
          <td><?php echo format_int_to_string($so->c_raw_60); ?></td>
          <td style="padding: 2px 0px;">
            <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_60; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_60; ?>%"></div>
            <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_60; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_60; ?>%"></div>
            <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_60; ?>%" title="Bad: <?php echo $so->i_ctry_b_pct_60; ?>%"></div>
          </td>
          <td><?php echo $so->prod_60; ?>%</td>
          <td><?php echo $so->return_60; ?>%</td>

          <td><?php echo format_int_to_string($so->i_raw_24); ?></td>
          <td><?php echo format_int_to_string($so->o_raw_24); ?></td>
          <td><?php echo format_int_to_string($so->c_raw_24); ?></td>
          <td style="padding: 2px 0px;">
            <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_24; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_24; ?>%"></div>
            <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_24; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_24; ?>%"></div>
            <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_24; ?>%" title="Bad: <?php echo $so->i_ctry_n_pct_24; ?>%"></div>
          </td>
          <td><?php echo format_int_to_string($so->i_uniq_24); ?></td>
          <td><?php echo $so->prod_24; ?>%</td>
          <td><?php echo $so->skim_24; ?>%</td>
          <td><?php echo $so->return_24; ?>%</td>
        </tr>
<?php
}
?>