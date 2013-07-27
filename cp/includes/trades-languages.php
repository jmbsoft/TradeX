      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Languages for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="900px">
          <div>

            <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 850px; min-width: 850px;">
              <thead>
                <tr>
                  <td style="width: 30px;"></td>
                  <td class="ta-center">Language</td>
                  <td class="ta-center" style="width: 150px;">% of Total</td>
                  <td class="ta-center" style="width: 75px;">In</td>
                </tr>
              </thead>
              <tbody>
                <?php
                list($total, $languages) = get_trade_languages($item['domain']);
                $counter = 1;
                $color = '#ececec';
                foreach( $languages as $lang => $amount ):
                    $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                    $percent = $total > 0 ? format_float_to_percent($amount/$total, 1) : 0;
                ?>
                <tr bgcolor="<?php echo $color; ?>">
                  <td class="ta-right" style="padding-right: 4px;">
                    <?php echo $counter++; ?>
                  </td>
                  <td>
                     <?php echo $lang; ?>
                  </td>
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

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
        </div>

      </div>