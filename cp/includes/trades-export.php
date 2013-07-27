      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Export Trades
        </div>

        <form method="post" action="xhr.php">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label class="short">Format:</label>
                <span>
                  <input type="text" name="format" size="90" value="{$return_url}|{$site_name}|{$site_description}|{$email}|{$icq}|{$nickname}"/>

                  <?php
                  $exportables = array('{$domain}' => "Trade's domain name",
                                       '{$return_url}' => "Trade's return URL",
                                       '{$site_name}' => "Trade's site name",
                                       '{$site_description}' => "Trade's site description",
                                       '{$email}' => "Webmaster's E-mail",
                                       '{$icq}' => "Webmaster's ICQ #",
                                       '{$nickname}' => "Webmaster's name/nickname");
                  ?>
                  <table class="exportables-legend" cellspacing="0">
                    <thead>
                      <tr>
                        <td>
                          Template
                        </td>
                        <td>
                          Description
                        </td>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $exportables as $var => $description ): ?>
                      <tr>
                        <td align="right">
                          <?php echo $var; ?>
                        </td>
                        <td>
                          <?php echo $description; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </span>
              </div>


              <div id="export-data" class="block-center" style="width: 800px; display: none;">
                <div class="fw-bold">Export Data:</div>
                <textarea rows="10" style="width: 100%;" wrap="off"></textarea>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/trade-export.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Export" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xTradesExport"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">

$('#dialog-content form')
.ajaxForm({beforeSubmit: function()
           {
               dialogButtonDisable();
           },
           success: function(data)
           {
               $('#export-data textarea').val(data.lines);
               $('#export-data').show();
               $('#dialog').center(document);
           }
          });

</script>