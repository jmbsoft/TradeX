<?php
global $C;

if( !isset($C['base_url']) ):
?>
<script language="JavaScript" type="text/javascript">
$(function()
{
    $('a[href="_xGlobalSettingsShow"]').click();
});
</script>
<?php
endif;
?>

    <div id="menu-bar">
      <div>
        <a href="index.php"><img src="images/logo-94x32.png" border="0"/></a>
        <a href="docs/index.html"><img src="images/help-22x22.png" border="0"/></a>
        <a href="index.php?r=_xLogout"><img src="images/logout-92x32.png" border="0"/></a>

        <span class="menu">
          &lsaquo; Trades &rsaquo;
          <div>
            <a href="_xTradesAddShow" class="dialog">Add Trade</a>
            <a href="_xTradesBulkAddShow" class="dialog">Bulk Add Trades</a>
            <a href="_xTradesDefaultsShow" class="dialog">New Trade Defaults</a>
            <a href="_xTradesExportShow" class="dialog">Export Trades</a>
          </div>
        </span>


        <span class="menu">
          &lsaquo; Stats &rsaquo;
          <div>
            <a href="index.php?r=_xStatsOverallShow">Overall</a>
            <a href="index.php?r=_xStatsHourlyShow">Hourly</a>
            <a href="index.php?r=_xStatsHistoryShow">History</a>
            <a href="index.php?r=_xStatsCountriesShow">Countries</a>
            <a href="index.php?r=_xStatsLanguagesShow">Languages</a>
            <a href="index.php?r=_xStatsLandingsShow">Landings</a>
            <a href="index.php?r=_xStatsPagesShow">Pages</a>
            <a href="index.php?r=_xStatsLinksShow">Links</a>
            <a href="index.php?r=_xStatsSearchTermsShow">Search Terms</a>
            <a href="index.php?r=_xOutlistsShow">Outlists</a>
          </div>
        </span>

        <span class="menu">
          &lsaquo; Toplists &rsaquo;
          <div>
            <a href="index.php?r=_xToplistsShow">Manage Toplists</a>
            <a href="_xToplistsAddShow" class="dialog">Add a Toplist</a>
            <a href="_xToplistsBuildAll" class="xhr">Build All Toplists</a>
          </div>
        </span>

        <span class="menu">
          &lsaquo; Network &rsaquo;
          <div>
            <a href="index.php?r=_xNetworkStatsShow">Stats</a>
            <a href="index.php?r=_xNetworkSitesShow">Manage Sites</a>
            <a href="_xNetworkSitesAddShow" class="dialog">Add a Site</a>
            <a href="index.php?r=_xNetworkSyncShow">Sync Settings</a>
          </div>
        </span>

        <span class="menu">
          &lsaquo; Templates &rsaquo;
          <div>
            <a href="index.php?r=_xSiteTemplatesShow">Site Templates</a>
            <a href="index.php?r=_xEmailTemplatesShow">E-mail Templates</a>
            <a href="_xEmailSignatureShow" class="dialog">E-mail Greeting &amp; Signature</a>
            <a href="_xTemplatesRecompileAll" class="xhr">Recompile Templates</a>
          </div>
        </span>

        <span class="menu">
          &lsaquo; Tools &rsaquo;
          <div>
            <a href="_xLinkGenerateShow" class="dialog">Link Generator</a>
            <a href="_xUrlEncodeShow" class="dialog">URL Encode/Decode</a>
            <a href="_xTradesExportShow" class="dialog">Export Trades</a>
            <a href="index.php?r=_xUpdateShow">Check For Update</a>
          </div>
        </span>

        <span class="menu">
          &lsaquo; Settings &rsaquo;
          <div>
            <a href="_xGlobalSettingsShow" class="dialog">Global Settings</a>
            <a href="index.php?r=_xSkimSchemesShow">Skim Schemes</a>
            <a href="_xGroupsShow" class="dialog">Groups</a>
            <a href="_xCategoriesShow" class="dialog">Categories</a>
            <a href="_xBlacklistShow" class="dialog">Blacklist</a>
            <a href="_xCountriesShow" class="dialog">Countries</a>
            <a href="_xSearchEnginesShow" class="dialog">Search Engines</a>
            <a href="_xTradeRulesShow" class="dialog">Trade Rules</a>
            <a href="_xChangeLoginShow" class="dialog">Change Login</a>
          </div>
        </span>

      </div>
    </div>
