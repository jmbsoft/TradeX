      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Search Engines
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel">
            <div style="padding-top: 2px;">

              <div style="width: 800px; margin-top: 4px;" class="fw-bold block-center">
                Enter the domains of sites that you want counted as search engines, one per line.<br />
                For the best results, do not include the domain suffix (e.g. com, net, co.uk, etc)<br />
                Format: <span class="fw-normal">Domain|Search term parameter</span>
              </div>
              <textarea name="engines" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo htmlspecialchars(file_get_contents(FILE_SEARCH_ENGINES)); ?></textarea>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/search-engines.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSearchEnginesSave"/>
        </form>

      </div>