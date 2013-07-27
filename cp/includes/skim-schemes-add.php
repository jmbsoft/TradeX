      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Add a Skim Scheme
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label class="short">Scheme Name:</label>
                <span><input name="scheme" value="" size="40" type="text"></span>
              </div>

              <div class="field">
                <label class="short" style="margin-top: 20px;">% To Content:</label>
                <span>

                  <table class="plain" cellpadding="0" cellspacing="0" style="padding: 0px;">
                    <tr>
                    <?php for( $i = 1; $i <= 7; $i++ ): ?>
                      <td class="ta-center"<?php echo ($i == 1 ? ' style="padding-left: 0px;"' : ''); ?>>
                        <?php echo $i; ?>
                      </td>
                    <?php endfor; ?>
                      <td></td>
                    <?php for( $i = 1; $i <= 3; $i++ ): ?>
                      <td class="ta-center">
                        <?php echo $i; ?>
                      </td>
                    <?php endfor; ?>
                    </tr>
                    <tr>
                    <?php for( $i = 1; $i <= 7; $i++ ): ?>
                      <td class="ta-center"<?php echo ($i == 1 ? ' style="padding-left: 0px;"' : ''); ?>>
                        <input type="text" name="click_<?php echo $i; ?>" size="3"/>
                      </td>
                    <?php endfor; ?>
                      <td>
                        <div style="width: 2px; background-color: #333; height: 22px;"></div>
                      </td>
                    <?php for( $i = 1; $i <= 3; $i++ ): ?>
                      <td class="ta-center">
                        <input type="text" name="cycle_<?php echo $i; ?>" size="3"/>
                      </td>
                    <?php endfor; ?>
                    </tr>
                  </table>

                </span>
              </div>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Add Skim Scheme" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSkimSchemesAdd"/>
        </form>

      </div>
