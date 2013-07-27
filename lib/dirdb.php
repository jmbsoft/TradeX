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


class DirDB
{
    var $directory;
    var $fields;
    var $sorter;
    var $desc;
    var $primary_key;
    var $filters = array();

    function Delete($item_id)
    {
        if( file_exists($this->directory . '/' . $item_id) )
        {
            @unlink($this->directory . '/' . $item_id);
        }
    }

    function Add($data)
    {
        $record = array();
        foreach( $this->fields as $field )
        {
            $record[] = DirDB::_format_incoming($data[$field]);
        }

        file_write($this->directory . '/' . $data[$this->primary_key], join('|', $record));
    }

    function ChangePrimaryKey($old, $new)
    {
        rename($this->directory . '/' . $old, $this->directory . '/' . $new);
    }

    function Update($item_id, $new_data)
    {
        $data = $this->Retrieve($item_id);

        $record = array();
        foreach( $this->fields as $field )
        {
            if( !isset($new_data[$field]) )
            {
                $record[] = DirDB::_format_incoming($data[$field]);
            }
            else
            {
                $record[] = DirDB::_format_incoming($new_data[$field]);
                $data[$field] = $new_data[$field];
            }
        }

        file_write($this->directory . '/' . $item_id, join('|', $record));

        return $data;
    }

    function Exists($item_id)
    {
        return is_file($this->directory . '/' . $item_id);
    }

    function Retrieve($item_id)
    {
        if( file_exists($this->directory . '/' . $item_id) && is_file($this->directory . '/' . $item_id) )
        {
            $data = explode('|', file_get_contents($this->directory . '/' . $item_id));
            if( count($this->fields) > count($data) )
            {
                $data = array_pad($data, count($this->fields), '');
            }
            $data = array_combine($this->fields, $data);

            $data[$this->primary_key] = $item_id;
            return DirDB::_format_outgoing($data);
        }

        return null;
    }

    function RetrieveAll($sorter = null, $desc = false)
    {
        $this->desc = $desc;
        if( !empty($sorter) )
        {
            $this->sorter = $sorter;
        }

        $items = array();
        foreach( dir_read_files($this->directory, null, false) as $file )
        {
            $data = explode('|', file_get_contents($this->directory . '/' . $file));
            if( count($this->fields) > count($data) )
            {
                $data = array_pad($data, count($this->fields), '');
            }
            $data = array_combine($this->fields, $data);

            $data[$this->primary_key] = $file;

            if( $this->_filter($data) )
            {
                $items[] = DirDB::_format_outgoing($data);
            }
        }

        usort($items, array(&$this, '_compare'));

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
        $string = string_format_lf($string, STRING_LF_UNIX);

        return str_replace(array(STRING_LF_UNIX, '|'), array('\n', '!@@!'), $string);
    }

    function _format_outgoing($string)
    {
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

class SkimSchemeBaseDB extends DirDB
{
    function SkimSchemeBaseDB()
    {
        $this->directory = DIR_SKIM_SCHEMES_BASE;
        $this->primary_key = 'scheme';
        $this->sorter = 'scheme';
        $this->fields = array('click_1',
                              'click_2',
                              'click_3',
                              'click_4',
                              'click_5',
                              'click_6',
                              'click_7',
                              'cycle_1',
                              'cycle_2',
                              'cycle_3',
                              'dynamic');
    }
}

class TradeDB extends DirDB
{
    function TradeDB()
    {
        $this->directory = DIR_TRADES;
        $this->primary_key = 'domain';
        $this->sorter = 'domain';
        $this->fields = array('return_url',
                              'status',
                              'color',
                              'flag_toplist',
                              'flag_grabber',
                              'email',
                              'password',
                              'nickname',
                              'icq',
                              'site_name',
                              'site_description',
                              'groups',
                              'categories',
                              'grabber_url',
                              'custom_thumbs',
                              'banner',
                              'notes',
                              'force_instant',
                              'flag_force_instant_high',
                              'force_instant_owed',
                              'force_hourly',
                              'force_hourly_end',
                              'force_hourly_owed',
                              'skim_scheme',
                              'trade_weight',
                              'push_to',
                              'push_weight',
                              'start_raws',
                              'start_clicks',
                              'start_prod',
                              'min_raws',
                              'min_clicks',
                              'min_prod',
                              'max_out',
                              'hourly_cap',
                              'daily_cap',
                              'excludes',
                              'flag_external',
                              'timestamp_added',
                              'timestamp_autostop',
                              'thumbnails',
                              'trigger_strings');
    }
}

class SystemDB extends DirDB
{
    function SystemDB()
    {
        $this->directory = DIR_SYSTEM;
        $this->primary_key = 'domain';
        $this->sorter = 'domain';
        $this->fields = array('color',
                              'notes',
                              'skim_scheme',
                              'excludes',
                              'send_method',
                              'traffic_url');
    }
}
?>
