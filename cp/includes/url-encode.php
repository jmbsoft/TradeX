      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          URL Encoder/Decoder
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label class="short">Format:</label>
                <span>
                  <select name="format">
                    <option value="urlencode">URL Encode</option>
                    <option value="urldecode">URL Decode</option>
                    <option value="base64_encode">Base 64 Encode</option>
                    <option value="base64_decode">Base 64 Decode</option>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short">URLs:</label>
                <span>
                  <textarea name="urls" style="width: 700px; height: 200px;" wrap="off"><?php echo isset($_REQUEST['urls']) ? $_REQUEST['urls'] : ''; ?></textarea>
                </span>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/url-encode-decode.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Encode/Decode" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xUrlEncode"/>
        </form>

      </div>
