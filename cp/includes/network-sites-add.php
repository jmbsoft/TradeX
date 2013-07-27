      <?php

      $editing = !empty($item);
      if( !$editing )
      {
          require_once 'textdb.php';
          $db = new NetworkDB();
          $item = $db->Defaults();
      }

      ?>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          <?php echo $editing ? 'Edit' : 'Add'; ?> a Network Site
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label title="The full URL to the TradeX control panel">Control Panel URL:</label>
                <span><input name="url" value="<?php echo $item['url']; ?>" size="80" type="text"></span>
              </div>

              <div class="field">
                <label title="The usermame for the TradeX control panel">Username:</label>
                <span><input name="username" value="<?php echo $item['username']; ?>" size="25" type="text"></span>
              </div>

              <div class="field">
                <label title="The password for the TradeX control panel">Password:</label>
                <span><input name="password" value="<?php echo $item['password']; ?>" size="25" type="text"></span>
              </div>

              <div class="field">
                <label title="The owner of this TradeX installation">Owner:</label>
                <span><input name="owner" value="<?php echo $item['owner']; ?>" size="25" type="text"></span>
              </div>

              <div class="field">
                <label title="The category of this TradeX installation">Category:</label>
                <span><input name="category" value="<?php echo $item['category']; ?>" size="25" type="text"></span>
              </div>

              <div class="field">
                <label></label>
                <span>
                  <?php echo form_checkbox('flag_stats', 'Display stats from this site', $item['flag_stats']); ?>
                </span>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/network-add-site.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="<?php echo $editing ? 'Update' : 'Add'; ?> Network Site" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xNetworkSites<?php echo $editing ? 'Edit' : 'Add'; ?>"/>
          <input type="hidden" name="domain" value="<?php echo $item['domain']; ?>"/>
        </form>

      </div>
