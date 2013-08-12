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

// To fix this error: Warning: date(): It is not safe to rely on the system's timezone settings.
// Uncomment the line below and enter your desired time zone. See: http://php.net/manual/en/timezones.php for a list of zones
//date_default_timezone_set('America/Chicago');

define('LIC_DOMAIN', 'Any');
define('LIC_USERNAME', '');
define('LIC_KEY', '');
define('LIC_POWERED', false);


// Directories
define('DIR_TRADES', DIR_DATA . '/trades');
define('DIR_SYSTEM', DIR_DATA . '/system');
define('DIR_BLACKLIST', DIR_DATA . '/blacklist');
define('DIR_TIMES', DIR_DATA . '/times');
define('DIR_SKIM_SCHEMES', DIR_DATA . '/skim_schemes');
define('DIR_SKIM_SCHEMES_BASE', DIR_SKIM_SCHEMES . '/base');
define('DIR_SKIM_SCHEMES_DYNAMIC', DIR_SKIM_SCHEMES . '/dynamic');


// Files
define('FILE_IN_PHP', DIR_BASE . '/in.php');
define('FILE_OUT_PHP', DIR_BASE . '/out.php');
define('FILE_IMAGE_PHP', DIR_BASE . '/image.php');
define('FILE_TRADE_RULES', DIR_DATA . '/trade_rules');
define('FILE_SEARCH_ENGINES', DIR_DATA . '/search_engines');
define('FILE_SEARCH_TERMS', DIR_DATA . '/search_terms');
define('FILE_COUNTRIES', DIR_DATA . '/countries');
define('FILE_GROUPS', DIR_DATA . '/groups');
define('FILE_CATEGORIES', DIR_DATA . '/categories');
define('FILE_TOPLISTS', DIR_DATA . '/toplists');
define('FILE_CAPTCHAS', DIR_DATA . '/captchas');
define('FILE_OUTLIST_MAIN', DIR_DATA . '/outlist_main');
define('FILE_OUTLIST_PRIMARY', DIR_DATA . '/outlist_primary');
define('FILE_OUTLIST_SECONDARY', DIR_DATA . '/outlist_secondary');
define('FILE_OUTLIST_FORCES', DIR_DATA . '/outlist_forces');
define('FILE_REGISTER_CONFIRMS', DIR_DATA . '/register_confirms');
define('FILE_PASSWORD_CONFIRMS', DIR_DATA . '/password_confirms');
define('FILE_NETWORK_SITES', DIR_DATA . '/network_sites');
define('FILE_NETWORK_SITES_CACHE', DIR_DATA . '/network_sites_cache');
define('FILE_NETWORK_SITES_VALUES', DIR_DATA . '/network_sites_values');
define('FILE_NETWORK_SYNC_CACHE', DIR_DATA . '/network_sync_cache');
define('FILE_TIME_TOPLISTS', DIR_TIMES . '/toplists');
define('FILE_TIME_STATS', DIR_TIMES . '/stats');
define('FILE_BLACKLIST_EMAIL', DIR_BLACKLIST . '/email');
define('FILE_BLACKLIST_DOMAIN', DIR_BLACKLIST . '/domain');
define('FILE_BLACKLIST_USER_IP', DIR_BLACKLIST . '/user_ip');
define('FILE_BLACKLIST_SERVER_IP', DIR_BLACKLIST . '/server_ip');
define('FILE_BLACKLIST_DNS', DIR_BLACKLIST . '/dns');
define('FILE_BLACKLIST_CONTENT', DIR_BLACKLIST . '/content');
define('FILE_BLACKLIST_WORD', DIR_BLACKLIST . '/word');
define('FILE_BLACKLIST_HEADER', DIR_BLACKLIST . '/header');
define('FILE_NEW_TRADE_DEFAULTS', DIR_DATA . '/new_trade_defaults');
define('FILE_LOG_UPDATE', DIR_LOGS . '/update.log');
define('FILE_HISTORY', DIR_DATA . '/history');
define('FILE_LOG_CRON', DIR_LOGS . '/cron.log');
define('FILE_LOG_GRABBER', DIR_LOGS . '/grabber.log');


// String
define('STRING_LF_UNIX', "\n");
define('STRING_LF_WINDOWS', "\r\n");
define('STRING_LF_MAC', "\r");
define('STRING_BLANK', '');


// Regular expressions
define('REGEX_SITE_TEMPLATES', '~^(?!email).*?(\.tpl$|\.css$)~');
define('REGEX_EMAIL_TEMPLATES', '~^email-(?!global)~');
define('REGEX_DATE', '~^\d\d\d\d-\d\d-\d\d$~');
define('REGEX_DATETIME', '~^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$~');
define('REGEX_EMAIL', '~^[\w\d][\w\d\,\.\-]*\@([\w\d\-]+\.)+([a-zA-Z]+)$~i');
define('REGEX_HTTP_URL', '~^http(s)?://[\w-]+\.[\w-]+(\S+)?$~i');
define('REGEX_TIME', '~^\d\d:\d\d:\d\d$~');
define('REGEX_TRAILING_SLASH', '~/+$~');
define('REGEX_CP_FUNCTION', '~^(_x[a-zA-Z0-9_]+)(\((.*?)\))?~');
define('REGEX_NOT_STATS_LOG', '~$(?<!-clicks|-in|-out|-history)~');
define('REGEX_IS_STATS_LOG', '~$(?<=-clicks|-in|-out)~');


// Directory functions
define('DIR_READ_ALL', 0x00000001);
define('DIR_READ_FILES', 0x00000002);
define('DIR_READ_DIRECTORIES', 0x00000004);


// Trade statuses
define('STATUS_UNCONFIRMED', 'Unconfirmed');
define('STATUS_NEW', 'New');
define('STATUS_ACTIVE', 'Active');
define('STATUS_AUTOSTOPPED', 'Autostopped');
define('STATUS_DISABLED', 'Disabled');


// Force types
define('FORCE_INSTANT', 'I');
define('FORCE_HOURLY', 'H');


// Points
define('POINTS_MAIN', 'Main');
define('POINTS_PRIMARY_BONUS', 'Primary Bonus');
define('POINTS_SECONDARY_BONUS', 'Secondary Bonus');
define('POINTS_FORCE', 'Force');


// Records
define('RECORD_SIZE_COUNTRY_WEIGHT', 20);
define('RECORD_SIZE_COUNTRY', 1);
define('STATS_PER_RECORD', 22);
define('RECORD_SIZE_STATS', STATS_PER_RECORD * 4);


// Misc
define('URL_DOWNLOAD', 'http://www.jmbsoft.com/download-tradex.php');
define('TOPLIST_SOURCE_FILE', 'File');
define('TOPLIST_SOURCE_TEMPLATE', 'Template');
define('SKIM_SCHEME_DEFAULT', 'default');
define('HOURS_PER_DAY', 24);
define('MINUTES_PER_DAY', 1440);
define('MINUTES_PER_HOUR', 60);
define('SECONDS_PER_DAY', 86400);
define('SECONDS_PER_HOUR', 3600);
define('SECONDS_PER_MINUTE', 60);


function grab_thumbs($trade, $url, $trigger_strings = '')
{
    global $C;

    require_once 'http.php';

    $images = array();
    $grabbed = 0;
    $trigger_strings = str_replace(',', '|', (empty($trigger_strings) ? $C['thumb_trigger_strings'] : "$trigger_strings|{$C['thumb_trigger_strings']}"));
    $http = new HTTP();
    $http->connect_timeout = 5;
    $http->read_timeout = 10;

    if( !$http->GET($url) )
    {
        return null;
    }

    if( preg_match_all('~<img.*?>~msi', $http->body, $matches) )
    {
        foreach( $matches[0] as $match )
        {
            $attributes = array();

            if( preg_match_all('~(?P<name>[a-z]+)\s*=\s*[\'"]?(?P<value>.*?)[\'"]?(?=\s*[a-z]+\s*=|\s*/?>)~i', $match, $attrs, PREG_SET_ORDER) )
            {
                foreach( $attrs as $attr )
                {
                    $attributes[strtolower(trim($attr['name']))] = $attr['value'];
                }

                $bad_height = isset($attributes['height']) && ($attributes['height'] < $C['thumb_height_min'] || $attributes['height'] > $C['thumb_height_max']);
                $bad_width = isset($attributes['width']) && ($attributes['width'] < $C['thumb_width_min'] || $attributes['width'] > $C['thumb_width_max']);
                $bad_src = !isset($attributes['src']) || !preg_match('~\.(jpg|jpeg)~i', $attributes['src']) || !preg_match('~' . $trigger_strings . '~i', $attributes['src']);
                if( $bad_width || $bad_height || $bad_src )
                {
                    continue;
                }

                $attributes['src'] = $http->_relative_to_absolute($url, $attributes['src']);

                if( $http->GET($attributes['src'], $url, true) )
                {
                    $thumb_file = tempnam(DIR_TEMP, 'thumb');
                    file_write($thumb_file, $http->body);

                    $size = getimagesize($thumb_file);

                    if( $size != false && $size[0] >= $C['thumb_width_min'] && $size[0] <= $C['thumb_width_max'] && $size[1] >= $C['thumb_height_min'] && $size[1] <= $C['thumb_height_max'] )
                    {
                        $grabbed++;
                        rename($thumb_file, DIR_THUMBS . "/$trade-$grabbed.jpg");
                        resize_thumb(DIR_THUMBS . "/$trade-$grabbed.jpg", $size, $C['thumb_resize_width'], $C['thumb_resize_height']);
                    }
                    else
                    {
                        @unlink($thumb_file);
                    }
                }

                if( $grabbed >= $C['thumb_grab_amount'] )
                {
                    break;
                }
            }
        }
    }

    return $grabbed;
}

function resize_thumb($thumbnail, $imgsize, $width, $height)
{
    global $C;

    // Image is already properly sized
    if( $imgsize[0] == $width && $imgsize[1] == $height )
    {
        return;
    }

    // Resize and crop with ImageMagick
    if( $C['have_magick'] )
    {
        shell_exec(
            $C['magick_mogrify_path'] . ' ' .
            $C['magick_mogrify_options'] . ' ' .
            '-resize "' . $width . 'x' . $height . '^" ' .
            '-gravity center ' .
            '-crop "' . $width . 'x' . $height . '+0+0" ' .
            escapeshellarg($thumbnail)
        );
    }

    // Resize and crop with GD
    else if( $C['have_gd'] )
    {
        $orig_width = $imgsize[0];
        $orig_height = $imgsize[1];

        $orig_ratio = $orig_width / $orig_height;
        $new_ratio = $width / $height;

        // Trim width
        if( $orig_ratio > $new_ratio )
        {
            $crop_width = round($orig_height * $new_ratio);
            $crop_height = $orig_height;
            $src_y = 0;
            $src_x = round(($orig_width - $crop_width) / 2);
        }
        // Trim height
        else
        {
            $crop_width = $orig_width;
            $crop_height = round($orig_width * ($height / $width));
            $src_x = 0;
            $src_y = round(($orig_height - $crop_height) / 2);
        }

        $img_src = @imagecreatefromjpeg($thumbnail);
        $img_dst = @imagecreatetruecolor($width, $height);

        // Resize and crop
        @imagecopyresampled($img_dst, $img_src, 0, 0, $src_x, $src_y, $width, $height, $crop_width, $crop_height);
        @imagedestroy($img_src);

        // Save the image back to disk
        @imagejpeg($img_dst, $thumbnail, 80);
        @imagedestroy($img_dst);
    }
}



