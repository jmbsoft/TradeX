<?php
require_once 'geoip-utility.php';
include 'global-header.php';
include 'global-menu.php';

$_REQUEST['log'] = isset($_REQUEST['log']) ? $_REQUEST['log'] : 'Out';
?>

    <div class="centered-header">
      Page Stats: <?php echo $_REQUEST['log']; ?> Last 24 Hours
    </div>

    <div class="block-center fw-bold ta-center" style="width: 800px; font-size: 110%; margin-bottom: 10px;">
      <a href="index.php?r=_xStatsPagesShow" class="option<?php if( $_REQUEST['log'] == 'Out' ) echo ' option-selected'; ?>" style="width: 32%;">Out</a>
      <a href="index.php?r=_xStatsPagesShow&log=Clicks" class="option<?php if( $_REQUEST['log'] == 'Clicks' ) echo ' option-selected'; ?>" style="width: 32%;">Clicks</a>
    </div>

    <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 850px; min-width: 850px;">
      <thead>
        <tr>
          <td style="width: 30px;"></td>
          <td class="ta-center">Page</td>
          <td class="ta-center" style="width: 100px;">% of Total</td>
          <td class="ta-center" style="width: 70px;">In</td>
        </tr>
      </thead>
      <tbody>
        <?php
        list($total, $pages) = get_overall_pages(strtolower($_REQUEST['log']));
        $counter = 1;
        $color = '#ececec';
        foreach( $pages as $page => $amount ):
            $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
            $percent = $total > 0 ? format_float_to_percent($amount/$total, 1) : 0;
            $page = htmlspecialchars($page);
        ?>
        <tr bgcolor="<?php echo $color; ?>">
          <td class="ta-right" style="padding-right: 4px;">
            <?php echo $counter++; ?>
          </td>
          <td><a href="<?php echo $page; ?>" target="_blank"><?php echo $page; ?></a></td>
          <td style="padding: 0px;" class="va-middle">
            <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
              <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
            </div>
            <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
          </td>
          <td class="ta-right">
            <?php echo format_int_to_string($amount); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>

    </table>

    <div id="toolbar-vspacer"></div>
<?php
include 'global-footer.php';
?>