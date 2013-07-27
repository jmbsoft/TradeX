      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Edit System Trade: <?php echo $item['domain']; ?>
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label>Color:</label>
                <span><input type="text" name="color" value="<?php echo $item['color']; ?>" size="10"></span>
              </div>

              <div class="field">
                <label>Notes:</label>
                <span><textarea name="notes" rows="5" cols="60"><?php echo $item['notes']; ?></textarea></span>
              </div>

              <div class="field">
                <label>Skim Scheme:</label>
                <span>
                  <select name="skim_scheme" style="width: 80px;">
                    <?php
                    $schemes = dir_read_files(DIR_SKIM_SCHEMES_BASE);
                    echo form_options($schemes, $item['skim_scheme']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label>Exclude Trades:</label>
                <span>
                  <select name="excludes[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                    <?php
                    $trades = dir_read_files(DIR_TRADES);
                    echo form_options_multi($trades, $item['excludes']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label>Send Method:</label>
                <span>
                  <select name="send_method">
                    <?php
                    $send_methods = array('Normal', 'Only to trades', 'To content if specified, otherwise to Traffic URL', 'Only to Traffic URL');
                    echo form_options_hash($send_methods, $item['send_method']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label>Traffic URL:</label>
                <span><input type="text" name="traffic_url" value="<?php echo $item['traffic_url']; ?>" size="80"></span>
              </div>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save Settings" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSystemTradesEdit"/>
          <input type="hidden" name="domain" value="<?php echo $item['domain']; ?>"/>
        </form>

      </div>