// Globals for updates
$g_stats_minute = false;
$g_stats_hourly = false;
$g_stats_daily = false;

function stats_get_all_files()
{
    $sys = glob(DIR_SYSTEM_STATS . '/*');
    $trade = glob(DIR_TRADE_STATS . '/*');

    if( !is_array($sys) ) $sys = array();
    if( !is_array($trade) ) $trade = array();

    return preg_grep(REGEX_NOT_STATS_LOG, array_merge($sys, $trade));
}

function logs_get_all_files()
{
    $sys = glob(DIR_SYSTEM_STATS . '/*');
    $trade = glob(DIR_TRADE_STATS . '/*');

    if( !is_array($sys) ) $sys = array();
    if( !is_array($trade) ) $trade = array();

    return preg_grep(REGEX_IS_STATS_LOG, array_merge($sys, $trade));
}

function generate_outlists($stats)
{
    global $C, $g_outlist_key, $g_stats_hourly, $g_stats_daily, $g_stats_minute;

    $now = time();

    require_once 'dirdb.php';
    $db = new TradeDB();

    // Get country weights
    $fp = fopen(FILE_COUNTRIES, 'r');
    $weights = explode('|', trim(fread($fp, RECORD_SIZE_COUNTRY_WEIGHT)));
    fclose($fp);

    // Calculate average click again
    $ca_avg = count($stats) > 1 ? $stats['total']['24h'][11] / count($stats) - 1 : 0;

    // Store points calculated for each eligible trade
    $points = array();

    // Store trades with forces
    $forces = array();

    foreach( $stats as $trade => $ts )
    {
        $settings = $db->Retrieve($trade);

        if( !empty($settings) )
        {
            // See if a new trade has met the rules for becoming active
            if( $settings['status'] == STATUS_NEW )
            {
                $prod_24 = $ts['24h'][1] > 0 ? $ts['24h'][7] / $ts['24h'][1] * 100 : 0;

                if( $settings['start_raws'] > 0 || $settings['start_clicks'] > 0 || $settings['start_prod'] > 0 )
                {
                    if( $ts['24h'][1] >= $settings['start_raws'] && $ts['24h'][7] >= $settings['start_clicks'] && $prod_24 >= $settings['start_prod'] )
                    {
                        $settings['status'] = STATUS_ACTIVE;
                        $settings['timestamp_autostop'] = $now;

                        $db->Update($trade, array('status' => STATUS_ACTIVE,
                                                  'start_raws' => 0,
                                                  'start_clicks' => 0,
                                                  'start_prod' => 0,
                                                  'timestamp_autostop' => $now));
                    }
                    else
                    {
                        // New trade has not yet met the minimum requirements
                        continue;
                    }
                }
                else
                {
                    // New trades must be manually activated by administrator (start_raws, start_clicks, start_prod all set to 0)
                    continue;
                }
            }


            // Check if autostopped trade can be re-activated
            if( $C['flag_reactivate_autostopped'] && $settings['status'] == STATUS_AUTOSTOPPED )
            {
                $prod_24 = $ts['24h'][1] > 0 ? $ts['24h'][7] / $ts['24h'][1] * 100 : 0;
                if( $ts['24h'][1] >= $settings['min_raws'] && $ts['24h'][7] >= $settings['min_clicks'] && $prod_24 >= $settings['min_prod'] )
                {
                    $db->Update($trade, array('status' => STATUS_ACTIVE, 'timestamp_autostop' => $now));
                    $settings['status'] = STATUS_ACTIVE;
                    $settings['timestamp_autostop'] = $now;
                    continue;
                }
            }


            // Process active trades
            if( $settings['status'] == STATUS_ACTIVE )
            {
                // If the autostop interval has elapsed, start checking autostop rules
                if( $now - $settings['timestamp_autostop'] >= $C['autostop_interval'] * SECONDS_PER_HOUR )
                {
                    $prod_24 = $ts['24h'][1] > 0 ? $ts['24h'][7] / $ts['24h'][1] * 100 : 0;
                    if( $ts['24h'][1] < $settings['min_raws'] || $ts['24h'][7] < $settings['min_clicks'] || $prod_24 < $settings['min_prod'] )
                    {
                        $db->Update($trade, array('status' => STATUS_AUTOSTOPPED));
                        continue;
                    }
                }


                // Updates to apply to the trade settings
                $updates = array();


                // If pushing ...
                if( $settings['push_to'] > 0 )
                {
                    // Amount achieved, so disable the push value
                    if( $ts['24h'][1] >= $settings['push_to'] )
                    {
                        $updates['push_to'] = $settings['push_to'] = 0;
                    }

                    // Amount not achieved, so use the push weight AND calculate in, out, and clicks to put them into main points
                    else
                    {
                        $settings['trade_weight'] = $settings['push_weight'];

                        // Calculate amount needed to reach the push
                        $in_needed = $settings['push_to'] - $ts['24h'][1];

                        // Set traffic quality
                        $ts['24h'][4] = $ts['60m'][4] = 75;
                        $ts['24h'][5] = $ts['60m'][5] = 25;
                        $ts['24h'][6] = $ts['60m'][6] = 0;

                        $ts['24h'][1] = $settings['push_to'];
                        $ts['24h'][2] = round($settings['push_to'] * 0.95);
                        $ts['24h'][7] = round($settings['push_to'] * 1.25);
                        $ts['24h'][15] = round($settings['push_to'] * 0.80);

                        $ts['60m'][1] = round($settings['push_to'] / 60);
                        $ts['60m'][2] = round($settings['push_to'] / 60 * 0.95);
                        $ts['60m'][7] = round($settings['push_to'] / 60 * 1.25);
                        $ts['60m'][15] = round($settings['push_to'] / 60 * 0.80);

                        //pow($i_uniq_24, 0.333) * $ts['24h'][7] * 0.65 / $ts['24h'][15] + pow($i_uniq_60, 0.333) * $ts['60m'][7] * 0.35 / $ts['60m'][15];
                    }
                }


                // If a force is set
                if( $settings['force_instant'] > 0 || $settings['force_hourly'] > 0 )
                {
                    // Handle instant force
                    if( $settings['force_instant'] > 0 )
                    {
                        $settings['force_instant_owed'] = $updates['force_instant_owed'] = max(0, $settings['force_instant_owed'] - $ts['1m'][21]);

                        if( $settings['force_instant_owed'] == 0 )
                        {
                            $settings['force_instant'] = $updates['force_instant'] = 0;
                            $settings['force_instant_owed'] = $updates['force_instant_owed'] = 0;
                            $settings['flag_force_instant_high'] = $updates['flag_force_instant_high'] = 0;
                        }
                    }

                    // Handle hourly force
                    if( $settings['force_hourly'] > 0 )
                    {
                        // See if the hourly force has expired
                        if( !empty($settings['force_hourly_end']) && $now >= strtotime($settings['force_hourly_end']) )
                        {
                            $settings['force_hourly'] = $updates['force_hourly'] = 0;
                            $settings['force_hourly_owed'] = $updates['force_hourly_owed'] = 0;
                            $settings['force_hourly_end'] = $updates['force_hourly_end'] = STRING_BLANK;
                        }


                        // Hourly update, so reset the amount owed to the amount configured
                        if( $g_stats_hourly )
                        {
                            $updates['force_hourly_owed'] = $settings['force_hourly_owed'] = $settings['force_hourly'];
                        }

                        // Deduct the amount received in the past minute
                        else if( $g_stats_minute )
                        {
                            $settings['force_hourly_owed'] = $updates['force_hourly_owed'] = max(0, $settings['force_hourly_owed'] - $ts['1m'][22]);
                        }
                    }
                }


                // Update trade settings if necessary
                if( !empty($updates) )
                {
                    $db->Update($trade, $updates);
                }


                // After updating forces, see if a force is still set
                $force_set = $settings['force_instant'] > 0 || $settings['force_hourly'] > 0;

                // Flag to indicate if this trade has a high priority instant force set
                $high_priority_instant = $settings['force_instant'] > 0 && $settings['flag_force_instant_high'];


                // Add to force list
                if( $force_set && !$high_priority_instant )
                {
                    $force_instant_pct = $settings['force_instant'] > 0 ? $settings['force_instant_owed'] / $settings['force_instant'] * 100 : 0;
                    $force_hourly_pct = $settings['force_hourly'] > 0 ? $settings['force_hourly_owed'] / $settings['force_hourly'] * 100 : 0;

                    $settings['force_type'] = FORCE_INSTANT;
                    $settings['max_owed'] = $settings['force_instant_owed'];
                    if( $force_hourly_pct > $force_instant_pct )
                    {
                        $settings['force_type'] = FORCE_HOURLY;
                        $settings['max_owed'] = $settings['force_hourly_owed'];
                    }

                    if( $settings['max_owed'] < 1 )
                    {
                        unset($settings['max_owed']);
                    }
                    else
                    {
                        $forces[$trade] = array('trade' => $settings,
                                                POINTS_FORCE => max($force_hourly_pct, $force_instant_pct));
                    }
                }


                // Max owed for high priority forces
                if( $force_set && $high_priority_instant )
                {
                    $settings['max_owed'] = $settings['force_instant_owed'];
                }


                // Check caps only if a high priority instant force is not set
                //    AND
                // A push is not set
                if( !$high_priority_instant && !$settings['push_to'] )
                {
                    // Trades that have exceeded their max out are not eligible for the outlist
                    if( $settings['max_out'] > 0 && $ts['24h'][15] >= $settings['max_out'] )
                    {
                        continue;
                    }

                    // Trades that have exceeded their hourly cap are not eligible for the outlist
                    $ret_60 = $ts['60m'][1] > 0 ? $ts['60m'][15] / $ts['60m'][1] * 100 : 0;
                    if( $settings['hourly_cap'] > 0 && $ret_60 >= $settings['hourly_cap'] )
                    {
                        continue;
                    }

                    // Trades that have exceeded their daily cap are not eligible for the outlist
                    $ret_24 = $ts['24h'][1] > 0 ? $ts['24h'][15] / $ts['24h'][1] * 100 : 0;
                    if( $settings['daily_cap'] > 0 && $ret_60 >= $settings['daily_cap'] )
                    {
                        continue;
                    }
                }
            }
            else
            {
                // Disabled and unconfirmed trades are not eligible to be in the outlists
                continue;
            }


            // Calculate 24h incoming traffic quality
            $i_ctry_tot_24 = $ts['24h'][4] + $ts['24h'][5] + $ts['24h'][6];
            $i_raw_24 = $i_uniq_24 = 0;
            if( $i_ctry_tot_24 > 0 )
            {
                $i_ctry_g_pct_24 = $ts['24h'][4] / $i_ctry_tot_24;
                $i_ctry_n_pct_24 = $ts['24h'][5] / $i_ctry_tot_24;
                $i_ctry_b_pct_24 = $ts['24h'][6] / $i_ctry_tot_24;

                $i_raw_g_24 = $i_ctry_g_pct_24 * $ts['24h'][1];
                $i_raw_n_24 = $i_ctry_n_pct_24 * $ts['24h'][1];
                $i_raw_b_24 = $i_ctry_b_pct_24 * $ts['24h'][1];

                $i_uniq_g_24 = $i_ctry_g_pct_24 * $ts['24h'][2];
                $i_uniq_n_24 = $i_ctry_n_pct_24 * $ts['24h'][2];
                $i_uniq_b_24 = $i_ctry_b_pct_24 * $ts['24h'][2];

                $i_raw_24 = round(($i_raw_g_24 * $weights[0]) + ($i_raw_n_24 * $weights[1]) + ($i_raw_b_24 * $weights[2]));
                $i_uniq_24 = round(($i_uniq_g_24 * $weights[0]) + ($i_uniq_n_24 * $weights[1]) + ($i_uniq_b_24 * $weights[2]));
            }


            // Calculate 60m incoming traffic quality
            $i_ctry_tot_60 = $ts['60m'][4] + $ts['60m'][5] + $ts['60m'][6];
            $i_raw_60 = $i_uniq_60 = 0;
            if( $i_ctry_tot_60 > 0 )
            {
                $i_ctry_g_pct_60 = $ts['60m'][4] / $i_ctry_tot_60;
                $i_ctry_n_pct_60 = $ts['60m'][5] / $i_ctry_tot_60;
                $i_ctry_b_pct_60 = $ts['60m'][6] / $i_ctry_tot_60;

                $i_raw_g_60 = $i_ctry_g_pct_60 * $ts['60m'][1];
                $i_raw_n_60 = $i_ctry_n_pct_60 * $ts['60m'][1];
                $i_raw_b_60 = $i_ctry_b_pct_60 * $ts['60m'][1];

                $i_uniq_g_60 = $i_ctry_g_pct_60 * $ts['60m'][2];
                $i_uniq_n_60 = $i_ctry_n_pct_60 * $ts['60m'][2];
                $i_uniq_b_60 = $i_ctry_b_pct_60 * $ts['60m'][2];

                $i_raw_60 = round(($i_raw_g_60 * $weights[0]) + ($i_raw_n_60 * $weights[1]) + ($i_raw_b_60 * $weights[2]));
                $i_uniq_60 = round(($i_uniq_g_60 * $weights[0]) + ($i_uniq_n_60 * $weights[1]) + ($i_uniq_b_60 * $weights[2]));
            }

            // Calculate max owed
            if( !isset($settings['max_owed']) )
            {
                $age = $now - $settings['timestamp_autostop'];
                if( $age < SECONDS_PER_DAY )
                {
                    $avg_in_minute = $ts['24h'][1] / ($age / SECONDS_PER_MINUTE);
                }
                else
                {
                    $avg_in_minute = $ts['24h'][1] / MINUTES_PER_DAY;
                }

                if( $ts['24h'][1] > $ts['24h'][15] )
                {
                    $owed_24 = ($ts['24h'][1] - $ts['24h'][15]) * ($settings['daily_cap'] / 100);
                }
                else
                {
                    $return_24 = $ts['24h'][1] > 0 ? $ts['24h'][15] / $ts['24h'][1] * 100 : 0;
                    $owed_24 = $return_24 >= $settings['daily_cap'] ? 0 : ($settings['daily_cap'] - $return_24) / 100 * $ts['24h'][15];
                }

                if( $ts['60m'][1] > $ts['60m'][15] )
                {
                    $owed_60 = ($ts['60m'][1] - $ts['60m'][15]) * ($settings['hourly_cap'] / 100);
                }
                else
                {
                    $return_60 = $ts['60m'][1] > 0 ? $ts['60m'][15] / $ts['60m'][1] * 100 : 0;
                    $owed_60 = $return_60 >= $settings['hourly_cap'] ? 0 : ($settings['hourly_cap'] - $return_60) / 100 * $ts['60m'][15];
                }

                $settings['max_owed'] = round(max($owed_60, $owed_24)) + round($avg_in_minute);
            }



            // Only include in outlist if at least 1 raw in the last 24 hours
            //    AND
            // max_owed is > 0
            if( ($i_raw_24 > 0 && $settings['max_owed'] > 0) || $high_priority_instant )
            {
                // Setup points array
                $points[$trade] = array('trade' => $settings,
                                        POINTS_MAIN => 0,
                                        POINTS_PRIMARY_BONUS => 0,
                                        POINTS_SECONDARY_BONUS => 0);

                $ts['24h'][15] = max(1, $ts['24h'][15]);
                $ts['60m'][15] = max(1, $ts['60m'][15]);

                $points[$trade][POINTS_MAIN] = (pow($i_uniq_24, 0.375) * $ts['24h'][7] / $ts['24h'][15]) * 0.60 + (pow($i_uniq_60, 0.375) * $ts['60m'][7] / $ts['60m'][15]) * 0.40;
                $points[$trade][POINTS_PRIMARY_BONUS] = ($ts['24h'][7] / $ts['24h'][15]) * 0.60 + ($ts['60m'][7] / $ts['60m'][15]) * 0.40;
                $points[$trade][POINTS_SECONDARY_BONUS] = $i_uniq_60 / $ts['60m'][15];

                if( $ts['24h'][15] > 100 )
                {
                    $ca_factor = max(0.9, min(1.1, $ts['24h'][11] / $ca_avg));
                    $points[$trade][POINTS_MAIN] *= $ca_factor;
                    $points[$trade][POINTS_PRIMARY_BONUS] *= $ca_factor;
                    $points[$trade][POINTS_SECONDARY_BONUS] *= $ca_factor;
                }


                // Calculate the weight to apply, and apply it to points
                $weight = $settings['trade_weight'] / 100;
                if( $weight != 1 )
                {
                    $points[$trade][POINTS_MAIN] *= $weight;
                    $points[$trade][POINTS_PRIMARY_BONUS] *= $weight;
                    $points[$trade][POINTS_SECONDARY_BONUS] *= $weight;
                }


                // Default modifier points to zero
                $modifier_points = array(POINTS_MAIN => 0,
                                         POINTS_PRIMARY_BONUS => 0,
                                         POINTS_SECONDARY_BONUS => 0);


                // Stats needed for modifiers
                $prod_24 = $ts['24h'][1] > 0 ? $ts['24h'][7] / $ts['24h'][1] * 100 : 0;
                $unique_24 = $ts['24h'][1] > 0 ? $ts['24h'][2] / $ts['24h'][1] * 100 : 0;
                $return_24 = $ts['24h'][1] > 0 ? $ts['24h'][15] / $ts['24h'][1] * 100 : 0;
                $proxy_24 = $ts['24h'][1] > 0 ? $ts['24h'][3] / $ts['24h'][1] * 100 : 0;


                // Productivity bonus modifier
                if( $C['mod_bonus_prod'] > 100 && $prod_24 >= $C['bonus_prod_low'] && $prod_24 <= $C['bonus_prod_high'] )
                {
                    apply_bonus_points_modifier($points[$trade], $modifier_points, $C['mod_bonus_prod']);
                }

                // Good uniques bonus modifier
                if( $C['mod_bonus_unique'] > 100 && $unique_24 >= $C['bonus_unique_low'] && $unique_24 <= $C['bonus_unique_high'] )
                {
                    apply_bonus_points_modifier($points[$trade], $modifier_points, $C['mod_bonus_unique']);
                }

                // Low return bonus modifier
                if( $C['mod_bonus_return'] > 100 && $return_24 >= $C['bonus_return_low'] && $return_24 <= $C['bonus_return_high'] )
                {
                    apply_bonus_points_modifier($points[$trade], $modifier_points, $C['mod_bonus_return']);
                }

                // Too high or low proxy % penalty
                if( $C['mod_penalty_proxy'] < 100 && $proxy_24 < $C['penalty_proxy_low'] && $proxy_24 > $C['penalty_proxy_high'] )
                {
                    apply_penalty_points_modifier($points[$trade], $modifier_points, $C['mod_penalty_proxy']);
                }

                // Too high or low unique % penalty
                if( $C['mod_penalty_unique'] < 100 && $unique_24 < $C['penalty_unique_low'] && $unique_24 > $C['penalty_unique_high'] )
                {
                    apply_penalty_points_modifier($points[$trade], $modifier_points, $C['mod_penalty_unique']);
                }

                // Too high return % penalty
                if( $C['mod_penalty_return'] < 100 && $unique_24 < $C['penalty_return_low'] && $unique_24 > $C['penalty_return_high'] )
                {
                    apply_penalty_points_modifier($points[$trade], $modifier_points, $C['mod_penalty_return']);
                }

                // Add modifier points
                $points[$trade][POINTS_MAIN] = max(0, $points[$trade][POINTS_MAIN] + $modifier_points[POINTS_MAIN]);
                $points[$trade][POINTS_PRIMARY_BONUS] = max(0, $points[$trade][POINTS_PRIMARY_BONUS] + $modifier_points[POINTS_PRIMARY_BONUS]);
                $points[$trade][POINTS_SECONDARY_BONUS] = max(0, $points[$trade][POINTS_SECONDARY_BONUS] + $modifier_points[POINTS_SECONDARY_BONUS]);
            }
        }
    }

    // Generate main outlist
    write_outlist(FILE_OUTLIST_MAIN, POINTS_MAIN, $points);

    // Generate primary bonus outlist
    write_outlist(FILE_OUTLIST_PRIMARY, POINTS_PRIMARY_BONUS, $points);

    // Generate secondary bonus outlist
    write_outlist(FILE_OUTLIST_SECONDARY, POINTS_SECONDARY_BONUS, $points);

    // Generate force outlist
    write_outlist(FILE_OUTLIST_FORCES, POINTS_FORCE, $forces);

    // Update dynamic skim schemes
    update_dynamic_skim_schemes($stats['total'], $now);
}

