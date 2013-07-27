                <b>URL:</b> <span><a href="<?php echo $item['return_url']; ?>" target="_blank"><?php echo $item['return_url']; ?></a></span><br />
                <b>Name:</b> <span><?php echo $item['site_name']; ?></span><br />
                <b>Description:</b> <span><?php echo $item['site_description']; ?></span><br />
                <b>E-mail:</b> <span><a href="mailto:<?php echo $item['email']; ?>"><?php echo $item['email']; ?></a></span><br />
                <b>Nickname:</b> <span><?php echo $item['nickname']; ?></span><br />
                <b>Categories:</b> <span><?php echo str_replace(',', ', ', $item['categories']) ?></span><br />
                <b>Groups:</b> <span><?php echo str_replace(',', ', ', $item['groups']); ?></span><br />
                <b><img src="images/reload-16x16.png" class="refresh-thumbs" trade="<?php echo $item['domain']; ?>" /> Thumbs:</b>
                <span class="trade-thumbs" trade="<?php echo $item['domain']; ?>">
                <?php
                if( $item['thumbnails'] > 0 ):
                    for( $i = 1; $i <= $item['thumbnails']; $i++ ):
                ?>
                    <img src="../thumbs/<?php echo "{$item['domain']}-$i.jpg"; ?>?<?php echo mt_rand(); ?>" />
                <?php
                    endfor;
                endif;
                ?>
                </span><br />