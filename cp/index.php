<?php
// Copyright 2011 JMB Software, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

require_once 'includes/functions.php';

if( $_REQUEST['r'] == 'btl' )
{
    build_all_toplists();
    return;
}


// Recompile templates on first access
if( !isset($C['base_url']) )
{
    recompile_templates();
}


headers_no_cache();
prepare_request();


if( file_exists('reset-access.php') || file_exists('../auto-install.php') )
{
    echo '<div style="font-weight: bold; color: red; font-size: 14pt; text-align: center;">' .
         'The auto-install.php and cp/reset-access.php files must be removed from your server before you can access the control panel' .
         '</div>';
    exit;
}



if( ($auth_error = cp_authenticate()) === true )
{
    cp_exec($_REQUEST['r'], '_xStatsOverallShow');
}
else
{
    include 'login.php';
}

function _xIndexShow()
{
    _xStatsOverallShow();
}

function _xUpdateShow()
{
    include 'update.php';
}

function _xSkimSchemesShow()
{
    include 'skim-schemes.php';
}

function _xToplistsShow()
{
    include 'toplists.php';
}

function _xOutlistsShow()
{
    include 'outlists.php';
}

function _xStatsHistoryShow()
{
    include 'stats-history.php';
}

function _xGraphDataHistoryStats()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory(null, $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-stats.php';
}

function _xGraphDataHistoryProd()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory(null, $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-prod.php';
}

function _xNetworkSyncShow()
{
    include 'network-sync.php';
}

function _xNetworkSitesShow()
{
    include 'network-sites.php';
}

function _xNetworkStatsShow()
{
    include 'network-stats.php';
}

function _xSiteTemplatesShow()
{
    include 'site-templates.php';
}

function _xEmailTemplatesShow()
{
    include 'email-templates.php';
}

function _xStatsOverallShow()
{
    include 'stats-overall.php';
}

function _xStatsHourlyShow()
{
    include 'stats-hourly.php';
}

function _xTradesGraphDataHistoryStats()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory($_REQUEST['domain'], $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-stats.php';
}

function _xTradesGraphDataHistoryProd()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory($_REQUEST['domain'], $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-prod.php';
}

function _xTradesGraphDataHourly()
{
    require_once 'stats.php';
    require_once 'dirdb.php';

    $db = get_trade_db($_REQUEST['domain']);
    $trade = $db->Retrieve($_REQUEST['domain']);
    $stats = new StatsHourly($trade);

    include 'trades-graph-data-hourly.php';
}

function _xTradesGraphDataProdReturn()
{
    require_once 'stats.php';
    require_once 'dirdb.php';

    $db = get_trade_db($_REQUEST['domain']);
    $trade = $db->Retrieve($_REQUEST['domain']);
    $stats = new StatsHourly($trade);

    include 'trades-graph-data-prod-return.php';
}

function _xTradesCountriesData()
{
    $stat = $_REQUEST['stat'];
    $domain = $_REQUEST['domain'];

    include 'trades-countries-data.php';
}

function _xStatsCountriesShow()
{
    include 'stats-countries.php';
}

function _xStatsLanguagesShow()
{
    include 'stats-languages.php';
}

function _xStatsLandingsShow()
{
    include 'stats-landings.php';
}

function _xStatsPagesShow()
{
    include 'stats-pages.php';
}

function _xStatsLinksShow()
{
    include 'stats-links.php';
}

function _xStatsSearchTermsShow()
{
    include 'stats-search-terms.php';
}

function _xLogout()
{
    cp_logout();
    include 'login.php';
}

?>