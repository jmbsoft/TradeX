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



define('XHR', true);

require_once 'includes/functions.php';
require_once 'validator.php';
require_once 'json.php';

headers_no_cache();
prepare_request();

if( ($auth_error = cp_authenticate()) === true )
{
    cp_exec($_REQUEST['r'], '_xFunctionMissing');
}
else
{
    JSON::Logout();
}

function _xMogrifyTest()
{
    global $C;

    $C['magick_mogrify_path'] = $_REQUEST['magick_mogrify_path'];
    check_image_resizer();

    if( $C['have_magick'] )
    {
        JSON::Success('The mogrify path appears to be valid!');
    }
    else if( $C['have_gd'] )
    {
        JSON::Warning('The mogrify path does not appear to be valid, however the GD PHP extension is installed and can be used instead');
    }
    else
    {
        JSON::Warning('The mogrify path does not appear to be valid and the GD PHP extension is either not installed or configured without JPEG support.  Thumbnail resizing will not be possible.');
    }
}

function _xGrabThumbs()
{
    require_once 'dirdb.php';

    $db = new TradeDB();
    $trade = $db->Retrieve($_REQUEST['trade']);

    if( $trade['flag_grabber'] )
    {
        if( string_is_empty($trade['grabber_url']) )
        {
            $trade['grabber_url'] = $trade['return_url'];
        }

        $thumbnails = grab_thumbs($trade['domain'], $trade['grabber_url'], $trade['trigger_strings']);

        switch($thumbnails)
        {
            case null:
                JSON::Warning('Thumbnails could not be downloaded from ' . $trade['domain']);
                break;

            case 0:
                JSON::Warning('HTTP connection for ' . $trade['domain'] . ' has failed');
                break;

            default:
                $db->Update($_REQUEST['trade'], array('thumbnails' => $thumbnails));

                $html = '';
                for( $i = 1; $i <= $thumbnails; $i++ )
                {
                    $html .= '<img src="../thumbs/' . $trade['domain'] . '-' . $i . '.jpg?' . mt_rand() . '" />';
                }

                JSON::Success(
                    array(
                        JSON_KEY_MESSAGE => 'Thumbnails have been successfully grabbed. Total: ' . $thumbnails,
                        JSON_KEY_HTML => $html,
                        JSON_KEY_ITEM_ID => $trade['domain']
                    )
                );
                break;
        }
    }
    else
    {
        JSON::Warning($trade['domain'] . ' does not have the "Grab thumbs" option enabled');
    }
}

function _xUpdateGetInstaller()
{
    global $C;



    $post_data = array(
        'upgrade' => true,
        'version' => $_REQUEST['version'],
        'key' => LIC_KEY,
        'domain' => domain_from_url($C['base_url'])
    );

    require_once 'http.php';
    $http = new HTTP();

    if( $http->POST(URL_DOWNLOAD, $post_data) )
    {
        if( preg_match('~X-SHA1: ([a-z0-9]+)~i', $http->response_headers, $matches) )
        {
            $installer_file = DIR_DATA . '/install.dat';
            $sha1 = $matches[1];
            file_write($installer_file, $http->body);

            if( $sha1 != sha1_file($installer_file) )
            {
                file_delete($installer_file);
                JSON::Error('File hash does not match, possible corrupted data.  Please try again.');
            }
            else
            {
                JSON::Success();
            }
        }
        else if( preg_match('~X-Error: ([a-z0-9_]+)~i', $http->response_headers, $matches) )
        {
            JSON::Error('Unable to locate a license for this domain');
        }
        else
        {
            JSON::Error('Download from jmbsoft.com failed, please try again');
        }
    }
    else
    {
        JSON::Error('Unable to connect to jmbsoft.com for update: ' + $http->error);
    }
}

function _xUpdateExtractInstaller()
{
    chdir('..');

    define('ITEM_TYPE_DIR', 'DIR');
    define('ITEM_TYPE_FILE', 'FILE');

    $installer_file = DIR_DATA . '/install.dat';

    file_append(FILE_LOG_UPDATE, "[" . date('r') . "] Update started...\n");

    $fp = fopen($installer_file, 'r');
    while( !feof($fp) )
    {
        list($type, $name, $permissions, $su_permissions, $on_install, $on_patch, $chunk, $b64contents) = explode('|', trim(fgets($fp)));

        $permissions = $_REQUEST['su'] ? octdec($su_permissions) : octdec($permissions);

        switch($type)
        {
            case ITEM_TYPE_DIR:
                if( !file_exists($name) )
                {
                    file_append(FILE_LOG_UPDATE, "\tCreating directory $name\n");

                    mkdir($name);
                    @chmod($name, $permissions);

                }
                break;

            case ITEM_TYPE_FILE:
                if( $on_patch == 1 || (!file_exists($name) && $on_patch == 2) )
                {
                    if( $chunk == 0 )
                    {
                        file_append(FILE_LOG_UPDATE, "\tExtracting file $name\n");
                    }

                    $fp_out = fopen($name, $chunk == 0 ? 'w' : 'a');
                    fwrite($fp_out, base64_decode($b64contents));
                    fclose($fp_out);
                    @chmod($name, $permissions);

                }
                break;
        }
    }
    fclose($fp);

    file_delete($installer_file);

    JSON::Success();
}

function _xPatch()
{
    require_once 'dirdb.php';

    write_config($C);
    recompile_templates();

    $db = new TradeDB();
    foreach( $db->RetrieveAll() as $trade )
    {
        if( $trade['thumbnails'] == STRING_BLANK )
        {
            $trade['thumbnails'] = 0;
            $db->Update($trade['domain'], $trade);
        }
    }

    JSON::Success();
}

function _xLinkGenerateShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('link-generate.php')));
}

function _xLinkGenerate()
{
    global $C;

    $params = array();

    if( !string_is_empty($_REQUEST['link']) )
    {
        $params[] = 'l=' . urlencode($_REQUEST['link']);
    }

    switch( $_REQUEST['type'] )
    {
        case 'trade':
            $params[] = 't=' . urlencode($_REQUEST['trade']);
            break;

        case 'scheme':
        case 'percent':
            if( $_REQUEST['type'] == 'scheme' )
            {
                $params[] = 'ss=' . urlencode($_REQUEST['skim_scheme']);
            }
            else
            {
                $params[] = 's=' . $_REQUEST['percent'];

                if( $_REQUEST['flag_fc'] )
                {
                    $params[] = 'fc=1';
                }
            }

            if( !string_is_empty($_REQUEST['category']) )
            {
                $params[] = 'c=' . urlencode($_REQUEST['category']);
            }

            if( !string_is_empty($_REQUEST['group']) )
            {
                $params[] = 'g=' . urlencode($_REQUEST['group']);
            }

            if( !string_is_empty($_REQUEST['content_url']) )
            {
                $params[] = 'u=' . ($_REQUEST['encoding'] == 'urlencode' ? urlencode($_REQUEST['content_url']) : base64_encode($_REQUEST['content_url']));
            }
            break;
    }

    JSON::Success(array('url' => $C['base_url'] . '/out.php?' . join('&', $params)));
}

