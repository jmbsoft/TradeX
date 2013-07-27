{template file="global-header.tpl" title="Register Your Site"}

    <script language="JavaScript" type="text/javascript">
    $(function()
    {
        // Reload CAPTCHA image
        $('.captcha-reload')
        .click(function()
        {
            $(this)
            .siblings('.captcha-image')
            .attr('src', 'code.php?' + Math.random());
        });
    });
    </script>

    <form method="post" action="register.php">

      <table align="center" width="900" cellspacing="0" cellpadding="4">
        <tr>
          <td colspan="2">
            <div class="ta-center">
              <h2>Register Your Site</h2>
            </div>

            <div class="fw-bold">Trade Rules</div>
            <div>{$g_trade_rules|rawhtml|nl2br}</div>

            <br />

            {if $g_trade_defaults.start_raws || $g_trade_defaults.start_clicks || $g_trade_defaults.start_prod}
            <div class="fw-bold">To Start Trading:</div>
            <ul>
              {if $g_trade_defaults.start_raws}<li>Send at least {$g_trade_defaults.start_raws} incoming click(s)</li>{/if}
              {if $g_trade_defaults.start_clicks}<li>Referred surfers generate at least {$g_trade_defaults.start_clicks} click(s)</li>{/if}
              {if $g_trade_defaults.start_prod}<li>Productivity of at least {$g_trade_defaults.start_prod}%</li>{/if}
            </ul>
            {/if}

            <div class="fw-bold"><span class="required">*</span> - Required field</div>
            <br />
          </td>
        </tr>
        {if $g_errors}
        <tr>
          <td colspan="2">
            <div class="error">
            Registration failed, please fix the following items:

            <ul>
            {foreach var=$error from=$g_errors}
              <li>{$error}</li>
            {/foreach}
            </ul>
            </div>
            <br />
          </td>
        </tr>
        {/if}
        <tr>
          <td class="fw-bold ta-right va-top"><span class="required">*</span> URL to Send Traffic</td>
          <td><input type="text" size="80" name="return_url" value="{$g_request.return_url}" /></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_email}<span class="required">*</span> {/if}E-mail Address</td>
          <td><input type="text" size="40" name="email" value="{$g_request.email}" /></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_site_name}<span class="required">*</span> {/if}Site Name</td>
          <td>
            <input type="text" size="60" name="site_name" value="{$g_request.site_name}" />
            <div class="fs-9pt">Your site name must be between {$g_config.site_name_min} and {$g_config.site_name_max} characters</div>
          </td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_site_description}<span class="required">*</span> {/if}Site Description</td>
          <td>
            <input type="text" size="80" name="site_description" value="{$g_request.site_description}" />
            <div class="fs-9pt">Your site description must be between {$g_config.site_description_min} and {$g_config.site_description_max} characters</div>
          </td>
        </tr>

        {* Display category selection if allowed by configuration and there is at least one category setup *}
        {if $g_config.flag_allow_select_category && count($g_categories)}
        <tr>
          <td class="fw-bold ta-right va-top"><span class="required">*</span> Category</td>
          <td>
            <select name="category">
              {options from=$g_categories selected=$g_request.category}
            </select>
          </td>
        </tr>
        {/if}

        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_icq}<span class="required">*</span> {/if}ICQ Number</td>
          <td><input type="text" size="15" name="icq" value="{$g_request.icq}" /></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_nickname}<span class="required">*</span> {/if}Name/Nickname</td>
          <td><input type="text" size="40" name="nickname" value="{$g_request.nickname}" /></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">{if $g_config.flag_req_banner}<span class="required">*</span> {/if}Banner URL</td>
          <td><input type="text" size="80" name="banner" value="{$g_request.banner}" /></td>
        </tr>

        {* DISPLAY CAPTCHA IF THAT OPTION IS ENABLED *}
        {if $g_config.flag_captcha_register}
        <tr>
          <td class="fw-bold ta-right va-top"><span class="required">*</span> Verification</td>
          <td>
            <img src="code.php" class="captcha-image" />
            <img src="images/reload-22x22.png" class="captcha-reload" />
            <br />
            <input type="text" name="captcha" size="20" />
          </td>
        </tr>
        {/if}

        <tr>
          <td class="ta-center" colspan="2">
            <input type="hidden" name="r" value="register" />
            <input type="submit" value="Submit" />
          </td>
        </tr>

      </table>

    </form>

{template file="global-footer.tpl"}