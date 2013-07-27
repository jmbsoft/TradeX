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

// Store the original working directory and change into the TradeX directory
$cwd = getcwd();
chdir(realpath(dirname(__FILE__)));

// Store the original include path and adjust to cwd only
$include_path = get_include_path();
set_include_path('.');

// Store the original timezone, and set to server timezone (PHP 5.1+ only)
//$timezone = date_default_timezone_get();
//date_default_timezone_set('America/Chicago');

// LinkX compatibility
$g_saved_config = null;
if( isset($GLOBALS['C']) && isset($GLOBALS['C']['page_new']) )
{
    $g_saved_config = $GLOBALS['C'];
}


// PHP configuration settings
@ini_set('memory_limit', '128M');
@set_magic_quotes_runtime(0);
@set_time_limit(90);


// Configuration settings
/*#<CONFIG>*/
$C = array('cookie_domain' => 'soft.jmb-soft.com',
'domain' => 'soft.jmb-soft.com',
'keyphrase' => 'd06c8abb142c4eda4d60df1aed2dde25',
'flag_filter_no_image' => '1');
/*#</CONFIG>*/


// Variables
$now = time();
$track_hit = true;
$is_engine = false;
$is_unique = true;
$session_length = 3600;
$cookie_session = false;
$ip_hash = md5($_SERVER['REMOTE_ADDR']);
$session_file = "data/sessions/{$ip_hash[0]}/{$ip_hash[0]}/{$_SERVER['REMOTE_ADDR']}";


// Session defaults
$session = array(
    't'   => 'no-referrer',
    'sys' => true,
    'l'   => null,
    'p'   => false,
    'd'   => null,
    'se'  => null,
    'st'  => null,
    'c'   => 246,
    'cq'  => 1,
    'cl'  => 0,
    'v'   => array(),
    'ei'  => isset($_GET['x']) ? explode('.', $_GET['x']) : array(),
    'ca'  => null,
    'ct'  => microtime_float(),
    'ni'  => $C['flag_filter_no_image'] ? true : false
);


// Cleanup variables
$_SERVER['HTTP_USER_AGENT'] = str_replace('|', '', $_SERVER['HTTP_USER_AGENT']);
$_SERVER['HTTP_REFERER'] = str_replace('|', '', $_SERVER['HTTP_REFERER']);


// Examine cookie
if( isset($_COOKIE['tdxsess']) )
{
    $is_unique = false;
    $cookie_data = base64_decode($_COOKIE['tdxsess']);
    $cookie_signature = sha1($C['keyphrase'] . $cookie_data);
    $cookie_session = unserialize($cookie_data);

    // Bad signature, so get session information from file
    if( !isset($_COOKIE['tdxsig']) || $_COOKIE['tdxsig'] != $cookie_signature )
    {
        $cookie_session = @unserialize(@file_get_contents($session_file));
    }
}


// Get session information from session file
else
{
    if( file_exists($session_file) )
    {
        $is_unique = false;
        $cookie_session = @unserialize(@file_get_contents($session_file));
    }
}