function update_dynamic_skim_schemes($stats, $now)
{
    list($c_day, $c_time) = explode('|', date('w|Gi', $now));

    foreach( dir_read_files(DIR_SKIM_SCHEMES_BASE) as $file )
    {
        $rule_match = false;
        $settings = explode('|', trim(file_get_contents(DIR_SKIM_SCHEMES_BASE . '/' . $file)));
        $dynamic = array_pop($settings);

        // This has a dynamic skim scheme
        if( $dynamic )
        {
            $fp = fopen(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $file, 'r');
            while( !feof($fp) )
            {
                $line = trim(fgets($fp));

                if( empty($line) )
                {
                    continue;
                }

                list($s_day, $s_hour, $s_minute, $e_day, $e_hour, $e_minute, $scheme) = explode('|', $line, 7);
                $s_time = $s_hour . $s_minute;
                $e_time = $e_hour . $e_minute;

                // Weekdays and weekends
                if( (/* weekdays */ ($s_day == 7 && ($c_day > 0 || $c_day < 6)) || /* weekends */ ($s_day == 8 && ($c_day == 0 || $c_day == 6))) && skim_scheme_chktime($s_time, $e_time, $c_time) )
                {
                    $rule_match = true;
                }

                // All Others
                else if( skim_scheme_chktime($s_day, $e_day, $c_day) && skim_scheme_chktime($s_time, $e_time, $c_time) )
                {
                    $rule_match = true;
                }

                if( $rule_match )
                {
                    file_write(DIR_SKIM_SCHEMES . '/' . $file, prep_skim_scheme($scheme));
                    break;
                }
            }
            fclose($fp);
        }

        if( !$rule_match )
        {
            file_write(DIR_SKIM_SCHEMES . '/' . $file, prep_skim_scheme($settings));
        }
    }
}

