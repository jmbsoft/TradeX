        <tr id="item-<?php echo $item['domain']; ?>">
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $item['domain']; ?>"/>
          </td>
          <td>
            <a href="<?php echo $item['url']; ?>" class="fw-bold cp-login" cpurl="<?php echo $item['url']; ?>" cpuser="<?php echo $item['username']; ?>" cppass="<?php echo $item['password']; ?>" target="_blank"><?php echo $item['domain']; ?></a>
          </td>
          <td>
            <?php echo $item['owner']; ?>
          </td>
          <td>
            <?php echo $item['category']; ?>
          </td>
          <td class="ta-right">
            <div class="p-relative">
              <a href="_xNetworkSitesEditShow" data="&domain=<?php echo $item['domain']; ?>" class="dialog">
                <img src="images/edit-22x22.png" border="0" title="Edit"/>
              </a>

              <a href="_xNetworkSitesDelete" data="&domain=<?php echo $item['domain']; ?>" class="xhr" confirm="Are you sure you want to delete this network site?">
                <img src="images/delete-22x22.png" border="0" title="Delete"/>
              </a>
            </div>
          </td>
        </tr>