function _xNetworkSync()
{
    
    require_once 'network-util.php';

    $settings = null;

    // Cache settings for next request
    if( isset($_REQUEST['cache']) )
    {
        $settings = array();
        $to_sync = explode(',', $_REQUEST['settings']);

        if( in_array(NETWORK_SYNC_BLACKLIST, $to_sync) )
        {
            $settings[NETWORK_SYNC_BLACKLIST] = array();

            foreach( dir_read_files(DIR_BLACKLIST) as $bl_file )
            {
                $settings[NETWORK_SYNC_BLACKLIST][$bl_file] = file_get_contents(DIR_BLACKLIST . '/' . $bl_file);
            }
        }


        if( in_array(NETWORK_SYNC_CATEGORIES, $to_sync) )
        {
            $settings[NETWORK_SYNC_CATEGORIES] = file_get_contents(FILE_CATEGORIES);
        }


        if( in_array(NETWORK_SYNC_COUNTRIES, $to_sync) )
        {
            $settings[NETWORK_SYNC_COUNTRIES] = file_get_contents(FILE_COUNTRIES);
        }


        if( in_array(NETWORK_SYNC_GROUPS, $to_sync) )
        {
            $settings[NETWORK_SYNC_GROUPS] = file_get_contents(FILE_GROUPS);
        }


        if( in_array(NETWORK_SYNC_NETWORK_SITES, $to_sync) )
        {
            require_once 'textdb.php';
            $db = new NetworkDB();
            $settings[NETWORK_SYNC_NETWORK_SITES] = $db->RetrieveAll();
        }


        if( in_array(NETWORK_SYNC_SEARCH_ENGINES, $to_sync) )
        {
            $settings[NETWORK_SYNC_SEARCH_ENGINES] = file_get_contents(FILE_SEARCH_ENGINES);
        }


        if( in_array(NETWORK_SYNC_SKIM_SCHEMES, $to_sync) )
        {
            $settings[NETWORK_SYNC_SKIM_SCHEMES] = array();

            foreach( dir_read_files(DIR_SKIM_SCHEMES) as $ss_file )
            {
                $settings[NETWORK_SYNC_SKIM_SCHEMES][$ss_file] = array();
                $settings[NETWORK_SYNC_SKIM_SCHEMES][$ss_file]['merged'] = file_get_contents(DIR_SKIM_SCHEMES . '/' . $ss_file);
                $settings[NETWORK_SYNC_SKIM_SCHEMES][$ss_file]['base'] = file_get_contents(DIR_SKIM_SCHEMES_BASE . '/' . $ss_file);
                $settings[NETWORK_SYNC_SKIM_SCHEMES][$ss_file]['dynamic'] = file_get_contents(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $ss_file);
            }
        }

        if( in_array(NETWORK_SYNC_TRADES, $to_sync) )
        {
            $trades = explode(',', $_REQUEST['trades']);

            require_once 'dirdb.php';
            $db = new TradeDB();

            $settings[NETWORK_SYNC_TRADES] = array();

            foreach( $trades as $trade )
            {
                $trade = $db->Retrieve($trade);

                if( !empty($trade) )
                {
                    $settings[NETWORK_SYNC_TRADES][] = $trade;
                }
            }
        }


        if( in_array(NETWORK_SYNC_TRADE_RULES, $to_sync) )
        {
            $settings[NETWORK_SYNC_TRADE_RULES] = file_get_contents(FILE_TRADE_RULES);
        }


        $settings = base64_encode(serialize($settings));
        file_write(FILE_NETWORK_SYNC_CACHE, $settings);
    }
    else
    {
        // Read settings from cache
        $settings = file_get_contents(FILE_NETWORK_SYNC_CACHE);
    }

    require_once 'textdb.php';

    $db = new NetworkDB();
    $site = $db->Retrieve($_REQUEST['domain']);

    if( empty($site) )
    {
        return JSON::Warning(array('response' => 'Site no longer exists in the database'));
    }


    // Sync settings to network site
    $nr = new NetworkRequest($site, NETWORK_FNC_SYNC, array('sync' => $settings));
    if( ($response = $nr->Execute()) === false )
    {
        return JSON::Warning(array('response' => $nr->error));
    }

    JSON::Success();
}

function _xNetworkStatsTotalGet()
{
    require_once 'stats.php';

    $so_total = new StatsOverall(array('domain' => 'Network Totals'));
    $cache = unserialize(file_get_contents(FILE_NETWORK_SITES_CACHE));

    if( is_array($cache) )
    {
        foreach( $cache as $domain => $data )
        {
            $so_total->AddStats($data['so']);
        }
    }

    JSON::Success(array('response' => $so_total));
}

function _xNetworkStatsGet()
{
    require_once 'network-util.php';
    require_once 'textdb.php';

    $db = new NetworkDB();
    $site = $db->Retrieve($_REQUEST['domain']);

    if( empty($site) )
    {
        network_stats_cache_remove($_REQUEST['domain']);
        return JSON::Warning(array('response' => 'Site no longer exists in the database'));
    }

    // Get stats for a network site
    $nr = new NetworkRequest($site, NETWORK_FNC_GET_STATS);
    if( ($response = $nr->Execute()) === false )
    {
        network_stats_cache_remove($_REQUEST['domain']);
        return JSON::Warning(array('response' => $nr->error));
    }

    network_stats_cache_update($site, $response);

    JSON::Success(array('response' => unserialize($response)));
}

function _xNetworkSitesAddShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('network-sites-add.php')));
}

function _xNetworkSitesAdd()
{
    

    $v = Validator::Get();

    $v->Register($_REQUEST['url'], VT_VALID_HTTP_URL, 'The Control Panel URL field must be a valid HTTP URL');
    $v->Register($_REQUEST['username'], VT_NOT_EMPTY, 'The Username field is required');
    $v->Register($_REQUEST['password'], VT_NOT_EMPTY, 'The Password field is required');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Network site could not be added; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $_REQUEST['domain'] = domain_from_url($_REQUEST['url']);
    $_REQUEST['url'] = preg_replace(array('~index.php$~i', '~(?<!/)$~'), array('', '/'), $_REQUEST['url']);

    require_once 'textdb.php';

    if( string_is_empty($_REQUEST['category']) )
    {
        $_REQUEST['category'] = '-- NONE --';
    }

    if( string_is_empty($_REQUEST['owner']) )
    {
        $_REQUEST['owner'] = '-- NONE --';
    }

    $db = new NetworkDB();
    $db->Add($_REQUEST);

    network_site_update_stored_values();

    JSON::Success(array(JSON_KEY_MESSAGE => 'Network site has been successfully added',
                        JSON_KEY_ROW => _xIncludeCapture('network-sites-tr.php', $_REQUEST),
                        JSON_KEY_ITEM_TYPE => 'network-sites',
                        JSON_KEY_DIALOG => _xIncludeCapture('network-sites-add.php')));
}

function _xNetworkSitesEditShow()
{
    require_once 'textdb.php';

    $db = new NetworkDB();
    $data = $db->Retrieve($_REQUEST['domain']);

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('network-sites-add.php', $data)));
}

function _xNetworkSitesEdit()
{
    

    $v = Validator::Get();

    $v->Register($_REQUEST['url'], VT_VALID_HTTP_URL, 'The Control Panel URL field must be a valid HTTP URL');
    $v->Register($_REQUEST['username'], VT_NOT_EMPTY, 'The Username field is required');
    $v->Register($_REQUEST['password'], VT_NOT_EMPTY, 'The Password field is required');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Network site could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $_REQUEST['domain'] = domain_from_url($_REQUEST['url']);
    $_REQUEST['url'] = preg_replace(array('~index.php$~i', '~(?<!/)$~'), array('', '/'), $_REQUEST['url']);

    require_once 'textdb.php';

    if( string_is_empty($_REQUEST['category']) )
    {
        $_REQUEST['category'] = '-- NONE --';
    }

    if( string_is_empty($_REQUEST['owner']) )
    {
        $_REQUEST['owner'] = '-- NONE --';
    }

    $db = new NetworkDB();
    $db->Update($_REQUEST['domain'], $_REQUEST);

    network_site_update_stored_values();

    JSON::Success(array(JSON_KEY_MESSAGE => 'Network site has been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('network-sites-add.php', $_REQUEST)));
}

function _xNetworkSitesBulkEditShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('network-sites-bulk-edit.php', $data)));
}

