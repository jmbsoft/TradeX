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


define('VT_NONE', -1);
define('VT_IS_EMPTY', 0);
define('VT_NOT_EMPTY', 1);
define('VT_GREATER', 2);
define('VT_GREATER_EQ', 3);
define('VT_LESS', 4);
define('VT_LESS_EQ', 5);
define('VT_VALID_EMAIL', 6);
define('VT_VALID_HTTP_URL', 7);
define('VT_LENGTH_LESS', 8);
define('VT_LENGTH_LESS_EQ', 9);
define('VT_LENGTH_GREATER', 10);
define('VT_LENGTH_GREATER_EQ', 11);
define('VT_IS_ZERO', 12);
define('VT_IS_ALPHANUM', 13);
define('VT_IS_NUMERIC', 14);
define('VT_IS_TRUE', 15);
define('VT_IS_FALSE', 16);
define('VT_NOT_TRUE', 17);
define('VT_NOT_FALSE', 18);
define('VT_REGEX_MATCH', 19);
define('VT_REGEX_NO_MATCH', 20);
define('VT_IS_BETWEEN', 21);
define('VT_VALID_DATETIME', 22);
define('VT_VALID_DATE', 23);
define('VT_VALID_TIME', 24);
define('VT_CONTAINS', 25);
define('VT_NOT_CONTAINS', 26);
define('VT_LENGTH_BETWEEN', 27);
define('VT_NOT_ZERO', 28);
define('VT_EQUALS', 29);
define('VT_NOT_EQUALS', 30);
define('VT_NO_DUPLICATE', 31);
define('VT_IS_IN', 32);
define('VT_NOT_IN', 33);
define('VT_FILE_IS_WRITEABLE', 34);
define('VT_DIR_IS_WRITEABLE', 35);
define('VT_IS_NULL', 36);
define('VT_NOT_NULL', 37);


define('VF_INPUT', 0);
define('VF_TYPE', 1);
define('VF_MESSAGE', 2);
define('VF_EXTRAS', 3);

class Validator
{

    var $validated = false;

    var $registered;

    var $failed;

    var $set_errors;

    function Validator()
    {
        $this->Reset();
    }

    function Reset()
    {
        $this->validated = false;
        $this->registered = array();
        $this->failed = array();
        $this->set_errors = array();
    }

    function Register($input, $vtype, $error_message, $extras = null)
    {
        $this->registered[] = array(VF_INPUT => $input,
                                    VF_TYPE => $vtype,
                                    VF_MESSAGE => $error_message,
                                    VF_EXTRAS => $extras);
    }

    function SetError($message)
    {
        $this->set_errors[] = $message;
    }

