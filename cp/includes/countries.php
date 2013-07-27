      <?php list($weights, $countries) = load_countries(); ?>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Countries
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel">
            <div>

              <div class="d-inline-block" style="padding: 0 15px; border-right: 1px dotted #afafaf;">
                <div style="font-size: 12pt;" class="fw-bold ta-center">Good</div>
                <div style="margin: 4px 0;" class="ta-center"><b>Weight:</b> <input type="text" name="weight_good" value="<?php echo $weights[0]; ?>" size="5"/></div>
                <select name="countries_good[]" multiple="multiple" size="20" style="font-family: monospace; width: 100%;">
                  <?php foreach( $countries[0] as $country ): ?>
                  <option value="<?php echo $country[0]; ?>"><?php echo $country[0] . ' - ' . $country[1]; ?></option>
                  <?php endforeach; ?>
                </select>
                <div style="margin-top: 4px;" class="ta-right">
                  <button type="button" from="good" to="normal">To Normal &gt;</button>
                  <button type="button" from="good" to="bad">To Bad &gt;&gt;</button>
                </div>
              </div>

              <div class="d-inline-block" style="padding: 0 15px; border-right: 1px dotted #afafaf;">
                <div style="font-size: 12pt;" class="fw-bold ta-center">Normal</div>
                <div style="margin: 4px 0;" class="ta-center"><b>Weight:</b> <input type="text" name="weight_normal" value="<?php echo $weights[1]; ?>" size="5"/></div>
                <select name="countries_normal[]" multiple="multiple" size="20" style="font-family: monospace; width: 100%;">
                  <?php foreach( $countries[1] as $country ): ?>
                  <option value="<?php echo $country[0]; ?>"><?php echo $country[0] . ' - ' . $country[1]; ?></option>
                  <?php endforeach; ?>
                </select>
                <div style="margin-top: 4px;" class="ta-center">
                  <button type="button" from="normal" to="good">&lt; To Good</button>
                  <button type="button" from="normal" to="bad">To Bad &gt;</button>
                </div>
              </div>

              <div class="d-inline-block" style="padding: 0 15px;">
                <div style="font-size: 12pt;" class="fw-bold ta-center">Bad</div>
                <div style="margin: 4px 0;" class="ta-center"><b>Weight:</b> <input type="text" name="weight_bad" value="<?php echo $weights[2]; ?>" size="5"/></div>
                <select name="countries_bad[]" multiple="multiple" size="20" style="font-family: monospace; width: 100%;">
                  <?php foreach( $countries[2] as $country ): ?>
                  <option value="<?php echo $country[0]; ?>"><?php echo $country[0] . ' - ' . $country[1]; ?></option>
                  <?php endforeach; ?>
                </select>
                <div style="margin-top: 4px;">
                  <button type="button" from="bad" to="good">&lt;&lt; To Good</button>
                  <button type="button" from="bad" to="normal">&lt; To Normal</button>
                </div>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/countries.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xCountriesSave"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('#dialog form')
.bind('form-submit-notify',
function(form, options)
{
    $('select[multiple] option').removeAttr('selected');
});

$('#button-save')
.click(function()
{
    $('select[multiple] option').attr('selected', 'selected');
});

$('#dialog-panel button[from]')
.click(function()
{
    var from = $(this).attr('from');
    var to = $(this).attr('to');

    var $clones = $('select[name="countries_'+from+'[]"] option:selected').clone();
    $('select[name="countries_'+from+'[]"] option:selected').remove();
    $('select[name="countries_'+to+'[]"]').append($clones);

    sortSelect.apply($('select[name="countries_'+to+'[]"]'));
});


function sortSelect()
{
    // get the select
    var $select = $(this);
    if( $select.length > 0 )
    {
        var $options = $('option', $select);
        var options = [];

        $options
        .each(function()
        {
            options.push({val: $(this).val(), text: $(this).text()});
        });

        options
        .sort(function(a, b)
        {
            if( a.val > b.val)
                return 1;
            else if (a.val==b.val)
                return 0;
            else
                return -1;
        });

        for( var i = 0, l = options.length; i < l; i++ )
        {
             $($options[i]).val(options[i].val).text(options[i].text);
        }
    }
}
</script>