function _xNetworkSitesBulkEdit()
{
    

    $v = Validator::Get();

    if( $_REQUEST['flag_update']['username'] )
    {
        $v->Register($_REQUEST['username'], VT_NOT_EMPTY, 'The Username field is required');
    }

    if( $_REQUEST['flag_update']['password'] )
    {
        $v->Register($_REQUEST['password'], VT_NOT_EMPTY, 'The Password field is required');
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Network sites could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    if( $_REQUEST['flag_update']['category'] && string_is_empty($_REQUEST['category']) )
    {
        $_REQUEST['category'] = '-- NONE --';
    }

    if( $_REQUEST['flag_update']['owner'] && string_is_empty($_REQUEST['owner']) )
    {
        $_REQUEST['owner'] = '-- NONE --';
    }

    foreach( $_REQUEST['flag_update'] as $field => $update )
    {
        if( !$update )
        {
            unset($_REQUEST[$field]);
        }
    }

    require_once 'textdb.php';
    $db = new NetworkDB();

    foreach( explode(',', $_REQUEST['domain']) as $domain )
    {
        if( string_is_empty($domain) )
        {
            continue;
        }

        $db->Update($domain, $_REQUEST);
    }

    network_site_update_stored_values();

    JSON::Success(array(JSON_KEY_MESSAGE => 'The selected network sites have been updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('network-sites-bulk-edit.php', $_REQUEST)));
}

function _xNetworkSitesDelete()
{
    

    network_site_delete($_REQUEST['domain']);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Network site has been deleted',
                        JSON_KEY_ITEM_TYPE => 'network-sites',
                        JSON_KEY_ITEM_ID => $_REQUEST['domain']));
}

function _xNetworkSitesDeleteBulk()
{
    

    network_site_delete(explode(',', $_REQUEST['domain']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'The selected network sites have been deleted'));
}

function _xSystemTradesEditShow($message = null)
{
    require_once 'dirdb.php';
    $db = new SystemDB();

    $data = $db->Retrieve($_REQUEST['domain']);

    JSON::Success(array(JSON_KEY_MESSAGE => $message,
                        JSON_KEY_DIALOG => _xIncludeCapture('system-trades-edit.php', $data)));
}

function _xSystemTradesEdit()
{
    

    require_once 'dirdb.php';

    $db = new SystemDB();

    $v = Validator::Get();

    if( $_REQUEST['send_method'] >= 2 )
    {
        $v->Register($_REQUEST['traffic_url'], VT_VALID_HTTP_URL, 'The Traffic URL must be a valid HTTP URL');
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'System trade could not be edited; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $db->Update($_REQUEST['domain'], system_trade_prepare_data($_REQUEST));

    _xSystemTradesEditShow('System trade has been successfully updated');
}

function _xTradesExportShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-export.php')));
}

function _xTradesExport()
{
    

    require_once 'dirdb.php';

    $db = new TradeDB();

    $lines = array();
    foreach( $db->RetrieveAll() as $trade )
    {
        $lines[] = preg_replace('~\{\$([a-z_]+)\}~e', '$trade[\'$1\']', $_REQUEST['format']);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Export data has been generated',
                        'lines' => join(STRING_LF_UNIX, $lines)));
}

function _xTradesDefaultsShow($message = null)
{
    $vars = array('dialog_title' => 'New Trade Defaults',
                  'function_name' => '_xTradesDefaultsSave',
                  'button_text' => 'Save Settings',
                  'editing' => false,
                  'default' => true,
                  'bulk' => false);

    $data = unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS));

    JSON::Success(array(JSON_KEY_MESSAGE => $message,
                        JSON_KEY_DIALOG => _xIncludeCapture('trades-add.php', $data, $vars)));
}

function _xTradesDefaultsSave()
{
    

    unset($_REQUEST['r']);
    file_write(FILE_NEW_TRADE_DEFAULTS, serialize($_REQUEST));
    _xTradesDefaultsShow('New trade defaults have been saved');
}

function _xTradesBulkAddShow($message = null)
{
    $vars = array('dialog_title' => 'Bulk Add Trades',
                  'function_name' => '_xTradesBulkAdd',
                  'button_text' => 'Add Trades',
                  'editing' => false,
                  'default' => false,
                  'bulk' => true);

    $data = unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS));

    JSON::Success(array(JSON_KEY_MESSAGE => $message,
                        JSON_KEY_DIALOG => _xIncludeCapture('trades-add.php', $data, $vars)));
}

function _xTradesBulkAdd()
{
    global $C;

    $C['flag_register_email_admin'] = false;

    

    $v = Validator::Get();

    $v->Register($_REQUEST['data'], VT_NOT_EMPTY, 'The trade data field is required');
    $v->Register(in_array('return_url', $_REQUEST['fields']), VT_IS_TRUE, 'The Return URL field must be one of the selected fields');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Trades could not be added; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    require_once 'dirdb.php';

    $db = new TradeDB();

    $added = 0;
    $duplicate = 0;
    $invalid = 0;
    $loginfo = array('Importing trades');

    $_REQUEST['fields'] = array_values(array_filter($_REQUEST['fields']));

    foreach( explode(STRING_LF_UNIX, string_format_lf($_REQUEST['data'])) as $line )
    {
        $data = array();
        $line_fields = explode($_REQUEST['separator'], $line);
        foreach( $_REQUEST['fields'] as $i => $field )
        {
            $data[$field] = $line_fields[$i];
        }

        $data['domain'] = domain_from_url($data['return_url']);

        if( !empty($data['domain']) && $db->Exists($data['domain']) )
        {
            $loginfo[] = "\t$line [Duplicate]";
            $duplicate++;
            continue;
        }

        $data = array_merge($_REQUEST, $data);

        $v->Reset();
        $v->Register($data['return_url'], VT_VALID_HTTP_URL, 'The Return URL is not a properly formatted URL');
        $v->Register($data['status'], VT_IS_IN, 'The Status value is not valid', array(STATUS_UNCONFIRMED, STATUS_NEW, STATUS_ACTIVE, STATUS_AUTOSTOPPED, STATUS_DISABLED));

        if( !$v->Validate() )
        {
            $loginfo[] = "\t$line [" . join(', ', $v->GetErrors()) . "]";
            $invalid++;
            continue;
        }

        trade_add($data);

        $added++;
    }

    log_append('import.log', join(STRING_LF_UNIX, $loginfo));

    if( $duplicate == 0 && $invalid == 0 )
    {
        $message = format_int_to_string($added) . ' trades successfully added';
        JSON::Success(array(JSON_KEY_MESSAGE => $message));
    }
    else
    {
        $message = format_int_to_string($added) . ' trades successfully added<br />' .
                   format_int_to_string($duplicate) . ' duplicates were skipped<br />' .
                   format_int_to_string($invalid) . ' with invalid formatting were skipped<br /><br />' .
                   'For details, check the import.log file in the logs directory of your TradeX installation';

        JSON::Warning(array(JSON_KEY_MESSAGE => $message));
    }
}

function _xTradesAddShow($message = null)
{
    $vars = array('dialog_title' => 'Add a Trade',
                  'function_name' => '_xTradesAdd',
                  'button_text' => 'Add Trade',
                  'editing' => false,
                  'default' => false,
                  'bulk' => false);

    $data = unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS));

    JSON::Success(array(JSON_KEY_MESSAGE => $message,
                        JSON_KEY_DIALOG => _xIncludeCapture('trades-add.php', $data, $vars)));
}