function prep_skim_scheme($scheme)
{
    if( !is_array($scheme) )
    {
        $scheme = explode('|', $scheme);
    }

    $empty = array('');
    $clicks = array_values(array_diff(array_slice($scheme, 0, 7), $empty));
    $cycle = array_values(array_diff(array_slice($scheme, 7), $empty));

    $num_fill = 50 - count($clicks);
    $num_cycle = count($cycle);

    for( $i = 0; $i < $num_fill; $i++ )
    {
        $clicks[] = $cycle[$i % $num_cycle];
    }

    return join('|', $clicks);
}

function skim_scheme_chktime($start, $end, $current)
{
    if( $start > $end )
    {
        if( $current >= $start || $current <= $end )
        {
            return true;
        }
    }
    else
    {
        if( $current >= $start && $current <= $end )
        {
            return true;
        }
    }

    return false;
}

function apply_bonus_points_modifier(&$points, &$modifier_points, $modifier)
{
    $modifier /= 100;
    $modifier_points[POINTS_MAIN] += $points[POINTS_MAIN] * $modifier;
    $modifier_points[POINTS_PRIMARY_BONUS] += $points[POINTS_PRIMARY_BONUS] * $modifier;
    $modifier_points[POINTS_SECONDARY_BONUS] += $points[POINTS_SECONDARY_BONUS] * $modifier;
}

function apply_penalty_points_modifier(&$points, &$modifier_points, $modifier)
{
    $modifier = ($modifier - 100) / 100;
    $modifier_points[POINTS_MAIN] -= $points[POINTS_MAIN] * $modifier;
    $modifier_points[POINTS_PRIMARY_BONUS] -= $points[POINTS_PRIMARY_BONUS] * $modifier;
    $modifier_points[POINTS_SECONDARY_BONUS] -= $points[POINTS_SECONDARY_BONUS] * $modifier;
}

