{template file="global-header.tpl" title="Stats"}

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Stats For: {$g_trade.domain}</h2>
          </div>

          <table class="item-table" cellspacing="0" cellpadding="4" align="center">
            <thead>
              <tr class="ta-center">
                <td colspan="4">Last 60 Minutes</td>
              </tr>
              <tr class="ta-center">
                <td width="100">In</td>
                <td width="100">Out</td>
                <td width="100">Clicks</td>
                <td width="100">Prod</td>
              </tr>
            </thead>
            <tbody>
              <tr class="ta-right">
                <td>{$g_stats.i_raw_60|t_tostring}</td>
                <td>{$g_stats.o_raw_60|t_tostring}</td>
                <td>{$g_stats.c_raw_60|t_tostring}</td>
                <td>{$g_stats.prod_60}%</td>
              </tr>
            </tbody>
          </table>

          <br />

          <table class="item-table" cellspacing="0" cellpadding="4" align="center">
            <thead>
              <tr class="ta-center">
                <td colspan="4">Last 24 Hours</td>
              </tr>
              <tr class="ta-center">
                <td width="100">In</td>
                <td width="100">Out</td>
                <td width="100">Clicks</td>
                <td width="100">Prod</td>
              </tr>
            </thead>
            <tbody>
              <tr class="ta-right">
                <td>{$g_stats.i_raw_24|t_tostring}</td>
                <td>{$g_stats.o_raw_24|t_tostring}</td>
                <td>{$g_stats.c_raw_24|t_tostring}</td>
                <td>{$g_stats.prod_24}%</td>
              </tr>
            </tbody>
          </table>

        </td>
      </tr>
    </table>

{template file="global-footer.tpl"}