function _xTradesAdd()
{
    global $C;

    $C['flag_register_email_admin'] = false;

    

    require_once 'dirdb.php';

    $db = new TradeDB();
    $domain = domain_from_url($_REQUEST['return_url']);

    $v = Validator::Get();

    $v->Register($_REQUEST['return_url'], VT_VALID_HTTP_URL, 'The Return URL must be a valid HTTP URL');

    if( !empty($domain) )
    {
        $v->Register($db->Exists($domain), VT_IS_FALSE, 'The trade you are trying to add already exists');
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Trade could not be added; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $_REQUEST['domain'] = $domain;
    trade_add($_REQUEST);

    _xTradesAddShow('Trade has been successfully added');
}

function _xTradesEditShow($message = null)
{
    $vars = array('dialog_title' => 'Edit a Trade',
                  'function_name' => '_xTradesEdit',
                  'button_text' => 'Update Trade',
                  'editing' => true,
                  'default' => false,
                  'bulk' => false);

    require_once 'dirdb.php';
    $db = new TradeDB();

    $data = $db->Retrieve($_REQUEST['domain']);

    JSON::Success(array(JSON_KEY_MESSAGE => $message,
                        JSON_KEY_DIALOG => _xIncludeCapture('trades-add.php', $data, $vars)));
}

function _xTradesEdit()
{
    

    require_once 'dirdb.php';

    $db = new TradeDB();
    $domain = domain_from_url($_REQUEST['return_url']);

    $v = Validator::Get();

    $v->Register($_REQUEST['return_url'], VT_VALID_HTTP_URL, 'The Return URL must be a valid HTTP URL');

    if( $domain != $_REQUEST['domain'] )
    {
        $v->Register($db->Exists($domain), VT_IS_FALSE, 'A trade already exists with the domain you are trying to edit');
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Trade could not be edited; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $trade = $db->Retrieve($_REQUEST['domain']);

    if( !empty($trade) )
    {
        // Update owed instance forces, if necessary
        if( $trade['force_instant'] != $_REQUEST['force_instant'] )
        {
            $_REQUEST['force_instant_owed'] = $_REQUEST['force_instant'];
        }

        // Update owed hourly forces, if necessary
        if( $trade['force_hourly'] != $_REQUEST['force_hourly'] )
        {
            $_REQUEST['force_hourly_owed'] = $_REQUEST['force_hourly'];
        }

        if( empty($_REQUEST['force_hourly']) )
        {
            $_REQUEST['force_hourly_end'] = '';
        }

        // Domain change
        if( $domain != $_REQUEST['domain'] )
        {
            $db->ChangePrimaryKey($_REQUEST['domain'], $domain);

            rename(DIR_TRADE_STATS . "/{$_REQUEST['domain']}", DIR_TRADE_STATS . "/{$domain}");
            rename(DIR_TRADE_STATS . "/{$_REQUEST['domain']}-in", DIR_TRADE_STATS . "/{$domain}-in");
            rename(DIR_TRADE_STATS . "/{$_REQUEST['domain']}-out", DIR_TRADE_STATS . "/{$domain}-out");
            rename(DIR_TRADE_STATS . "/{$_REQUEST['domain']}-clicks", DIR_TRADE_STATS . "/{$domain}-clicks");
            rename(DIR_TRADE_STATS . "/{$_REQUEST['domain']}-history", DIR_TRADE_STATS . "/{$domain}-history");

            $_REQUEST['domain'] = $domain;
        }

        $_REQUEST = trade_prepare_data($_REQUEST, true);
        unset($_REQUEST['thumbnails']);

        $db->Update($_REQUEST['domain'], $_REQUEST);
    }

    _xTradesEditShow('Trade has been successfully updated');
}

function _xTradesBulkEditShow()
{
    $_REQUEST['domain'] = filter_system_trades($_REQUEST['domain']);

    if( empty($_REQUEST['domain']) )
    {
        return JSON::Warning(array(JSON_KEY_DIALOG_CLOSE => true, JSON_KEY_MESSAGE => 'No valid trades were selected'));
    }

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-bulk-edit.php')));
}

function _xTradesBulkEdit()
{
    

    require_once 'dirdb.php';

    $_REQUEST = trade_prepare_data($_REQUEST, true);
    unset($_REQUEST['thumbnails']);

    foreach( $_REQUEST['flag_update'] as $field => $update )
    {
        if( !$update )
        {
            unset($_REQUEST[$field]);
        }
    }

    if( $_REQUEST['flag_update']['force_instant'] )
    {
        $_REQUEST['force_instant_owed'] = $_REQUEST['force_instant'];
    }

    if( $_REQUEST['flag_update']['force_hourly'] )
    {
        $_REQUEST['force_hourly_owed'] = $_REQUEST['force_hourly'];
    }

    require_once 'dirdb.php';
    $db = new TradeDB();

    foreach( explode(',', $_REQUEST['domain']) as $domain )
    {
        if( string_is_empty($domain) )
        {
            continue;
        }

        $db->Update($domain, $_REQUEST);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'The selected trades have been updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('trades-bulk-edit.php', $_REQUEST)));
}

function _xTradesEnable()
{
    

    $_REQUEST['domain'] = filter_system_trades($_REQUEST['domain']);

    if( empty($_REQUEST['domain']) )
    {
        return JSON::Warning(array(JSON_KEY_DIALOG_CLOSE => true, JSON_KEY_MESSAGE => 'No valid trades were selected'));
    }

    $now = time();

    require_once 'dirdb.php';
    $db = new TradeDB();

    foreach( explode(',', $_REQUEST['domain']) as $domain )
    {
        $trade = $db->Retrieve($domain);

        if( $trade['status'] != STATUS_ACTIVE )
        {
            $db->Update($trade['domain'], array('status' => STATUS_ACTIVE, 'timestamp_autostop' => $now));
        }
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'The selected trades have been enabled',
                        JSON_KEY_JS => 'markEnabled("' . $_REQUEST['domain'] . '");'));
}

function _xTradesDisable()
{
    

    $_REQUEST['domain'] = filter_system_trades($_REQUEST['domain']);

    if( empty($_REQUEST['domain']) )
    {
        return JSON::Warning(array(JSON_KEY_DIALOG_CLOSE => true, JSON_KEY_MESSAGE => 'No valid trades were selected'));
    }

    require_once 'dirdb.php';
    $db = new TradeDB();

    foreach( explode(',', $_REQUEST['domain']) as $domain )
    {
        $trade = $db->Retrieve($domain);

        if( $trade['status'] != STATUS_DISABLED )
        {
            $db->Update($trade['domain'], array('status' => STATUS_DISABLED));
        }
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'The selected trades have been disabled',
                        JSON_KEY_JS => 'markDisabled("' . $_REQUEST['domain'] . '");'));
}

function _xTradesEmailShow()
{
    require_once 'mailer.php';

    $_REQUEST['domain'] = filter_system_trades($_REQUEST['domain']);

    if( empty($_REQUEST['domain']) )
    {
        return JSON::Warning(array(JSON_KEY_DIALOG_CLOSE => true, JSON_KEY_MESSAGE => 'No valid trades were selected'));
    }

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-email.php')));
}

function _xTradesEmail()
{
    global $C, $compiler;

    require_once 'mailer.php';

    

    $v =& Validator::Get();

    $v->Register($_REQUEST[MAILER_KEY_SUBJECT], VT_NOT_EMPTY, "The 'Subject' field is required");
    $v->Register($_REQUEST[MAILER_KEY_BODY], VT_NOT_EMPTY, "The 'Body' field is required");

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'E-mail could not be sent; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    require_once 'dirdb.php';

    require_once 'template.php';
    require_once 'compiler.php';

    $_REQUEST[MAILER_KEY_SUBJECT] = $compiler->Compile($_REQUEST[MAILER_KEY_SUBJECT]);
    $_REQUEST[MAILER_KEY_BODY] = $compiler->Compile($_REQUEST[MAILER_KEY_BODY]);

    $mailer = new Mailer();
    $db = new TradeDB();

    $t = new Template();
    $t->AssignByRef('g_config', $C);

    foreach( explode(',', $_REQUEST['domain']) as $domain )
    {
        $trade = $db->Retrieve($domain);

        if( !empty($trade) && !string_is_empty($trade['email']) )
        {
            $t->AssignByRef('g_trade', $trade);
            $mailer->Mail($_REQUEST, $t, $trade['email'], string_is_empty($trade['nickname']) ? $trade['email'] : $trade['nickname']);
        }
    }

    JSON::Success('E-mail message has been sent to the selected trades');
}

function _xTradesDeleteShow()
{
    $_REQUEST['domain'] = filter_system_trades(explode(',', $_REQUEST['domain']));

    if( empty($_REQUEST['domain']) )
    {
        return JSON::Warning(array(JSON_KEY_DIALOG_CLOSE => true, JSON_KEY_MESSAGE => 'No valid trades were selected'));
    }

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-delete.php')));
}

function _xTradesDelete()
{
    

    trade_delete(explode(',', $_REQUEST['domain']));

    JSON::Success(array(JSON_KEY_DIALOG_CLOSE => true,
                        JSON_KEY_JS => 'markDeleted("' . $_REQUEST['domain'] . '");',
                        JSON_KEY_MESSAGE => 'The selected trades have been deleted'));
}

function _xTradesInfoBox()
{
    require_once 'dirdb.php';

    $db = new TradeDB();
    $trade = $db->Retrieve($_REQUEST['trade']);

    JSON::Success(array(JSON_KEY_HTML => _xIncludeCapture('trades-info-box.php', $trade)));
}

function _xTradesReset()
{
    

    trade_reset(explode(',', $_REQUEST['domain']));

    JSON::Success(array(JSON_KEY_JS => 'markReset("' . $_REQUEST['domain'] . '");',
                        JSON_KEY_MESSAGE => 'The selected trades have been reset'));
}

function _xTradesGraphShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-graph.php', $_REQUEST)));
}

