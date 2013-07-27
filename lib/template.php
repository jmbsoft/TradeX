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


function t_tostring($string)
{
    global $C;

    if( preg_match('~^\d+$~', $string) )
    {
        return number_format($string, 0, $C['dec_point'], $C['thousands_sep']);
    }

    return $string;
}

function t_chop($string, $length = 100, $append = '...')
{
    $len_append = strlen($append);
    return strlen($string) > $length ?
           trim(substr($string, 0, $length - $len_append)) . $append :
           $string;
}

function t_datetime($string, $format = 'M j, Y g:ia')
{
    return date($format, strtotime($string));
}

function t_date($string, $format = 'M j, Y')
{
    return date($format, strtotime($string . ' 12:00:00'));
}

class Template
{
    var $vars = array();

    function Assign($variable, $value = null)
    {
        if( !empty($variable) )
        {
            $this->vars[$variable] = $value;
        }
    }

    function AssignByRef($variable, &$value)
    {
        if( !empty($variable) )
        {
            $this->vars[$variable] = &$value;
        }
    }

    function Display($template)
    {
        ob_start();
        include(DIR_COMPILED . "/$template");
        $generated = ob_get_clean();
        echo $generated;
    }

    function Parse($template)
    {
        ob_start();
        eval('?>' . $template);
        return ob_get_clean();
    }

    function ParseFile($template)
    {
        ob_start();
        include(DIR_COMPILED . "/$template");
        return ob_get_clean();
    }
}


?>