function write_outlist($file, $sort_key, &$points, $amount = null)
{
    global $g_outlist_key;

    $g_outlist_key = $sort_key;
    uasort($points, 'cmp_outlist_points');

    $fp = fopen($file, 'w');
    flock($fp, LOCK_EX);
    foreach( $points as $trade => $data )
    {
        $high_priority_instant = $sort_key == POINTS_MAIN && $data['trade']['force_instant'] > 0 && $data['trade']['flag_force_instant_high'];
        $trade_points = $high_priority_instant ? format_float_to_percent($data['trade']['force_instant_owed'] / $data['trade']['force_instant'], 1) : round($data[$sort_key], 1);

        if( !$high_priority_instant && $trade_points <= 0 )
        {
            continue;
        }

        $line = join('|', array($trade,
                                $trade_points,
                                ',' . $data['trade']['categories'] . ',',
                                ',' . $data['trade']['groups'] . ',',
                                $data['trade']['return_url'],
                                $high_priority_instant,
                                $sort_key == POINTS_FORCE ? $data['trade']['force_type'] : null,
                                $data['trade']['flag_external']));

        $owed = ($file == FILE_OUTLIST_PRIMARY || $file == FILE_OUTLIST_SECONDARY) ? round($data['trade']['max_owed'] * 0.333) : $data['trade']['max_owed'];

        fwrite($fp, pack('LL', $owed, strlen($line)) . $line);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

function load_outlist($file)
{
    $total_points = 0;
    $trades = array();

    $fp = fopen($file, 'r');
    while( !feof($fp) )
    {
        $ints = fread($fp, 8);

        if( feof($fp) )
        {
            break;
        }

        $ints = unpack('Lowe/Lsize', $ints);
        $trade = explode('|', fread($fp, $ints['size']));

        if( $file != FILE_OUTLIST_MAIN || !$trade[5] )
        {
            $total_points += $trade[1];
        }

        $trade[] = $ints['owe'];
        $trades[] = $trade;
    }
    fclose($fp);

    return array('trades' => $trades, 'total' => $total_points);
}

function cmp_outlist_points($a, $b)
{
    global $g_outlist_key;

    // Sorting main points, so check if high priority instant forces are present
    if( $g_outlist_key == POINTS_MAIN )
    {
        $a_instant = $a['trade']['force_instant'] > 0 && $a['trade']['flag_force_instant_high'];
        $b_instant = $b['trade']['force_instant'] > 0 && $b['trade']['flag_force_instant_high'];

        if( $a_instant && $b_instant )
        {
            $a_instant_pct = $a['trade']['force_instant_owed'] / $a['trade']['force_instant'];
            $b_instant_pct = $b['trade']['force_instant_owed'] / $b['trade']['force_instant'];

            if( $a_instant_pct > $b_instant_pct )
            {
                return -1;
            }
            else if( $a_instant_pct < $b_instant_pct )
            {
                return 1;
            }

            return 0;
        }
        else if( $a_instant && !$b_instant )
        {
            return -1;
        }
        else if( !$a_instant && $b_instant )
        {
            return 1;
        }
    }


    // No high priority instant forces are present, sort normally by points
    if( $a[$g_outlist_key] > $b[$g_outlist_key] )
    {
        return -1;
    }
    else if( $a[$g_outlist_key] < $b[$g_outlist_key] )
    {
        return 1;
    }

    return 0;
}

function stats_check_update_time()
{
    global $C;

    if( !isset($C['base_url']) )
    {
        return;
    }

    $now = time();

    if( !file_exists(FILE_TIME_STATS) )
    {
        file_create(FILE_TIME_STATS);
    }

    $last = filemtime(FILE_TIME_STATS);
    $date_now = date('YmdHi', $now);
    $date_last = date('YmdHi', $last);

    if( $date_now != $date_last )
    {
        $fp = fopen(FILE_TIME_STATS, 'w');
        flock($fp, LOCK_EX | LOCK_NB, $would);

        if( $would )
        {
            fclose($fp);
            return;
        }

        stats_update($now, $last);

        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

function stats_update($now, $last)
{
    $n_date = getdate($now);
    $l_date = getdate($last);

    if( "{$n_date['year']}{$n_date['mon']}{$n_date['mday']}" != "{$l_date['year']}{$l_date['mon']}{$l_date['mday']}" )
    {
        stats_day_update($now, $last, $n_date, $l_date);
    }
    else if( "{$n_date['year']}{$n_date['mon']}{$n_date['mday']}{$n_date['hours']}" != "{$l_date['year']}{$l_date['mon']}{$l_date['mday']}{$l_date['hours']}" )
    {
        stats_hour_update($now, $last, $n_date, $l_date);
    }
    else
    {
        stats_minute_update($now, $last, $n_date, $l_date);
    }

    touch(FILE_TIME_STATS);
}

function stats_minute_update($now, $last, $n_date, $l_date)
{
    global $C, $g_stats_hourly, $g_stats_daily, $g_stats_minute;

    $g_stats_minute = true;

    // Number of seconds since the last update
    $since = $now - $last;

    // The minutes that should be reset back to zero
    // Should end up as an array containing the minutes of the day to reset (from 0 to 1439)
    $reset_minutes = null;

    // 24 hours or more since the last update
    if( $since >= SECONDS_PER_DAY )
    {
        $reset_minutes = range(0, MINUTES_PER_DAY - 1);
    }

    // 2 minutes or more since the last update
    else if( $since >= SECONDS_PER_MINUTE * 2 )
    {
        $minutes_since = floor($since / SECONDS_PER_MINUTE) - 1;
        $this_minute = $n_date['hours'] * MINUTES_PER_HOUR + $n_date['minutes'];

        $reset_minutes = array();
        for( $i = $this_minute; $i >= $this_minute - $minutes_since; $i-- )
        {
            $reset_minutes[] = $i >= 0 ? $i : MINUTES_PER_DAY + $i;
        }

        $reset_minutes = array_reverse($reset_minutes);
    }

    // 1 minute since the last update
    else
    {
        $reset_minutes = array($n_date['hours'] * MINUTES_PER_HOUR + $n_date['minutes']);
    }


    // Starting offset of minute stats
    $minutes_start = HOURS_PER_DAY * RECORD_SIZE_STATS;

    // Zeroed packed record
    $packed_record = trade_packed_record();

    // Record stats for generation of outlists
    $stats = array('total' => array('24h' => array_fill(1, STATS_PER_RECORD, 0), '60m' => array_fill(1, STATS_PER_RECORD, 0)));
    list($hour, $minute) = explode('-', date('H-i', $now - SECONDS_PER_HOUR));
    $minute_offset = ($hour * MINUTES_PER_HOUR + $minute) * RECORD_SIZE_STATS + $minutes_start;
    $end_of_file = (HOURS_PER_DAY + MINUTES_PER_DAY) * RECORD_SIZE_STATS;

    // Reset trades
    foreach( stats_get_all_files() as $stats_file )
    {
        $trade = basename($stats_file);
        $system_trade = strpos($stats_file, DIR_TRADE_STATS) === false;

        $fp = fopen($stats_file, 'r+');
        flock($fp, LOCK_EX);

        // Get 24 hour stats
        if( !$system_trade )
        {
            $stats[$trade] = array();
            $stats[$trade]['24h'] = array_fill(1, STATS_PER_RECORD, 0);
            for( $i = 0; $i < HOURS_PER_DAY; $i++ )
            {
                $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
                foreach( $r as $j => $k )
                {
                    $stats[$trade]['24h'][$j] += $k;
                    $stats['total']['24h'][$j] += $k;
                }
            }

            // Seek to minute of last update to get stats
            fseek($fp, $reset_minutes[0] == 0 ? $minutes_start + (MINUTES_PER_DAY - 1) * RECORD_SIZE_STATS: $minutes_start + ($reset_minutes[0] - 1) * RECORD_SIZE_STATS, SEEK_SET);
            $stats[$trade]['1m'] = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
        }



        // Seek to first minute to be zeroed
        fseek($fp, $minutes_start + $reset_minutes[0] * RECORD_SIZE_STATS, SEEK_SET);

        // Reset to 0
        foreach( $reset_minutes as $minute )
        {
            fwrite($fp, $packed_record);

            // Seek to minute 0 if we just zeroed minute 1439
            if( $minute == MINUTES_PER_DAY - 1 )
            {
                fseek($fp, $minutes_start, SEEK_SET);
            }
        }

        // Get 60 minute stats
        if( !$system_trade )
        {
            $stats[$trade]['60m'] = array_fill(1, STATS_PER_RECORD, 0);
            fseek($fp, $minute_offset, SEEK_SET);
            for( $i = 0; $i < MINUTES_PER_HOUR; $i++ )
            {
                $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
                foreach( $r as $j => $k )
                {
                    $stats[$trade]['60m'][$j] += $k;
                    $stats['total']['24h'][$j] += $k;
                }

                // Wrap around midnight with a seek
                if( ftell($fp) == $end_of_file )
                {
                    fseek($fp, HOURS_PER_DAY * RECORD_SIZE_STATS, SEEK_SET);
                }
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);
    }


    // Generate the outlists
    generate_outlists($stats);


    // Determine if toplists should be built
    if( !file_exists(FILE_TIME_TOPLISTS) )
    {
        file_create(FILE_TIME_TOPLISTS);
    }


    // Rebuild toplists, only if not using cron (indicated by rebuild interval set to -1)
    if( $C['toplist_rebuild_interval'] > 0 )
    {
        $toplists_last = filemtime(FILE_TIME_TOPLISTS);
        if( isset($C['base_url']) && $now - $toplists_last > $C['toplist_rebuild_interval'] * SECONDS_PER_MINUTE )
        {
            require_once 'http.php';
            $http = new HTTP();
            $http->GET("{$C['base_url']}/cp/index.php?r=btl");
        }
    }
}

function stats_hour_update($now, $last, $n_date, $l_date)
{
    global $g_stats_hourly, $g_stats_daily;

    $g_stats_hourly = true;

    // Number of seconds since the last update
    $since = $now - $last;

    // The hours that should be reset back to zero
    // Should end up as an array containing the hours of the day to reset (from 0 to 23)
    $reset_hours = null;

    // 24 hours or more since the last update
    if( $since >= SECONDS_PER_DAY )
    {
        $reset_hours = range(0, HOURS_PER_DAY - 1);
    }

    // 2 hours or more since the last update
    else if( $since >= SECONDS_PER_HOUR * 2 )
    {
        $reset_hours = array();
        $hours_since = floor($since / SECONDS_PER_HOUR) - 1;

        for( $i = $n_date['hours']; $i >= $n_date['hours'] - $hours_since; $i-- )
        {
            $reset_hours[] = $i >= 0 ? $i : HOURS_PER_DAY + $i;
        }

        $reset_hours = array_reverse($reset_hours);
    }

    // 1 hour since the last update
    else
    {
        $reset_hours = array($n_date['hours']);
    }

    // Zeroed packed record
    $packed_record = trade_packed_record();


    // Reset trades
    foreach( stats_get_all_files() as $stats_file )
    {
        $fp = fopen($stats_file, 'r+');
        flock($fp, LOCK_EX);

        // Seek to first hour
        fseek($fp, $reset_hours[0] * RECORD_SIZE_STATS, SEEK_SET);

        foreach( $reset_hours as $hour )
        {
            fwrite($fp, $packed_record);

            // Seek to hour 0 if we just zeroed hour 23
            if( $hour == HOURS_PER_DAY - 1 )
            {
                fseek($fp, 0, SEEK_SET);
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);
    }


    // Clear logs of old data
    $too_old = $now - SECONDS_PER_DAY;
    foreach( logs_get_all_files() as $log_file )
    {
        $fp_in = fopen($log_file, 'r');

        $fp_out = fopen($log_file, 'r+');
        flock($fp_out, LOCK_EX);

        while( !feof($fp_in) )
        {
            $line = fgets($fp_in);

            if( empty($line) )
            {
                continue;
            }

            $timestamp = substr($line, 0, 10);

            if( $timestamp >= $too_old )
            {
                fwrite($fp_out, $line);
            }
        }

        ftruncate($fp_out, ftell($fp_out));
        flock($fp_out, LOCK_UN);
        fclose($fp_out);

        fclose($fp_in);
    }

    // TODO: Determine if thumbnails should be grabbed

    // Trigger minute update
    stats_minute_update($now, $last, $n_date, $l_date);
}

function stats_day_update($now, $last, $n_date, $l_date)
{
    global $g_stats_hourly, $g_stats_daily;

    $g_stats_daily = true;
    $date_stats = date('Ymd', $last);
    $rt24_total = array_fill(1, STATS_PER_RECORD, 0);

    // Generate historical stats
    foreach( stats_get_all_files() as $stats_file )
    {
        $system = strpos($stats_file, DIR_SYSTEM_STATS) !== false;

        $rt24 = array_fill(1, STATS_PER_RECORD, 0);
        $fp = fopen($stats_file, 'r');
        for( $i = 0; $i < HOURS_PER_DAY; $i++ )
        {
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
            foreach( $r as $j => $k )
            {
                $rt24[$j] += $k;
                if( !$system )
                {
                    $rt24_total[$j] += $k;
                }
            }
        }

        $history_file = "$stats_file-history";
        file_append($history_file, join('|', array_merge(array($date_stats), $rt24)) . "\n");
    }

    // Write global history
    file_append(FILE_HISTORY, join('|', array_merge(array($date_stats), $rt24_total)) . "\n");

    // Trigger hour update
    stats_hour_update($now, $last, $n_date, $l_date);
}


function network_site_delete($domains)
{
    require_once 'textdb.php';

    if( !is_array($domains) )
    {
        $domains = array($domains);
    }

    $db = new NetworkDB();

    foreach( $domains as $domain )
    {
        $db->Delete($domain);
    }
}

function network_stats_cache_remove($domain)
{
    require_once 'stats.php';

    $cache = unserialize(file_get_contents(FILE_NETWORK_SITES_CACHE));

    unset($cache[$domain]);

    file_write(FILE_NETWORK_SITES_CACHE, serialize($cache));
}

function network_stats_cache_update($site, $response)
{
    require_once 'stats.php';

    $cache = unserialize(file_get_contents(FILE_NETWORK_SITES_CACHE));

    $so = unserialize($response);
    $so->trade = array('domain' => $site['domain']);

    $cache[$site['domain']] = array('timestamp' => time(),
                                    'so' => $so);

    file_write(FILE_NETWORK_SITES_CACHE, serialize($cache));
}

function network_site_update_stored_values()
{
    require_once 'textdb.php';

    $db = new NetworkDB();

    $values = array('categories' => array(), 'owners' => array());
    foreach( $db->RetrieveAll() as $site )
    {
        if( !string_is_empty($site['category']) )
        {
            $values['categories'][] = $site['category'];
        }

        if( !string_is_empty($site['owner']) )
        {
            $values['owners'][] = $site['owner'];
        }
    }

    $values['categories'] = array_unique($values['categories']);
    $values['owners'] = array_unique($values['owners']);

    file_write(FILE_NETWORK_SITES_VALUES, serialize($values));
}


function toplist_delete($toplist_ids)
{
    require_once 'textdb.php';

    if( !is_array($toplist_ids) )
    {
        $toplist_ids = array($toplist_ids);
    }

    $db = new ToplistsDB();

    foreach( $toplist_ids as $toplist_id )
    {
        $db->Delete($toplist_id);
    }
}

function build_all_toplists()
{
    require_once 'textdb.php';

    $db = new ToplistsDB();
    $toplists = $db->RetrieveAll();

    foreach( $toplists as $toplist )
    {
        build_toplist($toplist);
    }

    touch(FILE_TIME_TOPLISTS);
}

function build_toplist($toplist)
{
    global $g_stats, $g_time, $compiler, $C;

    require_once 'dirdb.php';
    require_once 'compiler.php';
    require_once 'template.php';
    require_once 'stats.php';

    if( empty($toplist) )
    {
        return;
    }

    // Check that source file exists and is readable
    if( $toplist['source'] == TOPLIST_SOURCE_FILE )
    {
        if( !is_file($toplist['infile']) || !is_readable($toplist['infile']) )
        {
            return log_toplist_error("Input file '{$toplist['infile']}' does not exist or is not readable");
        }

        $toplist['template'] = file_get_contents($toplist['infile']);
    }

    // Check that the destination file exists and is writeable, or the directory exists and is writeable
    if( !file_exists($toplist['outfile']) )
    {
        $directory = dirname($toplist['outfile']);

        if( !is_dir($directory) || !is_writeable($directory) )
        {
            return log_toplist_error("Output directory '$directory' does not exist or is not writeable");
        }
    }
    else if( !is_writeable($toplist['outfile']) )
    {
        return log_toplist_error("Output file '{$toplist['outfile']}' is not writeable");
    }


    // Compile and check for errors
    if( ($compiled = $compiler->Compile($toplist['template'])) === false )
    {
        return log_toplist_error("Toplist template for '{$toplist['outfile']}' contains errors");
    }


    // Load and cache overall stats
    if( !isset($g_stats) )
    {
        $_REQUEST['status'] = STATUS_ACTIVE;
        $g_stats = load_overall_stats(DIR_TRADE_STATS, get_trades(), TRUE);
    }

    // Get time
    if( !isset($g_time) )
    {
        $g_time = time();
    }


    // Prepare categories and groups
    $toplist['categories'] = empty($toplist['categories']) ? null : explode(',', $toplist['categories']);
    $toplist['groups'] = empty($toplist['groups']) ? null : explode(',', $toplist['groups']);

    // Prepare requirements & sources
    $toplist['trade_sources'] = unserialize($toplist['trade_sources']);
    $toplist['req_field'] = unserialize($toplist['req_field']);
    $toplist['req_operator'] = unserialize($toplist['req_operator']);
    $toplist['req_value'] = unserialize($toplist['req_value']);


    // Sort
    $_REQUEST['sort_by'] = $toplist['sort_by'];
    $_REQUEST['trade_sources'] = $toplist['trade_sources'];
    usort($g_stats, 'cmp_overall_stats');


    $t = new Template();

    $rank = 1;
    $trades = array();
    foreach( $g_stats as /** @var StatsOverall */ $so )
    {
        // Allowed in toplist?
        if( !$so->trade['flag_toplist'] )
        {
            continue;
        }

        // Check categories
        if( !empty($toplist['categories']) && count(array_intersect($toplist['categories'], explode(',', $so->trade['categories']))) == 0 )
        {
            continue;
        }

        // Check groups
        if( !empty($toplist['groups']) && count(array_intersect($toplist['groups'], explode(',', $so->trade['groups']))) == 0 )
        {
            continue;
        }

        // Only trades with thumbnails
        if( $toplist['flag_thumbs_only'] && $so->trade['thumbnails'] < 1 )
        {
            continue;
        }

        // Check requirement
        if( !empty($toplist['req_field']) && !$so->ignore_requirements )
        {
            for( $i = 0; $i < count($toplist['req_field']); $i++ )
            {
                $req_failed = FALSE;

                switch($toplist['req_operator'][$i])
                {
                    case '>=':
                        $req_failed = !($so->{$toplist['req_field'][$i]} >= $toplist['req_value'][$i]);
                        break;

                    case '<':
                        $req_failed = !($so->{$toplist['req_field'][$i]} < $toplist['req_value'][$i]);
                        break;

                    case '<=':
                        $req_failed = !($so->{$toplist['req_field'][$i]} <= $toplist['req_value'][$i]);
                        break;

                    case '>':
                        $req_failed = !($so->{$toplist['req_field'][$i]} > $toplist['req_value'][$i]);
                        break;

                    default:
                        $req_failed = FALSE;
                        break;
                }

                if( $req_failed )
                {
                    break;
                }
            }

            if( $req_failed )
            {
                continue;
            }
        }

        $trade = get_object_vars($so);
        $trade = array_merge($trade, $trade['trade']);
        unset($trade['trade']);

        $trades[$rank] = $trade;
        $rank++;
    }

    $t->AssignByRef('g_trades', $trades);
    $t->AssignByRef('g_config', $C);
    $t->Assign('g_timestamp', $g_time);
    $t->Assign('g_date', date($C['date_format'], $g_time));
    $t->Assign('g_datetime', date($C['date_format'] . ' ' . $C['time_format'], $g_time));

    $output = $t->Parse($compiled);

    file_write($toplist['outfile'], $output);
}

function log_toplist_error($message)
{
    $message = "[" . date('r') . "] Error building toplist: $message\n";
    file_append(FILE_ERROR_LOG, $message);
}


function get_dns($domain)
{
    // TODO: Only execute if dig and shell_exec are available
    return;

    $nameservers = array();
    $found = false;

    while( substr_count($domain, '.') >= 1 )
    {
        $output = shell_exec("dig $domain NS +nocmd +nostats +noquestion +nocomment");

        foreach( explode("\n", $output) as $line )
        {
            if( preg_match('~NS\s+([^\s]+)$~i', $line, $matches) )
            {
                $nameservers[] = preg_replace('~\.$~', '', $matches[1]);
            }
        }

        if( $found )
        {
            break;
        }

        $domain = substr($domain, strpos($domain, '.') + 1);
    }

    return join(',', $nameservers);
}

function check_blacklist($trade)
{
    $checks = array('content' => $trade['content'],
                    'word' => $trade['site_name'] . ' ' . $trade['site_description'],
                    'dns' => get_dns($trade['domain']),
                    'domain' => $trade['domain'],
                    'email' => $trade['email'],
                    'header' => $trade['header'],
                    'server_ip' => $trade['server_ip'],
                    'user_ip' => $_SERVER['REMOTE_ADDR']);

    foreach( dir_read_files(DIR_BLACKLIST) as $file )
    {
        // Only check the specific blacklist if a check is set
        if( !string_is_empty($checks[$file]) )
        {
            $fp = fopen(DIR_BLACKLIST . '/' . $file, 'r');

            while( !feof($fp) )
            {
                list($item, $reason) = explode('|', trim(fgets($fp)));

                if( !string_is_empty($item) && stristr($checks[$file], $item) !== false )
                {
                    return array($item, $reason);
                }
            }

            fclose($fp);
        }
    }

    return false;
}

function get_config_defaults()
{
    global $C;

    require_once 'mailer.php';

    $domain = domain_from_url('http://' . $_SERVER['HTTP_HOST']);

    $defaults = array();
    $defaults['site_name'] = $domain;
    $defaults['traffic_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/index.shtml';
    $defaults['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('~/cp/.*~', '', $_SERVER['REQUEST_URI']);
    $defaults['cookie_domain'] = $domain;
    $defaults['cookie_path'] = preg_replace('~/cp/.*~', '', $_SERVER['REQUEST_URI']);
    $defaults['keyphrase'] = md5(uniqid(mt_rand(), true));
    $defaults['email_address'] = 'webmaster@' . $domain;
    $defaults['email_name'] = $domain;
    $defaults['date_format'] = 'm-d-Y';
    $defaults['time_format'] = 'h:i:s';
    $defaults['email_method'] = MAILER_METHOD_PHP;
    $defaults['captcha_min'] = '4';
    $defaults['captcha_max'] = '6';
    $defaults['flag_captcha_words'] = '1';
    $defaults['flag_accept_new_trades'] = '1';
    $defaults['flag_captcha_register'] = '1';
    $defaults['flag_allow_login'] = '1';
    $defaults['site_name_min'] = '10';
    $defaults['site_name_max'] = '50';
    $defaults['site_description_min'] = '10';
    $defaults['site_description_max'] = '500';
    $defaults['autostop_interval'] = '24';
    $defaults['flag_req_email'] = '1';
    $defaults['flag_req_site_name'] = '1';
    $defaults['flag_req_site_description'] = '';
    $defaults['flag_req_icq'] = '';
    $defaults['flag_req_nickname'] = '1';
    $defaults['flag_req_banner'] = '';
    $defaults['bonus_prod_low'] = '110';
    $defaults['bonus_prod_high'] = '150';
    $defaults['mod_bonus_prod'] = '105';
    $defaults['bonus_unique_low'] = '90';
    $defaults['bonus_unique_high'] = '98';
    $defaults['mod_bonus_unique'] = '105';
    $defaults['bonus_return_low'] = '0';
    $defaults['bonus_return_high'] = '90';
    $defaults['mod_bonus_return'] = '120';
    $defaults['penalty_proxy_low'] = '2';
    $defaults['penalty_proxy_high'] = '20';
    $defaults['mod_penalty_proxy'] = '90';
    $defaults['penalty_unique_low'] = '85';
    $defaults['penalty_unique_high'] = '98';
    $defaults['mod_penalty_unique'] = '90';
    $defaults['penalty_return_low'] = '0';
    $defaults['penalty_return_high'] = '175';
    $defaults['mod_penalty_return'] = '80';
    $defaults['distrib_forces'] = '30';
    $defaults['distrib_main'] = '50';
    $defaults['distrib_primary'] = '10';
    $defaults['distrib_secondary'] = '10';
    $defaults['toplist_rebuild_interval'] = '15';
    $defaults['fast_click'] = '1.25';
    $defaults['count_clicks'] = '10';
    $defaults['thumb_grab_interval'] = '12';
    $defaults['thumb_grab_amount'] = '3';
    $defaults['thumb_width_min'] = '75';
    $defaults['thumb_height_min'] = '75';
    $defaults['thumb_width_max'] = '400';
    $defaults['thumb_height_max'] = '400';
    $defaults['have_gd'] = 0;
    $defaults['have_magick'] = 0;
    $defaults['magick_mogrify_path'] = '';
    $defaults['magick_mogrify_options'] = '+profile "*" -format jpg -quality 80';
    $defaults['thumb_resize_width'] = '120';
    $defaults['thumb_resize_height'] = '160';
    $defaults['thumb_trigger_strings'] = 'thumbs/,tn/,thumbnails/';

    $C = array_merge($defaults, $C);
}

function get_date_range($input)
{
    $date_format = 'Ymd';
    $from = null;
    $to = null;

    switch($input['date_range'])
    {
        case 'custom':
            if( $input['breakdown'] == STATS_HISTORY_REGEX_REPLACE_MONTHLY )
            {
                $input['from_day'] = '01';
                $input['to_day'] = date('d', mktime(12, 0, 0, $input['to_month'] + 1, 0, $input['to_year']));
            }
            else if( $input['breakdown'] == STATS_HISTORY_REGEX_REPLACE_YEARLY )
            {
                $input['from_day'] = '01';
                $input['from_month'] = '01';
                $input['to_day'] = '31';
                $input['to_month'] = '12';
            }

            $from = "{$input['from_year']}{$input['from_month']}{$input['from_day']}";
            $to = "{$input['to_year']}{$input['to_month']}{$input['to_day']}";
            break;

        case 'this_week':
            $from = date($date_format, strtotime(date('w') == 0 ? 'this sunday' : 'last sunday'));
            $to = date($date_format, strtotime('this saturday'));
            break;

        case 'last_week':
            $saturday = strtotime('last saturday');
            $from = date($date_format, strtotime('last sunday', $saturday));
            $to = date($date_format, $saturday);
            break;

        case 'this_month':
            $from = date($date_format, mktime(12, 0, 0, date('n'), 1, date('Y')));
            $to = date($date_format, mktime(12, 0, 0, date('n', strtotime('next month')), 0, date('Y')));
            break;

        case 'last_month':
            $from = date($date_format, mktime(12, 0, 0, date('n', strtotime('last month')), 1, date('Y')));
            $to = date($date_format, mktime(12, 0, 0, date('n'), 0, date('Y')));
            break;

        case 'this_year':
            $from = date($date_format, mktime(12, 0, 0, 1, 1, date('Y')));
            $to = date($date_format, mktime(12, 0, 0, 12, 31, date('Y')));
            break;

        case 'last_year':
            $from = date($date_format, mktime(12, 0, 0, 1, 1, date('Y', strtotime('last year'))));
            $to = date($date_format, mktime(12, 0, 0, 12, 31, date('Y', strtotime('last year'))));
            break;

        default:
            list($from, $to) = explode(',', $input['date_range']);
            $from = date($date_format, strtotime($from));
            $to = date($date_format, strtotime($to));
            break;
    }

    // Swap if $from is greater than $to
    if( $from > $to )
    {
        list($from, $to) = array($to, $from);
    }

    return array($from, $to);
}

function get_random_password()
{
    // 1 symbol, 2 numbers, 3 uppercase, 4 lowercase
    $lc_letters = array('a','b','c','d','e','f','g','h','i','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z');
    $uc_letters = array_map('strtoupper', $lc_letters);
    $numbers = array(2,3,4,5,6,7,8,9);
    $symbols = array('!','@','#','$','%','^','&','*','+','~','?');

    shuffle($lc_letters);
    shuffle($uc_letters);
    shuffle($numbers);
    shuffle($symbols);

    $password = array(array_pop($symbols),
                      array_pop($numbers),
                      array_pop($numbers),
                      array_pop($uc_letters),
                      array_pop($uc_letters),
                      array_pop($uc_letters),
                      array_pop($lc_letters),
                      array_pop($lc_letters),
                      array_pop($lc_letters),
                      array_pop($lc_letters));

    shuffle($password);

    return join('', $password);
}

function headers_no_cache()
{
    header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
}

function get_country_quality($country_id)
{
    $fp = fopen(FILE_COUNTRIES, 'r');
    fseek($fp, RECORD_SIZE_COUNTRY_WEIGHT + $country_id * RECORD_SIZE_COUNTRY, SEEK_SET);
    $quality = fread($fp, RECORD_SIZE_COUNTRY);
    fclose($fp);

    return $quality;
}


function get_trades()
{
    require_once 'dirdb.php';

    $db = new TradeDB();

    if( isset($_REQUEST['status']) )
    {
        $db->AddFilter('status', $_REQUEST['status']);
    }

    if( isset($_REQUEST['category']) )
    {
        $db->AddFilter('categories', $_REQUEST['category'], true);
    }

    if( isset($_REQUEST['group']) )
    {
        $db->AddFilter('groups', $_REQUEST['group'], true);
    }

    return $db->RetrieveAll();
}

function get_system_trades()
{
    global $cache_system_trades;

    if( !isset($cache_system_trades) )
    {
        require_once 'dirdb.php';
        $db = new SystemDB();
        $cache_system_trades = $db->RetrieveAll();
    }

    return $cache_system_trades;
}

function get_all_trades_logs($log = 'in')
{
    if( !in_array($log, array('in', 'out', 'clicks')) )
    {
        $log = 'in';
    }

    $trades = dir_read_files(DIR_TRADES);
    foreach( $trades as $i => $trade )
    {
        $trades[$i] = DIR_TRADE_STATS . "/$trade-$log";
    }

    $system_trades = dir_read_files(DIR_SYSTEM);
    foreach( $system_trades as $i => $trade )
    {
        $system_trades[$i] = DIR_SYSTEM_STATS . "/$trade-$log";
    }

    return array_merge($trades, $system_trades);
}

function get_trade_db($domain)
{
    require_once 'dirdb.php';

    return is_system_trade($domain) ? new SystemDB() : new TradeDB();
}

function get_trade_stats_dir($trade)
{
    return is_system_trade($trade) ? DIR_SYSTEM_STATS : DIR_TRADE_STATS;
}

function is_system_trade($trade)
{
    return file_exists(DIR_SYSTEM . "/$trade");
}

function filter_system_trades($trades)
{
    $return_string = false;
    if( !is_array($trades) )
    {
        $return_string = true;
        $trades = explode(',', $trades);
    }

    $output = array();

    foreach( $trades as $trade )
    {
        if( !is_system_trade($trade) )
        {
            $output[] = $trade;
        }
    }

    return $return_string ? join(',', $output) : $output;
}

function get_trade_log_stats($logs, $index, $filter = null)
{
    $items = array();
    $total = 0;
    $too_old = time() - SECONDS_PER_DAY;

    if( !is_array($logs) )
    {
        $logs = array($logs);
    }

    foreach( $logs as $log )
    {
        $fp_in = fopen($log, 'r');
        $fp_out = fopen($log, 'r+');
        flock($fp_out, LOCK_EX);

        while( !feof($fp_in) )
        {
            $line = fgets($fp_in);

            if( empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);

            if( $data[0] >= $too_old )
            {
                fwrite($fp_out, $line);

                if( !empty($filter) )
                {
                    $data[$index] = preg_replace($filter['search'], $filter['replace'], $data[$index]);
                }

                if( !isset($items[$data[$index]]) )
                {
                    $items[$data[$index]] = 0;
                }

                $items[$data[$index]]++;
                $total++;
            }
        }

        ftruncate($fp_out, ftell($fp_out));
        flock($fp_out, LOCK_UN);
        fclose($fp_out);
        fclose($fp_in);
    }

    arsort($items);

    return array($total, $items);
}

function get_trade_detailed_stats($trade)
{
    require_once 'stats.php';

    $db = get_trade_db($trade);
    $trade = $db->Retrieve($trade);
    $stats = load_overall_stats(get_trade_stats_dir($trade['domain']), array($trade));
    $stats = array('overall' => $stats[0]);

    return $stats;
}

function get_trade_countries($trade, $log = 'in')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 4);
}

function get_trade_referrers($trade, $log = 'in')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 5);
}

function get_trade_landings($trade, $log = 'in')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 6, array('search' => '~\?.*~', 'replace' => ''));
}

function get_trade_links($trade, $log = 'clicks')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 6);
}