function _xTradesCountriesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-countries.php', $_REQUEST)));
}

function _xTradesReferrersShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-referrers.php', $_REQUEST)));
}

function _xTradesLinksShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-links.php', $_REQUEST)));
}

function _xTradesPagesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-pages.php', $_REQUEST)));
}

function _xTradesLandingsShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-landings.php', $_REQUEST)));
}

function _xTradesLanguagesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-languages.php', $_REQUEST)));
}

function _xTradesDetailedShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-detailed.php', $_REQUEST)));
}

function _xTradesHistoryShow()
{
    require_once 'stats.php';
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trades-historical.php', $_REQUEST)));
}

function _xGlobalSettingsShow()
{
    get_config_defaults();
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('global-settings.php')));
}

function _xGlobalSettingsSave()
{
    

    // Required fields
    $required = array('site_name' => 'Site Name',
                      'traffic_url' => 'Traffic URL',
                      'base_url' => 'TradeX URL',
                      'cookie_domain' => 'Cookie Domain',
                      'cookie_path' => 'Cookie Path',
                      'keyphrase' => 'Passphrase',
                      'email_address' => 'E-mail Address',
                      'email_name' => 'E-mail Name',
                      'date_format' => 'Date Format',
                      'time_format' => 'Time Format',
                      'captcha_min' => 'Code Length (Min)',
                      'captcha_max' => 'Code Length (Max)',
                      'site_name_min' => 'Site Name Length (Min)',
                      'site_name_max' => 'Site Name Length (Max)',
                      'site_description_min' => 'Site Description Length (Min)',
                      'site_description_max' => 'Site Description Length (Max)',
                      'autostop_interval' => 'Autostop Interval',
                      'bonus_prod_low' => 'Good Productivity - Range (Low)',
                      'bonus_prod_high' => 'Good Productivity - Range (High)',
                      'mod_bonus_prod' => 'Good Productivity - Modifier',
                      'bonus_unique_low' => 'Good Percentage of Uniques - Range (Low)',
                      'bonus_unique_high' => 'Good Percentage of Uniques - Range (High)',
                      'mod_bonus_unique' => 'Good Percentage of Uniques - Modifier',
                      'bonus_return_low' => 'Low Return Percentage - Range (Low)',
                      'bonus_return_high' => 'Low Return Percentage - Range (High)',
                      'mod_bonus_return' => 'Low Return Percentage - Modifier',
                      'penalty_proxy_low' => 'Too High or Too Low Proxy Percentage - Range (Low)',
                      'penalty_proxy_high' => 'Too High or Too Low Proxy Percentage - Range (High)',
                      'mod_penalty_proxy' => 'Too High or Too Low Proxy Percentage - Modifier',
                      'penalty_unique_low' => 'Too High or Too Low Unique Percentage - Range (Low)',
                      'penalty_unique_high' => 'Too High or Too Low Unique Percentage - Range (High)',
                      'mod_penalty_unique' => 'Too High or Too Low Unique Percentage - Modifier',
                      'penalty_return_low' => 'Too High Return Percentage - Range (Low)',
                      'penalty_return_high' => 'Too High Return Percentage - Range (High)',
                      'mod_penalty_return' => 'Too High Return Percentage - Modifier',
                      'distrib_forces' => 'Forces',
                      'distrib_main' => 'Main',
                      'distrib_primary' => 'Primary Bonus',
                      'distrib_secondary' => 'Secondary Bonus',
                      'trades_satisfied_url' => 'Trades Satisified URL',
                      'toplist_rebuild_interval' => 'Toplist Build Interval',
                      'fast_click' => 'Fast Click',
                      'count_clicks' => 'Count Clicks');

    $v = Validator::Get();

    $v->Register(FILE_IN_PHP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_IN_PHP . ' script must have 666 permissions');
    $v->Register(FILE_OUT_PHP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_OUT_PHP . ' script must have 666 permissions');
    $v->Register(FILE_IMAGE_PHP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_IMAGE_PHP . ' script must have 666 permissions');

    foreach( $required as $field => $label )
    {
        $v->Register($_REQUEST[$field], VT_NOT_EMPTY, "The '$label' field is required");
    }

    // Check traffic distribution
    $traffic_distrib = $_REQUEST['distrib_forces'] + $_REQUEST['distrib_main'] + $_REQUEST['distrib_primary'] + $_REQUEST['distrib_secondary'];
    $v->Register($traffic_distrib, VT_EQUALS, 'The traffic distribution values must total 100%', 100);

    $v->Register($_REQUEST['keyphrase'], VT_IS_ALPHANUM, "The 'Passphrase' field may contain only English letters and numbers");

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Settings could not be saved; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    write_config($_REQUEST);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Global software settings have been saved!',
                        JSON_KEY_DIALOG => _xIncludeCapture('global-settings.php')));
}

function _xSkimSchemesAddShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('skim-schemes-add.php')));
}

function _xSkimSchemesAdd()
{
    

    require_once 'dirdb.php';

    $db = new SkimSchemeBaseDB();

    $v = Validator::Get();
    $v->Register($_REQUEST['scheme'], VT_NOT_EMPTY, 'The Scheme Name field is required');

    if( !string_is_empty($_REQUEST['scheme']) )
    {
        $v->Register($_REQUEST['scheme'], VT_REGEX_MATCH, 'The Scheme Name may contain only English letters, numbers, dashes and underscores', '~^[a-z0-9\-_]+$~i');
        $v->Register($db->Retrieve($_REQUEST['scheme']), VT_IS_NULL, 'This Scheme Name already exists');
    }

    $v->Register($_REQUEST['cycle_1'], VT_NOT_EMPTY, 'The first cycle value must be filled in');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Skim scheme could not be added; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $db->Add($_REQUEST);

    require_once 'textdb.php';

    $db = new SkimSchemesDynamicDB();
    $db->db_file = DIR_SKIM_SCHEMES_DYNAMIC . '/' . $_REQUEST['scheme'];
    $db->Add($db->Defaults());

    file_write(DIR_SKIM_SCHEMES . '/' .  $_REQUEST['scheme'], file_get_contents(DIR_SKIM_SCHEMES_BASE . '/' . $_REQUEST['scheme']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Skim scheme has been successfully added',
                        JSON_KEY_ROW => _xIncludeCapture('skim-schemes-tr.php', $_REQUEST),
                        JSON_KEY_ITEM_TYPE => 'skim-schemes',
                        JSON_KEY_DIALOG => _xIncludeCapture('skim-schemes-add.php')));
}

