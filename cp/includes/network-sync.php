<?php
include 'global-header.php';
include 'global-menu.php';

$values = unserialize(file_get_contents(FILE_NETWORK_SITES_VALUES));
$cache = unserialize(file_get_contents(FILE_NETWORK_SITES_CACHE));

require_once 'network-util.php';
require_once 'textdb.php';
require_once 'dirdb.php';

$db = new NetworkDB();
$sites = $db->RetrieveAll('domain');

$db = new TradeDB();
$trades = $db->RetrieveAll('domain');

?>
    <script type="text/javascript" src="js/network-sync.js"></script>


    <div class="centered-header">
      Sync Network Sites
    </div>


    <!-- SYNC PROGRESS START -->
    <fieldset style="margin: 8px auto 30px; display: none;" id="sync-progress">
      <legend>Sync Progress</legend>

      <div id="sync-current" class="message-notice ta-center va-middle">
        <img src="images/activity-16x16.gif"/>
        <span>Syncing <span id="sync-site"></span> (<span id="sync-num-done"></span> of <span id="sync-num-total"></span>)</span>
      </div>

      <b class="margin-top-10px">Results:</b>
      <div id="sync-complete">
      </div>

    </fieldset>
    <!-- SYNC PROGRESS END -->


    <!-- SYNC SETTINGS START -->
    <fieldset style="margin: 8px auto;" class="sync-hide">
      <legend>Select Settings to Sync</legend>

      <div class="ta-center margin-bottom-10px">
        <span id="select-settings-all" class="selectable-option">Select All</span>
        <span id="select-settings-none" class="selectable-option">Select None</span>
      </div>

      <div id="select-settings" class="selectable-container">
        <span value="<?php echo NETWORK_SYNC_TRADES; ?>">Trades</span>
        <span value="<?php echo NETWORK_SYNC_SKIM_SCHEMES; ?>">Skim Schemes</span>
        <span value="<?php echo NETWORK_SYNC_GROUPS; ?>">Groups</span>
        <span value="<?php echo NETWORK_SYNC_CATEGORIES; ?>">Categories</span>
        <span value="<?php echo NETWORK_SYNC_BLACKLIST; ?>">Blacklist</span>
        <span value="<?php echo NETWORK_SYNC_COUNTRIES; ?>">Countries</span>
        <span value="<?php echo NETWORK_SYNC_SEARCH_ENGINES; ?>">Search Engines</span>
        <span value="<?php echo NETWORK_SYNC_TRADE_RULES; ?>">Trade Rules</span>
        <span value="<?php echo NETWORK_SYNC_NETWORK_SITES; ?>">Network Sites</span>
      </div>

    </fieldset>
    <!-- SYNC SETTINGS END -->



    <!-- TRADES START -->
    <fieldset id="sync-trades" style="margin: 32px auto 8px; display: none;" class="sync-hide">
      <legend>Select Trades to Sync</legend>

      <div class="ta-center margin-bottom-10px">
        <span id="select-trades-all" class="selectable-option">Select All</span>
        <span id="select-trades-none" class="selectable-option">Select None</span>
      </div>

      <?php
      $categories = array_map('trim', file(FILE_CATEGORIES));
      asort($categories);
      if( count($categories) ):
      ?>
      <div class="ta-center margin-bottom-10px">
        <span class="selectable-checkboxes selectable-trades-checkboxes">
          <div class="selectable-checkboxes-header">Select By Category</div>
          <div class="selectable-checkboxes-container">
            <?php foreach( $categories as $category ): ?>
            <span><input type="checkbox" name="category" value="<?php echo $category; ?>" /> <?php echo $category; ?></span>
            <?php endforeach; ?>
          </div>
        </span>
      </div>
      <?php endif; ?>

      <div id="select-trades" class="selectable-container">
        <?php foreach( $trades as $trade ): ?>
        <span category=",<?php echo $trade['categories']; ?>," value="<?php echo $trade['domain']; ?>"><?php echo $trade['domain']; ?></span>
        <?php endforeach; ?>
      </div>

    </fieldset>
    <!-- TRADES END -->


    <br />


    <!-- NETWORK SITES START -->
    <fieldset style="margin: 8px auto;" class="sync-hide">
      <legend>Select Sites to Sync</legend>

      <div class="ta-center margin-bottom-10px">
        <span id="select-sites-all" class="selectable-option">Select All</span>
        <span id="select-sites-none" class="selectable-option">Select None</span>
      </div>


      <div class="ta-center margin-bottom-10px">
        <?php
        if( is_array($values) && count($values['categories']) ):
            asort($values['categories']);
        ?>
        <span class="selectable-checkboxes selectable-sites-checkboxes">
          <div class="selectable-checkboxes-header">Select By Category</div>
          <div class="selectable-checkboxes-container">
            <?php foreach( $values['categories'] as $category ): ?>
            <span><input type="checkbox" name="category" value="<?php echo $category; ?>" /> <?php echo $category; ?></span>
            <?php endforeach; ?>
          </div>
        </span>
        <?php endif; ?>

        <?php
        if( is_array($values) && count($values['owners']) ):
            asort($values['owners']);
        ?>
        <span class="selectable-checkboxes selectable-sites-checkboxes">
          <div class="selectable-checkboxes-header">Select By Nick</div>
          <div class="selectable-checkboxes-container">
            <?php foreach( $values['owners'] as $owner ): ?>
            <span><input type="checkbox" name="owner" value="<?php echo $owner; ?>"> <?php echo $owner; ?></span>
            <?php endforeach; ?>
          </div>
        </span>
        <?php endif; ?>
      </div>

      <div id="select-sites" class="selectable-container">
        <?php foreach( $sites as $site ): ?>
        <span category="<?php echo $site['category']; ?>" owner="<?php echo $site['owner']; ?>" value="<?php echo $site['domain']; ?>"><?php echo $site['domain']; ?></span>
        <?php endforeach; ?>
      </div>

    </fieldset>
    <!-- NETWORK SITES END -->



    <!-- TOOLBAR START -->
    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xNetworkSitesAddShow" class="dialog" title="Add"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/sync-32x32.png" class="action" title="Sync">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/network-sync-settings.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>
    <!-- TOOLBAR END -->

<?php
include 'global-footer.php';
?>