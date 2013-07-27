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
require_once 'template.php';
require_once 'utility.php';


$functions = array('stats' => '_xStatsShow',
                   'forgot' => '_xForgotPasswordShow',
                   'remind' => '_xForgotPasswordConfirm',
                   'confirm' => '_xForgotPasswordConfirmed');

prepare_request();

$t = new Template();
$t->AssignByRef('g_config', $C);
$t->AssignByRef('g_request', $_REQUEST);


if( !$C['flag_allow_login'] )
{
    $t->Display('trade-stats-disabled.tpl');
    exit;
}


$r = $_REQUEST['r'];
if( isset($functions[$r]) )
{
    call_user_func($functions[$r]);
}
else
{
    _xStatsLoginShow();
}

function _xStatsLoginShow()
{
    global $t, $C;

    $t->Display('trade-stats-login.tpl');
}

function _xStatsShow()
{
    global $t, $C;

    require_once 'validator.php';

    $v =& Validator::Get();

    $v->Register($_REQUEST['domain'], VT_NOT_EMPTY, "The 'Domain' field is required");
    $v->Register($_REQUEST['password'], VT_NOT_EMPTY, "The 'Password' field is required");

    $trade = null;
    if( !string_is_empty($_REQUEST['domain']) && !string_is_empty($_REQUEST['password']) )
    {
        require_once 'dirdb.php';

        $db = new TradeDB();
        $trade = $db->Retrieve($_REQUEST['domain']);

        $v->Register(empty($trade), VT_NOT_TRUE, "The Domain you entered is not in our database");
        if( !empty($trade) )
        {
            $v->Register(sha1($_REQUEST['password']), VT_EQUALS, "Invalid password for this domain", $trade['password']);
        }
    }

    if( !$v->Validate() )
    {
        $t->Assign('g_errors', $v->GetErrors());
        return _xStatsLoginShow();
    }

    require_once 'lib/stats.php';

    $stats = load_overall_stats(DIR_TRADE_STATS, array($trade));

    $t->AssignByRef('g_stats', get_object_vars($stats[0]));
    $t->AssignByRef('g_trade', $trade);
    $t->Display('trade-stats.tpl');
}

function _xForgotPasswordShow()
{
    global $t, $C;

    $t->Display('trade-stats-forgot.tpl');
}

function _xForgotPasswordConfirm()
{
    global $t, $C;

    require_once 'validator.php';

    $v =& Validator::Get();

    $v->Register($_REQUEST['domain'], VT_NOT_EMPTY, "The 'Domain' field is required");
    $v->Register($_REQUEST['email'], VT_VALID_EMAIL, "The 'E-mail' field must be a valid e-mail address");

    $trade = null;
    if( !string_is_empty($_REQUEST['domain']) )
    {
        require_once 'dirdb.php';

        $db = new TradeDB();
        $trade = $db->Retrieve($_REQUEST['domain']);

        $v->Register(empty($trade), VT_IS_FALSE, "The Domain entered does not exist in our database");

        if( !empty($trade) )
        {
            $v->Register($_REQUEST['email'], VT_EQUALS, "The E-mail entered does not match the e-mail address for this domain", $trade['email']);
        }
    }

    if( !$v->Validate() )
    {
        $t->Assign('g_errors', $v->GetErrors());
        return _xForgotPasswordShow();
    }

    require_once 'textdb.php';
    $trade['confirm_id'] = md5(uniqid(rand(), true));

    $db = new PasswordConfirmsDB();
    $db->Add(array('confirm_id' => $trade['confirm_id'],
                   'domain' => $_REQUEST['domain'],
                   'timestamp' => time()));

    $t->AssignByRef('g_trade', $trade);

    require_once 'mailer.php';
    $m = new Mailer();
    $m->Mail('email-forgot-confirm.tpl', $t, $trade['email'], $trade['email']);

    $t->Display('trade-stats-forgot-confirm.tpl');
}

function _xForgotPasswordConfirmed()
{
    global $t, $C;

    require_once 'textdb.php';

    $db = new PasswordConfirmsDB();
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

        $password = get_random_password();

        require_once 'dirdb.php';
        $db = new TradeDB();
        $trade = $db->Update($confirm['domain'], array('password' => sha1($password)));
        $trade['password'] = $password;
        $t->AssignByRef('g_trade', $trade);
    }

    $t->Display('trade-stats-forgot-confirmed.tpl');
}

?>