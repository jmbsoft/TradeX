<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'dirdb.php';

$db = new SkimSchemeBaseDB();
$schemes = $db->RetrieveAll()
?>

<script type="text/javascript" src="js/skim-schemes.js"></script>

    <div class="centered-header">
      Skim Schemes: <span id="num-items"><?php echo count($schemes); ?></span> Configured
    </div>

    <form action="xhr.php" method="post" class="xhr-form" id="skim-schemes-form">
      <table align="center" width="90%" cellspacing="0" class="item-table">
        <thead>
          <tr>
            <td class="ta-center" style="width: 25px;">
              <input type="checkbox" class="check-all"/>
            </td>
            <td>
              Scheme
            </td>
            <td class="ta-center">
              1
            </td>
            <td class="ta-center">
              2
            </td>
            <td class="ta-center">
              3
            </td>
            <td class="ta-center">
              4
            </td>
            <td class="ta-center">
              5
            </td>
            <td class="ta-center">
              6
            </td>
            <td class="ta-center">
              7
            </td>
            <td class="ta-center">
              Cycle
            </td>
            <td class="ta-center">
              Dynamic
            </td>
            <td style="width: 100px;">
            </td>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach( $schemes as $original )
          {
              $item = string_htmlspecialchars($original);
              include 'skim-schemes-tr.php';
          }
          ?>
        </tbody>
      </table>
      <input type="hidden" name="r" value="_xSkimSchemesSave"/>
    </form>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xSkimSchemesAddShow" class="dialog" title="Add"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/save-32x32.png" class="action" title="Save">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/skim-schemes.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';
?>