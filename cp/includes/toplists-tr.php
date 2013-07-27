        <tr id="item-<?php echo $item['toplist_id']; ?>">
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $item['toplist_id']; ?>"/>
          </td>
          <td class="ta-center">
            <?php echo $item['toplist_id']; ?>
          </td>
          <td>
            <?php echo $item['source']; ?>
          </td>
          <td>
            <?php echo $item['outfile']; ?>
          </td>
          <td>
            <?php echo $item['groups']; ?>
          </td>
          <td>
            <?php echo $item['categories']; ?>
          </td>
          <td class="ta-right">
            <div class="p-relative">
              <a href="_xToplistsBuild" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="xhr" confirm="Are you sure you want to build this toplist?">
                <img src="images/build-22x22.png" border="0" title="Build"/>
              </a>

              <a href="_xToplistsEditShow" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="dialog">
                <img src="images/edit-22x22.png" border="0" title="Edit"/>
              </a>

              <a href="_xToplistsDelete" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="xhr" confirm="Are you sure you want to delete this toplist?">
                <img src="images/delete-22x22.png" border="0" title="Delete"/>
              </a>
            </div>
          </td>
        </tr>