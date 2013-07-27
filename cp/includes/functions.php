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


require_once '../lib/global.php';
require_once 'utility.php';

// Indicate we are in the control panel
define('IN_CONTROL_PANEL', true);

// Directories
define('DIR_CP_INCLUDES', DIR_BASE . '/cp/includes');
define('DIR_CP_SESSION', DIR_DATA . '/cp_sessions');

// Files
define('FILE_CP_USER', DIR_DATA . '/cp_user');

// Misc defines
define('CP_USERNAME_FIELD', 'cp_username');
define('CP_PASSWORD_FIELD', 'cp_password');
define('CP_SESSION_FIELD', 'cp_session');
define('CP_COOKIE_NAME', 'tradexcp');
define('CP_COOKIE_PATH', preg_replace('~/cp/.*~', '/cp/', $_SERVER['REQUEST_URI']));
define('CP_COOKIE_DOMAIN', preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']));
define('CP_SESSION_DURATION', 7776000);


// Setup include path
set_include_path(join(PATH_SEPARATOR, array(get_include_path(), DIR_CP_INCLUDES)));


// Check if it is time for an update
stats_check_update_time();

function cp_authenticate($session = true)
{
    cp_session_cleanup();

    if( isset($_REQUEST[CP_USERNAME_FIELD]) )
    {
        if( string_is_empty($_REQUEST[CP_USERNAME_FIELD]) )
        {
            return 'The username field was left blank';
        }

        if( string_is_empty($_REQUEST[CP_PASSWORD_FIELD]) )
        {
            return 'The password field was left blank';
        }



        list($username, $password) = explode('|', file_first_line(FILE_CP_USER));

        if( $username == $_REQUEST[CP_USERNAME_FIELD] && $password == sha1($_REQUEST[CP_PASSWORD_FIELD]) )
        {
            if( $session )
            {
                cp_session_create($username);
            }

            return true;
        }

        return 'The supplied username/password combination is not valid';
    }
    else if( isset($_COOKIE[CP_COOKIE_NAME]) )
    {
        return cp_session_authenticate($_COOKIE[CP_COOKIE_NAME]);
    }
}

function cp_session_authenticate($cookie)
{
    parse_str($cookie, $cookie_data);
    $filename = DIR_CP_SESSION . '/' . file_sanitize($cookie_data[CP_SESSION_FIELD]);

    if( file_exists($filename) && is_file($filename) )
    {
        list($username, $session, $timestamp, $browser, $ip) = explode('|', file_first_line($filename));

        if( $username == $cookie_data[CP_USERNAME_FIELD] && $browser == sha1($_SERVER['HTTP_USER_AGENT']) && $ip == $_SERVER['REMOTE_ADDR'] )
        {
            define('CP_LOGGED_IN_USERNAME', $username);
            return true;
        }

        cp_logout();
        return 'Invalid control panel account';
    }
    else
    {
        cp_logout();
        return 'Your control panel session has expired';
    }
}

function cp_session_create($username)
{
    $session = sha1(uniqid(rand(), true));
    $filename = DIR_CP_SESSION . '/' . $session;

    define('CP_LOGGED_IN_USERNAME', $username);

    file_write($filename, "$username|$session|" . time() . "|" . sha1($_SERVER['HTTP_USER_AGENT']) . "|{$_SERVER['REMOTE_ADDR']}");

    setcookie(CP_COOKIE_NAME,
              CP_USERNAME_FIELD . '=' . urlencode($username) . '&' . CP_SESSION_FIELD . '=' . urlencode($session),
              0,
              CP_COOKIE_PATH,
              CP_COOKIE_DOMAIN);
}

function cp_logout()
{
    parse_str($_COOKIE[CP_COOKIE_NAME], $cookie_data);
    $filename = DIR_CP_SESSION . '/' . file_sanitize($cookie_data[CP_SESSION_FIELD]);

    file_delete($filename);
    setcookie(CP_COOKIE_NAME, false, time() - CP_SESSION_DURATION, CP_COOKIE_PATH, CP_COOKIE_DOMAIN);
}

function cp_session_cleanup($clean_all = false)
{
    foreach( dir_read_files(DIR_CP_SESSION) as $file )
    {
        $file = DIR_CP_SESSION . '/' . $file;
        list($username, $session, $timestamp, $browser, $ip) = explode('|', file_first_line($file));

        if( $clean_all || $timestamp < time() - CP_SESSION_DURATION )
        {
            file_delete($file);
        }
    }
}

function merge_skim_scheme($scheme)
{
    require_once 'dirdb.php';

    $scheme_base = '-1|00|00|-1|00|00|' . file_get_contents(DIR_SKIM_SCHEMES_BASE . '/' . $scheme);

    $db = new SkimSchemeBaseDB();
    $data = $db->Retrieve($scheme);

    if( $data['dynamic'] )
    {
        $scheme_dynamic = file_get_contents(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $scheme);
        file_write(DIR_SKIM_SCHEMES . '/' . $scheme, $scheme_dynamic . $scheme_base);
    }
    else
    {
        file_write(DIR_SKIM_SCHEMES . '/' . $scheme, $scheme_base);
    }
}

function write_config($settings)
{
    global $C;

    if( !file_exists(FILE_HISTORY) )
    {
        file_create(FILE_HISTORY);
    }

    unset($settings['r']);

    $settings['domain'] = preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']);

    $C = array_merge($C, $settings);

    check_image_resizer();

    $C['base_url'] = preg_replace('~/$~', '', $C['base_url']);

    $fp = fopen(DIR_LIB . '/config.php', 'r+');
    flock($fp, LOCK_EX);
    fwrite($fp, "<?php\nglobal \$C;\n\$C = array();\n");

    foreach( $C as $key => $val )
    {
        $val = str_replace(array('\"', '\.'), array('"', '.'), addslashes($val));
        fwrite($fp, "\$C['$key'] = '$val';\n");
    }

    fwrite($fp, "?>");
    ftruncate($fp, ftell($fp));
    flock($fp, LOCK_UN);
    fclose($fp);


    $in_settings = "\$C = array('cookie_domain' => '{$C['cookie_domain']}',\n" .
                   "'domain' => '{$C['domain']}',\n" .
                   "'keyphrase' => '{$C['keyphrase']}',\n" .
                   "'flag_filter_no_image' => '{$C['flag_filter_no_image']}');";

    $out_settings = "\$C = array('domain' => '{$C['cookie_domain']}',\n" .
                    "'keyphrase' => '{$C['keyphrase']}',\n" .
                    "'distrib_forces' => '{$C['distrib_forces']}',\n" .
                    "'distrib_main' => '{$C['distrib_main']}',\n" .
                    "'distrib_primary' => '{$C['distrib_primary']}',\n" .
                    "'distrib_secondary' => '{$C['distrib_secondary']}',\n" .
                    "'count_clicks' => '{$C['count_clicks']}',\n" .
                    "'fast_click' => '{$C['fast_click']}',\n" .
                    "'trades_satisfied_url' => '{$C['trades_satisfied_url']}',\n" .
                    "'flag_filter_no_image' => '{$C['flag_filter_no_image']}');";

    $img_settings = "\$C = array('dir_base' => '" . DIR_BASE . "',\n" .
                    "'domain' => '{$C['cookie_domain']}',\n" .
                    "'keyphrase' => '{$C['keyphrase']}');";

    // Write settings to in.php
    $in = string_format_lf(file_get_contents(FILE_IN_PHP));
    $in = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $in_settings . "\n/*#</CONFIG>*/", $in);
    if( version_compare(PHP_VERSION, '5.1.0', '>=') )
    {
        $timezone = date_default_timezone_get();
        $in = preg_replace('~/?/?date_default_timezone_set\(\'.*?\'\);~', "date_default_timezone_set('$timezone');", $in);
        $in = str_replace('//date_default_timezone_set($timezone);', 'date_default_timezone_set($timezone);', $in);
        $in = str_replace('//$timezone = date_default_timezone_get();', '$timezone = date_default_timezone_get();', $in);
    }
    file_write(FILE_IN_PHP, $in, null);

    // Write settings to out.php
    $out = string_format_lf(file_get_contents(FILE_OUT_PHP));
    $out = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $out_settings . "\n/*#</CONFIG>*/", $out);
    file_write(FILE_OUT_PHP, $out, null);

    // Write settings to image.php
    $img = string_format_lf(file_get_contents(FILE_IMAGE_PHP));
    $img = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $img_settings . "\n/*#</CONFIG>*/", $img);
    file_write(FILE_IMAGE_PHP, $img, null);
}

