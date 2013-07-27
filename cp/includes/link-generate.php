      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Link Generator
        </div>

        <form id="link-generator-form" method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <fieldset>
                <legend>Link Settings</legend>

                <div class="field">
                  <label>Link Type:</label>
                  <span>
                    <select name="type">
                      <option value="percent">% to Content</option>
                      <option value="scheme">Skim Scheme</option>
                      <option value="trade">To Specific Trade</option>
                    </select>
                  </span>
                </div>

                <div class="field hideable percent">
                  <label>% to Content:</label>
                  <span>
                    <input type="text" name="percent" value="70" size="5"/> %
                  </span>
                </div>

                <div class="field hideable percent">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_fc', 'First click always to content'); ?>
                  </span>
                </div>

                <div class="field hideable scheme">
                  <label>Skim Scheme:</label>
                  <span>
                    <select name="skim_scheme">
                      <?php
                      $schemes = dir_read_files(DIR_SKIM_SCHEMES);
                      echo form_options($schemes);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field hideable percent scheme">
                  <label>Content URL:</label>
                  <span>
                    <input type="text" name="content_url" value="" size="80"/>
                  </span>
                </div>

                <div class="field hideable percent scheme">
                  <label>URL Encoding:</label>
                  <span>
                    <select name="encoding">
                      <option value="urlencode">urlencode</option>
                      <option value="base64_encode">base64_encode</option>
                    </select>
                  </span>
                </div>

                <div class="field hideable percent scheme">
                  <label>Limit to Category:</label>
                  <span>
                    <select name="category">
                      <option value="">-- ALL --</option>
                      <?php
                      $categories = array_map('trim', file(FILE_CATEGORIES));
                      echo form_options($categories);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field hideable percent scheme">
                  <label>Limit to Group:</label>
                  <span>
                    <select name="group">
                      <option value="">-- ALL --</option>
                      <?php
                      $groups = array_map('trim', file(FILE_GROUPS));
                      echo form_options($groups);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field hideable trade">
                  <label>Trade:</label>
                  <span>
                    <select name="trade">
                      <?php
                      $trades = dir_read_files(DIR_TRADES);
                      echo form_options($trades);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Link Identifier:</label>
                  <span>
                    <input type="text" name="link" value="" size="20"/> <span class="fsize-8pt">(leave blank for none)</span>
                  </span>
                </div>

              </fieldset>


              <fieldset id="generated-link" class="d-none">
                <legend>Generated Link</legend>
                <input type="text" style="width: 99%;">
              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/link-generator.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Generate" />
            <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xLinkGenerate"/>
        </form>

      </div>


<script language="JavaScript" type="text/javascript">
$(function()
{
    $('select[name="type"]')
    .change(function()
    {
        var type = $(this).val();

        $('.hideable').hide();
        $('.' + type).show();
    }).
    change();


    $('#link-generator-form')
    .bind('form-success', function(e, data)
    {
        $('#generated-link input[type="text"]').val(data.url);
        $('#generated-link').show();
    });
});
</script>