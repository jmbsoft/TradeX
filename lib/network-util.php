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

define('NETWORK_SCRIPT', 'network.php');
define('NETWORK_SUCCESS', '!!@@SUCCESS@@!!');

// Network sync options
define('NETWORK_SYNC_TRADES', 'trades');
define('NETWORK_SYNC_SKIM_SCHEMES', 'skim-schemes');
define('NETWORK_SYNC_GROUPS', 'groups');
define('NETWORK_SYNC_CATEGORIES', 'categories');
define('NETWORK_SYNC_BLACKLIST', 'blacklist');
define('NETWORK_SYNC_COUNTRIES', 'countries');
define('NETWORK_SYNC_SEARCH_ENGINES', 'search-engines');
define('NETWORK_SYNC_TRADE_RULES', 'trade-rules');
define('NETWORK_SYNC_NETWORK_SITES', 'network-sites');

// Network functions
define('NETWORK_FNC_GET_STATS', '_xStatsGet');
define('NETWORK_FNC_SYNC', '_xSync');

function network_success($data)
{
    echo NETWORK_SUCCESS . serialize($data);
}



class NetworkRequest
{
    var $url;
    var $post_data;
    var $error;

    function NetworkRequest($site, $fnc, $extra_post = array())
    {
        $this->url = $site['url'] . NETWORK_SCRIPT;
        $this->post_data = array_merge(array('cp_username' => $site['username'],
                                             'cp_password' => $site['password'],
                                             'r' => $fnc),
                                       $extra_post);
    }

    function Execute()
    {
        require_once 'http.php';

        $http = new HTTP();

        if( $http->POST($this->url, $this->post_data) )
        {
            if( strpos($http->body, NETWORK_SUCCESS) === 0 )
            {
                return substr($http->body, strlen(NETWORK_SUCCESS));
            }
            else
            {
                $this->error = substr(strip_tags($http->body), 0, 100);
                return false;
            }
        }
        else
        {
            $this->error = $http->error;
            return false;
        }
    }
}


?>