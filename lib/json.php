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


define('JSON_STATUS_SUCCESS', 0);
define('JSON_STATUS_WARNING', 1);
define('JSON_STATUS_ERROR', 2);
define('JSON_STATUS_LOGOUT', 3);

define('JSON_KEY_STATUS', 'status');
define('JSON_KEY_DIALOG', 'dialog');
define('JSON_KEY_DIALOG_CLOSE', 'dialog_close');
define('JSON_KEY_MESSAGE', 'message');
define('JSON_KEY_ERRORS', 'errors');
define('JSON_KEY_WARNINGS', 'warnings');
define('JSON_KEY_HTML', 'html');
define('JSON_KEY_JS', 'js');
define('JSON_KEY_EVAL', 'eval');
define('JSON_KEY_CODE', 'code');
define('JSON_KEY_SUBJECT', 'subject');
define('JSON_KEY_BODY', 'body');
define('JSON_KEY_LOG', 'log');
define('JSON_KEY_SEARCH', 'search');
define('JSON_KEY_ROW', 'row');
define('JSON_KEY_ITEM_ID', 'item_id');
define('JSON_KEY_ITEM_TYPE', 'item_type');
define('JSON_KEY_SEARCH_RESULTS', 'search_results');
define('JSON_KEY_SAVED_SEARCH', 'saved_search');

class JSON
{

    function Response($status, $data)
    {
        if( !is_array($data) )
        {
            $data = array(JSON_KEY_MESSAGE => $data);
        }

        $data[JSON_KEY_STATUS] = $status;
        echo JSON::_encode($data);
    }

    function Success($data = array())
    {
        JSON::Response(JSON_STATUS_SUCCESS, $data);
    }

    function Warning($data = array())
    {
        JSON::Response(JSON_STATUS_WARNING, $data);
    }

    function Error($data = array())
    {
        JSON::Response(JSON_STATUS_ERROR, $data);
    }

    function Logout()
    {
        JSON::Response(JSON_STATUS_LOGOUT, array());
    }

    function _encode($var)
    {
        switch( gettype($var) )
        {
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'integer':
                return (int) $var;

            case 'double':
            case 'float':
                return (float) $var;

            case 'string':
                $ascii = str_replace(array("\\", "\t", "\n", "\r", '"', '/'),
                                     array("\\\\", '\t', '\n', '\r', "\\\"", "\\/"),
                                     $var);

                return  '"'.$ascii.'"';

            case 'array':
                // treat as a JSON object
                if( is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1)) )
                {
                    $properties = array_map(array('JSON', 'name_value'),
                                            array_keys($var),
                                            array_values($var));

                    return '{' . join(',', $properties) . '}';
                }

                // treat it like a regular array
                $elements = array_map(array('JSON', '_encode'), $var);
                return '[' . join(',', $elements) . ']';

            case 'object':
                $vars = get_object_vars($var);

                $properties = array_map(array('JSON', 'name_value'),
                                        array_keys($vars),
                                        array_values($vars));

                return '{' . join(',', $properties) . '}';

            default:
                return 'null';
        }
    }

    function name_value($name, $value)
    {
        $encoded_value = JSON::_encode($value);
        return JSON::_encode(strval($name)) . ':' . $encoded_value;
    }
}

?>