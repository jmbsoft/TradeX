{template file="global-header.tpl" title="Stats Login Lost Password"}

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Stats Login Lost Password</h2>
          </div>

          {if $g_invalid_confirm}

          {* MESSAGE TO DISPLAY WHEN CONFIRMATION LINK IS NOT VALID *}
          <div class="error">
            The confirmation link you followed is either invalid or expired.  Confirmation links are good for 24 hours.
          </div>

          {else}

          {* MESSAGE TO DISPLAY WHEN CONFIRMATION LINK IS VALID *}
          Your new account password is listed below!

          <br /><br />

          <b>Stats Login:</b> <a href="{$g_config.base_url}/trade-stats.php" target="_blank">{$g_config.base_url}/trade-stats.php</a><br />
          <b>Your Account Password:</b> {$g_trade.password}

          {/if}

        </td>
      </tr>
    </table>

{template file="global-footer.tpl"}