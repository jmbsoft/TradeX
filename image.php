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


/*#<CONFIG>*/
$C = array('dir_base' => '/home/soft/html/tradex',
'domain' => 'soft.jmb-soft.com',
'keyphrase' => 'd06c8abb142c4eda4d60df1aed2dde25');
/*#</CONFIG>*/


// Session variables
$now = time();
$g_session = null;
$session_length = 3600;
$ip_hash = md5($_SERVER['REMOTE_ADDR']);
$session_file = "data/sessions/{$ip_hash[0]}/{$ip_hash[0]}/{$_SERVER['REMOTE_ADDR']}";

// Session cookie is set
if( isset($_COOKIE['tdxsess']) )
{
    $cookie_data = base64_decode($_COOKIE['tdxsess']);
    $cookie_signature = sha1($C['keyphrase'] . $cookie_data);
    $g_session = unserialize($cookie_data);

    // Good signature
    if( isset($_COOKIE['tdxsig']) && $_COOKIE['tdxsig'] == $cookie_signature )
    {
        $g_session['ni'] = false;

        // Update cookie
        $serialized = serialize($g_session);
        setcookie('tdxsess', base64_encode($serialized), $now + $session_length, '/', $C['domain']);
        setcookie('tdxsig', sha1($C['keyphrase'] . $serialized), $now + $session_length, '/', $C['domain']);

        // Update session file
        $fp = fopen($session_file, 'w');
        flock($fp, LOCK_EX);
        fwrite($fp, $serialized);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

$images = array('actions-16x16.png',
                'add-16x16.png',
                'build-16x16.png',
                'calendar-16x16.png',
                'detailed-16x16.png');

header('Content-type: image/png');
readfile($C['dir_base'] . '/cp/images/' . $images[array_rand($images)]);

// Debug logging
//$fp = fopen('logs/debug.log', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, "IMAGE|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE['tdxsess']}|$serialized\n");
//flock($fp, LOCK_UN);
//fclose($fp);


?>