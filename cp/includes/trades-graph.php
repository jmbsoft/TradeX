      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Graph: Hourly Stats for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="900px">
          <div>

            <div class="block-center fw-bold ta-center" style="width: 850px; font-size: 110%; margin-bottom: 10px;">
              <span fnc="_xTradesGraphDataHourly" class="option option-selected" style="width: 32%;">In, Out &amp; Clicks</span>
              <span fnc="_xTradesGraphDataProdReturn" class="option" style="width: 32%;">Productivity &amp; Return</span>
            </div>

            <div class="block-center" id="vchart" style="width: 850px; height: 400px; border: 1px solid #666; background: transparent url(images/activity-32x32.gif) no-repeat 50% 50%;"></div>

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
        </div>

      </div>

<script language="JavaScript" type="text/javascript">
var vChart = new Visifire('xap/SL.Visifire.Charts.xap', 'Visifire', 850, 400);
vChart.preLoad = function(args)
{
    args[0].Rendered = function(sender, eventArgs)
    {
        $('#Visifire').css({visibility: 'visible'});
    };
};
vChart.setDataUri('index.php?r=_xTradesGraphDataHourly&domain=<?php echo urlencode($item['domain']); ?>');
vChart.render('vchart');

$('span.option')
.click(function()
{
    $('#Visifire').css({visibility: 'hidden'});
    $(this).addClass('option-selected').siblings().removeClass('option-selected');
    vChart.setDataUri('index.php?r=' + $(this).attr('fnc') + '&domain=<?php echo urlencode($item['domain']); ?>');
    vChart.render('vchart');
});
</script>