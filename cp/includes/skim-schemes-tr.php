        <tr id="item-<?php echo $item['scheme']; ?>"<?php if( $item['scheme'] == SKIM_SCHEME_DEFAULT ) echo ' class="default"'; ?>>
          <td class="ta-center">
            <?php if( $item['scheme'] != SKIM_SCHEME_DEFAULT ): ?>
            <input type="checkbox" value="<?php echo $item['scheme']; ?>"/>
            <?php endif; ?>
          </td>
          <td>
            <?php echo $item['scheme']; ?>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_1]" value="<?php echo $item['click_1']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_2]" value="<?php echo $item['click_2']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_3]" value="<?php echo $item['click_3']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_4]" value="<?php echo $item['click_4']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_5]" value="<?php echo $item['click_5']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_6]" value="<?php echo $item['click_6']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][click_7]" value="<?php echo $item['click_7']; ?>"/>
          </td>
          <td class="ta-center">
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][cycle_1]" value="<?php echo $item['cycle_1']; ?>"/>
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][cycle_2]" value="<?php echo $item['cycle_2']; ?>"/>
            <input type="text" size="3" name="scheme[<?php echo $item['scheme']; ?>][cycle_3]" value="<?php echo $item['cycle_3']; ?>"/>
          </td>
          <td class="ta-center">
            <div class="p-relative">
              <?php echo form_checkbox("scheme[{$item['scheme']}][dynamic]", '', $item['dynamic']); ?>


              <a href="_xSkimSchemesDynamicEditShow" data="&scheme=<?php echo $item['scheme']; ?>" class="dialog" style="position: absolute; top: -5px; margin-left: 6px;">
                <img src="images/edit-22x22.png" border="0" title="Edit"/>
              </a>
            <div class="p-relative">
          </td>
          <td class="ta-right">
            <?php if( $item['scheme'] != SKIM_SCHEME_DEFAULT ): ?>
            <div class="p-relative">
              <a href="_xSkimSchemesDelete" data="&scheme=<?php echo $item['scheme']; ?>" class="xhr" confirm="Are you sure you want to delete this skim scheme?">
                <img src="images/delete-22x22.png" border="0" title="Delete"/>
              </a>
            </div>
            <?php else: ?>
            <div style="height: 28px;"></div>
            <?php endif; ?>
          </td>
        </tr>