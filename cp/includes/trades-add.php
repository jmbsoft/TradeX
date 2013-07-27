      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          <?php echo $dialog_title; ?>
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <?php if( $bulk ): ?>
              <fieldset class="short-labels">
                <legend>Trades</legend>

                <div class="field">
                  <label>Separator:</label>
                  <span>
                    <select name="separator">
                      <option value="|">Pipe (|)</option>
                      <option value=",">Comma (,)</option>
                      <option value=";">Semicolon (;)</option>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Fields:</label>
                  <span>
                  <?php for( $i = 0; $i < 10; $i++ ): ?>
                    <select name="fields[]" style="margin-bottom: 4px;">
                      <option value="">None</option>
                      <option value="return_url">Return URL</option>
                      <option value="status">Status</option>
                      <option value="site_name">Site Name</option>
                      <option value="site_description">Site Description</option>
                      <option value="color">Color</option>
                      <option value="email">E-mail</option>
                      <option value="password">Password</option>
                      <option value="nickname">Nick</option>
                      <option value="icq">ICQ #</option>
                      <option value="grabber_url">Grabber URL</option>
                      <option value="trigger_strings">Trigger Strings</option>
                      <option value="banner">Banner</option>
                      <option value="notes">Notes</option>
                    </select>
                  <?php endfor; ?>
                  </span>
                </div>

                <div class="block-center" style="width: 800px;">
                  <div class="fw-bold">Enter the trade data, one trade per line</div>
                  <div>Format: <span id="data-format"></span></div>
                  <textarea name="data" rows="8" class="d-block" style="width: 100%;"></textarea>
                </div>


              </fieldset>
              <?php endif; ?>


              <fieldset>
                <legend>Base Settings</legend>

                <?php if( !$default && !$bulk ): ?>
                <div class="field">
                  <label>Return URL:</label>
                  <span><input type="text" name="return_url" value="<?php echo $item['return_url']; ?>" size="80"></span>
                </div>
                <?php endif; ?>

                <div class="field">
                  <label>Status:</label>
                  <span>
                    <select name="status">
                      <?php
                      $statuses = $default ? array(STATUS_NEW,STATUS_ACTIVE) : array(STATUS_UNCONFIRMED,STATUS_NEW,STATUS_ACTIVE,STATUS_AUTOSTOPPED,STATUS_DISABLED);
                      echo form_options($statuses, $item['status']);
                      ?>
                    </select>
                  </span>
                  <label class="very-short">Color:</label>
                  <span><input type="text" name="color" value="<?php echo $item['color']; ?>" size="8"></span>
                  <label style="width: 2em;"></label>
                  <span>
                    <?php echo form_checkbox('flag_toplist', 'Display in toplist', $item['flag_toplist']); ?>
                  </span>
                  <label style="width: 2em;"></label>
                  <span>
                    <?php echo form_checkbox('flag_grabber', 'Grab thumbs', $item['flag_grabber']); ?>
                  </span>
                </div>

                <?php if( $default || $bulk  ): ?>
                <div class="field">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_confirm', 'Require confirmation by e-mail', $item['flag_confirm']); ?>
                  </span>
                </div>
                <?php endif; ?>

                <?php if( !$default && !$bulk  ): ?>
                <div class="field">
                  <label>E-mail Address:</label>
                  <span>
                    <input type="text" name="email" value="<?php echo $item['email']; ?>" size="40">
                    <?php
                    if( !$editing )
                    {
                        echo form_checkbox('flag_confirm', 'Require confirmation', $item['flag_confirm']);
                    }
                    ?>
                  </span>
                </div>

                <div class="field">
                  <label>Password:</label>
                  <span><input type="text" name="password" value="" size="15"><?php if( !empty($item['password']) ): ?> <img src="images/key-16x16.png" title="Password Set" style="vertical-align: middle;"/><?php endif; ?></span>
                  <label class="very-short">Nick:</label>
                  <span><input type="text" name="nickname" value="<?php echo $item['nickname']; ?>" size="15"></span>
                  <label class="very-short">ICQ #:</label>
                  <span><input type="text" name="icq" value="<?php echo $item['icq']; ?>" size="15"></span>
                </div>

                <div class="field">
                  <label>Site Name:</label>
                  <span><input type="text" name="site_name" value="<?php echo $item['site_name']; ?>" size="50"></span>
                </div>

                <div class="field">
                  <label>Site Description:</label>
                  <span><input type="text" name="site_description" value="<?php echo $item['site_description']; ?>" size="90"></span>
                </div>
                <?php endif; ?>

                <div class="field">
                  <label>Groups:</label>
                  <span>
                    <select name="groups[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $groups = array_map('trim', file(FILE_GROUPS));
                      echo form_options_multi($groups, $item['groups']);
                      ?>
                    </select>
                  </span>
                  <label>Categories:</label>
                  <span>
                    <select name="categories[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $categories = array_map('trim', file(FILE_CATEGORIES));
                      echo form_options_multi($categories, $item['categories']);
                      ?>
                    </select>
                  </span>
                </div>

                <?php if( !$default && !$bulk  ): ?>
                <div class="field">
                  <label>Grabber URL:</label>
                  <span><input type="text" name="grabber_url" value="<?php echo $item['grabber_url']; ?>" size="90"></span>
                </div>

                <div class="field">
                  <label>Trigger Strings:</label>
                  <span><input type="text" name="trigger_strings" value="<?php echo $item['trigger_strings']; ?>" size="60"></span>
                </div>

                <div class="field">
                  <label>Custom Thumbs:</label>
                  <span><textarea name="custom_thumbs" rows="5" cols="60"><?php echo $item['custom_thumbs']; ?></textarea></span>
                </div>

                <div class="field">
                  <label>Banner:</label>
                  <span><input type="text" name="banner" value="<?php echo $item['banner']; ?>" size="90"></span>
                </div>

                <div class="field">
                  <label>Notes:</label>
                  <span><textarea name="notes" rows="5" cols="60"><?php echo $item['notes']; ?></textarea></span>
                </div>
                <?php endif; ?>

              </fieldset>


              <fieldset>
                <legend>Forces</legend>

                <div class="field">
                  <label>Instant:</label>
                  <span>
                    <input type="text" name="force_instant" value="<?php echo $item['force_instant']; ?>" size="5">
                  </span>
                  <label style="width: 2em;"></label>
                  <span>
                    <?php echo form_checkbox('flag_force_instant_high', 'High priority', $item['flag_force_instant_high']); ?>
                  </span>
                </div>

                <div class="field">
                  <label>Hourly:</label>
                  <span>
                    <input type="text" name="force_hourly" value="<?php echo $item['force_hourly']; ?>" size="5">
                    until
                    <input type="text" name="force_hourly_end" value="<?php echo $item['force_hourly_end']; ?>" size="20" class="datetimepicker">
                  </span>
                </div>

              </fieldset>


              <fieldset>
                <legend>Trade Settings</legend>

                <div class="field">
                  <label>Skim Scheme:</label>
                  <span>
                    <select name="skim_scheme" style="width: 80px;">
                      <?php
                      $schemes = dir_read_files(DIR_SKIM_SCHEMES_BASE);
                      echo form_options($schemes, $item['skim_scheme']);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Weight:</label>
                  <span>
                    <input type="text" name="trade_weight" value="<?php echo $item['trade_weight']; ?>" size="5"> %
                  </span>
                </div>

                <div class="field">
                  <label>Push To:</label>
                  <span>
                    <input type="text" name="push_to" value="<?php echo $item['push_to']; ?>" size="5">
                  </span>
                  <label class="short">Push Weight:</label>
                  <span>
                    <input type="text" name="push_weight" value="<?php echo $item['push_weight']; ?>" size="5"> %
                  </span>
                </div>

                <div class="field">
                  <label>Start Raws:</label>
                  <span><input type="text" name="start_raws" value="<?php echo $item['start_raws']; ?>" size="5"></span>

                  <label class="short">Start Clicks:</label>
                  <span><input type="text" name="start_clicks" value="<?php echo $item['start_clicks']; ?>" size="5"><span style="color: #fff;"> %</span></span>

                  <label class="short">Start Prod:</label>
                  <span><input type="text" name="start_prod" value="<?php echo $item['start_prod']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label>Min Raws:</label>
                  <span><input type="text" name="min_raws" value="<?php echo $item['min_raws']; ?>" size="5"></span>

                  <label class="short">Min Clicks:</label>
                  <span><input type="text" name="min_clicks" value="<?php echo $item['min_clicks']; ?>" size="5"><span style="color: #fff;"> %</span></span>

                  <label class="short">Min Prod:</label>
                  <span><input type="text" name="min_prod" value="<?php echo $item['min_prod']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label>Max Out:</label>
                  <span><input type="text" name="max_out" value="<?php echo $item['max_out']; ?>" size="5"></span>

                  <label class="short">Hourly Cap:</label>
                  <span><input type="text" name="hourly_cap" value="<?php echo $item['hourly_cap']; ?>" size="5"> %</span>

                  <label class="short">Daily Cap:</label>
                  <span><input type="text" name="daily_cap" value="<?php echo $item['daily_cap']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label>Exclude Trades:</label>
                  <span>
                    <select name="excludes[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $trades = dir_read_files(DIR_TRADES);
                      echo form_options_multi($trades, $item['excludes']);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_external', 'Use external info', $item['flag_external']); ?>
                  </span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/trade-add.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="<?php echo $button_text; ?>" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="<?php echo $function_name; ?>"/>
          <?php if( $editing ): ?>
          <input type="hidden" name="domain" value="<?php echo $item['domain']; ?>"/>
          <?php endif; ?>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('input.datetimepicker')
.calendar();

$('#flag_confirm')
.click(confirmClicked);

confirmClicked.apply($('#flag_confirm'));

function confirmClicked()
{
    if( $(this).is(':checked') )
    {
        $('select[name="status"] option[value="Unconfirmed"]').attr('selected', 'selected');
    }
}


<?php if( $bulk ): ?>
$('select[name="fields[]"], select[name="separator"]')
.change(function()
{
    var separator = $('select[name="separator"]').val();
    var fields = new Array();

    $('select[name="fields[]"]')
    .each(function(i, el)
    {
        var text = $('option:selected', el).text();
        if( text != 'None' )
        {
            fields.push(text);
        }
    });

    $('#data-format').text(fields.join(separator));
});

$('select[name="fields[]"]:first-child option[value="return_url"]').attr('selected', 'selected');
$('select[name="fields[]"]:first-child').change();
<?php endif; ?>
</script>

