      <?php
      global $C;

      $C = string_htmlspecialchars($C);

      check_image_resizer();
      ?>
      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Global Software Settings
        </div>

        <form method="post" action="xhr.php" enctype="multipart/form-data" class="xhr-form">

          <div id="dialog-panel" dwidth="900px">
            <div style="padding-top: 2px;">

              <fieldset>
                <legend>Base Settings</legend>

                <div class="field">
                  <label>Site Name:</label>
                  <span><input type="text" size="70" value="<?php echo $C['site_name']; ?>" name="site_name"/></span>
                </div>

                <div class="field">
                  <label>Traffic URL:</label>
                  <span><input type="text" size="80" value="<?php echo $C['traffic_url']; ?>" name="traffic_url"/></span>
                </div>

                <div class="field">
                  <label>TradeX URL:</label>
                  <span><input type="text" size="80" value="<?php echo $C['base_url']; ?>" name="base_url"/></span>
                </div>

                <div class="field">
                  <label>Cookie Domain:</label>
                  <span><input type="text" size="40" value="<?php echo $C['cookie_domain']; ?>" name="cookie_domain"/></span>
                </div>

                <div class="field">
                  <label>Cookie Path:</label>
                  <span><input type="text" size="20" value="<?php echo $C['cookie_path']; ?>" name="cookie_path"/></span>
                </div>

                <div class="field">
                  <label>Passphrase:</label>
                  <span><input type="text" size="40" value="<?php echo $C['keyphrase']; ?>" name="keyphrase"/></span>
                </div>

                <div class="field">
                  <label>Date Format:</label>
                  <span><input name="date_format" value="<?php echo $C['date_format']; ?>" size="10" type="text"></span>
                </div>

                <div class="field">
                  <label>Time Format:</label>
                  <span><input name="time_format" value="<?php echo $C['time_format']; ?>" size="10" type="text"></span>
                </div>

                <!--
                <div class="field">
                  <label>Decimal Point:</label>
                  <span><input name="dec_point" value="<?php echo $C['dec_point']; ?>" size="2" type="text"></span>
                </div>

                <div class="field">
                  <label>Thousands Separator:</label>
                  <span><input name="thousands_sep" value="<?php echo $C['thousands_sep']; ?>" size="2" type="text"></span>
                </div>
                -->

                <input name="dec_point" value="." size="2" type="hidden">
                <input name="thousands_sep" value="," size="2" type="hidden">

              </fieldset>

              <fieldset>
                <legend>E-mail Settings</legend>

                <div class="field">
                  <label>E-mail Address:</label>
                  <span><input type="text" size="50" value="<?php echo $C['email_address']; ?>" name="email_address"/></span>
                </div>

                <div class="field">
                  <label>E-mail Name:</label>
                  <span><input type="text" size="50" value="<?php echo $C['email_name']; ?>" name="email_name"/></span>
                </div>

                <div class="field">
                  <label>E-mail Method:</label>
                  <span>
                    <select name="email_method">
                      <?php
                      require_once 'mailer.php';
                      $mailers = array(MAILER_METHOD_PHP, MAILER_METHOD_SENDMAIL, MAILER_METHOD_SMTP);
                      echo form_options($mailers, $C['email_method']);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field" style="display: none;">
                  <label>Sendmail Path:</label>
                  <span><input type="text" class="mailer-field mailer-sendmail" size="50" value="<?php echo $C['sendmail_path']; ?>" name="sendmail_path"/></span>
                </div>

                <div class="field" style="display: none;">
                  <label>SMTP Hostname:</label>
                  <span><input type="text" class="mailer-field mailer-smtp" size="50" value="<?php echo $C['smtp_hostname']; ?>" name="smtp_hostname"/></span>
                </div>

                <div class="field" style="display: none;">
                  <label>SMTP Port:</label>
                  <span><input type="text" class="mailer-field mailer-smtp" size="10" value="<?php echo $C['smtp_port']; ?>" name="smtp_port"/></span>
                </div>

                <div class="field" style="display: none;">
                  <label></label>
                  <span>
                    <?php echo form_checkbox('flag_smtp_ssl', 'Use SSL for SMTP server connection', $C['flag_smtp_ssl'], 'class="mailer-field mailer-smtp"'); ?>
                  </span>
                </div>

                <div class="field" style="display: none;">
                  <label>SMTP Username:</label>
                  <span><input type="text" class="mailer-field mailer-smtp" size="50" value="<?php echo $C['smtp_username']; ?>" name="smtp_username"/></span>
                </div>

                <div class="field" style="display: none;">
                  <label>SMTP Password:</label>
                  <span><input type="password" class="mailer-field mailer-smtp" size="50" value="<?php echo $C['smtp_password']; ?>" name="smtp_password"/></span>
                </div>

              </fieldset>



              <fieldset>
                <legend>CAPTCHA Settings</legend>

                <div class="field">
                  <label>Code Length:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['captcha_min']; ?>" name="captcha_min"/>
                    to
                    <input type="text" size="5" value="<?php echo $C['captcha_max']; ?>" name="captcha_max"/>
                    characters
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_captcha_words', 'Use words file for CAPTCHA strings', $C['flag_captcha_words']); ?></span>
                </div>

              </fieldset>



              <fieldset>
                <legend>Registration &amp; Account Settings</legend>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_accept_new_trades', 'Accepting new trades', $C['flag_accept_new_trades']); ?></span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_captcha_register', 'Use CAPTCHA on registration form', $C['flag_captcha_register']); ?></span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_allow_select_category', 'Allow users to select a category for their site on the registration form', $C['flag_allow_select_category']); ?></span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_allow_login', 'Allow users to login with a password to view their stats', $C['flag_allow_login']); ?></span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_register_email_user', 'Send confirmation e-mail to user upon successful registration', $C['flag_register_email_user']); ?></span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_register_email_admin', 'E-mail administrator when new trades are registered', $C['flag_register_email_admin']); ?></span>
                </div>

                <div class="field">
                  <label>Site Name Length:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['site_name_min']; ?>" name="site_name_min"/>
                    to
                    <input type="text" size="5" value="<?php echo $C['site_name_max']; ?>" name="site_name_max"/>
                    characters
                  </span>
                </div>

                <div class="field">
                  <label>Site Description Length:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['site_description_min']; ?>" name="site_description_min"/>
                    to
                    <input type="text" size="5" value="<?php echo $C['site_description_max']; ?>" name="site_description_max"/>
                    characters
                  </span>
                </div>

                <div class="field">
                  <label>Required Fields:</label>
                  <span>
                    <?php echo form_checkbox('flag_req_email', 'E-mail Address', $C['flag_req_email']); ?><br />
                    <?php echo form_checkbox('flag_req_site_name', 'Site Name', $C['flag_req_site_name']); ?><br />
                    <?php echo form_checkbox('flag_req_site_description', 'Site Description', $C['flag_req_site_description']); ?><br />
                    <?php echo form_checkbox('flag_req_icq', 'ICQ Number', $C['flag_req_icq']); ?><br />
                    <?php echo form_checkbox('flag_req_nickname', 'Name/Nickname', $C['flag_req_nickname']); ?><br />
                    <?php echo form_checkbox('flag_req_banner', 'Banner URL', $C['flag_req_banner']); ?>
                  </span>
                </div>

              </fieldset>


              <fieldset>
                <legend>Thumbnail Grabber Settings</legend>

                <!--
                <div class="field">
                  <label>Interval:</label>
                  <span><input type="text" size="3" value="<?php echo $C['thumb_grab_interval']; ?>" name="thumb_grab_interval"/> hours</span>
                </div>
                -->

                <div class="field">
                  <label># to Grab:</label>
                  <span><input type="text" size="3" value="<?php echo $C['thumb_grab_amount']; ?>" name="thumb_grab_amount"/></span>
                </div>

                <div class="field">
                  <label>Trigger Strings:</label>
                  <span><input type="text" size="80" value="<?php echo $C['thumb_trigger_strings']; ?>" name="thumb_trigger_strings"/></span>
                </div>

                <div class="field">
                  <label>Minimum Dimensions:</label>
                  <span>
                    <input type="text" size="3" value="<?php echo $C['thumb_width_min']; ?>" name="thumb_width_min"/>
                    x
                    <input type="text" size="3" value="<?php echo $C['thumb_height_min']; ?>" name="thumb_height_min"/>
                  </span>
                </div>

                <div class="field">
                  <label>Maximum Dimensions:</label>
                  <span>
                    <input type="text" size="3" value="<?php echo $C['thumb_width_max']; ?>" name="thumb_width_max"/>
                    x
                    <input type="text" size="3" value="<?php echo $C['thumb_height_max']; ?>" name="thumb_height_max"/>
                  </span>
                </div>

                <div class="field">
                  <label>mogrify Path:</label>
                  <span>
                    <input type="text" size="50" value="<?php echo $C['magick_mogrify_path']; ?>" name="magick_mogrify_path"/>
                    <img src="images/test-16x16.png" title="Test This Path" class="c-click" id="test-mogrify" />
                  </span>
                </div>

                <div class="field">
                  <label>mogrify Options:</label>
                  <span><input type="text" size="80" value="<?php echo $C['magick_mogrify_options']; ?>" name="magick_mogrify_options"/></span>
                </div>

                <div class="field">
                  <label>Resize Dimensions:</label>
                  <span>
                    <input type="text" size="3" value="<?php echo $C['thumb_resize_width']; ?>" name="thumb_resize_width"/>
                    x
                    <input type="text" size="3" value="<?php echo $C['thumb_resize_height']; ?>" name="thumb_resize_height"/>
                  </span>
                </div>

              </fieldset>


              <fieldset>
                <legend>Points Modifiers - Bonuses</legend>

                <div class="settings-header">Good Productivity</div>

                <div class="field">
                  <label>Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['bonus_prod_low']; ?>" name="bonus_prod_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['bonus_prod_high']; ?>" name="bonus_prod_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_bonus_prod']; ?>" name="mod_bonus_prod"/> %</span>
                </div>


                <div class="settings-header">Good Percentage of Uniques</div>

                <div class="field">
                  <label>Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['bonus_unique_low']; ?>" name="bonus_unique_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['bonus_unique_high']; ?>" name="bonus_unique_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_bonus_unique']; ?>" name="mod_bonus_unique"/> %</span>
                </div>


                <div class="settings-header">Low Return Percentage</div>

                <div class="field">
                  <label>Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['bonus_return_low']; ?>" name="bonus_return_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['bonus_return_high']; ?>" name="bonus_return_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_bonus_return']; ?>" name="mod_bonus_return"/> %</span>
                </div>

              </fieldset>



              <fieldset>
                <legend>Points Modifiers - Penalties</legend>

                <div class="settings-header">Too High or Too Low Proxy Percentage</div>

                <div class="field">
                  <label>Allowed Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['penalty_proxy_low']; ?>" name="penalty_proxy_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['penalty_proxy_high']; ?>" name="penalty_proxy_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_penalty_proxy']; ?>" name="mod_penalty_proxy"/> %</span>
                </div>


                <div class="settings-header">Too High or Too Low Unique Percentage</div>

                <div class="field">
                  <label>Allowed Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['penalty_unique_low']; ?>" name="penalty_unique_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['penalty_unique_high']; ?>" name="penalty_unique_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_penalty_unique']; ?>" name="mod_penalty_unique"/> %</span>
                </div>


                <div class="settings-header">Too High Return Percentage</div>

                <div class="field">
                  <label>Allowed Range:</label>
                  <span>
                    <input type="text" size="5" value="<?php echo $C['penalty_return_low']; ?>" name="penalty_return_low"/> % to
                    <input type="text" size="5" value="<?php echo $C['penalty_return_high']; ?>" name="penalty_return_high"/> %
                  </span>
                </div>

                <div class="field">
                  <label>Modifier:</label>
                  <span><input type="text" size="5" value="<?php echo $C['mod_penalty_return']; ?>" name="mod_penalty_return"/> %</span>
                </div>

              </fieldset>



              <fieldset>
                <legend>Traffic Distribution</legend>

                <div class="field">
                  <label>Forces:</label>
                  <span><input type="text" size="5" value="<?php echo $C['distrib_forces']; ?>" name="distrib_forces"/> %</span>
                </div>

                <div class="field">
                  <label>Main:</label>
                  <span><input type="text" size="5" value="<?php echo $C['distrib_main']; ?>" name="distrib_main"/> %</span>
                </div>

                <div class="field">
                  <label>Primary Bonus:</label>
                  <span><input type="text" size="5" value="<?php echo $C['distrib_primary']; ?>" name="distrib_primary"/> %</span>
                </div>

                <div class="field">
                  <label>Secondary Bonus:</label>
                  <span><input type="text" size="5" value="<?php echo $C['distrib_secondary']; ?>" name="distrib_secondary"/> %</span>
                </div>

              </fieldset>



              <fieldset>
                <legend>Other Settings</legend>

                <div class="field">
                  <label>Trades Satisfied URL:</label>
                  <span><input type="text" size="80" value="<?php echo $C['trades_satisfied_url']; ?>" name="trades_satisfied_url"/></span>
                </div>

                <div class="field">
                  <label>Count Clicks:</label>
                  <span><input type="text" size="5" value="<?php echo $C['count_clicks']; ?>" name="count_clicks"/></span>
                </div>

                <div class="field">
                  <label>Fast Click:</label>
                  <span><input type="text" size="5" value="<?php echo $C['fast_click']; ?>" name="fast_click"/> seconds</span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_filter_no_image', 'Filter users that do not load the image.php script', $C['flag_filter_no_image']); ?></span>
                </div>

                <div class="field">
                  <label>Autostop Interval:</label>
                  <span><input type="text" size="5" value="<?php echo $C['autostop_interval']; ?>" name="autostop_interval"/> hours</span>
                </div>

                <div class="field">
                  <label></label>
                  <span><?php echo form_checkbox('flag_reactivate_autostopped', 'Automatically re-activate trades that have been autostopped', $C['flag_reactivate_autostopped']); ?></span>
                </div>

                <div class="field">
                  <label>Toplist Build Interval:</label>
                  <span><input type="text" size="5" value="<?php echo $C['toplist_rebuild_interval']; ?>" name="toplist_rebuild_interval"/> minutes</span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/global-settings.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save Settings" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xGlobalSettingsSave"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('#dialog-panel')
.bind('dialog-visible', function()
{
    // [Skeleton]
    $('select[name="email_method"]')
    .change(function()
    {
        var mailer = $(this).val().toLowerCase();
        $('.mailer-field').parents('div.field').hide();
        $('.mailer-' + mailer).parents('div.field').show();
    })
    .change();
});


$('#test-mogrify')
.click(function()
{
    var $icon = $(this);
    $icon.attr('src', 'images/activity-16x16.gif');

    $.ajax({
        data: 'r=_xMogrifyTest&magick_mogrify_path=' + escape($('input[name="magick_mogrify_path"]').val()),
        complete: function()
        {
            $icon.attr('src', 'images/test-16x16.png');
        }
    });
});
</script>