// HTTP_REFERER is set and not empty
if( isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) )
{
    $parsed = parse_url($_SERVER['HTTP_REFERER']);

    // Don't track invalid URLs, invalid hostnames or intra-site traffic
    if( $parsed === false || !isset($parsed['host']) || strpos($parsed['host'], $C['domain']) !== false || !preg_match('~^[a-z0-9]+[a-z0-9.\-]+?[a-z0-9]$~i', $parsed['host']) )
    {
        $track_hit = false;
    }

    else
    {
        $host = strtolower($parsed['host']);
        $session['d'] = strpos($host, 'www.') === 0 ? str_replace('www.', '', $host) : $host;

        // See if this is a trade
        if( file_exists('data/trades/' . $session['d']) )
        {
            $session['ei'][] = substr(crc32($session['d']), -4);
            $session['t'] = 'trade';
            $session['sys'] = false;
        }

        // See if this is a search engine
        else
        {
            //$session['d'] = null;

            $engines = array(/*#<ENGINES>*/'7search.','adflasher.','aim.search.aol.','alicesuche.aol.','alltheinternet.','alltheweb.','altavista.','aolrecherche.aol.','aolsearch.aol.co.','aolsearcht1.search.aol.','aolsearcht10.search.aol.','aolsearcht2.search.aol.','aolsearcht3.search.aol.','aolsearcht4.search.aol.','aolsearcht5.search.aol.','aolsearcht6.search.aol.','aolsearcht7.search.aol.','aolsearcht8.search.aol.','aolsearcht9.search.aol.','arianna.libero.','armstrongmywire.','ask.','askmen.','askpeter.','atlanticbb.','att.','au.search.yahoo.','baidu.','bestlookit.','bing.','bollywoodsargam.','br.search.yahoo.','buscador.terra.com.','buscador.terra.es.','ca.search.yahoo.','centurylink.','charter.','clusty.','comcast.','crawler.','de.altavisa.','de.search.yahoo.','delicious.','devilfinder.','dogpile.','earthlink.','element.serachpluswin.','eniro.','es.altavista.','es.search.yahoo.','fastbrowsersearch.','fi.search.','fireball.','fr.search.yahoo.','google.co.in.','google.co.','google.','hk.search.yahoo.','home.myhughesnet.','home.speedbit.','home.suddenlink.','icq.','images.es.ask.','images.google.','images.search.yahoo.','images.uk.ask.','iminent.','impapp.aol.','incredimail.','info.','isearch.babylon.','japanesegarden.','jumpopen.','kvasir.','live.vodafone.','livefreesearch.','m.bing.','m.google.','m.yahoo.','mchsi.','mediacomtoday.','msxml.excite.','mx.search.yahoo.','my.att.','my.freeze.','mycricket.','mysearch.','mywebsearch.','nanotraff.','navigationshilfe.t-online.','net.','netscape.','news.eircom.','nigma.ru','nl.search.yahoo.','no.search.yahoo.','nova.rambler.','nwcable.','nz.search.yahoo.','portal.wowway.','qx.search.yahoo.','reallybigsearch.','ricerca.virgilio.','sanford.metrocast.','scroogle.','search.aol.','search.aol.','search.babylon.','search.bt.','search.comcast.','search.conduit.','search.earthlink.','search.icq.','search.incredimail.','search.lycos.','search.myway.','search.mywebsearch.','search.orange.co.','search.peoplepc.','search.rr.','search.sify.','search.sky.','search.sweetim.','search.virginmedia.','search.winamp.','search.windstream.','search.yahoo.','search.','search27.','search29.info.','searchalot.','searchatlas.centrum.','searchcanvas.','searchservice.myspace.','seoarea.','sg.search.yahoo.','sogou.','start.facemoods.','start.speedbit.','startgoogle.startpagina.','startlap.','suche.aol.','suche.gmx.','suche.t-online.','suche.web.','szukaj.onet.','talktalk.co.','talktalk.','tattoodle.','terra.com.','toolbar.inbox.','toolbarhome.','tw.search.yahoo.','twcsearch.fastsearch.','uk.m.yahoo.','uk.search.yahoo.','univision.','us.m.yahoo.','us.m2.yahoo.','us.yhs.search.yahoo.','verden.abcsok.','verizon.','wap.aol.','wap.google.','wap.vodaphone.','websearch.cs.','www5.google.','yandex.','yidong.google.','zuula.'/*#</ENGINES>*/);
            foreach( $engines as $i => $session['se'] )
            {
                if( strpos($session['d'], $session['se']) === 0 )
                {
                    $is_engine = true;
                    $engine_parameters = array(/*#<ENGINE_PARAMS>*/'qu','q','query','q','s','q','q','query','q','q','q','q','q','q','q','q','q','q','q','query','q','q','q','q','q','string','p','wd','key','q','search_term','p','query','query','p','q','q','query','q','q','q','p','p','q','@/dogpile/ws/results/Web/(.*?)/','q','q','search_word','q','p','q','p','q','p','q','q','q','p','q','q','q','q','q','q','p','q','@/iminent-es_mx/ws/results/Web/(.*?)/','txtSearch','q','qkw','q','term','q','q','searchText','q','q','q','p','q','q','q','p','string','keywords','q','searchfor','searchfor','q','q','q','q','q','s','p','p','query','q','p','q','p','keywords','qs','q','Gw','q','query','q','p','q','q','q','q','q','query','searchfor','searchfor','q','q','qs','q','term','q','q','query','@/windstream/ws/results/Web/(.*?)/','p','q','qkw','qkw','q','q','q','qry','q','p','query','w','q','q','q','query','su','q','su','qt','query','query','q','query','qkw','q','p','q','p','p','query','p','p','p','q','q','query','q','searchText','query','q','text','q','st'/*#</ENGINE_PARAMS>*/);

                    if( $engine_parameters[$i][0] == '@' )
                    {
                        if( preg_match('~' . substr($engine_parameters[$i], 1) . '~i', $parsed['path'], $matches) )
                        {
                            $session['st'] = stripslashes($matches[1]);
                        }
                    }
                    else
                    {
                        parse_str($parsed['query'], $query);
                        $session['st'] = stripslashes($query[$engine_parameters[$i]]);
                    }

                    $session['t'] = 'search-engine';

                    break;
                }
            }

            if( !$is_engine )
            {
                $session['se'] = null;
                $session['t'] = 'non-trade';
            }
        }
    }
}


