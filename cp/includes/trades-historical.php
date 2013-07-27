      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Historical Stats for <?php echo $item['domain']; ?>
        </div>

        <form id="form-historical-stats">

          <div id="dialog-panel" dwidth="950px">
            <div>

              <fieldset>
                <legend>Select Date Range</legend>

                <div class="field">
                  <label>Date Range:</label>
                  <span>
                    <select name="date_range">
                      <option value="this_week">This Week</option>
                      <option value="last_week">Last Week</option>
                      <option value="this_month">This Month</option>
                      <option value="last_month">Last Month</option>
                      <option value="this_year">This Year</option>
                      <option value="last_year">Last Year</option>
                      <option value="-1 day,-7 days">Last 7 Days</option>
                      <option value="-1 day,-14 days">Last 14 Days</option>
                      <option value="-1 day,-30 days">Last 30 Days</option>
                      <option value="-1 day,-60 days">Last 60 Days</option>
                      <option value="-1 day,-90 days">Last 90 Days</option>
                      <option value="-1 day,-120 days">Last 120 Days</option>
                      <option value="-1 day,-365 days">Last 365 Days</option>
                      <option value="custom">Custom</option>
                    </select>
                  </span>
                </div>

                <div class="field field-custom d-none">
                  <label>From:</label>
                  <span>
                    <select name="from_month">
                      <?php for( $i = 1; $i <= 12; $i++ ): ?>
                      <option value="<?php printf("%02d", $i); ?>"><?php echo date('F', strtotime('2010-' . sprintf("%02d", $i) . '-01 12:00:00')) ?></option>
                      <?php endfor; ?>
                    </select>

                    <select name="from_day">
                      <?php for( $i = 1; $i <= 31; $i++ ): ?>
                      <option value="<?php printf("%02d", $i); ?>"><?php printf("%02d", $i); ?></option>
                      <?php endfor; ?>
                    </select>

                    <select name="from_year">
                      <?php echo form_options(range(2009,2050), date('Y')); ?>
                    </select>
                  </span>
                </div>

                <div class="field field-custom d-none">
                  <label>To:</label>
                  <span>
                    <select name="to_month">
                      <?php for( $i = 1; $i <= 12; $i++ ): ?>
                      <option value="<?php printf("%02d", $i); ?>"><?php echo date('F', strtotime('2010-' . sprintf("%02d", $i) . '-01 12:00:00')) ?></option>
                      <?php endfor; ?>
                    </select>

                    <select name="to_day">
                      <?php for( $i = 1; $i <= 31; $i++ ): ?>
                      <option value="<?php printf("%02d", $i); ?>"><?php printf("%02d", $i); ?></option>
                      <?php endfor; ?>
                    </select>

                    <select name="to_year">
                      <?php echo form_options(range(2009,2050), date('Y')); ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Breakdown:</label>
                  <span>
                    <select name="breakdown">
                      <option value="<?php echo STATS_HISTORY_REGEX_REPLACE_DAILY; ?>">Daily</option>
                      <option value="<?php echo STATS_HISTORY_REGEX_REPLACE_MONTHLY; ?>">Monthly</option>
                      <option value="<?php echo STATS_HISTORY_REGEX_REPLACE_YEARLY; ?>">Yearly</option>
                    </select>
                  </span>
                </div>

              </fieldset>

              <div id="usage-notice" class="message-notice ta-center">
                Select a date range above and then press the Show Stats button to view the historical stats
              </div>

              <div class="block-center d-none margin-top-10px" id="chart-stats" style="border: 1px solid #666; width: 850px; height: 400px;"></div>

              <div class="block-center d-none margin-top-10px" id="chart-prod" style="border: 1px solid #666; width: 850px; height: 400px;"></div>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="button" id="button-show-stats" value="Show Stats" />
            <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
          </div>

        </form>

      </div>


<script language="JavaScript" type="text/javascript">
$('select[name="date_range"]')
.change(function()
{
    if( $(this).val() == 'custom' )
    {
        $('div.field-custom').show();
    }
    else
    {
        $('div.field-custom').hide();
    }
});


$('select[name="breakdown"]')
.change(function()
{
    var val = $(this).val();

    switch(val)
    {
        case '<?php echo STATS_HISTORY_REGEX_REPLACE_DAILY; ?>':
            $('select[name="from_day"]').show();
            $('select[name="from_month"]').show();
            $('select[name="to_day"]').show();
            $('select[name="to_month"]').show();
            break;

        case '<?php echo STATS_HISTORY_REGEX_REPLACE_MONTHLY; ?>':
            $('select[name="from_day"]').hide();
            $('select[name="from_month"]').show();
            $('select[name="to_day"]').hide();
            $('select[name="to_month"]').show();
            break;

        case '<?php echo STATS_HISTORY_REGEX_REPLACE_YEARLY; ?>':
            $('select[name="from_day"]').hide();
            $('select[name="from_month"]').hide();
            $('select[name="to_day"]').hide();
            $('select[name="to_month"]').hide();
            break;
    }
});


var chart_stats = null;
var chart_prod = null;

$('#button-show-stats')
.click(function()
{
    if( chart_stats == null )
    {
        chart_stats = new Visifire('xap/SL.Visifire.Charts.xap', 'chart_stats', 850, 400);
    }

    if( chart_prod == null )
    {
        chart_prod = new Visifire('xap/SL.Visifire.Charts.xap', 'chart_prod', 850, 400);
    }

    var query_data = '&' + $('#form-historical-stats').formSerialize();

    $('#chart-stats, #chart-prod').show();
    $('#usage-notice').hide();

    chart_stats.setDataUri('index.php?r=_xTradesGraphDataHistoryStats&domain=<?php echo urlencode($item['domain']); ?>' + query_data);
    chart_stats.render('chart-stats');

    chart_prod.setDataUri('index.php?r=_xTradesGraphDataHistoryProd&domain=<?php echo urlencode($item['domain']); ?>' + query_data);
    chart_prod.render('chart-prod');

    $('#dialog').center(document);
});
</script>