function _xSkimSchemesSave()
{
    

    require_once 'dirdb.php';

    $db = new SkimSchemeBaseDB();
    foreach( $_REQUEST['scheme'] as $scheme => $settings)
    {
        if( !string_is_empty($settings['cycle_1']) )
        {
            $db->Update($scheme, $settings);
        }
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Skim scheme settings have been saved'));
}

function _xSkimSchemesDelete()
{
    

    require_once 'dirdb.php';

    $db = new SkimSchemeBaseDB();
    $db->Delete($_REQUEST['scheme']);

    @unlink(DIR_SKIM_SCHEMES . '/' . $_REQUEST['scheme']);
    @unlink(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $_REQUEST['scheme']);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Skim scheme has been deleted',
                        JSON_KEY_ITEM_TYPE => 'skim-schemes',
                        JSON_KEY_ITEM_ID => $_REQUEST['scheme']));
}

function _xSkimSchemesDeleteBulk()
{
    

    require_once 'dirdb.php';

    $db = new SkimSchemeBaseDB();

    foreach( explode(',', $_REQUEST['scheme']) as $scheme )
    {
        $db->Delete($scheme);
        @unlink(DIR_SKIM_SCHEMES . '/' . $scheme);
        @unlink(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $scheme);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Skim schemes have been deleted'));
}

function _xSkimSchemesDynamicEditShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('skim-schemes-dynamic.php', $_REQUEST)));
}



function _xSkimSchemesDynamicEdit()
{
    

    require_once 'textdb.php';

    $db = new SkimSchemesDynamicDB();
    $db->db_file = DIR_SKIM_SCHEMES_DYNAMIC . '/' . $_REQUEST['scheme'];

    $db->Clear();

    for( $i = 0; $i < count($_REQUEST['rule']); $i++ )
    {
        $data = array();
        foreach( $_REQUEST as $field => $value )
        {
            if( is_array($value) )
            {
                $data[$field] = $value[$i];
            }
        }

        if( string_is_empty($data['cycle_1']) )
        {
            $data['cycle_1'] = 70;
        }

        $db->Add($data);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Dynamic skim scheme settings have been saved',
                        JSON_KEY_DIALOG => _xIncludeCapture('skim-schemes-dynamic.php', $_REQUEST)));
}

function _xToplistsAddShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('toplists-add.php')));
}

function _xToplistsAdd()
{
    

    $v = Validator::Get();

    $v->Register($_REQUEST['outfile'], VT_NOT_EMPTY, 'The Output File field is required');
    $v->Register(is_dir($_REQUEST['outfile']), VT_IS_FALSE, 'The Output File value cannot point to a directory');

    if( $_REQUEST['source'] == TOPLIST_SOURCE_FILE )
    {
        $v->Register($_REQUEST['infile'], VT_NOT_EMPTY, 'The Input File field is required');
        $v->Register(is_dir($_REQUEST['infile']), VT_IS_FALSE, 'The Input File value cannot point to a directory');
    }
    else
    {
        $v->Register($_REQUEST['template'], VT_NOT_EMPTY, 'The Template field is required');
    }


    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Toplist could not be added; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $_REQUEST['categories'] = empty($_REQUEST['categories']) ? '' : join(',', $_REQUEST['categories']);
    $_REQUEST['groups'] = empty($_REQUEST['groups']) ? '' : join(',', $_REQUEST['groups']);
    $_REQUEST['req_field'] = serialize($_REQUEST['req_field']);
    $_REQUEST['req_operator'] = serialize($_REQUEST['req_operator']);
    $_REQUEST['req_value'] = serialize($_REQUEST['req_value']);
    $_REQUEST['trade_sources'] = serialize($_REQUEST['trade_sources']);

    require_once 'textdb.php';

    $db = new ToplistsDB();
    $_REQUEST['toplist_id'] = $db->Add($_REQUEST);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Toplist has been successfully added',
                        JSON_KEY_ROW => _xIncludeCapture('toplists-tr.php', $_REQUEST),
                        JSON_KEY_ITEM_TYPE => 'toplists',
                        JSON_KEY_DIALOG => _xIncludeCapture('toplists-add.php')));
}

function _xToplistsEditShow()
{
    require_once 'textdb.php';

    $db = new ToplistsDB();
    $data = $db->Retrieve($_REQUEST['toplist_id']);

    $data['req_field'] = unserialize($data['req_field']);
    $data['req_operator'] = unserialize($data['req_operator']);
    $data['req_value'] = unserialize($data['req_value']);
    $data['trade_sources'] = unserialize($data['trade_sources']);

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('toplists-add.php', $data)));
}

function _xToplistsEdit()
{
    

    $v = Validator::Get();

    $v->Register($_REQUEST['outfile'], VT_NOT_EMPTY, 'The Output File field is required');
    $v->Register(is_dir($_REQUEST['outfile']), VT_IS_FALSE, 'The Output File value cannot point to a directory');

    if( $_REQUEST['source'] == TOPLIST_SOURCE_FILE )
    {
        $v->Register($_REQUEST['infile'], VT_NOT_EMPTY, 'The Input File field is required');
        $v->Register(is_dir($_REQUEST['infile']), VT_IS_FALSE, 'The Input File value cannot point to a directory');
    }
    else
    {
        $v->Register($_REQUEST['template'], VT_NOT_EMPTY, 'The Template field is required');
    }


    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Toplist could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $_REQUEST['categories'] = empty($_REQUEST['categories']) ? '' : join(',', $_REQUEST['categories']);
    $_REQUEST['groups'] = empty($_REQUEST['groups']) ? '' : join(',', $_REQUEST['groups']);
    $_REQUEST['req_field'] = serialize($_REQUEST['req_field']);
    $_REQUEST['req_operator'] = serialize($_REQUEST['req_operator']);
    $_REQUEST['req_value'] = serialize($_REQUEST['req_value']);
    $_REQUEST['trade_sources'] = serialize($_REQUEST['trade_sources']);

    require_once 'textdb.php';

    $db = new ToplistsDB();
    $db->Update($_REQUEST['toplist_id'], $_REQUEST);

    $_REQUEST['trade_sources'] = unserialize($_REQUEST['trade_sources']);
    $_REQUEST['req_field'] = unserialize($_REQUEST['req_field']);
    $_REQUEST['req_operator'] = unserialize($_REQUEST['req_operator']);
    $_REQUEST['req_value'] = unserialize($_REQUEST['req_value']);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Toplist has been successfully updated',
                        JSON_KEY_ROW => _xIncludeCapture('toplists-tr.php', $_REQUEST),
                        JSON_KEY_ITEM_ID => $_REQUEST['toplist_id'],
                        JSON_KEY_ITEM_TYPE => 'toplists',
                        JSON_KEY_DIALOG => _xIncludeCapture('toplists-add.php', $_REQUEST)));
}

function _xToplistsDelete()
{
    

    toplist_delete($_REQUEST['toplist_id']);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Toplist has been deleted',
                        JSON_KEY_ITEM_TYPE => 'toplists',
                        JSON_KEY_ITEM_ID => $_REQUEST['toplist_id']));
}

function _xToplistsDeleteBulk()
{
    

    toplist_delete(explode(',', $_REQUEST['toplist_id']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Toplists have been deleted'));
}

function _xToplistsBuild()
{
    

    require_once 'textdb.php';

    $db = new ToplistsDB();

    foreach( explode(',', $_REQUEST['toplist_id']) as $toplist_id )
    {
        $toplist = $db->Retrieve($toplist_id);

        build_toplist($toplist);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Selected toplists have been built<br />If one or more of your toplists does not get generated, check the logs/error.log file for possible error messages'));
}

function _xToplistsBuildAll()
{
    

    build_all_toplists();

    JSON::Success(array(JSON_KEY_MESSAGE => 'All toplists have been built<br />If one or more of your toplists does not get generated, check the logs/error.log file for possible error messages'));
}

function _xUrlEncodeShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('url-encode.php')));
}