// Check last update to see if it is time for building toplists, resetting stats, generating outlist, etc
$last = filemtime('data/times/stats');
$date_now = date('YmdHi', $now);
$date_last = date('YmdHi', $last);

if( $date_now != $date_last )
{
    $fp = fopen('data/times/stats', 'w');
    flock($fp, LOCK_EX | LOCK_NB, $would);

    // We have the lock, proceed with stats update
    if( !$would )
    {
        require_once 'lib/global.php';
        require_once 'utility.php';

        stats_update($now, $last, true);
    }
    else
    {
        // Other process has the lock, wait for it to complete the stats update
        flock($fp, LOCK_EX);
    }

    flock($fp, LOCK_UN);
    fclose($fp);
}


// Track this hit
if( $track_hit )
{
    // Get information from the existing session
    if( $cookie_session !== false )
    {
        $session['l']  = $cookie_session['l'];
        $session['p']  = $cookie_session['p'];
        $session['c']  = $cookie_session['c'];
        $session['cq'] = $cookie_session['cq'];
    }

    // Lookup information for new session
    else
    {
        // Get country id and quality
        list($session['c'], $session['cq']) = geoip_country($_SERVER['REMOTE_ADDR']);

        // Get system language
        if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && preg_match('~^([^,;]+)~', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches) )
        {
            $session['l'] = strtolower($matches[1]);
        }

        // Check proxy headers
        if( isset($_SERVER['HTTP_VIA']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
        {
            $session['p'] = true;
        }
    }


    // Generate session file
    $fp = fopen($session_file, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, serialize($session));
    flock($fp, LOCK_UN);
    fclose($fp);


    // Log search engine and search term
    if( !empty($session['se']) )
    {
        $fp = fopen('data/search_terms', 'a');
        flock($fp, LOCK_EX);
        fwrite($fp, "$now|{$session['se']}|{$session['st']}\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }


    // Log client data
    $logfile = $session['sys'] ? "data/system_stats/{$session['t']}-in" : "data/trade_stats/{$session['d']}-in";
    $fp = fopen($logfile, 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, "$now|{$_SERVER['REMOTE_ADDR']}|{$session['p']}|{$_SERVER['HTTP_USER_AGENT']}|{$session['c']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$session['l']}\n");
    flock($fp, LOCK_UN);
    fclose($fp);


    // Update incoming stats
    $record_items = 22;
    $in_items = 6;
    $pack_arg = 'L' . $in_items;
    $record_size = $record_items * 4;
    $in_size = $in_items * 4;
    $hour_now = substr($date_now, 8, 2);
    $minute_now = substr($date_now, 10, 2);
    $hour_offset = $hour_now * $record_size;
    $minute_offset = (($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size);
    $statsfile = $session['sys'] ? "data/system_stats/{$session['t']}" : "data/trade_stats/{$session['d']}";
    $fp = fopen($statsfile, 'r+');
    flock($fp, LOCK_EX);

    // Seek to hour, read, update
    fseek($fp, $hour_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $in_size));
    $r[1]++;
    if( $is_unique ) $r[2]++;
    if( $session['p'] ) $r[3]++;
    $r[4 + $session['cq']]++;
    fseek($fp, -$in_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $in_size);


    // Seek to minute, read, update
    fseek($fp, $minute_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $in_size));
    $r[1]++;
    if( $is_unique ) $r[2]++;
    if( $session['p'] ) $r[3]++;
    $r[4 + $session['cq']]++;
    fseek($fp, -$in_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $in_size);

    flock($fp, LOCK_UN);
    fclose($fp);


    // Output Javascript code to set the session cookie
    $serialized = serialize($session);
    $expires = gmdate('D, d M Y H:i:s', $now + $session_length);
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n" .
         "document.cookie='tdxsess=" . base64_encode($serialized) . "; expires=" . $expires . " GMT; path=/; domain={$C['cookie_domain']}';\n" .
         "document.cookie='tdxsig=" . sha1($C['keyphrase'] . $serialized) . "; expires=" . $expires . " GMT; path=/; domain={$C['cookie_domain']}';\n" .
         "</script>\n";
}


// Debug logging
//$fp = fopen('logs/debug.log', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, "IN|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE['tdxsess']}|$serialized|$track_hit\n");
//flock($fp, LOCK_UN);
//fclose($fp);


// LinkX compatibility
if( !empty($g_saved_config) )
{
    $GLOBALS['C'] = $g_saved_config;
}

// Restore original include path
set_include_path($include_path);

// Restore original timezone (PHP 5.1+ only)
//date_default_timezone_set($timezone);

// Restore original working directory
chdir($cwd);


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
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}

?>