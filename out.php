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



@ini_set('memory_limit', -1);
@set_magic_quotes_runtime(0);
@set_time_limit(0);


/*#<CONFIG>*/
$C = array('domain' => 'soft.jmb-soft.com',
'keyphrase' => 'd06c8abb142c4eda4d60df1aed2dde25',
'distrib_forces' => '30',
'distrib_main' => '50',
'distrib_primary' => '10',
'distrib_secondary' => '10',
'count_clicks' => '10',
'fast_click' => '1.25',
'trades_satisfied_url' => 'http://www.anandtech.com/',
'flag_filter_no_image' => '1');
/*#</CONFIG>*/


// Globals
$g_force_type = null;


// Session variables
$now = time();
$session_length = 3600;
$unique = false;
$ip_hash = md5($_SERVER['REMOTE_ADDR']);
$session_file = "data/sessions/{$ip_hash[0]}/{$ip_hash[0]}/{$_SERVER['REMOTE_ADDR']}";
$g_external_info = false;


// Session defaults
$g_session = array(
    't' => 'unknown',
    'sys' => true,
    'l' => null,
    'p' => false,
    'd' => null,
    'se' => null,
    'st' => null,
    'c'   => 246,
    'cq'  => 1,
    'cl'  => 0,
    'ei' => array(),
    'ca'  => null,
    'v' => array(),
    'ni' => false
);


$_SERVER['HTTP_USER_AGENT'] = str_replace('|', '', $_SERVER['HTTP_USER_AGENT']);
$_SERVER['HTTP_REFERER'] = str_replace('|', '', $_SERVER['HTTP_REFERER']);


// Session cookie is set
if( isset($_COOKIE['tdxsess']) )
{
    $cookie_data = base64_decode($_COOKIE['tdxsess']);
    $cookie_signature = sha1($C['keyphrase'] . $cookie_data);
    $g_session = unserialize($cookie_data);

    // Bad signature
    if( !isset($_COOKIE['tdxsig']) || $_COOKIE['tdxsig'] != $cookie_signature )
    {
        $g_session['t'] = 'unknown';
        $g_session['sys'] = true;
    }

    // Cookie data does not match server-side stored data
    else if( !is_file($session_file) || $cookie_data != file_get_contents($session_file) )
    {
        $g_session['t'] = 'unknown';
        $g_session['sys'] = true;
    }
}
else
{
    // Existing session
    if( ($session_time = @filemtime($session_file)) !== false && $session_time >= $now - $session_length )
    {
        $g_session = unserialize(file_get_contents($session_file));
        $g_session['t'] = 'no-cookie';
        $g_session['sys'] = true;
    }
}


// Filter no-image
if( $C['flag_filter_no_image'] && $g_session['ni'] === true )
{
    $g_session['t'] = 'no-image';
    $g_session['sys'] = true;
}


// Set to not-counted if click amount is too high
if( $g_session['cl'] + 1 > $C['count_clicks'] )
{
    $g_session['t'] = 'not-counted';
    $g_session['sys'] = true;
}


$i_skim_scheme = $g_session['sys'] ? 2 : 23;
$trade = explode('|', file_get_contents($g_session['sys'] ? "data/system/{$g_session['t']}" : "data/trades/{$g_session['d']}"));
$trade['domain'] = $g_session['d'];


// Skim percent set in URL (top priority)
if( $_GET['s'] )
{
    $skim = intval($_GET['s']);
}

// Skim scheme set in URL (second priority)
else if( $_GET['ss'] )
{
    $skim = skim_from_scheme($_GET['ss'], $g_session['cl']);
}

// Use skim scheme of trade (low priority)
else
{
    $skim = skim_from_scheme($trade[$i_skim_scheme], $g_session['cl']);
}


// Flag to indicate if this click is going to a trade
$to_trade = false;
$is_first_click = $_GET['fc'] && $g_session['cl'] == 0;
if( !$is_first_click && $skim < 100 && mt_rand(1,100) >= $skim )
{
    $to_trade = true;
}


