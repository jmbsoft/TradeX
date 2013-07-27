      <?php
      require_once 'stats.php';
      require_once 'geoip-utility.php';
      $dstats = new StatsDetailed($item['domain']);

      $quality_colors = array('#5F8C02', '#EA8612', '#D52727');
      ?>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Detailed Stats for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="900px">
          <div>

            <?php $stats = get_trade_detailed_stats($original['domain']); ?>

            <fieldset>
              <legend>24 Hour Traffic Stats</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td></td>
                    <td class="ta-center">Raw</td>
                    <td class="ta-center">Unique</td>
                    <td class="ta-center">Proxy</td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ta-right fw-bold" style="padding-right: 4px">In</td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->i_raw_24); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->i_uniq_24); printf(" (%d%%)", $stats['overall']->i_raw_24 > 0 ? format_float_to_percent($stats['overall']->i_uniq_24 / $stats['overall']->i_raw_24) : 0); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->i_proxy_24); printf(" (%d%%)", $stats['overall']->i_raw_24 > 0 ? format_float_to_percent($stats['overall']->i_proxy_24 / $stats['overall']->i_raw_24) : 0); ?></td>
                  </tr>
                  <tr bgcolor="#ececec">
                    <td class="ta-right fw-bold" style="padding-right: 4px">Out</td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->o_raw_24); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->o_uniq_24); printf(" (%d%%)", $stats['overall']->o_raw_24 > 0 ? format_float_to_percent($stats['overall']->o_uniq_24 / $stats['overall']->o_raw_24) : 0); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->o_proxy_24); printf(" (%d%%)", $stats['overall']->o_raw_24 > 0 ? format_float_to_percent($stats['overall']->o_proxy_24 / $stats['overall']->o_raw_24) : 0); ?></td>
                  </tr>
                  <tr>
                    <td class="ta-right fw-bold" style="padding-right: 4px">Clicks</td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->c_raw_24); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->c_uniq_24); printf(" (%d%%)", $stats['overall']->c_raw_24 > 0 ? format_float_to_percent($stats['overall']->c_uniq_24 / $stats['overall']->c_raw_24) : 0); ?></td>
                    <td class="ta-right"><?php echo format_int_to_string($stats['overall']->c_proxy_24); printf(" (%d%%)", $stats['overall']->c_raw_24 > 0 ? format_float_to_percent($stats['overall']->c_proxy_24 / $stats['overall']->c_raw_24) : 0); ?></td>
                  </tr>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Country Stats</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Country</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_ctry as $ctry => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                      $quality = get_country_quality($ctry);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td style="color: <?php echo $quality_colors[$quality]; ?>;" class="fw-bold"><?php echo $geoip_country_names[$ctry]; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Country</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_ctry as $ctry => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                      $quality = get_country_quality($ctry);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td style="color: <?php echo $quality_colors[$quality]; ?>;" class="fw-bold"><?php echo $geoip_country_names[$ctry]; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Country</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_ctry as $ctry => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                      $quality = get_country_quality($ctry);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td style="color: <?php echo $quality_colors[$quality]; ?>;" class="fw-bold"><?php echo $geoip_country_names[$ctry]; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>



            <fieldset>
              <legend>User Agent Stats</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">User Agent</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_agent as $agent => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                      $agent = htmlspecialchars($agent);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><div style="width: 550px; overflow: hidden;" title="<?php echo $agent; ?>"><?php echo $agent; ?></div></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">User Agent</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_agent as $agent => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                      $agent = htmlspecialchars($agent);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><div style="width: 550px; overflow: hidden;" title="<?php echo $agent; ?>"><?php echo $agent; ?></div></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">User Agent</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_agent as $agent => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                      $agent = htmlspecialchars($agent);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><div style="width: 550px; overflow: hidden;" title="<?php echo $agent; ?>"><?php echo $agent; ?></div></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>System Language Stats</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Language</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_lang as $lang => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                      $lang = htmlspecialchars($lang);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $lang; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Language</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_lang as $lang => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                      $lang = htmlspecialchars($lang);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $lang; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Language</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_lang as $lang => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                      $lang = htmlspecialchars($lang);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $lang; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Top Referrers</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Referrer</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_ref as $ref => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                      $ref = htmlspecialchars($ref);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><a href="<?php echo $ref; ?>" target="_blank"><?php echo $ref; ?></a></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Top Landing Pages</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Landing Page</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_land as $landing => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                      $landing = htmlspecialchars($landing);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><a href="<?php echo $landing; ?>" target="_blank"><?php echo $landing; ?></a></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Top Links</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Link</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_link as $link => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                      $link = htmlspecialchars($link);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $link; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Link</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_link as $link => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                      $link = htmlspecialchars($link);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $link; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Top Pages</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Page</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_page as $page => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                      $page = htmlspecialchars($page);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><a href="<?php echo $page; ?>" target="_blank"><?php echo $page; ?></a></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Page</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_page as $page => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                      $page = htmlspecialchars($page);
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><a href="<?php echo $page; ?>" target="_blank"><?php echo $page; ?></a></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Active IPs</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">IP</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_ip as $ip => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $ip; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">IP</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_ip as $ip => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $ip; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">IP</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_ip as $ip => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $ip; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>


            <fieldset>
              <legend>Active Proxy</legend>

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      In Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Proxy</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">In</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->i_proxy as $proxy => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->i_total > 0 ? format_float_to_percent($amount/$dstats->i_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $proxy; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Out Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Proxy</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Out</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->o_proxy as $proxy => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->o_total > 0 ? format_float_to_percent($amount/$dstats->o_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $proxy; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

              <br />

              <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 810px; min-width: 810px;">
                <thead>
                  <tr>
                    <td colspan="4" style="font-size: 10pt;">
                      Clicks Last 24 Hours
                    </td>
                  </tr>
                  <tr>
                    <td style="width: 30px;"></td>
                    <td class="ta-center">Proxy</td>
                    <td class="ta-center" width="100">% of Total</td>
                    <td class="ta-center" width="70">Clicks</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $counter = 1;
                  $color = '#ececec';
                  foreach( $dstats->c_proxy as $proxy => $amount ):
                      $color = $color == '#ffffff' ? '#ececec' : '#ffffff';
                      $percent = $dstats->c_total > 0 ? format_float_to_percent($amount/$dstats->c_total, 1) : 0;
                  ?>
                  <tr bgcolor="<?php echo $color; ?>">
                    <td class="ta-right" style="padding-right: 4px;"><?php echo $counter++; ?></td>
                    <td><?php echo $proxy; ?></td>
                    <td style="padding: 0px;" class="va-middle">
                      <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
                        <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
                      </div>
                      <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
                    </td>
                    <td class="ta-right"><?php echo format_int_to_string($amount); ?></td>
                  </tr>
                  <?php
                  endforeach;
                  ?>
                </tbody>
              </table>

            </fieldset>

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
        </div>

      </div>