function get_trade_pages($trade, $log = 'clicks')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 5);
}

function get_trade_languages($trade, $log = 'in')
{
    $dir = get_trade_stats_dir($trade);
    $trade = file_sanitize($trade);

    return get_trade_log_stats("$dir/$trade-$log", 7);
}

function get_overall_countries($log = 'in')
{
    $logs = get_all_trades_logs($log);
    return get_trade_log_stats($logs, 4);
}

function get_overall_languages($log = 'in')
{
    $logs = get_all_trades_logs($log);
    return get_trade_log_stats($logs, 7);
}

function get_overall_landings($log = 'in')
{
    $logs = get_all_trades_logs($log);
    return get_trade_log_stats($logs, 6, array('search' => '~\?.*~', 'replace' => ''));
}

function get_overall_pages($log = 'clicks')
{
    $logs = get_all_trades_logs($log);
    return get_trade_log_stats($logs, 5);
}

function get_overall_links($log = 'clicks')
{
    $logs = get_all_trades_logs($log);
    return get_trade_log_stats($logs, 6);
}

function get_overall_search_terms()
{
    return get_trade_log_stats(FILE_SEARCH_TERMS, 2);
}

function trade_packed_record()
{
    return call_user_func_array('pack', array_merge(array('L'.STATS_PER_RECORD), array_fill(0, STATS_PER_RECORD, 0)));
}