function _xUrlEncode()
{
    $_REQUEST['urls'] = string_format_lf($_REQUEST['urls']);

    $urls = array();
    foreach( explode(STRING_LF_UNIX, $_REQUEST['urls']) as $url )
    {
        switch($_REQUEST['format'])
        {
            case 'base64_encode':
                $url = base64_encode($url);
                break;

            case 'base64_decode':
                $url = base64_decode($url);
                break;

            case 'urlencode':
                $url = urlencode($url);
                break;

            case 'urldecode':
                $url = urldecode($url);
                break;
        }

        $urls[] = $url;
    }

    $_REQUEST['urls'] = join(STRING_LF_UNIX, $urls);

    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('url-encode.php')));
}

function _xTemplatesRecompileAll()
{
    if( ($result = recompile_templates()) !== true )
    {
        return JSON::Warning($result);
    }

    JSON::Success('All templates have been recompiled!');
}

function _xEmailTemplatesLoad()
{
    $template = DIR_TEMPLATES . '/' . file_sanitize($_REQUEST['template']);
    $code = file($template);
    $subject = trim(array_shift($code));
    $body = string_format_lf(join('', $code));

    JSON::Success(array(JSON_KEY_SUBJECT => $subject,
                        JSON_KEY_BODY => $body));
}

function _xEmailTemplatesSave()
{
    global $compiler;

    

    $filename = file_sanitize($_REQUEST['template'], 'tpl', 'tpl');
    $template = DIR_TEMPLATES . "/$filename";
    $compiled = DIR_COMPILED . "/$filename";

    if( !is_writable($template) )
    {
        return JSON::Warning('Template file has incorrect permissions; change to 666 then try again');
    }

    require_once 'compiler.php';

    $template_code = $_REQUEST['subject'] . STRING_LF_UNIX .
                     string_format_lf($_REQUEST['template_code']);

    if( ($code = $compiler->Compile($template_code)) === false )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Template contains errors',
                                   JSON_KEY_WARNINGS => $compiler->GetErrors()));
    }

    $template_code = $_REQUEST['subject'] . STRING_LF_UNIX .
                     string_format_lf($_REQUEST['template_code']);

    file_write($template, $template_code);
    file_write($compiled, $code);
    JSON::Success('Template has been successfully saved');
}

function _xEmailTemplatesReplaceShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('email-templates-replace.php')));
}

function _xEmailTemplatesReplace()
{
    global $compiler;

    

    $v =& Validator::Get();

    $v->Register(is_array($_REQUEST['templates']), VT_NOT_FALSE, 'You must select at least one template for this action');
    $v->Register($_REQUEST['search'], VT_NOT_EMPTY, 'The Search For field is required');

    if( is_array($_REQUEST['templates']) )
    {
        $templates = array();
        foreach( $_REQUEST['templates'] as $template )
        {
            $template = file_sanitize($template, 'tpl,css', 'tpl');
            $filename = DIR_TEMPLATES . "/$template";
            $templates[$template] = $filename;
            $v->Register($filename, VT_FILE_IS_WRITEABLE, "The template file $template has incorrect permissions; change to 666 then try again");
        }
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Search and replace could not be executed; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    require_once 'compiler.php';

    $search = string_format_lf($_REQUEST['search']);
    $replace = string_format_lf($_REQUEST['replace']);
    $replacements = 0;

    foreach( $templates as $template => $filename )
    {
        $template_code = file_get_contents($filename);
        $new_code = str_replace($search, $replace, $template_code);

        // Changes have been made
        if( $new_code != $template_code && ($code = $compiler->Compile($new_code)) !== false )
        {
            file_write($filename, $new_code);
            file_write(DIR_COMPILED . "/$template", $code);
            $replacements++;
        }
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Search and replace has been completed.  Templates updated: ' . format_int_to_string($replacements),
                        JSON_KEY_DIALOG => _xIncludeCapture('email-templates-replace.php')));
}

function _xEmailSignatureShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('email-signature.php')));
}

function _xEmailSignatureSave()
{
    global $compiler;

    

    $v =& Validator::Get();

    $greeting_template = DIR_TEMPLATES . '/email-global-greeting.tpl';
    $signature_template = DIR_TEMPLATES . '/email-global-signature.tpl';
    $greeting_compiled = DIR_COMPILED . '/email-global-greeting.tpl';
    $signature_compiled = DIR_COMPILED . '/email-global-signature.tpl';

    if( !is_writable($greeting_template) || !is_writable($signature_template) )
    {
        return JSON::Warning('Both the email-global-greeting.tpl and email-global-signature.tpl files must have 666 permissions.  Please change the permissions on these files, and then try saving again.');
    }

    require_once 'compiler.php';

    file_write($greeting_compiled, $compiler->Compile($_REQUEST['greeting']));
    file_write($greeting_template, $_REQUEST['greeting']);

    file_write($signature_compiled, $compiler->Compile($_REQUEST['signature']));
    file_write($signature_template, $_REQUEST['signature']);

    JSON::Success(array(JSON_KEY_MESSAGE => 'The e-mail greeting and signature have been saved',
                        JSON_KEY_DIALOG => _xIncludeCapture('email-signature.php')));
}

function _xSiteTemplatesLoad()
{
    $template = DIR_TEMPLATES . '/' . file_sanitize($_REQUEST['template']);
    $code = string_format_lf(file_get_contents($template));

    JSON::Success(array(JSON_KEY_CODE => $code));
}

function _xSiteTemplatesSave()
{
    global $compiler;

    

    $filename = file_sanitize($_REQUEST['template'], 'tpl,css', 'tpl');
    $template = DIR_TEMPLATES . "/$filename";
    $compiled = DIR_COMPILED . "/$filename";

    if( !is_writeable($template) )
    {
        return JSON::Warning('Template file has incorrect permissions; change to 666 then try again');
    }

    require_once 'compiler.php';

    if( ($code = $compiler->Compile($_REQUEST['template_code'])) === false )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Template contains errors',
                                   JSON_KEY_WARNINGS => $compiler->GetErrors()));
    }

    file_write($template, $_REQUEST['template_code']);
    file_write($compiled, $code);

    JSON::Success('Template has been successfully saved');
}

function _xSiteTemplatesReplaceShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('site-templates-replace.php')));
}

function _xSiteTemplatesReplace()
{
    global $compiler;

    

    $v =& Validator::Get();

    $v->Register(is_array($_REQUEST['templates']), VT_NOT_FALSE, 'You must select at least one template for this action');
    $v->Register($_REQUEST['search'], VT_NOT_EMPTY, 'The Search For field is required');

    if( is_array($_REQUEST['templates']) )
    {
        $templates = array();
        foreach( $_REQUEST['templates'] as $template )
        {
            $template = file_sanitize($template, 'tpl,css', 'tpl');
            $filename = DIR_TEMPLATES . "/$template";
            $templates[$template] = $filename;
            $v->Register($filename, VT_FILE_IS_WRITEABLE, "The template file $template has incorrect permissions; change to 666 then try again");
        }
    }

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Search and replace could not be executed; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    require_once 'compiler.php';

    $search = string_format_lf($_REQUEST['search']);
    $replace = string_format_lf($_REQUEST['replace']);
    $replacements = 0;

    foreach( $templates as $template => $filename )
    {
        $template_code = file_get_contents($filename);
        $new_code = str_replace($search, $replace, $template_code);

        // Changes have been made
        if( $new_code != $template_code && ($code = $compiler->Compile($new_code)) !== false )
        {
            file_write($filename, $new_code);
            file_write(DIR_COMPILED . "/$template", $code);

            $replacements++;
        }
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Search and replace has been completed.  Templates updated: ' . format_int_to_string($replacements),
                        JSON_KEY_DIALOG => _xIncludeCapture('site-templates-replace.php')));
}

