<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'textdb.php';

$_REQUEST['sort_by'] = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : null;

$db = new ToplistsDB();
$toplists = $db->RetrieveAll($_REQUEST['sort_by']);
?>

    <script type="text/javascript" src="js/toplists.js"></script>

    <div class="centered-header">
      Toplists: <span id="num-items"><?php echo count($toplists); ?></span> Configured
    </div>

    <table align="center" width="90%" cellspacing="0" class="item-table">
      <thead>
        <tr>
          <td class="ta-center" style="width: 25px;">
            <input type="checkbox" class="check-all"/>
          </td>
          <td class="ta-center<?php if( $db->sorter == 'toplist_id' ) echo ' sort-by'; ?>" style="width: 55px;">
            <a href="index.php?r=_xToplistsShow&sort_by=toplist_id">ID</a>
          </td>
          <td class="<?php if( $db->sorter == 'source' ) echo 'sort-by'; ?>" style="width: 90px;">
            <a href="index.php?r=_xToplistsShow&sort_by=source">Source</a>
          </td>
          <td class="<?php if( $db->sorter == 'outfile' ) echo 'sort-by'; ?>">
            <a href="index.php?r=_xToplistsShow&sort_by=outfile">Output</a>
          </td>
          <td class="<?php if( $db->sorter == 'groups' ) echo 'sort-by'; ?>">
            <a href="index.php?r=_xToplistsShow&sort_by=groups">Groups</a>
          </td>
          <td class="<?php if( $db->sorter == 'categories' ) echo 'sort-by'; ?>">
            <a href="index.php?r=_xToplistsShow&sort_by=categories">Categories</a>
          </td>
          <td style="width: 100px;">
          </td>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach( $toplists as $original )
        {
            $item = string_htmlspecialchars($original);
            include 'toplists-tr.php';
        }
        ?>
      </tbody>
    </table>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xToplistsAddShow" class="dialog" title="Add"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/build-32x32.png" class="action" title="Build">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/toplist-manage.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';
?>