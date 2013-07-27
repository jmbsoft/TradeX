      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Country Stats for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="850px">
          <div>

            <div class="block-center fw-bold ta-center" style="width: 800px; font-size: 110%; margin-bottom: 10px;">
              <span class="option option-selected" style="width: 32%;">In</span>
              <span class="option" style="width: 32%;">Out</span>
              <span class="option" style="width: 32%;">Clicks</span>
            </div>

            <div class="block-center" id="ammap" style="width: 800px; height: 400px; border: 1px solid #666; background: transparent url(images/activity-32x32.gif) no-repeat 50% 50%;"></div>

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
        </div>

      </div>

<script language="JavaScript" type="text/javascript">
var ammap = null;

function reloadData(stat, src)
{
    $(ammap).css({visibility: 'hidden'});
    ammap.reloadData('index.php?r=_xTradesCountriesData&stat=' + stat + '&domain=<?php echo $item['domain']; ?>');
    $(src).addClass('option-selected').siblings().removeClass('option-selected');
}

function amMapCompleted(map_id)
{
    ammap = document.getElementById('ammap-object');
    $(ammap).css({visibility: 'visible'});
}

function amProcessCompleted(map_id, process_name)
{
    if( process_name == 'reloadData' )
    {
        $(ammap).css({visibility: 'visible'});
    }
}

var so = new SWFObject("swf/ammap.swf", "ammap-object", "800", "400", "8", "#ffffff");
so.addParam('map_id', 'ammap-object');
so.addVariable("path", "swf/");
so.addVariable("data_file", escape("index.php?r=_xTradesCountriesData&stat=In&domain=<?php echo $item['domain']; ?>"));
so.addVariable("settings_file", escape("assets/ammap-settings.xml"));
so.write("ammap");

$('span.option')
.click(function()
{
    reloadData($(this).text(), this);
});
</script>