function trade_reset($trades)
{
    require_once 'dirdb.php';

    if( !is_array($trades) )
    {
        $trades = array($trades);
    }

    foreach( $trades as $trade )
    {
        $is_sys_trade = is_system_trade($trade);
        $dir = $is_sys_trade ? DIR_SYSTEM_STATS : DIR_TRADE_STATS;

        // Clear stats file
        $packed_record = trade_packed_record();
        $fp = fopen("$dir/$trade", 'w');
        for( $i = 0; $i < HOURS_PER_DAY + MINUTES_PER_DAY; $i++ )
        {
            fwrite($fp, $packed_record, RECORD_SIZE_STATS);
        }
        fclose($fp);
        @chmod("$dir/$trade", 0666);

        // Clear log files
        file_write("$dir/$trade-clicks");
        file_write("$dir/$trade-history");
        file_write("$dir/$trade-in");
        file_write("$dir/$trade-out");

        // Reset the autostop timestamp
        if( !$is_sys_trade )
        {
            $db = new TradeDB();
            $db->Update($trade, array('timestamp_autostop' => time()));
        }
    }
}

function trade_add(&$data, $registered = false)
{
    global $C;

    require_once 'dirdb.php';
    require_once 'mailer.php';
    require_once 'template.php';

    $t = new Template();
    $t->AssignByRef('g_config', $C);
    $t->AssignByRef('g_trade', $data);

    $db = new TradeDB();
    $db->Add(trade_prepare_data($data));

	// Create stats file
    $packed_record = trade_packed_record();
    $fp = fopen(DIR_TRADE_STATS . "/{$data['domain']}", 'w');
    for( $i = 0; $i < HOURS_PER_DAY + MINUTES_PER_DAY; $i++ )
    {
        fwrite($fp, $packed_record, RECORD_SIZE_STATS);
    }
    fclose($fp);
    @chmod(DIR_TRADE_STATS . "/{$data['domain']}", 0666);

    // Create log files
    file_create(DIR_TRADE_STATS . "/{$data['domain']}-clicks");
    file_create(DIR_TRADE_STATS . "/{$data['domain']}-history");
    file_create(DIR_TRADE_STATS . "/{$data['domain']}-in");
    file_create(DIR_TRADE_STATS . "/{$data['domain']}-out");

    if( $data['flag_confirm'] )
    {
        require_once 'textdb.php';

        $data['confirm_id'] = md5(uniqid(rand(), true));

        $confdb = new RegisterConfirmsDB();
        $confdb->Add(array('confirm_id' => $data['confirm_id'],
                           'domain' => $data['domain'],
                           'timestamp' => time()));

        $m = new Mailer();
        $m->Mail('email-register-confirm.tpl', $t, $data['email'], $data['email']);
    }
    else
    {
        if( $C['flag_register_email_user'] && !string_is_empty($data['email']) && $registered )
        {
            $m = new Mailer();
            $m->Mail('email-register-complete.tpl', $t, $data['email'], $data['email']);
        }

        if( $C['flag_register_email_admin'] )
        {
            $m = new Mailer();
            $m->Mail('email-register-admin.tpl', $t, $C['email_address'], $C['email_name']);
        }
    }
}

function trade_delete($domains)
{
    require_once 'dirdb.php';

    if( !is_array($domains) )
    {
        $domains = array($domains);
    }

    $db = new TradeDB();

    foreach( $domains as $domain )
    {
        $trade = $db->Retrieve($domain);

        if( $_REQUEST['flag_blacklist_domain'] )
        {
            file_append(FILE_BLACKLIST_DOMAIN, "$domain|{$_REQUEST['blacklist_reason']}\n");
        }

        if( $_REQUEST['flag_blacklist_server_ip'] && ($ip = gethostbyname($domain)) != $domain )
        {
            file_append(FILE_BLACKLIST_SERVER_IP, "$ip|{$_REQUEST['blacklist_reason']}\n");
        }

        if( $_REQUEST['flag_blacklist_email'] && !string_is_empty($trade['email']) )
        {
            file_append(FILE_BLACKLIST_EMAIL, "{$trade['email']}|{$_REQUEST['blacklist_reason']}\n");
        }

        $db->Delete($domain);

        file_delete(DIR_TRADE_STATS . "/$domain");
        file_delete(DIR_TRADE_STATS . "/$domain-clicks");
        file_delete(DIR_TRADE_STATS . "/$domain-history");
        file_delete(DIR_TRADE_STATS . "/$domain-in");
        file_delete(DIR_TRADE_STATS . "/$domain-out");

        if( $trade['thumbnails'] > 0 )
        {
            for( $i = 1; $i <= $trade['thumbnails']; $i++ )
            {
                file_delete(DIR_THUMBS . "/$domain-$i.jpg");
            }
        }
    }
}

function system_trade_prepare_data($data)
{
    if( isset($data['excludes']) && is_array($data['excludes']) )
    {
        $data['excludes'] = join(',', $data['excludes']);
    }
    else
    {
        $data['excludes'] = STRING_BLANK;
    }

    return $data;
}

