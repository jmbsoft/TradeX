      <?php

      $editing = !empty($item);
      if( !$editing )
      {
          require_once 'textdb.php';
          $db = new ToplistsDB();
          $item = $db->Defaults();
      }

      $trade_sources = array(
          'all' => 'All Trades',
          'outlist_main' => 'Main Outlist',
          'outlist_primary' => 'Primary Bonus Outlist',
          'outlist_secondary' => 'Secondary Bonus Outlist',
          'outlist_forces' => 'Forces'
      );

      $sorters = array('i_raw_60' => '60 Minutes - In Raw',
                       'i_uniq_60' => '60 Minutes - In Unique',
                       'i_uniq_pct_60' => '60 Minutes - In Unique %',
                       'i_proxy_60' => '60 Minutes - In Proxy',
                       'i_ctry_g_60' => '60 Minutes - In Good Country',
                       'i_ctry_n_60' => '60 Minutes - In Normal Country',
                       'i_ctry_b_60' => '60 Minutes - In Bad Country',
                       'i_ctry_g_pct_60' => '60 Minutes - In Good Country %',
                       'i_ctry_n_pct_60' => '60 Minutes - In Normal Country %',
                       'i_ctry_b_pct_60' => '60 Minutes - In Bad Country %',
                       'o_raw_60' => '60 Minutes - Out Raw',
                       'o_proxy_60' => '60 Minutes - Out Proxy',
                       'o_ctry_g_60' => '60 Minutes - Out Good Country',
                       'o_ctry_n_60' => '60 Minutes - Out Normal Country',
                       'o_ctry_b_60' => '60 Minutes - Out Bad Country',
                       'o_ctry_g_pct_60' => '60 Minutes - Out Good Country %',
                       'o_ctry_n_pct_60' => '60 Minutes - Out Normal Country %',
                       'o_ctry_b_pct_60' => '60 Minutes - Out Bad Country %',
                       'c_raw_60' => '60 Minutes - Clicks Raw',
                       'c_proxy_60' => '60 Minutes - Clicks Proxy',
                       'c_trades_60' => '60 Minutes - Clicks To Trades',
                       'c_ctry_g_60' => '60 Minutes - Clicks Good Country',
                       'c_ctry_n_60' => '60 Minutes - Clicks Normal Country',
                       'c_ctry_b_60' => '60 Minutes - Clicks Bad Country',
                       'c_ctry_g_pct_60' => '60 Minutes - Clicks Good Country %',
                       'c_ctry_n_pct_60' => '60 Minutes - Clicks Normal Country %',
                       'c_ctry_b_pct_60' => '60 Minutes - Clicks Bad Country %',
                       'prod_60' => '60 Minutes - Productivity',
                       't_prod_60' => '60 Minutes - Trade Productivity',
                       'return_60' => '60 Minutes - Return',
                       'skim_60' => '60 Minutes - Skim',
                       'i_raw_24' => '24 Hours - In Raw',
                       'i_uniq_24' => '24 Hours - In Unique',
                       'i_uniq_pct_24' => '24 Hours - In Unique %',
                       'i_proxy_24' => '24 Hours - In Proxy',
                       'i_ctry_g_24' => '24 Hours - In Good Country',
                       'i_ctry_n_24' => '24 Hours - In Normal Country',
                       'i_ctry_b_24' => '24 Hours - In Bad Country',
                       'i_ctry_g_pct_24' => '24 Hours - In Good Country %',
                       'i_ctry_n_pct_24' => '24 Hours - In Normal Country %',
                       'i_ctry_b_pct_24' => '24 Hours - In Bad Country %',
                       'o_raw_24' => '24 Hours - Out Raw',
                       'o_proxy_24' => '24 Hours - Out Proxy',
                       'o_ctry_g_24' => '24 Hours - Out Good Country',
                       'o_ctry_n_24' => '24 Hours - Out Normal Country',
                       'o_ctry_b_24' => '24 Hours - Out Bad Country',
                       'o_ctry_g_pct_24' => '24 Hours - Out Good Country %',
                       'o_ctry_n_pct_24' => '24 Hours - Out Normal Country %',
                       'o_ctry_b_pct_24' => '24 Hours - Out Bad Country %',
                       'c_raw_24' => '24 Hours - Clicks Raw',
                       'c_proxy_24' => '24 Hours - Clicks Proxy',
                       'c_trades_24' => '24 Hours - Clicks To Trades',
                       'c_ctry_g_24' => '24 Hours - Clicks Good Country',
                       'c_ctry_n_24' => '24 Hours - Clicks Normal Country',
                       'c_ctry_b_24' => '24 Hours - Clicks Bad Country',
                       'c_ctry_g_pct_24' => '24 Hours - Clicks Good Country %',
                       'c_ctry_n_pct_24' => '24 Hours - Clicks Normal Country %',
                       'c_ctry_b_pct_24' => '24 Hours - Clicks Bad Country %',
                       'prod_24' => '24 Hours - Productivity',
                       't_prod_24' => '24 Hours - Trade Productivity',
                       'return_24' => '24 Hours - Return',
                       'skim_24' => '24 Hours - Skim',
                       'domain' => 'Domain',
                       'site_name' => 'Site Name');

      ?>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          <?php echo $editing ? 'Edit' : 'Add'; ?> a Toplist
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="860px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label title="Select the source for the toplist template" class="short">Source:</label>
                <span>
                  <select name="source">
                    <?php
                    $sources = array(TOPLIST_SOURCE_FILE, TOPLIST_SOURCE_TEMPLATE);
                    echo form_options($sources, $item['source']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field" id="field_template">
                <label class="short">Template:</label>
                <span>
                  <textarea name="template" style="width: 700px; height: 200px;" wrap="off"><?php echo $item['template']; ?></textarea>
                </span>
              </div>

              <div class="field" id="field_infile">
                <label class="short">Input File:</label>
                <span><input name="infile" value="<?php echo $item['infile']; ?>" size="80" type="text"></span>
              </div>

              <div class="field">
                <label class="short">Output File:</label>
                <span><input name="outfile" value="<?php echo $item['outfile']; ?>" size="80" type="text"></span>
              </div>

              <div class="field">
                <label class="short">Groups:</label>
                <span>
                  <select name="groups[]" multiple="multiple" size="6" style="min-width: 150px;">
                    <?php
                    $groups = array_map('trim', file(FILE_GROUPS));
                    echo form_options_multi($groups, $item['groups']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short">Categories:</label>
                <span>
                  <select name="categories[]" multiple="multiple" size="6" style="min-width: 150px;">
                    <?php
                    $categories = array_map('trim', file(FILE_CATEGORIES));
                    echo form_options_multi($categories, $item['categories']);
                    ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short"></label>
                <span>
                  <?php echo form_checkbox('flag_thumbs_only', 'Only trades that have thumbnails', $item['flag_thumbs_only']); ?>
                </span>
              </div>

              <div class="field">
                <label class="short">Trade Priority:</label>
                <div class="d-inline-block">
                  <?php for( $i = 0; $i < count($item['trade_sources']); $i++ ): ?>
                  <div style="margin: 2px 0px;">
                    <select name="trade_sources[]">
                      <?php echo form_options_hash($trade_sources, $item['trade_sources'][$i]); ?>
                    </select>

                    <img src="images/add-16x16.png" border="0" width="16" height="16" class="c-click" style="margin-left: 8px;">
                    <img src="images/remove-16x16.png" border="0" width="16" height="16" class="c-click" style="margin-left: 8px;">
                  </div>
                  <?php endfor; ?>
                </div>
              </div>

              <div class="field">
                <label class="short">Sort By:</label>
                <span>
                  <select name="sort_by">
                    <?php echo form_options_hash($sorters, $item['sort_by']); ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short">Requirement:</label>
                <div class="d-inline-block">
                <?php for( $i = 0; $i < count($item['req_field']); $i++ ): ?>
                  <div style="margin: 2px 0px;">
                    <select name="req_field[]">
                      <option value=""></option>
                      <?php echo form_options_hash($sorters, $item['req_field'][$i]); ?>
                    </select>

                    <select name="req_operator[]">
                      <option value=""></option>
                      <?php
                      $operators = array('>', '>=', '<', '<=');
                      echo form_options($operators, $original['req_operator'][$i]);
                      ?>
                    </select>

                    <input type="text" name="req_value[]" size="10" value="<?php echo $item['req_value'][$i]; ?>"/>

                    <img src="images/add-16x16.png" border="0" width="16" height="16" class="c-click" style="margin-left: 8px;">
                    <img src="images/remove-16x16.png" border="0" width="16" height="16" class="c-click" style="margin-left: 8px;">
                  </div>
                <?php endfor; ?>
                </div>
              </div>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/toplist-add.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="<?php echo $editing ? 'Update' : 'Add'; ?> Toplist" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xToplists<?php echo $editing ? 'Edit' : 'Add'; ?>"/>
          <input type="hidden" name="toplist_id" value="<?php echo $item['toplist_id']; ?>"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('select[name="source"]')
.change(function()
{
    switch($(this).val())
    {
        case '<?php echo TOPLIST_SOURCE_FILE; ?>':
            $('#field_template').hide();
            $('#field_infile').show();
            break;

        case '<?php echo TOPLIST_SOURCE_TEMPLATE; ?>':
            $('#field_template').show();
            $('#field_infile').hide();
            break;
    }

    $('#dialog').center(document);
})
.change();


$('img[src$=add-16x16.png]')
.click(function()
{
    $clone = $(this.parentNode)
    .clone(true)
    .insertAfter(this.parentNode);

    $('input[type="text"]', $clone)
    .val('');

    $('select option:first-child', $clone)
    .attr('selected', 'selected');
});

$('img[src$=remove-16x16.png]')
.click(function()
{
   if( $(this.parentNode).siblings().length > 0 )
   {
       $(this.parentNode).remove();
   }
});
</script>