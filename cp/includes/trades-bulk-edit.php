      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Bulk Edit Trades
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <div class="message-notice ta-center">
                Select the fields you wish to update by clicking on the field name (shift-click on checkboxes).<br />
                Only fields marked in <span class="updating">red text</span> will be updated, others will be
                left unchanged.
              </div>

              <fieldset>
                <legend>Base Settings</legend>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[status]" value="0" />
                    Status:
                  </label>
                  <span>
                    <select name="status">
                      <?php
                      $statuses = array(STATUS_UNCONFIRMED,STATUS_NEW,STATUS_ACTIVE,STATUS_AUTOSTOPPED,STATUS_DISABLED);
                      echo form_options($statuses, STATUS_ACTIVE);
                      ?>
                    </select>
                  </span>
                  <label class="very-short updatefield">
                    <input type="hidden" name="flag_update[color]" value="0" />
                    Color:
                  </label>
                  <span>
                    <input type="text" name="color" value="" size="8">
                  </span>
                  <label style="width: 2em;"></label>
                  <span class="updatefield">
                    <?php echo form_checkbox('flag_toplist', 'Display in toplist'); ?>
                    <input type="hidden" name="flag_update[flag_toplist]" value="0" />
                  </span>
                  <label style="width: 2em;"></label>
                  <span class="updatefield">
                    <?php echo form_checkbox('flag_grabber', 'Grab thumbs'); ?>
                    <input type="hidden" name="flag_update[flag_grabber]" value="0" />
                  </span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[email]" value="0" />
                    E-mail Address:
                  </label>
                  <span>
                    <input type="text" name="email" value="" size="40">
                  </span>
                </div>


                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[password]" value="0" />
                    Password:
                  </label>
                  <span><input type="text" name="password" value="" size="15"></span>
                  <label class="very-short updatefield">
                    <input type="hidden" name="flag_update[nickname]" value="0" />
                    Nick:
                  </label>
                  <span><input type="text" name="nickname" value="" size="15"></span>
                  <label class="very-short updatefield">
                    <input type="hidden" name="flag_update[icq]" value="0" />
                    ICQ #:
                  </label>
                  <span><input type="text" name="icq" value="" size="15"></span>
                </div>


                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[groups]" value="0" />
                    Groups:
                  </label>
                  <span>
                    <select name="groups[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $groups = array_map('trim', file(FILE_GROUPS));
                      echo form_options_multi($groups);
                      ?>
                    </select>
                  </span>
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[categories]" value="0" />
                    Categories:
                  </label>
                  <span>
                    <select name="categories[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $categories = array_map('trim', file(FILE_CATEGORIES));
                      echo form_options_multi($categories);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[trigger_strings]" value="0" />
                    Trigger Strings:
                  </label>
                  <span><input type="text" name="trigger_strings" value="" size="60"></span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[notes]" value="0" />
                    Notes:
                  </label>
                  <span><textarea name="notes" rows="5" cols="60"></textarea></span>
                </div>

              </fieldset>


              <fieldset>
                <legend>Forces</legend>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[force_instant]" value="0" />
                    Instant:
                  </label>
                  <span>
                    <input type="text" name="force_instant" value="<?php echo $item['force_instant']; ?>" size="5">
                  </span>
                  <label style="width: 2em;"></label>
                  <span class="updatefield">
                    <?php echo form_checkbox('flag_force_instant_high', 'High priority', $item['flag_force_instant_high']); ?>
                    <input type="hidden" name="flag_update[flag_force_instant_high]" value="0" />
                  </span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[force_hourly]" value="0" />
                    Hourly:
                  </label>
                  <span>
                    <input type="text" name="force_hourly" value="<?php echo $item['force_hourly']; ?>" size="5">
                    <b class="updatefield">
                      <input type="hidden" name="flag_update[force_hourly_end]" value="0" />
                      until
                    </b>
                    <input type="text" name="force_hourly_end" value="<?php echo $item['force_hourly_end']; ?>" size="20" class="datetimepicker">
                  </span>
                </div>

              </fieldset>

              <fieldset>
                <legend>Trade Settings</legend>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[skim_scheme]" value="0" />
                    Skim Scheme:
                  </label>
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
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[trade_weight]" value="0" />
                    Weight:
                  </label>
                  <span>
                    <input type="text" name="trade_weight" value="<?php echo $item['trade_ratio']; ?>" size="5"> %
                  </span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[push_to]" value="0" />
                    Push To:
                  </label>
                  <span>
                    <input type="text" name="push_to" value="<?php echo $item['push_to']; ?>" size="5">
                  </span>
                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[push_weight]" value="0" />
                    Push Weight:
                  </label>
                  <span>
                    <input type="text" name="push_weight" value="<?php echo $item['push_weight']; ?>" size="5"> %
                  </span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[start_raws]" value="0" />
                    Start Raws:
                  </label>
                  <span><input type="text" name="start_raws" value="<?php echo $item['start_raws']; ?>" size="5"></span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[start_clicks]" value="0" />
                    Start Clicks:
                  </label>
                  <span><input type="text" name="start_clicks" value="<?php echo $item['start_clicks']; ?>" size="5"><span style="color: #fff;"> %</span></span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[start_prod]" value="0" />
                    Start Prod:
                  </label>
                  <span><input type="text" name="start_prod" value="<?php echo $item['start_prod']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[min_raws]" value="0" />
                    Min Raws:
                  </label>
                  <span><input type="text" name="min_raws" value="<?php echo $item['min_raws']; ?>" size="5"></span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[min_clicks]" value="0" />
                    Min Clicks:
                  </label>
                  <span><input type="text" name="min_clicks" value="<?php echo $item['min_clicks']; ?>" size="5"><span style="color: #fff;"> %</span></span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[min_prod]" value="0" />
                    Min Prod:</label>
                  <span><input type="text" name="min_prod" value="<?php echo $item['min_prod']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[max_out]" value="0" />
                    Max Out:
                  </label>
                  <span><input type="text" name="max_out" value="<?php echo $item['max_out']; ?>" size="5"></span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[hourly_cap]" value="0" />
                    Hourly Cap:
                  </label>
                  <span><input type="text" name="hourly_cap" value="<?php echo $item['hourly_cap']; ?>" size="5"> %</span>

                  <label class="short updatefield">
                    <input type="hidden" name="flag_update[daily_cap]" value="0" />
                    Daily Cap:
                  </label>
                  <span><input type="text" name="daily_cap" value="<?php echo $item['daily_cap']; ?>" size="5"> %</span>
                </div>

                <div class="field">
                  <label class="updatefield">
                    <input type="hidden" name="flag_update[excludes]" value="0" />
                    Exclude Trades:
                  </label>
                  <span>
                    <select name="excludes[]" multiple="multiple" style="min-width: 150px; height: 6.3em;">
                      <?php
                      $trades = dir_read_files(DIR_TRADES);
                      echo form_options_multi($trades);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span class="updatefield">
                    <?php echo form_checkbox('flag_external', 'Use external info'); ?>
                    <input type="hidden" name="flag_update[flag_external]" value="0" />
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
            <input type="submit" id="button-save" value="Update Trades" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xTradesBulkEdit"/>
          <input type="hidden" name="domain" value="<?php echo $_REQUEST['domain']; ?>"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('.updatefield')
.updatefield();

$('input.datetimepicker')
.calendar()

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

