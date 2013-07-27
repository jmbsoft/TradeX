{template file="global-header.tpl" title="Registration Complete"}

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Registration Complete</h2>
          </div>

          {if $g_invalid_confirm}

          {* MESSAGE TO DISPLAY WHEN CONFIRMATION LINK IS NOT VALID *}
          <div class="error">
            The confirmation link you followed is either invalid or expired.  Confirmation links are good for 24 hours.
          </div>

          {else}

          {* MESSAGE TO DISPLAY WHEN CONFIRMATION LINK IS VALID *}
          Your trade account has been successfully setup and you can start sending traffic at any time!

          <br /><br />

          <b>Send Traffic To:</b> <a href="" target="_blank">{$g_config.traffic_url}</a><br />
          {if $g_config.flag_allow_login}
          <b>Stats Login:</b> <a href="{$g_config.base_url}/trade-stats.php" target="_blank">{$g_config.base_url}/trade-stats.php</a><br />
          <b>Your Account Password:</b> {$g_trade.password}
          {/if}

          {/if}

        </td>
      </tr>
    </table>

{template file="global-footer.tpl"}