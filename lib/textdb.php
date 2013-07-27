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


require_once 'utility.php';


class TextDB
{
    var $db_file;
    var $fields;
    var $primary_key;
    var $sorter;
    var $desc;
    var $auto_increment = false;
    var $filters = array();

    function Clear()
    {
        file_write($this->db_file, '');
    }

    function Delete($item_id)
    {
        $fp_r = fopen($this->db_file, 'r');
        $fp_w = fopen($this->db_file, 'r+');
        flock($fp_w, LOCK_EX);

        while( !feof($fp_r) )
        {
            $line = trim(fgets($fp_r));

            if( string_is_empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);
            $data = array_combine($this->fields, $data);

            if( $data[$this->primary_key] == $item_id )
            {
                continue;
            }

            fwrite($fp_w, $line . STRING_LF_UNIX);
        }

        ftruncate($fp_w, ftell($fp_w));
        flock($fp_w, LOCK_UN);
        fclose($fp_w);
        fclose($fp_r);
    }

    function Add($data)
    {
        $fp = fopen($this->db_file, file_exists($this->db_file) ? 'r+' : 'w');
        flock($fp, LOCK_EX);

        if( $this->auto_increment )
        {
            $data[$this->primary_key] = 0;

            while( !feof($fp) )
            {
                $line = trim(fgets($fp));

                if( string_is_empty($line) )
                {
                    continue;
                }

                $existing = explode('|', $line);
                if( count($this->fields) > count($existing) )
                {
                    $existing = array_pad($existing, count($this->fields), '');
                }
                $existing = array_combine($this->fields, $existing);

                $data[$this->primary_key] = max($data[$this->primary_key], $existing[$this->primary_key]);
            }

            $data[$this->primary_key]++;
        }

        $record = array();
        foreach( $this->fields as $field )
        {
            $record[] = TextDB::_format_incoming($data[$field]);
        }

        fseek($fp, 0, SEEK_END);
        fwrite($fp, join('|', $record) . STRING_LF_UNIX);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $data[$this->primary_key];
    }

