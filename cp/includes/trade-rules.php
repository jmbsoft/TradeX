      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Trade Rules
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel">
            <div style="padding-top: 2px;">

              <div style="width: 800px; margin-top: 4px;" class="fw-bold block-center">
                Setup the rules that your trades must follow, one per line.<br />
                The trade rules will be displayed on the signup form using the $g_trade_rules template variable.
              </div>
              <textarea name="rules" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo htmlspecialchars(file_get_contents(FILE_TRADE_RULES)); ?></textarea>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/trade-rules.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xTradeRulesSave"/>
        </form>

      </div>