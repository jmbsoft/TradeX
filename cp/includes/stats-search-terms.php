<?php
include 'global-header.php';
include 'global-menu.php';
?>

    <div class="centered-header">
      Search Term Stats
    </div>

    <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 600px; min-width: 600px;">
      <thead>
        <tr>
          <td style="width: 30px;"></td>
          <td class="ta-center">Search Term</td>
          <td class="ta-center" style="width: 100px;">% of Total</td>
          <td class="ta-center" style="width: 70px;">In</td>
        </tr>
      </thead>
      <tbody>
        <?php
        list($total, $terms) = get_overall_search_terms();
        $counter = 1;
        $color = '#ececec';
        foreach( $terms as $term => $amount ):
            $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
            $percent = $total > 0 ? format_float_to_percent($amount/$total, 1) : 0;
            $term = htmlspecialchars(urldecode($term));
        ?>
        <tr bgcolor="<?php echo $color; ?>">
          <td class="ta-right" style="padding-right: 4px;">
            <?php echo $counter++; ?>
          </td>
          <td><?php echo $term; ?></td>
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