function load_countries()
{
    global $geoip_country_codes, $geoip_country_names;

    require_once 'geoip-utility.php';

    $fp = fopen(FILE_COUNTRIES, 'r');
    $weights = explode('|', trim(fread($fp, RECORD_SIZE_COUNTRY_WEIGHT)));

    $countries = array(0 => array(),
                       1 => array(),
                       2 => array());

    asort($geoip_country_codes);
    foreach( $geoip_country_codes as $i => $code )
    {
        if( string_is_empty($code) )
        {
            continue;
        }

        fseek($fp, RECORD_SIZE_COUNTRY_WEIGHT + $i * RECORD_SIZE_COUNTRY);
        $quality = fread($fp, RECORD_SIZE_COUNTRY);
        $countries[$quality][]
          = array($code,
                  $geoip_country_names[$i]);
    }
    fclose($fp);

    return array($weights, $countries);
}

function recompile_templates()
{
    require_once 'compiler.php';

    $compiler = new Compiler();
    $files = dir_read_files(DIR_TEMPLATES);

    foreach( $files as $file )
    {
        $compiled = DIR_COMPILED . '/' . $file;

        if( ($code = $compiler->CompileFile($file, DIR_TEMPLATES)) === false )
        {
            return array(JSON_KEY_MESSAGE => 'Template ' . $file . ' contains errors',
                         JSON_KEY_WARNINGS => Compiler::GetErrors());
        }

        file_write($compiled, $code);
    }

    return true;
}