// System trade, determine send method
if( $g_session['sys'] )
{
    switch($trade[4])
    {
        case '0': // Normal
            break;

        case '1': // Only to trades
            $to_trade = true;
            break;

        case '2': // To content if specified, otherwise traffic URL
            if( !isset($_GET['u']) )
            {
                $_GET['u'] = $trade[5];
            }
            $to_trade = false;
            break;


        case '3': // Only to traffic URL
            $_GET['u'] = $trade[5];
            $to_trade = false;
            break;
    }
}


// Sending to trade if no parameters specified
if( !$to_trade && !isset($_GET['u']) && !isset($_GET['t']) )
{
    $to_trade = TRUE;
}


// Sending to a specific trade
if( isset($_GET['t']) && file_exists("data/trades/{$_GET['t']}") )
{
    $to_trade = true;
    $send_to_trade = basename($_GET['t']);
    $send_to_data = explode('|', file_get_contents("data/trades/$send_to_trade"));
    $_GET['u'] = $send_to_data[0];
    $g_external_info = $send_to_data[37];
}

// Select trade
else if( $to_trade )
{
    $send_to_trade = select_trade($trade);

    if( empty($send_to_trade) )
    {
        $_GET['u'] = $C['trades_satisfied_url'];
        $to_trade = false;
    }
}


// Stats files sizes and times
$record_items = 22;
$record_size = $record_items * 4;
$date_now = date('YmdHi', $now);
$hour_now = substr($date_now, 8, 2);
$minute_now = substr($date_now, 10, 2);


