<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'textdb.php';

$db = new NetworkDB();
$sites = $db->RetrieveAll('domain');
?>

    <script type="text/javascript" src="js/network-sites.js"></script>

    <div class="centered-header">
      Network Sites: <span id="num-items"><?php echo count($sites); ?></span> Configured
    </div>

    <table id="network-sites" align="center" width="1000" cellspacing="0" class="item-table">
      <thead>
        <tr class="ta-center">
          <th style="width: 25px;">
            <input type="checkbox" class="check-all"/>
          </th>
          <th>
            Domain
          </th>
          <th style="width: 150px;">
            Owner
          </th>
          <th style="width: 150px;">
            Category
          </th>
          <th style="width: 100px;">
          </th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach( $sites as $original )
        {
            $item = string_htmlspecialchars($original);
            include 'network-sites-tr.php';
        }
        ?>
      </tbody>
    </table>

    <form id="form-network-login" action="" method="post" target="_blank">
      <input type="hidden" name="cp_username" value=""/>
      <input type="hidden" name="cp_password" value=""/>
      <input type="hidden" name="r" value="_xIndexShow"/>
    </form>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xNetworkSitesAddShow" class="dialog" title="Add"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/edit-32x32.png" class="action" title="Bulk Edit">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/network-manage-sites.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';
?>