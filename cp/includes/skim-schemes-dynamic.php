      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Dynamic Skim Scheme for '<?php echo $item['scheme']; ?>'
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <?php
              require_once 'textdb.php';
              $db = new SkimSchemesDynamicDB();
              $db->db_file = DIR_SKIM_SCHEMES_DYNAMIC . '/' . $item['scheme'];

              foreach( $db->RetrieveAll() as $index => $scheme ):
              ?>
              <fieldset class="p-relative" style="background-color: #fff;">
                <legend>Rule #<span class="rule-number"><?php echo $index + 1; ?></span> </legend>

                <img src="images/delete-22x22.png" class="c-click" style="float: right;" title="Delete"/>
                <input type="hidden" name="rule[]" value=""/>

                <div class="field">
                  <label class="short" style="margin-top: 20px;">% To Content:</label>
                  <span>

                    <table class="plain" cellpadding="0" cellspacing="0" style="padding: 0px;">
                      <tr>
                      <?php for( $i = 1; $i <= 7; $i++ ): ?>
                        <td class="ta-center"<?php echo ($i == 1 ? ' style="padding-left: 0px;"' : ''); ?>>
                          <?php echo $i; ?>
                        </td>
                      <?php endfor; ?>
                        <td></td>
                        <td colspan="3" class="ta-center">
                          Cycle
                        </td>
                      </tr>
                      <tr>
                      <?php for( $i = 1; $i <= 7; $i++ ): ?>
                        <td class="ta-center"<?php echo ($i == 1 ? ' style="padding-left: 0px;"' : ''); ?>>
                          <input type="text" name="click[<?php echo $i; ?>][]" value="<?php echo $scheme['click'][$i]; ?>" size="3"/>
                        </td>
                      <?php endfor; ?>
                        <td>
                          <div style="width: 2px; background-color: #333; height: 22px;"></div>
                        </td>
                      <?php for( $i = 1; $i <= 3; $i++ ): ?>
                        <td class="ta-center">
                          <input type="text" name="cycle_<?php echo $i; ?>[]" value="<?php echo $scheme['cycle_' . $i]; ?>" size="3"/>
                        </td>
                      <?php endfor; ?>
                      </tr>
                    </table>

                  </span>
                </div>

                <div class="field">
                  <label class="short">Start:</label>
                  <span>
                    <select name="start_day[]">
                      <?php
                      $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Weekdays', 'Weekends');
                      echo form_options_hash($days, $scheme['start_day']);
                      ?>
                    </select>
                    at
                    <select name="start_hour[]">
                      <?php
                      $hours = array(); foreach( range(0,23) as $i ) $hours[] = sprintf("%02d", $i);
                      echo form_options($hours, $scheme['start_hour']);
                      ?>
                    </select> :
                    <select name="start_minute[]">
                      <?php
                      $minutes = array(); foreach( range(0,59) as $i ) $minutes[] = sprintf("%02d", $i);
                      echo form_options($minutes, $scheme['start_minute']);
                      ?>
                    </select>
                  </span>

                  <label class="short">End:</label>
                  <span>
                    <select name="end_day[]">
                      <?php
                      echo form_options_hash($days, $scheme['end_day']);
                      ?>
                    </select>
                    at
                    <select name="end_hour[]">
                      <?php
                      echo form_options($hours, $scheme['end_hour']);
                      ?>
                    </select> :
                    <select name="end_minute[]">
                      <?php
                      echo form_options($minutes, $scheme['end_minute']);
                      ?>
                    </select>
                  </span>
                </div>

              </fieldset>
              <?php endforeach; ?>

            </div>
          </div>



          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save" />
            <input type="button" value="Add Rule" style="margin-left: 10px;" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="scheme" value="<?php echo $item['scheme']; ?>"/>
          <input type="hidden" name="r" value="_xSkimSchemesDynamicEdit"/>
        </form>

      </div>


<script language="JavaScript" type="text/javascript">
$('#dialog-panel > div')
.sortable({start: function(evt, ui) { ui.item.css({cursor: 'move'}); },
           stop: function(evt, ui)
                 {
                     ui.item.css({cursor: 'default'});
                     renumberRules();
                 }});

$("#dialog-panel > div")
.disableSelection();


$('input[value="Add Rule"]')
.click(function()
{
    var max_rule = 0;
    $('span.rule-number').each(function() { max_rule = parseInt($(this).text()); });

    var $clone = $('#dialog-panel > div > fieldset:first-child')
                 .clone(true)
                 .appendTo('#dialog-panel > div');

    $('input[type="text"]', $clone).val('');
    $('select option:first-child', $clone).attr('selected', true);

    renumberRules();

    $('#dialog')
    .center(document);
});


$('img[src="images/delete-22x22.png"]')
.click(function()
{
    if( $('#dialog-panel fieldset').length == 1 )
    {
        alert('There must be at least one rule!');
        return;
    }

    $(this)
    .parents('fieldset')
    .remove();

    renumberRules();

    $('#dialog')
    .center(document);
});


function renumberRules()
{
    var rule_number = 0;
    $('span.rule-number').each(function() { $(this).text(++rule_number); });
}
</script>