// Sending to trade, so get return URL and update out stats
if( $to_trade && file_exists("data/trade_stats/$send_to_trade") )
{
    // Update visited trades
    $unique = !in_array($send_to_trade, $g_session['v']);
    $g_session['v'][] = $send_to_trade;
    $g_session['ei'][] = substr(crc32($send_to_trade), -4);


    // Update out stats
    $out_items = 8;
    $pack_arg = 'L' . $out_items;
    $out_size = $out_items * 4;
    $hour_offset = ($hour_now * $record_size) + (14 * 4);
    $minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (14 * 4);
    $fp = fopen("data/trade_stats/$send_to_trade", 'r+');
    flock($fp, LOCK_EX);

    // Seek to hour, read, update
    fseek($fp, $hour_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $out_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[4 + $g_session['cq']]++;
    if( $g_force_type == 'I' ) $r[7]++;
    if( $g_force_type == 'H' ) $r[8]++;
    fseek($fp, -$out_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8]), $out_size);

    // Seek to minute, read, update
    fseek($fp, $minute_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $out_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[4 + $g_session['cq']]++;
    if( $g_force_type == 'I' ) $r[7]++;
    if( $g_force_type == 'H' ) $r[8]++;
    fseek($fp, -$out_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8]), $out_size);

    flock($fp, LOCK_UN);
    fclose($fp);


    // Update out log
    $fp = fopen("data/trade_stats/$send_to_trade-out", 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, "$now|{$_SERVER['REMOTE_ADDR']}|{$g_session['p']}|{$_SERVER['HTTP_USER_AGENT']}|{$g_session['c']}|{$_SERVER['HTTP_REFERER']}|{$_GET['l']}|{$g_session['l']}|0|0\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

// Update user click count
$unique = $g_session['cl'] == 0;
$g_session['cl']++;


// Update click stats
$click_items = 6;
$pack_arg = 'L' . $click_items;
$click_size = $click_items * 4;
$hour_offset = ($hour_now * $record_size) + (6 * 4);
$minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (6 * 4);
$statsfile = $g_session['sys'] ? "data/system_stats/{$g_session['t']}" : "data/trade_stats/{$g_session['d']}";

if( file_exists($statsfile) )
{
    $fp = fopen($statsfile, 'r+');
    flock($fp, LOCK_EX);

    // Seek to hour, read, update
    fseek($fp, $hour_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $click_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    if( $to_trade ) $r[4]++;
    $r[6 + $g_session['cq']]++;
    fseek($fp, -$click_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $click_size);

    // Seek to minute, read, update
    fseek($fp, $minute_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $click_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[6 + $g_session['cq']]++;
    fseek($fp, -$click_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $click_size);

    flock($fp, LOCK_UN);
    fclose($fp);
}

// Update click again stats
if( !empty($g_session['ca']) )
{
    $g_session['ca'] = basename($g_session['ca']);

    if( file_exists("data/trade_stats/{$g_session['ca']}") )
    {
        $ca_items = 1;
        $pack_arg = 'L' . $ca_items;
        $ca_size = $ca_items * 4;
        $hour_offset = ($hour_now * $record_size) + (10 * 4);
        $minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (10 * 4);
        $fp = fopen("data/trade_stats/{$g_session['ca']}", 'r+');
        flock($fp, LOCK_EX);

        // Seek to hour, read, update
        fseek($fp, $hour_offset, SEEK_SET);
        $r = unpack($pack_arg, fread($fp, $ca_size));
        $r[1]++;
        fseek($fp, -$ca_size, SEEK_CUR);
        fwrite($fp, pack($pack_arg, $r[1]), $ca_size);

        // Seek to minute, read, update
        fseek($fp, $minute_offset, SEEK_SET);
        $r = unpack($pack_arg, fread($fp, $ca_size));
        $r[1]++;
        fseek($fp, -$ca_size, SEEK_CUR);
        fwrite($fp, pack($pack_arg, $r[1]), $ca_size);

        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
$g_session['ca'] = $to_trade ? $send_to_trade : null;


// Update click log
$fast = (microtime_float() - $g_session['ct']) <= $C['fast_click'];
$logfile = $g_session['sys'] ? "data/system_stats/{$g_session['t']}-clicks" : "data/trade_stats/{$g_session['d']}-clicks";
if( file_exists($logfile) )
{
    $fp = fopen($logfile, 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, "$now|{$_SERVER['REMOTE_ADDR']}|{$g_session['p']}|{$_SERVER['HTTP_USER_AGENT']}|{$g_session['c']}|{$_SERVER['HTTP_REFERER']}|{$_GET['l']}|{$g_session['l']}|$fast|{$g_session['ni']}\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}


// Update cookie
$g_session['ei'] = array_unique($g_session['ei']);
$g_session['ct'] = microtime_float();
$serialized = serialize($g_session);
setcookie('tdxsess', base64_encode($serialized), $now + $session_length, '/', $C['domain']);
setcookie('tdxsig', sha1($C['keyphrase'] . $serialized), $now + $session_length, '/', $C['domain']);


// Update session file
$fp = fopen($session_file, 'w');
flock($fp, LOCK_EX);
fwrite($fp, $serialized);
flock($fp, LOCK_UN);
fclose($fp);


// Send to URL
$_GET['u'] = preg_match('~^http://~i', $_GET['u']) ? $_GET['u'] : base64_decode($_GET['u']);

if( $g_external_info )
{
    $_GET['u'] .= (strpos($_GET['u'], '?') === false ? '?' : '&') . 'x=' . join('.', $g_session['ei']);
}

header("Location: {$_GET['u']}", true, 301);

// Debug logging
//$fp = fopen('logs/debug.log', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, "OUT|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE['tdxsess']}|$serialized|$to_trade|{$_GET['u']}\n");
//flock($fp, LOCK_UN);
//fclose($fp);


function select_trade($trade)
{
    global $C, $g_force_type, $g_session, $g_external_info;

    if( filesize('data/outlist_forces') < 10 )
    {
        $C['distrib_main'] += $C['distrib_forces'];
        $C['distrib_forces'] = 0;
    }

    $percent = mt_rand(1,100);
    $select_order = null;

    if( $percent < $C['distrib_forces'] )
    {
        $select_order = array('outlist_forces', 'outlist_main');
    }
    else if( $percent > $C['distrib_forces'] && $percent < $C['distrib_forces'] + $C['distrib_main'] )
    {
        $select_order = array('outlist_main');
    }
    else if( $percent > $C['distrib_forces'] + $C['distrib_main'] && $percent < $C['distrib_forces'] + $C['distrib_main'] + $C['distrib_primary'] )
    {
        $select_order = array('outlist_primary');
    }
    else
    {
        $select_order = array('outlist_secondary');
    }


    $send_to_trade = array(null);
    $i_excludes = $g_session['sys'] ? 3 : 36;

    foreach( $select_order as $outlist_file )
    {
        $fp = fopen("data/$outlist_file", 'r+');
        flock($fp, LOCK_EX);

        while( !feof($fp) )
        {
            $ints = fread($fp, 8);

            if( feof($fp) )
            {
                break;
            }

            $ints = unpack('Lowe/Lsize', $ints);

            if( $ints['owe'] > 0 )
            {
                $stt = explode('|', fread($fp, $ints['size']));

                if( is_allowed_trade($trade, $stt, $i_excludes) )
                {
                    // Deduct owed
                    fseek($fp, -($ints['size'] + 8), SEEK_CUR);
                    fwrite($fp, pack('L', --$ints['owe']));
                    flock($fp, LOCK_UN);
                    fclose($fp);

                    $send_to_trade = $stt;

                    break 2;
                }
            }
            else
            {
                fseek($fp, $ints['size'], SEEK_CUR);
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);
    }


    if( !empty($send_to_trade[0]) )
    {
        $g_external_info = $send_to_trade[7];
        $_GET['u'] = $send_to_trade[4];

        if( $outlist_file == 'outlist_main' && $send_to_trade[5] )
        {
            $g_force_type = 'I';
        }
        else if( $outlist_file == 'outlist_forces' )
        {
            $g_force_type = $send_to_trade[6];
        }
    }

    return $send_to_trade[0];
}

function is_allowed_trade(&$trade, &$send_to_trade, &$i_excludes)
{
    global $g_session;

    // Don't send back to self
    if( !empty($trade['domain']) && $trade['domain'] == $send_to_trade[0] )
    {
        return false;
    }

    // Check category
    if( isset($_GET['c']) && !empty($send_to_trade[2]) && strpos($send_to_trade[2], ",{$_GET['c']},") === false )
    {
        return false;
    }

    // Check group
    if( isset($_GET['g']) && !empty($send_to_trade[3]) && strpos($send_to_trade[3], ",{$_GET['g']},") === false )
    {
        return false;
    }

    // Check excludes
    if( !empty($trade[$i_excludes]) && strpos(",{$trade[$i_excludes]},", ",{$send_to_trade[0]},") !== false )
    {
        return false;
    }

    // Don't send to already visited
    if( in_array($send_to_trade[0], $g_session['v']) )
    {
        return false;
    }

    // Don't send to already visited (external info)
    if( in_array(substr(crc32($send_to_trade[0]), -4), $g_session['ei']) )
    {
        return false;
    }

    return true;
}

function skim_from_scheme($scheme_name, $click)
{
    // Sanitize
    $scheme_name = preg_replace('~[^a-z0-9\-_]~i', '', $scheme_name);

    if( file_exists("data/skim_schemes/$scheme_name") )
    {
        $scheme = explode('|', file_get_contents("data/skim_schemes/$scheme_name"));
        return $scheme[$click % 50];
    }

    // Return default skim
    return 70;
}

function geoip_country($ip_address)
{
    $geoip_country_begin = 16776960;
    $standard_record_length = 3;

    $fp = fopen('assets/geoip.dat', 'rb');
    $long_ip = ip2long($ip_address);
    $offset = 0;
    $country_id = null;
    $quality = 1;

    for( $depth = 31; $depth >= 0; --$depth )
    {
        fseek($fp, 2 * $standard_record_length * $offset, SEEK_SET);
        $buf = fread($fp, 2 * $standard_record_length);

        $x = array(0,0);

        for( $i = 0; $i < 2; ++$i )
        {
            for( $j = 0; $j < $standard_record_length; ++$j )
            {
                $x[$i] += ord($buf[$standard_record_length * $i + $j]) << ($j * 8);
            }
        }

        if( $long_ip & (1 << $depth) )
        {
            if( $x[1] >= $geoip_country_begin )
            {
               $country_id = $x[1] - $geoip_country_begin;
            }

            $offset = $x[1];
        }
        else
        {
            if( $x[0] >= $geoip_country_begin )
            {
                $country_id = $x[0] - $geoip_country_begin;
            }

            $offset = $x[0];
        }

        if( !empty($country_id) )
        {
            break;
        }
    }

    fclose($fp);

    if( !empty($country_id) )
    {
        $record_size_country_weight = 20;
        $record_size_country = 1;

        $fp = fopen('data/countries', 'r');
        fseek($fp, $record_size_country_weight + $country_id * $record_size_country, SEEK_SET);
        $quality = fread($fp, $record_size_country);
        fclose($fp);
    }

    return array($country_id, $quality);
}



// Get microtime as float
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


?>
