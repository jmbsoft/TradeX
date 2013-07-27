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

require_once 'lib/global.php';
require_once 'utility.php';
require_once 'template.php';


$functions = array('register' => '_xRegister',
                   'confirm' => '_xConfirmShow');

prepare_request();

$t = new Template();
$t->AssignByRef('g_config', $C);
$t->AssignByRef('g_request', $_REQUEST);


if( !$C['flag_accept_new_trades'] )
{
    $t->Display('register-closed.tpl');
    exit;
}


$r = $_REQUEST['r'];
if( isset($functions[$r]) )
{
    call_user_func($functions[$r]);
}
else
{
    _xRegisterShow();
}

function _xRegisterShow()
{
    global $t, $C;

    // Get trade rules
    $t->Assign('g_trade_rules', file_get_contents(FILE_TRADE_RULES));
    $t->Assign('g_trade_rules_array', file(FILE_TRADE_RULES));

    // Categories
    $t->Assign('g_categories', array_map('trim', file(FILE_CATEGORIES)));

    // Default new trade settings
    $t->Assign('g_trade_defaults', unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS)));

    $t->Display('register.tpl');
}

function _xRegister()
{
    global $t, $C;

    require_once 'validator.php';

    $_REQUEST = string_strip_tags($_REQUEST);

    $v =& Validator::Get();

    $v->Register($_REQUEST['return_url'], VT_VALID_HTTP_URL, "The 'URL to Send Traffic' field must be a valid HTTP URL");

    if( !string_is_empty($_REQUEST['return_url']) )
    {
        require_once 'http.php';
        $http = new HTTP();
        $v->Register($http->GET($_REQUEST['return_url'], null, true), VT_NOT_FALSE, "The 'URL to Send Traffic' does not seem to be working: " . $http->error);
        $_REQUEST['header'] = $http->response_headers;
        $_REQUEST['content'] = $http->body;
    }

    if( $C['flag_req_email'] || !empty($_REQUEST['email']) )
    {
        $v->Register($_REQUEST['email'], VT_VALID_EMAIL, "The 'E-mail Address' field must be a valid email");
    }

    if( $C['flag_req_site_name'] || !empty($_REQUEST['site_name']) )
    {
        $v->Register($_REQUEST['site_name'], VT_LENGTH_BETWEEN, "The 'Site Name' field must have between {$C['site_name_min']} and {$C['site_name_max']} characters", array($C['site_name_min'], $C['site_name_max']));
    }

    if( $C['flag_req_site_description'] || !empty($_REQUEST['site_description']) )
    {
        $v->Register($_REQUEST['site_description'], VT_LENGTH_BETWEEN, "The 'Site Description' field must have between {$C['site_description_min']} and {$C['site_description_max']} characters", array($C['site_description_min'], $C['site_description_max']));
    }

    if( $C['flag_req_icq'] || !empty($_REQUEST['icq']) )
    {
        $v->Register($_REQUEST['icq'], VT_IS_NUMERIC, "The 'ICQ Number' field must be numeric");
    }

    if( $C['flag_req_nickname'] || !empty($_REQUEST['nickname']) )
    {
        $v->Register($_REQUEST['nickname'], VT_NOT_EMPTY, "The 'Nickname' field is required");
    }

    if( $C['flag_req_banner'] || !empty($_REQUEST['banner']) )
    {
        $v->Register($_REQUEST['banner'], VT_VALID_HTTP_URL, "The 'Banner URL' field must be a valid HTTP URL");

        if( !string_is_empty($_REQUEST['banner']) )
        {
            require_once 'http.php';
            $http = new HTTP();
            $v->Register($http->GET($_REQUEST['banner'], null, true), VT_NOT_FALSE, "The 'Banner URL' does not seem to be working: " . $http->error);
        }
    }

    if( $C['flag_captcha_register'] )
    {
        require_once 'captcha.php';

        $captcha = new Captcha();
        $captcha->Verify();
    }

    $_REQUEST['domain'] = domain_from_url($_REQUEST['return_url']);

    require_once 'dirdb.php';
    $db = new TradeDB();
    $v->Register($db->Exists($_REQUEST['domain']), VT_IS_FALSE, "The site you are trying to register already exists in our database");


    // Check blacklist
    $_REQUEST['server_ip'] = gethostbyname($domain);
    $_REQUEST['dns'] = gethostbyname($domain);
    if( ($blacklisted = check_blacklist($_REQUEST)) !== false )
    {
        $v->SetError("You have matched one or more of our blacklist items and cannot register new trade accounts" . (!empty($blacklisted[1]) ? ": " . $blacklisted[1] : ''));
    }


    // Check category
    $categories = array_map('trim', file(FILE_CATEGORIES));
    if( $C['flag_allow_select_category'] && count($categories) )
    {
        $v->Register(in_array($_REQUEST['category'], $categories), VT_IS_TRUE, "You have selected an invalid category");
        $_REQUEST['categories'] = array($_REQUEST['category']);
    }


    if( !$v->Validate() )
    {
        $t->Assign('g_errors', $v->GetErrors());
        return _xRegisterShow();
    }

    $_REQUEST = array_merge($_REQUEST, unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS)));
    $password = $_REQUEST['password'] = get_random_password();

    $t->AssignByRef('g_trade', $_REQUEST);

    trade_add($_REQUEST, true);

    $_REQUEST['password'] = $password;

    $t->Display('register-complete.tpl');
}

function _xConfirmShow()
{
    global $t, $C;

    require_once 'textdb.php';

    $db = new RegisterConfirmsDB();
    $db->DeleteExpired();
    $confirm = $db->Retrieve($_REQUEST['id']);

    require_once 'validator.php';
    $v =& Validator::Get();

    $v->Register(empty($confirm), VT_IS_FALSE, 'Invalid or expired confirmation code');

    if( !$v->Validate() )
    {
        $t->Assign('g_invalid_confirm', true);
    }
    else
    {
        $db->Delete($_REQUEST['id']);

        $defaults = unserialize(file_get_contents(FILE_NEW_TRADE_DEFAULTS));

        require_once 'dirdb.php';

        $password = get_random_password();

        $db = new TradeDB();
        $trade = $db->Update($confirm['domain'], array('status' => $defaults['status'], 'timestamp_autostop' => time(), 'password' => sha1($password)));
        $trade['password'] = $password;
        $t->AssignByRef('g_trade', $trade);


        require_once 'mailer.php';

        if( $C['flag_register_email_user'] && !string_is_empty($trade['email']) )
        {
            $m = new Mailer();
            $m->Mail('email-register-complete.tpl', $t, $trade['email'], $trade['email']);
        }


        if( $C['flag_register_email_admin'] )
        {
            $m = new Mailer();
            $m->Mail('email-register-admin.tpl', $t, $C['email_address'], $C['email_name']);
        }
    }

    $t->Display('register-confirm.tpl');
}

?>