function trade_prepare_data(&$data, $editing = false)
{
    if( isset($data['categories']) && is_array($data['categories']) )
    {
        $data['categories'] = join(',', $data['categories']);
    }
    else
    {
        $data['categories'] = STRING_BLANK;
    }

    if( isset($data['groups']) && is_array($data['groups']) )
    {
        $data['groups'] = join(',', $data['groups']);
    }
    else
    {
        $data['groups'] = STRING_BLANK;
    }

    if( isset($data['excludes']) && is_array($data['excludes']) )
    {
        $data['excludes'] = join(',', $data['excludes']);
    }
    else
    {
        $data['excludes'] = STRING_BLANK;
    }

    if( !string_is_empty($data['password']) && strlen($data['password']) != 40 )
    {
        $data['password'] = sha1($data['password']);
    }
    else if( $editing )
    {
        unset($data['password']);
    }

    if( !$editing )
    {
        $data['timestamp_active'] = null;
        $data['timestamp_added'] = $data['timestamp_autostop'] = time();

        if( $data['flag_confirm'] )
        {
            $data['status'] = STATUS_UNCONFIRMED;
        }
    }

    if( !isset($data['thumbnails']) )
    {
        $data['thumbnails'] = 0;
    }

    return $data;
}

function domain_from_url($url)
{
    $parsed_url = parse_url($url);
    return strtolower(preg_replace('~^www\.~i', '', $parsed_url['host']));
}

function string_remove_blank_lines($string, $sort = true)
{
    $string = string_format_lf($string);

    $lines = array();
    foreach( explode(STRING_LF_UNIX, $string) as $line )
    {
        if( !string_is_empty($line) )
        {
            $lines[] = $line;
        }
    }

    if( $sort )
    {
        sort($lines);
    }

    return join(STRING_LF_UNIX, $lines);
}

function string_format_comma_separated($string)
{
    if( strlen($string) < 1 || strstr($string, ',') === false )
    {
        return $string;
    }

    $items = array();

    foreach( explode(',', trim($string)) as $item )
    {
        $items[] = trim($item);
    }

    return join(',', $items);
}

function string_is_empty($string)
{
    if( is_array($string) || is_object($string) )
    {
        return false;
    }

    return preg_match('~^\s*$~s', $string) == 1;
}

function string_format_lf($string, $format = STRING_LF_UNIX)
{
    return is_array($string) ?
           array_map('string_format_lf', $string) :
           preg_replace('~' . STRING_LF_WINDOWS . '|' . STRING_LF_MAC . '|' . STRING_LF_UNIX . '~', $format, $string);
}

function string_htmlspecialchars($string)
{
    return is_array($string) ?
           array_map('string_htmlspecialchars', $string) :
           htmlspecialchars($string, ENT_QUOTES);
}

function string_strip_tags($string)
{
    return is_array($string) ?
           array_map('string_strip_tags', $string) :
           strip_tags($string);
}

function string_to_bool($value)
{
    if( is_bool($value) )
    {
        return $value;
    }
    else if( is_numeric($value) )
    {
        return $value != 0;
    }
    else if( preg_match('~^true$~i', $value) )
    {
        return true;
    }
    else if( preg_match('~^false$~i', $value) )
    {
        return false;
    }

    return false;
}

function string_stripslashes($value)
{
    return is_array($value) ?
           array_map('string_stripslashes', $value) :
           stripslashes($value);
}

function string_trim($value)
{
    return is_array($value) ?
           array_map('string_trim', $value) :
           trim($value);
}

function string_nullify($string)
{
    if( string_is_empty($string) )
    {
        return null;
    }

    return $string;
}


function log_append($file, $message, $date = null)
{
    if( empty($date) )
    {
        $date = date('r');
    }

    file_append(DIR_LOGS . '/' . $file, "[" . $date . "] $message\n");
}

function file_create($filename, $permissions = 0666)
{
    if( !file_exists($filename) )
    {
        file_write($filename, STRING_BLANK, $permissions);
    }
}

function file_write($filename, $data = STRING_BLANK, $permissions = 0666)
{
    $fh = fopen($filename, file_exists($filename) ? 'r+' : 'w');
    flock($fh, LOCK_EX);
    fseek($fh, 0);
    fwrite($fh, $data);
    ftruncate($fh, ftell($fh));
    flock($fh, LOCK_UN);
    fclose($fh);

    if( !empty($permissions) )
    {
        @chmod($filename, $permissions);
    }
}

function file_append($filename, $data = STRING_BLANK, $permissions = 0666)
{
    $fh = fopen($filename, 'a');
    flock($fh, LOCK_EX);
    fwrite($fh, $data);
    flock($fh, LOCK_UN);
    fclose($fh);

    if( !empty($permissions) )
    {
        @chmod($filename, $permissions);
    }
}

function file_first_line($filename)
{
    $fh = fopen($filename, 'r');
    flock($fh, LOCK_SH);
    $line = trim(fgets($fh));
    flock($fh, LOCK_UN);
    fclose($fh);

    return $line;
}

function file_delete($filename)
{
    @unlink($filename);
}

function file_sanitize($filename, $allowed_extensions = null, $force_extension = null)
{
    $info = pathinfo($filename);
    $last_period = strrpos($info['basename'], '.');
    $filename = $last_period === false ? $info['basename'] : substr($info['basename'], 0, $last_period);
    $extension = isset($info['extension']) ? $info['extension'] : '';
    $filename = preg_replace('~[^a-z0-9_\-.]~', '', $filename);

    if( !empty($allowed_extensions) )
    {
        if( !is_array($allowed_extensions) )
        {
            $allowed_extensions = explode(',', $allowed_extensions);
        }

        if( !in_array($info['extension'], $allowed_extensions) )
        {
            $extension = $force_extension;
        }
    }
    else if( !empty($force_extension) )
    {
        $extension = $force_extension;
    }

    $extension = !empty($extension) ? '.' . preg_replace('~[^a-z0-9]~', '', $extension) : '';

    if( string_is_empty($filename) )
    {
        $filename = 'none';
    }

    return preg_replace('~\.+~', '.', $filename . $extension);
}


function dir_create($directory, $mode = 0777)
{
    if( !is_dir($directory) && !file_exists($directory) )
    {
        $old_umask = umask(0);
        mkdir($directory, $mode);
        umask($old_umask);
    }
}

function dir_strip_trailing_slash($directory)
{
    return preg_replace('~/+$~', '', $directory);
}

function dir_remove($directory, $recursive = true)
{
    $directory = dir_strip_trailing_slash($directory);

    if( $recursive )
    {
        foreach( dir_read($directory, null, DIR_READ_ALL, false) as $item )
        {
            if( $item == '.' || $item == '..' )
            {
                continue;
            }

            is_dir("$directory/$item") ? dir_remove("$directory/$item", true) : file_delete("$directory/$item");
        }
    }

    @rmdir($directory);
}

function dir_read($directory, $pattern, $type, $sort = true)
{
    if( !file_exists($directory) )
    {
        trigger_error('File not found', E_USER_ERROR);
    }

    if( !is_dir($directory) )
    {
        trigger_error('Not a directory', E_USER_ERROR);
    }

    $contents = array();
    $dh = opendir($directory);
    while( ($file = readdir($dh)) !== false )
    {
        if( $type & DIR_READ_ALL || (($type & DIR_READ_FILES) && is_file("$directory/$file")) || (($type & DIR_READ_DIRECTORIES) && is_dir("$directory/$file")) )
        {
            $contents[] = $file;
        }
    }
    closedir($dh);

    if( $pattern )
    {
        $contents = preg_grep($pattern, $contents);
    }

    $contents = array_values($contents);

    if( $sort )
    {
        natsort($contents);
    }

    return $contents;
}

function dir_read_all($directory, $pattern = null, $sort = true)
{
    return dir_read($directory, $pattern, DIR_READ_ALL, $sort);
}

function dir_read_directories($directory, $pattern = null, $sort = true)
{
    return dir_read($directory, $pattern, DIR_READ_DIRECTORIES, $sort);
}

function dir_read_files($directory, $pattern = null, $sort = true)
{
    return dir_read($directory, $pattern, DIR_READ_FILES, $sort);
}


function form_options($options = array(), $selected = null)
{
    $html = STRING_BLANK;

    if( !is_array($options) )
    {
        $options = explode(',', string_format_comma_separated($options));
    }

    if( count($options) < 1 )
    {
        return $html . STRING_LF_UNIX;
    }

    foreach( $options as $option )
    {
        $html .= '<option value="' . htmlspecialchars($option) . '"' . ($option == $selected ? ' selected="selected"' : '') . '>' .
                 htmlspecialchars($option) . '</option>' . STRING_LF_UNIX;
    }

    return $html;
}

function form_options_hash($options = array(), $selected = null)
{
    $html = STRING_BLANK;

    if( !is_array($options) )
    {
        $options = explode(',', string_format_comma_separated($options));
    }

    if( count($options) < 1 )
    {
        return $html . STRING_LF_UNIX;
    }

    foreach( $options as $value => $text )
    {
        $html .= '<option value="' . htmlspecialchars($value) . '"' . ($value == $selected ? ' selected="selected"' : '') . '>' .
                 htmlspecialchars($text) . '</option>' . STRING_LF_UNIX;
    }

    return $html;
}

function form_options_multi($options = array(), $selected = array())
{
    $html = STRING_BLANK;

    if( !is_array($options) )
    {
        $options = explode(',', string_format_comma_separated($options));
    }

    if( !is_array($selected) )
    {
        $selected = explode(',', string_format_comma_separated($selected));
    }

    if( count($options) < 1 )
    {
        return $html . STRING_LF_UNIX;
    }

    foreach( $options as $option )
    {
        $html .= '<option value="' . htmlspecialchars($option) . '"' . (in_array($option, $selected) ? ' selected="selected"' : '') . '>' .
                 htmlspecialchars($option) . '</option>' . STRING_LF_UNIX;
    }

    return $html;
}

function form_checkbox($name, $label, $value = 0, $attrs = '')
{
    return '<div class="checkbox">' . STRING_LF_UNIX .
           '  <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $attrs . '/>' . STRING_LF_UNIX .
           '  ' . $label . STRING_LF_UNIX .
           '</div>';
}


function format_int_to_string($integer)
{
    global $C;
    return is_numeric($integer) ? number_format($integer, 0, $C['dec_point'], $C['thousands_sep']) : $integer;
}

function format_float_to_string($float, $decimals)
{
    global $C;
    return is_numeric($float) ? number_format($float, $decimals, $C['dec_point'], $C['thousands_sep']) : $float;
}

function format_float_to_percent($float, $precision = 0)
{
    return sprintf('%.' . $precision . 'f', $float * 100);
}


if( !function_exists('array_combine') )
{
    function array_combine($arr1, $arr2)
    {
        $out = array();

        $arr1 = array_values($arr1);
        $arr2 = array_values($arr2);

        foreach( $arr1 as $key1 => $value1 )
        {
            $out[(string)$value1] = isset($arr2[$key1]) ? $arr2[$key1] : null;
        }

        return $out;
    }
}
?>
