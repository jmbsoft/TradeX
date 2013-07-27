      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Bulk Edit Network Sites
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="500px">
            <div style="padding-top: 2px;">

              <div class="message-notice">
              Select the fields you wish to update by clicking on the field name (shift-click on checkboxes).
              Only fields marked in <span class="updating">red text</span> will be updated, others will be
              left unchanged.
              </div>

              <div class="field">
                <label class="short updatefield" title="The usermame for the TradeX control panel">
                  <input type="hidden" name="flag_update[username]" value="0" />
                  Username:
                </label>
                <span><input name="username" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label class="short updatefield" title="The password for the TradeX control panel">
                  <input type="hidden" name="flag_update[password]" value="0" />
                  Password:
                </label>
                <span><input name="password" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label class="short updatefield" title="The owner of this TradeX installation">
                  <input type="hidden" name="flag_update[owner]" value="0" />
                  Owner:
                </label>
                <span><input name="owner" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label class="short updatefield" title="The category of this TradeX installation">
                  <input type="hidden" name="flag_update[category]" value="0" />
                  Category:
                </label>
                <span><input name="category" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label class="short"></label>
                <span class="updatefield">
                  <?php echo form_checkbox('flag_stats', 'Display stats from this site'); ?>
                  <input type="hidden" name="flag_update[flag_stats]" value="0" />
                </span>
              </div>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Update Network Sites" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xNetworkSitesBulkEdit"/>
          <input type="hidden" name="domain" value="<?php echo $_REQUEST['domain']; ?>"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('.updatefield').updatefield();
</script>