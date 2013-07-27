<?php
require_once 'geoip-utility.php';

// Load stats from the log file
list($total, $countries) = get_trade_countries($domain, file_sanitize(strtolower($stat)));

// Default to 0 for all un-represented countries
foreach( $geoip_country_codes as $i => $cc )
{
    if( !isset($countries[$i]) )
    {
        $countries[$i] = 0;
    }
}

?>
<map map_file="world.swf" zoom="95%" zoom_x="20.35%" zoom_y="1.95%">
  <areas>
      <area title="borders" mc_name="borders" color="#ffffff" balloon="false"></area>
      <?php foreach( $geoip_country_codes as $i => $cc ): ?>
      <area title="<?php echo $geoip_country_names[$i]; ?>" mc_name="<?php echo $cc; ?>"<?php if( !$countries[$i] ): ?> color="#afafaf"<?php endif; ?> value="<?php echo $countries[$i]; ?>"></area>
      <?php endforeach; ?>
      <area title="Kosovo" mc_name="KV" color="#afafaf" value="0"></area>
  </areas>

    <labels>
      <label x="5" y="95%" text_size="12" align="left">
      <text><![CDATA[<b><?php echo $stat; ?> Last 24h for <?php echo $domain; ?></b>]]></text>
    </label>
  </labels>
</map>