    function Update($item_id, $new_data)
    {
        // Don't overwrite primary key
        unset($new_data[$this->primary_key]);

        $fp = fopen($this->db_file, 'r+');
        flock($fp, LOCK_EX);

        $record_position = null;
        $old_data = null;
        $buffer = '';
        while( !feof($fp) )
        {
            $position = ftell($fp);
            $line = trim(fgets($fp));

            if( string_is_empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);
            if( count($this->fields) > count($data) )
            {
                $data = array_pad($data, count($this->fields), '');
            }
            $data = array_combine($this->fields, $data);

            if( $data[$this->primary_key] == $item_id )
            {
                $record_position = $position;
                $old_data = $data;
            }
            else if( $record_position !== null )
            {
                $buffer .= $line . STRING_LF_UNIX;
            }
        }


        // Record found
        if( $record_position !== null )
        {
            $record = array();
            foreach( $this->fields as $field )
            {
                if( !isset($new_data[$field]) )
                {
                    $record[] = $old_data[$field];
                }
                else
                {
                    $record[] = TextDB::_format_incoming($new_data[$field]);
                    $old_data[$field] = $new_data[$field];
                }
                //$record[] = TextDB::_format_incoming($new_data[$field]);
            }

            fseek($fp, $record_position, SEEK_SET);
            fwrite($fp, join('|', $record) . STRING_LF_UNIX . $buffer);
            ftruncate($fp, ftell($fp));
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $old_data;
    }

    function Exists($item_id)
    {
        return $this->Retrieve($item_id) !== null;
    }

    function Retrieve($item_id)
    {
        $item = null;
        $fp = fopen($this->db_file, 'r');
        flock($fp, LOCK_SH);

        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( string_is_empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);
            if( count($this->fields) > count($data) )
            {
                $data = array_pad($data, count($this->fields), '');
            }
            $data = array_combine($this->fields, $data);

            if( $data[$this->primary_key] == $item_id )
            {
                $item = TextDB::_format_outgoing($data);
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $item;
    }

    function RetrieveAll($sorter = null, $desc = false)
    {
        $this->desc = $desc;
        if( !empty($sorter) )
        {
            $this->sorter = $sorter;
        }

        $items = array();
        $fp = fopen($this->db_file, 'r');
        flock($fp, LOCK_SH);

        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( string_is_empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);
            if( count($this->fields) > count($data) )
            {
                $data = array_pad($data, count($this->fields), '');
            }
            $data = array_combine($this->fields, $data);

            if( $this->_filter($data) )
            {
                $items[] = TextDB::_format_outgoing($data);
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if( !empty($this->sorter) )
        {
            usort($items, array(&$this, '_compare'));
        }

        return $items;
    }

    function Count()
    {
        $items = 0;
        $fp = fopen($this->db_file, 'r');
        flock($fp, LOCK_SH);

        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( string_is_empty($line) )
            {
                continue;
            }

            $items++;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $items;
    }

    function ClearFilters()
    {
        $this->filters = array();
    }

    function AddFilter($field, $value, $multi = false)
    {
        if( !string_is_empty($value) )
        {
            $this->filters[] = array('field' => $field, 'value' => $value, 'multi' => $multi);
        }
    }

    function _filter($data)
    {
        foreach( $this->filters as $filter )
        {
            if( $filter['multi'] )
            {
                $parts = explode(',', $data[$filter['field']]);

                if( !in_array($filter['value'], $parts) )
                {
                    return false;
                }
            }
            else
            {
                if( $data[$filter['field']] != $filter['value'] )
                {
                    return false;
                }
            }
        }

        return true;
    }

    function _compare($a, $b)
    {
        if( is_numeric($a[$this->sorter]) && is_numeric($b[$this->sorter]) )
        {
            if( $a[$this->sorter] < $b[$this->sorter] )
            {
                return $this->desc ? 1 : -1;
            }
            else if( $a[$this->sorter] > $b[$this->sorter] )
            {
                return $this->desc ? -1 : 1;
            }

            return 0;
        }
        else
        {
            return $this->desc ?
                   -strnatcasecmp($a[$this->sorter], $b[$this->sorter]) :
                   strnatcasecmp($a[$this->sorter], $b[$this->sorter]);
        }
    }

    function _format_incoming($string)
    {
        if( is_array($string) )
        {
            return array_map(array('TextDB', '_format_incoming'), $string);
        }

        $string = string_format_lf($string, STRING_LF_UNIX);

        return str_replace(array(STRING_LF_UNIX, '|'), array('\n', '!@@!'), $string);
    }

    function _format_outgoing($string)
    {
        if( is_array($string) )
        {
            return array_map(array('TextDB', '_format_outgoing'), $string);
        }

        return str_replace(array('\n', '!@@!'), array(STRING_LF_UNIX, '|'), $string);
    }

    function _defaults()
    {
        $defaults = array();
        foreach( $this->fields as $field )
        {
            $defaults[$field] = STRING_BLANK;
        }

        return $defaults;
    }
}

class ToplistsDB extends TextDB
{

    function ToplistsDB()
    {
        $this->db_file = FILE_TOPLISTS;
        $this->primary_key = 'toplist_id';
        $this->sorter = 'toplist_id';
        $this->auto_increment = true;
        $this->fields = array('toplist_id',
                              'source',
                              'template',
                              'infile',
                              'outfile',
                              'groups',
                              'categories',
                              'sort_by',
                              'req_field',
                              'req_operator',
                              'req_value',
                              'flag_thumbs_only',
                              'trade_sources');
    }

    function Defaults()
    {
        $defaults = parent::_defaults();

        $defaults['infile'] = $_SERVER['DOCUMENT_ROOT'];
        $defaults['outfile'] = $_SERVER['DOCUMENT_ROOT'];

        return $defaults;
    }
}

class NetworkDB extends TextDB
{

    function NetworkDB()
    {
        $this->db_file = FILE_NETWORK_SITES;
        $this->primary_key = 'domain';
        $this->sorter = 'domain';
        $this->auto_increment = false;
        $this->fields = array('domain',
                              'url',
                              'username',
                              'password',
                              'category',
                              'owner',
                              'flag_stats');
    }

    function Defaults()
    {
        return parent::_defaults();
    }
}

class CaptchasDB extends TextDB
{

    function CaptchasDB()
    {
        $this->db_file = FILE_CAPTCHAS;
        $this->primary_key = 'session';
        $this->sorter = 'session';
        $this->auto_increment = false;
        $this->fields = array('session',
                              'code',
                              'timestamp');
    }

    function DeleteExpired()
    {
        $expired = time() - 86400;

        foreach( $this->RetrieveAll() as $captcha )
        {
            if( $captcha['timestamp'] < $expired )
            {
                $this->Delete($captcha['session']);
            }
        }
    }

    function Defaults()
    {
        return parent::_defaults();
    }
}

class RegisterConfirmsDB extends TextDB
{

    function RegisterConfirmsDB()
    {
        $this->db_file = FILE_REGISTER_CONFIRMS;
        $this->primary_key = 'confirm_id';
        $this->sorter = 'confirm_id';
        $this->auto_increment = false;
        $this->fields = array('confirm_id',
                              'domain',
                              'timestamp');
    }

    function DeleteExpired()
    {
        $expired = time() - 86400;

        foreach( $this->RetrieveAll() as $confirm )
        {
            if( $confirm['timestamp'] < $expired )
            {
                $this->Delete($confirm['confirm_id']);
            }
        }
    }

    function Defaults()
    {
        return parent::_defaults();
    }
}

class PasswordConfirmsDB extends TextDB
{

    function PasswordConfirmsDB()
    {
        $this->db_file = FILE_PASSWORD_CONFIRMS;
        $this->primary_key = 'confirm_id';
        $this->sorter = 'confirm_id';
        $this->auto_increment = false;
        $this->fields = array('confirm_id',
                              'domain',
                              'timestamp');
    }

    function DeleteExpired()
    {
        $expired = time() - 86400;

        foreach( $this->RetrieveAll() as $confirm )
        {
            if( $confirm['timestamp'] < $expired )
            {
                $this->Delete($confirm['confirm_id']);
            }
        }
    }

    function Defaults()
    {
        return parent::_defaults();
    }
}

class SkimSchemesDynamicDB extends TextDB
{

    function SkimSchemesDynamicDB()
    {
        $this->db_file = FILE_TOPLISTS;
        $this->primary_key = 'start_day';
        $this->sorter = null;
        $this->auto_increment = false;
        $this->fields = array('start_day',
                              'start_hour',
                              'start_minute',
                              'end_day',
                              'end_hour',
                              'end_minute',
                              'click_1',
                              'click_2',
                              'click_3',
                              'click_4',
                              'click_5',
                              'click_6',
                              'click_7',
                              'cycle_1',
                              'cycle_2',
                              'cycle_3');
    }

    function Defaults()
    {
        return parent::_defaults();
    }
}

?>