    function Validate($callback = null)
    {
        // Only do the actual validation once
        if( !$this->validated )
        {
            $this->validated = true;

            foreach( $this->registered as $v )
            {
                $result = null;

                switch( $v[VF_TYPE] )
                {
                    case VT_CONTAINS:
                        $result = (stristr($v[VF_INPUT], $v[VF_EXTRAS]) !== false);
                        break;

                    case VT_NOT_CONTAINS:
                        $result = (stristr($v[VF_INPUT], $v[VF_EXTRAS]) === false);
                        break;

                    case VT_GREATER:
                        $result = ($v[VF_INPUT] > $v[VF_EXTRAS]);
                        break;

                    case VT_GREATER_EQ:
                        $result = ($v[VF_INPUT] >= $v[VF_EXTRAS]);
                        break;

                    case VT_IS_ALPHANUM:
                        $result = (preg_match('~^[a-z0-9]+$~i', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_IS_BETWEEN:
                        $between = explode(',', $v[VF_EXTRAS]);
                        $result = ($v[VF_INPUT] >= $between[0] && $v[VF_INPUT] <= $between[1]);
                        break;

                    case VT_IS_EMPTY:
                        $result = ($v[VF_INPUT] == null || preg_match('~^\s*$~s', $v[VF_INPUT]));
                        break;

                    case VT_NOT_EMPTY:
                        $result = ($v[VF_INPUT] !== null && !preg_match('~^\s*$~s', $v[VF_INPUT]));
                        break;

                    case VT_IS_FALSE:
                        $result = ($v[VF_INPUT] === false);
                        break;

                    case VT_NOT_FALSE:
                        $result = ($v[VF_INPUT] !== false);
                        break;

                    case VT_IS_NUMERIC:
                        $result = (!is_object($v[VF_INPUT]) && !is_null($v[VF_INPUT]) && !is_bool($v[VF_INPUT]) && !is_resource($v[VF_INPUT]) && preg_match('~^-?[0-9]+$~i', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_IS_TRUE:
                        $result = ($v[VF_INPUT] === true);
                        break;

                    case VT_NOT_TRUE:
                        $result = ($v[VF_INPUT] !== true);
                        break;

                    case VT_IS_ZERO:
                        $result = ($v[VF_INPUT] == 0);
                        break;

                    case VT_NOT_ZERO:
                        $result = ($v[VF_INPUT] != 0);
                        break;

                    case VT_LENGTH_GREATER:
                        $result = (strlen($v[VF_INPUT]) > $v[VF_EXTRAS]);
                        break;

                    case VT_LENGTH_GREATER_EQ:
                        $result = (strlen($v[VF_INPUT]) >= $v[VF_EXTRAS]);
                        break;

                    case VT_LENGTH_LESS:
                        $result = (strlen($v[VF_INPUT]) < $v[VF_EXTRAS]);
                        break;

                    case VT_LENGTH_LESS_EQ:
                        $result = (strlen($v[VF_INPUT]) <= $v[VF_EXTRAS]);
                        break;

                    case VT_LENGTH_BETWEEN:
                        $between = is_array($v[VF_EXTRAS]) ? $v[VF_EXTRAS] : explode(',', $v[VF_EXTRAS]);
                        $length = strlen($v[VF_INPUT]);
                        $result = ($length >= $between[0] && $length <= $between[1]);
                        break;

                    case VT_LESS:
                        $result = ($v[VF_INPUT] < $v[VF_EXTRAS]);
                        break;

                    case VT_LESS_EQ:
                        $result = ($v[VF_INPUT] <= $v[VF_EXTRAS]);
                        break;

                    case VT_REGEX_MATCH:
                        $result = (preg_match($v[VF_EXTRAS], $v[VF_INPUT]) !== 0);
                        break;

                    case VT_REGEX_NO_MATCH:
                        $result = (preg_match($v[VF_EXTRAS], $v[VF_INPUT]) === 0);
                        break;

                    case VT_VALID_DATE:
                        $result = (preg_match('~^\d\d\d\d-\d\d-\d\d$~', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_VALID_DATETIME:
                        $result = (preg_match('~^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$~', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_VALID_EMAIL:
                        $result = (preg_match('~^[\w\d][\w\d\,\.\-]*\@([\w\d\-]+\.)+([a-zA-Z]+)$~i', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_VALID_HTTP_URL:
                        $result = (preg_match('~^http(s)?://[\w-]+\.[\w-]+(\S+)?$~i', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_VALID_TIME:
                        $result = (preg_match('~^\d\d:\d\d:\d\d$~', $v[VF_INPUT]) !== 0);
                        break;

                    case VT_EQUALS:
                        $result = ($v[VF_EXTRAS] == $v[VF_INPUT]);
                        break;

                    case VT_NOT_EQUALS:
                        $result = ($v[VF_EXTRAS] != $v[VF_INPUT]);
                        break;

                    case VT_IS_IN:
                        $v[VF_EXTRAS] = !is_array($v[VF_EXTRAS]) ? explode(',', $v[VF_EXTRAS]) : $v[VF_EXTRAS];
                        $result = in_array($v[VF_INPUT], $v[VF_EXTRAS]);
                        break;

                    case VT_NOT_IN:
                        $v[VF_EXTRAS] = !is_array($v[VF_EXTRAS]) ? explode(',', $v[VF_EXTRAS]) : $v[VF_EXTRAS];
                        $result = !in_array($v[VF_INPUT], $v[VF_EXTRAS]);
                        break;

                    case VT_FILE_IS_WRITEABLE:
                    case VT_DIR_IS_WRITEABLE:
                        $result = is_writable($v[VF_INPUT]);
                        break;

                    case VT_IS_NULL:
                        $result = $v[VF_INPUT] === null;
                        break;

                    case VT_NOT_NULL:
                        $result = $v[VF_INPUT] !== null;
                        break;
                }

                if( $result === false )
                {
                    $this->failed[] = $v[VF_MESSAGE];
                }
            }

            // Merge validation errors with manually set error messages
            $this->failed = array_merge($this->failed, $this->set_errors);
        }

        if( $callback != null && function_exists($callback) )
        {
            call_user_func($callback, $this);
        }

        return (count($this->failed) == 0);
    }

    function GetErrors()
    {
        return ((count($this->failed) > 0) ? $this->failed : null);
    }

    function &Get()
    {
        static $self = null;

        if( empty($self) )
        {
            $self = new Validator();
        }

        return $self;
    }
}


?>
