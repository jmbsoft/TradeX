{template file="global-header.tpl" title="Registration Complete"}

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Registration Complete</h2>
          </div>

          {if $g_trade.status == STATUS_UNCONFIRMED}

          {* MESSAGE TO DISPLAY WHEN REQUIRING THAT NEW REGISTRATIONS BE CONFIRMED BY E-MAIL *}
          Your account information has been recorded and a confirmation e-mail has been sent to <b>{$g_trade.email}</b>.
          This e-mail message contains a link that you must visit to confirm your account.  Please visit that link
          within 24 hours.

          {else}

          {* MESSAGE TO DISPLAY WHEN NOT USING CONFIRMATION E-MAIL *}
          Your trade account has been successfully setup and you can start sending traffic at any time!

          <br /><br />

          <b>Send Traffic To:</b> <a href="{$g_config.traffic_url}" target="_blank">{$g_config.traffic_url}</a><br />
          {if $g_config.flag_allow_login}
          <b>Stats Login:</b> <a href="{$g_config.base_url}/trade-stats.php" target="_blank">{$g_config.base_url}/trade-stats.php</a><br />
          <b>Your Account Password:</b> {$g_trade.password}
          {/if}

          {/if}

        </td>
      </tr>
    </table>

{template file="global-footer.tpl"}