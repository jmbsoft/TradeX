      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Change Control Panel Login
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel">
            <div style="padding-top: 2px;">

              <div class="field">
                <label title="The control panel username you would like to use" class="short">Old Password:</label>
                <span><input name="old_password" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label title="The control panel username you would like to use" class="short">New Username:</label>
                <span><input name="username" value="" size="25" type="text"></span>
              </div>

              <div class="field">
                <label title="The control panel password you would like to use" class="short">New Password:</label>
                <span><input name="password" value="" size="25" type="text"></span>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/change-login.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Change Login" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xChangeLogin"/>
        </form>

      </div>