function cp_exec($function, $default = '_xIndexShow')
{
    if( empty($function) )
    {
        call_user_func($default);
        return;
    }
    else if( preg_match('~^(_x[a-zA-Z0-9_]+)(\((.*?)\))?~', $function, $matches) )
    {
        $function = $matches[1];
        $arguments = isset($matches[3]) ? explode(',', $matches[3]) : array();

        if( function_exists($function) )
        {
            call_user_func_array($function, $arguments);
            return;
        }
    }

    trigger_error('Not a valid TradeX function', E_USER_ERROR);
}

function check_image_resizer()
{
    global $C;

    $C['have_magick'] = 0;
    $C['have_gd'] = 0;

    // Check ImageMagick
    if( !string_is_empty($C['magick_mogrify_path']) )
    {
        set_error_handler('shell_exec_error_handler');
        $output = shell_exec($C['magick_mogrify_path'] . ' -resize "90x120^" 2>&1');
        restore_error_handler();

        if( empty($output) && empty($GLOBALS['shell_exec_errors']) )
        {
            $C['have_magick'] = 1;
        }
    }


    // Check GD
    if( extension_loaded('gd') )
    {
        $gdinfo = gd_info();
        if( $gdinfo['JPG Support'] )
        {
            $C['have_gd'] = 1;
        }
    }
}

function shell_exec_error_handler($errno, $errstr)
{
    if( !isset($GLOBALS['shell_exec_errors']) || !is_array($GLOBALS['shell_exec_errors']) )
    {
        $GLOBALS['shell_exec_errors'] = array();
    }

    $GLOBALS['shell_exec_errors'][] = $errstr;
}





?>
