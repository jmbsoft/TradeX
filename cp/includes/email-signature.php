      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          E-mail Greeting &amp; Signature
        </div>

        <form method="post" action="xhr.php" enctype="multipart/form-data" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <fieldset class="short-labels">
                <legend>Greeting &amp; Signature Settings</legend>

                <div class="field">
                  <label>Greeting:</label>
                  <span><textarea name="greeting" id="greeting" rows="10" cols="88"><?php echo htmlspecialchars(file_get_contents(DIR_TEMPLATES . '/email-global-greeting.tpl')); ?></textarea></span>
                </div>

                <div class="field">
                  <label>Signature:</label>
                  <span><textarea name="signature" id="signature" rows="10" cols="88"><?php echo htmlspecialchars(file_get_contents(DIR_TEMPLATES . '/email-global-signature.tpl')); ?></textarea></span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/templates-greeting-signature.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save Changes" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xEmailSignatureSave"/>
        </form>

      </div>