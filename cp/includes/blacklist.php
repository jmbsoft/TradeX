      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Blacklist
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <fieldset>
                <legend>E-mail Addresses</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> E-mail address (or hostname)|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_EMAIL)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_EMAIL); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>Domains</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> Domain (or URL)|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_DOMAIN)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_DOMAIN); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>User IP</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> IP address|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_USER_IP)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_USER_IP); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>Server IP</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> IP address|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_SERVER_IP)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_SERVER_IP); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>Nameservers</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> Nameserver|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_DNS)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_DNS); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>Words</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> Word|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_WORD)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_WORD); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>Page Content</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> Content|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_CONTENT)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_CONTENT); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

              <fieldset>
                <legend>HTTP Headers</legend>

                <div style="width: 800px; margin-top: 4px;" class="block-center">
                  <b>Format:</b> HTTP Header|Reason for blacklist
                  <span style="float: right;" class="fw-bold">One Per Line!</span>
                </div>
                <?php $items = htmlspecialchars(file_get_contents(FILE_BLACKLIST_HEADER)); ?>
                <textarea name="bl[<?php echo basename(FILE_BLACKLIST_HEADER); ?>]" style="width: 800px; height: 200px;" wrap="off" class="d-block block-center"><?php echo $items; ?></textarea>
              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/blacklist.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xBlacklistSave"/>
        </form>

      </div>