{template file="global-header.tpl" title="Stats Login"}

    <form action="trade-stats.php" method="post">

      <table align="center" width="900" cellspacing="0" cellpadding="4">
        <tr>
          <td colspan="2">
            <div class="ta-center">
              <h2>Stats Login</h2>
            </div>
          </td>
        </tr>
        {if $g_errors}
        <tr>
          <td colspan="2">
            <div class="error">
            Login failed, please fix the following items:

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
          <td class="fw-bold ta-right va-top" width="40%">Domain</td>
          <td>
            <input type="text" size="25" name="domain" value="{$g_request.domain}" />
          </td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">Password</td>
          <td>
            <input type="password" size="25" name="password" value="" />
            <a href="trade-stats.php?r=forgot" class="fs-9pt">Lost Password?</a>
          </td>
        </tr>
        <tr>
          <td class="ta-center" colspan="2">
            <input type="hidden" name="r" value="stats" />
            <input type="submit" value="View Stats" />
          </td>
        </tr>
      </table>

    </form>

{template file="global-footer.tpl"}