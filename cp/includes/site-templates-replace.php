      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Site Templates Search and Replace
        </div>

        <form method="post" action="xhr.php" enctype="multipart/form-data" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <fieldset class="short-labels">
                <legend>Search and Replace Settings</legend>

                <div class="field">
                  <label>Templates:</label>
                  <span>
                    <?php $templates = string_htmlspecialchars(dir_read_files(DIR_TEMPLATES, REGEX_SITE_TEMPLATES, true)); ?>
                    <select name="templates[]" id="templates" multiple="multiple" size="10">
                      <?php echo form_options($templates); ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Search For:</label>
                  <span><textarea name="search" id="search" rows="5" cols="88"></textarea></span>
                </div>

                <div class="field">
                  <label>Replace With:</label>
                  <span><textarea name="replace" id="replace" rows="5" cols="88"></textarea></span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Apply Changes" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSiteTemplatesReplace"/>
        </form>

      </div>