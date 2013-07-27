      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Confirm Deletion of Trades
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <fieldset class="short-labels">
                <legend>Trades to be Deleted</legend>

                <div style="border: 1px dotted #afafaf; max-height: 100px; overflow: auto; padding: 4px; margin: 5px;">
                  <?php foreach( $_REQUEST['domain'] as $domain ): ?>
                  <span style="display: inline-block; width: 24%"><?php echo $domain; ?></span>
                  <?php endforeach; ?>
                </div>

              </fieldset>


              <fieldset class="short-labels">
                <legend>Blacklist Settings</legend>

                <div class="field">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_blacklist_domain', 'Blacklist domain'); ?>
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_blacklist_server_ip', 'Blacklist server IP'); ?>
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_blacklist_email', 'Blacklist e-mail address'); ?>
                  </span>
                </div>

                <div class="field">
                  <label title="The reason for blacklisting these trades">Reason:</label>
                  <span>
                    <input type="text" name="blacklist_reason" value="" size="80"/>
                  </span>
                </div>
              </fieldset>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Delete Trades" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xTradesDelete"/>
          <input type="hidden" name="domain" value="<?php echo join(',', $_REQUEST['domain']); ?>"/>
        </form>

      </div>