function _xGroupsShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('groups.php')));
}

function _xGroupsSave()
{
    

    $v =& Validator::Get();
    $v->Register(FILE_GROUPS, VT_FILE_IS_WRITEABLE, 'The ' . FILE_GROUPS . ' file has incorrect permissions; change them to 666');
    $v->Register($_REQUEST['groups'], VT_REGEX_NO_MATCH, 'Group names may not contain commas', '~,~');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Groups could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    file_write(FILE_GROUPS, string_remove_blank_lines($_REQUEST['groups']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Groups have been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('groups.php')));
}

function _xCategoriesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('categories.php')));
}

function _xCategoriesSave()
{
    

    $v =& Validator::Get();
    $v->Register(FILE_CATEGORIES, VT_FILE_IS_WRITEABLE, 'The ' . FILE_CATEGORIES . ' file has incorrect permissions; change them to 666');
    $v->Register($_REQUEST['categories'], VT_REGEX_NO_MATCH, 'Category names may not contain commas', '~,~');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Categories could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    file_write(FILE_CATEGORIES, string_remove_blank_lines($_REQUEST['categories']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Categories have been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('categories.php')));
}

function _xCountriesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('countries.php')));
}

function _xCountriesSave()
{
    

    global $geoip_country_codes, $geoip_country_names;

    require_once 'geoip-utility.php';

    $v =& Validator::Get();
    $v->Register(FILE_COUNTRIES, VT_FILE_IS_WRITEABLE, 'The ' . FILE_COUNTRIES . ' file has incorrect permissions; change them to 666');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Country settings could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $fp = fopen(FILE_COUNTRIES, 'r+');
    flock($fp, LOCK_EX);
    fwrite($fp, str_pad(join('|', array($_REQUEST['weight_good'], $_REQUEST['weight_normal'], $_REQUEST['weight_bad'])), RECORD_SIZE_COUNTRY_WEIGHT), RECORD_SIZE_COUNTRY_WEIGHT);
    foreach( $geoip_country_codes as $i => $code )
    {
        $quality = 1;
        if( in_array($code, $_REQUEST['countries_good']) )
        {
            $quality = 0;
        }
        else if( in_array($code, $_REQUEST['countries_bad']) )
        {
            $quality = 2;
        }

        fwrite($fp, str_pad($quality, RECORD_SIZE_COUNTRY), RECORD_SIZE_COUNTRY);
    }
    flock($fp, LOCK_UN);
    fclose($fp);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Country settings have been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('countries.php')));
}

function _xBlacklistShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('blacklist.php')));
}

function _xBlacklistSave()
{
    

    $v =& Validator::Get();
    $v->Register(FILE_BLACKLIST_DNS, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_DNS . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_DOMAIN, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_DOMAIN . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_EMAIL, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_EMAIL . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_SERVER_IP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_SERVER_IP . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_USER_IP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_USER_IP . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_CONTENT, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_CONTENT . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_BLACKLIST_HEADER, VT_FILE_IS_WRITEABLE, 'The ' . FILE_BLACKLIST_HEADER . ' file has incorrect permissions; change them to 666');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Blacklist could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    foreach( $_REQUEST['bl'] as $file => $items )
    {
        $file = DIR_BLACKLIST . '/' . file_sanitize($file);
        file_write($file, string_remove_blank_lines($items) . STRING_LF_UNIX);
    }

    JSON::Success(array(JSON_KEY_MESSAGE => 'Blacklist has been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('blacklist.php')));
}

function _xSearchEnginesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('search-engines.php')));
}

function _xSearchEnginesSave()
{
    

    $v =& Validator::Get();
    $v->Register(FILE_SEARCH_ENGINES, VT_FILE_IS_WRITEABLE, 'The ' . FILE_SEARCH_ENGINES . ' file has incorrect permissions; change them to 666');
    $v->Register(FILE_IN_PHP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_IN_PHP . ' script must have 666 permissions');
    $v->Register(FILE_OUT_PHP, VT_FILE_IS_WRITEABLE, 'The ' . FILE_OUT_PHP . ' script must have 666 permissions');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Search engines could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    $domains = array();
    $params = array();
    $engines = string_remove_blank_lines(str_replace("'", '', $_REQUEST['engines']));

    foreach( explode(STRING_LF_UNIX, $engines) as $line )
    {
        list($domain, $param) = explode('|', $line);
        $domains[] = $domain;
        $params[] = $param;
    }

    // Update the in.php file
    $in = file_get_contents(FILE_IN_PHP);
    $in = preg_replace(array('~/\*#<ENGINES>\*/.*?/\*#</ENGINES>\*/~',
                             '~/\*#<ENGINE_PARAMS>\*/.*?/\*#</ENGINE_PARAMS>\*/~'),
                       array('/*#<ENGINES>*/' . (count($domains) ? "'" . join("','", $domains) . "'" : '') . '/*#</ENGINES>*/',
                             '/*#<ENGINE_PARAMS>*/' . (count($params) ? "'" . join("','", $params) . "'" : '') . '/*#</ENGINE_PARAMS>*/'),
                       $in);

    file_write(FILE_IN_PHP, $in, null);


    // Update the search_engines file
    file_write(FILE_SEARCH_ENGINES, $engines);

    JSON::Success(array(JSON_KEY_MESSAGE => 'Search engines have been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('search-engines.php')));
}

function _xTradeRulesShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('trade-rules.php')));
}

function _xTradeRulesSave()
{
    

    $v =& Validator::Get();
    $v->Register(FILE_TRADE_RULES, VT_FILE_IS_WRITEABLE, 'The ' . FILE_TRADE_RULES . ' file has incorrect permissions; change them to 666');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Trade rules could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    file_write(FILE_TRADE_RULES, string_remove_blank_lines($_REQUEST['rules']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Trade rules have been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('trade-rules.php')));
}

function _xChangeLoginShow()
{
    JSON::Success(array(JSON_KEY_DIALOG => _xIncludeCapture('change-login.php')));
}

function _xChangeLogin()
{
    

    $v =& Validator::Get();

    list($username, $password) = explode('|', file_first_line(FILE_CP_USER));

    $v->Register($_REQUEST['username'], VT_NOT_EMPTY, 'The Username field is required');
    $v->Register($_REQUEST['password'], VT_NOT_EMPTY, 'The Password field is required');
    $v->Register(sha1($_REQUEST['old_password']), VT_EQUALS, 'The Old Password is invalid', $password);
    $v->Register($_REQUEST['username'] . $_REQUEST['password'], VT_NOT_CONTAINS, 'The | character is not allowed in your username or password', '|');
    $v->Register(FILE_CP_USER, VT_FILE_IS_WRITEABLE, 'The ' . FILE_CP_USER . ' file has incorrect permissions; change them to 666');

    if( !$v->Validate() )
    {
        return JSON::Warning(array(JSON_KEY_MESSAGE => 'Login information could not be updated; please fix the following items',
                                   JSON_KEY_WARNINGS => $v->GetErrors()));
    }

    file_write(FILE_CP_USER, $_REQUEST['username'] . '|' . sha1($_REQUEST['password']));

    JSON::Success(array(JSON_KEY_MESSAGE => 'Control panel login has been successfully updated',
                        JSON_KEY_DIALOG => _xIncludeCapture('change-login.php')));
}

function _xIncludeCapture($file, $original = null, $vars = array())
{
    extract($vars, EXTR_SKIP);

    $item = string_htmlspecialchars($original);

    ob_start();
    include $file;
    return ob_get_clean();
}

function _xFunctionMissing()
{
    JSON::Error('Function argument was missing from the request');
}


?>
