      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          E-mail Trades
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <fieldset class="short-labels">
                <legend>Trades to be E-mailed</legend>

                <div style="border: 1px dotted #afafaf; max-height: 100px; overflow: auto; padding: 4px; margin: 5px;">
                  <?php foreach( explode(',', $_REQUEST['domain']) as $domain ): ?>
                  <span style="display: inline-block; width: 24%"><?php echo $domain; ?></span>
                  <?php endforeach; ?>
                </div>

              </fieldset>


              <fieldset class="short-labels">
                <legend>E-mail Message</legend>

                <div class="field">
                  <label title="The subject for the e-mail message">Subject:</label>
                  <span>
                    <input type="text" name="<?php echo MAILER_KEY_SUBJECT; ?>" value="" size="80"/>
                  </span>
                </div>

                <div class="field">
                  <label title="The body of the e-mail message">Body:</label>
                  <span>
                    <textarea name="<?php echo MAILER_KEY_BODY; ?>" style="width: 660px; height: 300px;"></textarea>
                  </span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="E-mail Trades" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xTradesEmail"/>
          <input type="hidden" name="domain" value="<?php echo $_REQUEST['domain']; ?>"/>
        </form>

      </div>