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

// Set PHP configuration options
@ini_set('display_errors', 'On');
@ini_set('memory_limit', -1);
@ini_set('zend.ze1_compatibility_mode', 'Off');
@ini_set('pcre.backtrack_limit', 1000000);
@ini_set('default_charset', 'UTF-8');
@ini_set('html_errors', 0);
@set_magic_quotes_runtime(0);
@set_time_limit(0);

// Setup error reporting level

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);


// Version information


define('VERSION', '1.0.1');
define('RELEASED', 'August 11, 2013');
define('TIMESTAMP', '1376269662');



// Directory paths
define('DIR_BASE', realpath(dirname(__FILE__) . '/../'));
define('DIR_LOGS', DIR_BASE . '/logs');
define('DIR_LIB', DIR_BASE . '/lib');
define('DIR_ASSETS', DIR_BASE . '/assets');
define('DIR_DATA', DIR_BASE . '/data');
define('DIR_TEMP', DIR_BASE . '/tmp');
define('DIR_THUMBS', DIR_BASE . '/thumbs');
define('DIR_TRADE_STATS', DIR_DATA . '/trade_stats');
define('DIR_SYSTEM_STATS', DIR_DATA . '/system_stats');
define('DIR_TEMPLATES', DIR_BASE . '/templates');
define('DIR_COMPILED', DIR_TEMPLATES . '/_compiled');

// Files
define('FILE_ERROR_LOG', DIR_LOGS . '/error.log');


// Setup the include path
set_include_path(join(PATH_SEPARATOR, array('.', DIR_LIB)));


// Setup error handler
set_error_handler('error_handler');

function prepare_request()
{
    $_REQUEST = process_request_vars(array_merge($_POST, $_GET));
}

function process_request_vars($var)
{
    return is_array($var) ?
           array_map('process_request_vars', $var) :
           trim(get_magic_quotes_gpc() == 1 ? stripslashes($var) : $var);
}

function error_handler($errno, $errstr, $errfile, $errline)
{
    $reporting = error_reporting();

    if( $reporting == 0 || !($errno & $reporting) )
    {
        return;
    }



    require_once 'utility.php';
    file_append(DIR_LOGS . '/error.log', "[" . date('r') . "] $errstr in $errfile on $errline\n");

    if( defined('IN_CONTROL_PANEL') )
    {
        echo "A fatal error has occurred: $errstr in $errfile on $errline\n";
    }
    else
    {
        echo "A fatal error has occurred.  If you are the administrator check the error log for details\n";
    }

    exit;
}

require_once 'config.php';

?>