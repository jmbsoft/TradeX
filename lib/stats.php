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

define('STATS_UNKNOWN', '--');

function cmp_overall_stats($a, $b)
{
    if( isset($_REQUEST['trade_sources']) )
    {
        foreach( $_REQUEST['trade_sources'] as $source )
        {
            if( $source == 'all' )
            {
                break;
            }

            $property = 'pos_' . $source;

            // Neither trade is in this outlist
            if( empty($a->{$property}) && empty($b->{$property}) )
            {
                continue;
            }

            else if( !empty($a->{$property}) && !empty($b->{$property}) )
            {
                $a->ignore_requirements = TRUE;
                $b->ignore_requirements = TRUE;

                return
                    $a->{$property} == $b->{$property} ?
                    0
                    :
                    ($a->{$property} < $b->{$property} ? -1 : 1);

            }

            else if( !empty($a->{$property}) )
            {
                $a->ignore_requirements = TRUE;
                return -1;
            }

            else if( !empty($b->{$property}) )
            {
                $b->ignore_requirements = TRUE;
                return 1;
            }

        }
    }

    switch($_REQUEST['sort_by'])
    {
        case 'trade':
            return is_array($a->trade) ? strnatcmp($a->trade['domain'], $b->trade['domain']) : strnatcmp($a->trade, $b->trade);

        case 'domain':
        case 'site_name':
            return strnatcmp($a->trade[$_REQUEST['sort_by']], $b->trade[$_REQUEST['sort_by']]);

        default:
            if( strpos($_REQUEST['sort_by'], '.') !== false )
            {
                list($field, $index) = explode('.', $_REQUEST['sort_by']);
                $a_val = $a->{$field}[$index];
                $b_val = $b->{$field}[$index];
            }
            else
            {
                $a_val = $a->{$_REQUEST['sort_by']};
                $b_val = $b->{$_REQUEST['sort_by']};
            }

            if( $a_val < $b_val )
            {
                return 1;
            }
            else if( $a_val > $b_val )
            {
                return -1;
            }

            return is_array($a->trade) ? strnatcmp($a->trade['domain'], $b->trade['domain']) : strnatcmp($a->trade, $b->trade);
    }
}

function load_hourly_stats_trades()
{
    return load_hourly_stats(DIR_TRADE_STATS, get_trades());
}

function load_hourly_stats_system()
{
    return load_hourly_stats(DIR_SYSTEM_STATS, get_system_trades());
}

function load_hourly_stats($directory, $trades)
{
    $stats = array();
    foreach( $trades as $trade )
    {
        $stats[] = new StatsHourly($trade);
    }

    return $stats;
}

function load_overall_stats_trades()
{
    return load_overall_stats(DIR_TRADE_STATS, get_trades());
}

function load_overall_stats_system()
{
    return load_overall_stats(DIR_SYSTEM_STATS, get_system_trades());
}

function load_overall_stats($directory, $trades, $outlist_positions = FALSE)
{
    list($hour, $minute) = explode('-', date('H-i', time() - 3540));
    $minute_offset = ($hour * MINUTES_PER_HOUR + $minute) * RECORD_SIZE_STATS;
    $end_of_file = (HOURS_PER_DAY + MINUTES_PER_DAY) * RECORD_SIZE_STATS;
    $stats = array();

    // Load outlist positions
    if( $outlist_positions )
    {
        $positions = array();

        $outlists = array(
            'outlist_main' => FILE_OUTLIST_MAIN,
            'outlist_primary' => FILE_OUTLIST_PRIMARY,
            'outlist_secondary' => FILE_OUTLIST_SECONDARY,
            'outlist_forces' => FILE_OUTLIST_FORCES
        );

        foreach( $outlists as $key => $outlist_file )
        {
            $position = 0;
            $positions[$key] = array();

            $fp = fopen("$outlist_file", 'r+');
            flock($fp, LOCK_EX);

            while( !feof($fp) )
            {
                $ints = fread($fp, 8);

                if( feof($fp) )
                {
                    break;
                }

                $ints = unpack('Lowe/Lsize', $ints);
                $stt = explode('|', fread($fp, $ints['size']));
                $positions[$key][$stt[0]] = ++$position;
            }

            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    foreach( $trades as $trade )
    {
        $domain = $trade['domain'];
        $file = $directory . '/' . $domain;

        $rt24 = array_fill(1, STATS_PER_RECORD, 0);
        $fp = fopen($file, 'r');
        for( $i = 0; $i < HOURS_PER_DAY; $i++ )
        {
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
            foreach( $r as $j => $k )
            {
                $rt24[$j] += $k;
            }
        }

        $rt60 = array_fill(1, STATS_PER_RECORD, 0);
        fseek($fp, $minute_offset, SEEK_CUR);
        for( $i = 0; $i < MINUTES_PER_HOUR; $i++ )
        {
            //$minute++;
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
            foreach( $r as $j => $k )
            {
                $rt60[$j] += $k;
            }

            // Wrap around midnight with a seek
            if( ftell($fp) == $end_of_file )
            {
                fseek($fp, HOURS_PER_DAY * RECORD_SIZE_STATS, SEEK_SET);
            }
        }

        fclose($fp);

        $trade_stats = new StatsOverall($trade, $rt60, $rt24);

        if( $outlist_positions )
        {
            $trade_stats->pos_outlist_forces = isset($positions['outlist_forces'][$domain]) ? $positions['outlist_forces'][$domain] : NULL;
            $trade_stats->pos_outlist_main = isset($positions['outlist_main'][$domain]) ? $positions['outlist_main'][$domain] : NULL;
            $trade_stats->pos_outlist_primary = isset($positions['outlist_primary'][$domain]) ? $positions['outlist_primary'][$domain] : NULL;
            $trade_stats->pos_outlist_secondary = isset($positions['outlist_secondary'][$domain]) ? $positions['outlist_secondary'][$domain] : NULL;
        }

        $stats[] = $trade_stats;
    }

    return $stats;
}

function load_site_stats()
{
    list($hour, $minute) = explode('-', date('H-i', time() - 3540));
    $minute_offset = ($hour * MINUTES_PER_HOUR + $minute) * RECORD_SIZE_STATS;
    $end_of_file = (HOURS_PER_DAY + MINUTES_PER_DAY) * RECORD_SIZE_STATS;
    $stats = array();

    $rt24 = array_fill(1, STATS_PER_RECORD, 0);
    $rt60 = array_fill(1, STATS_PER_RECORD, 0);
    $trades = array_merge(get_trades(), get_system_trades());
    foreach( $trades as $trade )
    {
        $file = get_trade_stats_dir($trade['domain']) . '/' . $trade['domain'];

        $fp = fopen($file, 'r');
        for( $i = 0; $i < HOURS_PER_DAY; $i++ )
        {
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
            foreach( $r as $j => $k )
            {
                $rt24[$j] += $k;
            }
        }

        fseek($fp, $minute_offset, SEEK_CUR);
        for( $i = 0; $i < MINUTES_PER_HOUR; $i++ )
        {
            $minute++;
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));
            foreach( $r as $j => $k )
            {
                $rt60[$j] += $k;
            }

            // Wrap around midnight with a seek
            if( ftell($fp) == $end_of_file )
            {
                fseek($fp, HOURS_PER_DAY * RECORD_SIZE_STATS, SEEK_SET);
            }
        }

        fclose($fp);
    }

    return new StatsOverall($trade, $rt60, $rt24);
}

class StatsHourly
{
    var $trade;
    var $file;

    var $i_raw = array();
    var $i_raw_24 = 0;
    var $o_raw = array();
    var $o_raw_24 = 0;
    var $c_raw = array();
    var $c_raw_24 = 0;
    var $c_trades = array();
    var $c_trades_24 = 0;
    var $prod = array();
    var $t_prod = array();
    var $skim = array();
    var $return = array();
    var $prod_24 = 0;
    var $t_prod_24 = 0;
    var $skim_24 = 0;
    var $return_24 = 0;

    function StatsHourly($trade = null)
    {
        if( !is_array($trade) )
        {
            $this->trade = array('domain' => $trade);
            for( $i = 0; $i < HOURS_PER_DAY; $i++ )
            {
                $this->i_raw[$i] = 0;
                $this->o_raw[$i] = 0;
                $this->c_raw[$i] = 0;
                $this->c_trades[$i] = 0;
                $this->prod[$i] = 0;
                $this->t_prod[$i] = 0;
                $this->skim[$i] = 0;
                $this->return[$i] = 0;
            }
        }
        else
        {
            $this->trade = $trade;
            $this->file = (is_system_trade($trade['domain']) ? DIR_SYSTEM_STATS : DIR_TRADE_STATS) . '/' . $trade['domain'];
            $this->_read_stats();
        }
    }

    function AddStats($s)
    {
        for( $i = 0; $i < HOURS_PER_DAY; $i++ )
        {
            $this->i_raw[$i] += $s->i_raw[$i];
            $this->o_raw[$i] += $s->o_raw[$i];
            $this->c_raw[$i] += $s->c_raw[$i];
            $this->c_trades[$i] += $s->c_trades[$i];

            $this->prod[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->c_raw[$i]/$this->i_raw[$i]) : 0;
            $this->t_prod[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->c_trades[$i]/$this->i_raw[$i]) : 0;
            $this->skim[$i] = $this->c_raw[$i] > 0 ? format_float_to_percent(1 - $this->c_trades[$i] / $this->c_raw[$i]) : 0;
            $this->return[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->o_raw[$i]/$this->i_raw[$i]) : 0;
        }

        $this->i_raw_24 += $s->i_raw_24;
        $this->o_raw_24 += $s->o_raw_24;
        $this->c_raw_24 += $s->c_raw_24;
        $this->c_trades_24 += $s->c_trades_24;

        $this->prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_raw_24/$this->i_raw_24) : 0;
        $this->t_prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_trades_24/$this->i_raw_24) : 0;
        $this->skim_24 = $this->c_raw_24 > 0 ? format_float_to_percent(1 - $this->c_trades_24 / $this->c_raw_24) : 0;
        $this->return_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->o_raw_24/$this->i_raw_24) : 0;
    }

    function _read_stats()
    {
        $fp = fopen($this->file, 'r');
        for( $i = 0; $i < HOURS_PER_DAY; $i++ )
        {
            $r = unpack('L' . STATS_PER_RECORD, fread($fp, RECORD_SIZE_STATS));

            $this->i_raw[$i] = $r[1];
            $this->i_raw_24 += $r[1];

            $this->o_raw[$i] = $r[15];
            $this->o_raw_24 += $r[15];

            $this->c_raw[$i] = $r[7];
            $this->c_raw_24 += $r[7];

            $this->c_trades[$i] = $r[10];
            $this->c_trades_24 += $r[10];

            $this->prod[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->c_raw[$i]/$this->i_raw[$i]) : 0;
            $this->t_prod[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->c_trades[$i]/$this->i_raw[$i]) : 0;
            $this->skim[$i] = $this->c_raw[$i] > 0 ? format_float_to_percent(1 - $this->c_trades[$i] / $this->c_raw[$i]) : 0;
            $this->return[$i] = $this->i_raw[$i] > 0 ? format_float_to_percent($this->o_raw[$i]/$this->i_raw[$i]) : 0;
        }
        fclose($fp);

        $this->prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_raw_24/$this->i_raw_24) : 0;
        $this->t_prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_trades_24/$this->i_raw_24) : 0;
        $this->skim_24 = $this->c_raw_24 > 0 ? format_float_to_percent(1 - $this->c_trades_24 / $this->c_raw_24) : 0;
        $this->return_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->o_raw_24/$this->i_raw_24) : 0;
    }
}

class StatsOverall
{

    var $trade;

    // In last 60 minutes
    var $i_raw_60 = 0;
    var $i_uniq_60 = 0;
    var $i_uniq_pct_60 = 0;
    var $i_proxy_60 = 0;
    var $i_ctry_g_60 = 0;
    var $i_ctry_n_60 = 0;
    var $i_ctry_b_60 = 0;
    var $i_ctry_tot_60 = 0;
    var $i_ctry_g_pct_60 = 0;
    var $i_ctry_n_pct_60 = 0;
    var $i_ctry_b_pct_60 = 0;

    // In last 24 hours
    var $i_raw_24 = 0;
    var $i_uniq_24 = 0;
    var $i_uniq_pct_24 = 0;
    var $i_proxy_24 = 0;
    var $i_ctry_g_24 = 0;
    var $i_ctry_n_24 = 0;
    var $i_ctry_b_24 = 0;
    var $i_ctry_tot_24 = 0;
    var $i_ctry_g_pct_24 = 0;
    var $i_ctry_n_pct_24 = 0;
    var $i_ctry_b_pct_24 = 0;

    // Clicks last 60 minutes
    var $c_raw_60 = 0;
    var $c_uniq_60 = 0;
    var $c_proxy_60 = 0;
    var $c_trades_60 = 0;
    var $c_again_60 = 0;
    var $c_ctry_g_60 = 0;
    var $c_ctry_n_60 = 0;
    var $c_ctry_b_60 = 0;
    var $c_ctry_tot_60 = 0;
    var $c_ctry_g_pct_60 = 0;
    var $c_ctry_n_pct_60 = 0;
    var $c_ctry_b_pct_60 = 0;

    // Clicks last 24 hours
    var $c_raw_24 = 0;
    var $c_uniq_24 = 0;
    var $c_proxy_24 = 0;
    var $c_trades_24 = 0;
    var $c_again_24 = 0;
    var $c_ctry_g_24 = 0;
    var $c_ctry_n_24 = 0;
    var $c_ctry_b_24 = 0;
    var $c_ctry_tot_24 = 0;
    var $c_ctry_g_pct_24 = 0;
    var $c_ctry_n_pct_24 = 0;
    var $c_ctry_b_pct_24 = 0;

    // Out last 60 minutes
    var $o_raw_60 = 0;
    var $o_uniq_60 = 0;
    var $o_proxy_60 = 0;
    var $o_ctry_g_60 = 0;
    var $o_ctry_n_60 = 0;
    var $o_ctry_b_60 = 0;
    var $o_ctry_tot_60 = 0;
    var $o_ctry_g_pct_60 = 0;
    var $o_ctry_n_pct_60 = 0;
    var $o_ctry_b_pct_60 = 0;

    // Out last 24 hours
    var $o_raw_24 = 0;
    var $o_uniq_24 = 0;
    var $o_proxy_24 = 0;
    var $o_ctry_g_24 = 0;
    var $o_ctry_n_24 = 0;
    var $o_ctry_b_24 = 0;
    var $o_ctry_tot_24 = 0;
    var $o_ctry_g_pct_24 = 0;
    var $o_ctry_n_pct_24 = 0;
    var $o_ctry_b_pct_24 = 0;

    // Forces last 60 minutes
    var $f_instant_60 = 0;
    var $f_hourly_60 = 0;

    // Forces last 24 hours
    var $f_instant_24 = 0;
    var $f_hourly_24 = 0;

    // Productivity (CLICKS/RAW INCOMING)
    var $prod_60 = 0;
    var $prod_24 = 0;

    // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
    var $t_prod_60 = 0;
    var $t_prod_24 = 0;

    // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
    var $skim_60 = 0;
    var $skim_24 = 0;

    // Return (OUT/RAW INCOMING)
    var $return_60 = 0;
    var $return_24 = 0;

    // Position in outlists
    var $pos_outlist_main = NULL;
    var $pos_outlist_primary = NULL;
    var $pos_outlist_secondary = NULL;
    var $pos_outlist_forces = NULL;
    var $ignore_requirements = FALSE;

    function StatsOverall($trade, $stats60 = null, $stats24 = null)
    {
        $this->trade = $trade;

        if( !empty($stats60) )
        {
            $i = 1;

            // In last 60 minutes
            $this->i_raw_60 = $stats60[$i++];
            $this->i_uniq_60 = $stats60[$i++];
            $this->i_uniq_pct_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->i_uniq_60/$this->i_raw_60) : 0;
            $this->i_proxy_60 = $stats60[$i++];
            $this->i_ctry_g_60 = $stats60[$i++];
            $this->i_ctry_n_60 = $stats60[$i++];
            $this->i_ctry_b_60 = $stats60[$i++];
            $this->i_ctry_tot_60 = $this->i_ctry_g_60 + $this->i_ctry_n_60 + $this->i_ctry_b_60;
            $this->i_ctry_g_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_g_60/$this->i_ctry_tot_60) : 0;
            $this->i_ctry_n_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_n_60/$this->i_ctry_tot_60) : 0;
            $this->i_ctry_b_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_b_60/$this->i_ctry_tot_60) : 0;

            // Clicks last 60 minutes
            $this->c_raw_60 = $stats60[$i++];
            $this->c_uniq_60 = $stats60[$i++];
            $this->c_proxy_60 = $stats60[$i++];
            $this->c_trades_60 = $stats60[$i++];
            $this->c_again_60 = $stats60[$i++];
            $this->c_ctry_g_60 = $stats60[$i++];
            $this->c_ctry_n_60 = $stats60[$i++];
            $this->c_ctry_b_60 = $stats60[$i++];
            $this->c_ctry_tot_60 = $this->c_ctry_g_60 + $this->c_ctry_n_60 + $this->c_ctry_b_60;
            $this->c_ctry_g_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_g_60/$this->c_ctry_tot_60) : 0;
            $this->c_ctry_n_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_n_60/$this->c_ctry_tot_60) : 0;
            $this->c_ctry_b_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_b_60/$this->c_ctry_tot_60) : 0;

            // Out last 60 minutes
            $this->o_raw_60 = $stats60[$i++];
            $this->o_uniq_60 = $stats60[$i++];
            $this->o_proxy_60 = $stats60[$i++];
            $this->o_ctry_g_60 = $stats60[$i++];
            $this->o_ctry_n_60 = $stats60[$i++];
            $this->o_ctry_b_60 = $stats60[$i++];
            $this->o_ctry_tot_60 = $this->o_ctry_g_60 + $this->o_ctry_n_60 + $this->o_ctry_b_60;
            $this->o_ctry_g_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_g_60/$this->o_ctry_tot_60) : 0;
            $this->o_ctry_n_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_n_60/$this->o_ctry_tot_60) : 0;
            $this->o_ctry_b_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_b_60/$this->o_ctry_tot_60) : 0;

            // Forces last 60 minutes
            $this->f_instant_60 = $stats60[$i++];
            $this->f_hourly_60 = $stats60[$i++];

            // Productivity (CLICKS/RAW INCOMING)
            $this->prod_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->c_raw_60/$this->i_raw_60) : 0;

            // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
            $this->t_prod_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->c_trades_60/$this->i_raw_60) : 0;

            // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
            $this->skim_60 = $this->c_raw_60 > 0 ? format_float_to_percent(1 - $this->c_trades_60 / $this->c_raw_60) : 0;

            // Return (OUT/RAW INCOMING)
            $this->return_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->o_raw_60/$this->i_raw_60) : 0;
        }


        if( !empty($stats24) )
        {
            $i = 1;

            // In last 24 hours
            $this->i_raw_24 = $stats24[$i++];
            $this->i_uniq_24 = $stats24[$i++];
            $this->i_uniq_pct_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->i_uniq_24/$this->i_raw_24) : 0;
            $this->i_proxy_24 = $stats24[$i++];
            $this->i_ctry_g_24 = $stats24[$i++];
            $this->i_ctry_n_24 = $stats24[$i++];
            $this->i_ctry_b_24 = $stats24[$i++];
            $this->i_ctry_tot_24 = $this->i_ctry_g_24 + $this->i_ctry_n_24 + $this->i_ctry_b_24;
            $this->i_ctry_g_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_g_24/$this->i_ctry_tot_24) : 0;
            $this->i_ctry_n_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_n_24/$this->i_ctry_tot_24) : 0;
            $this->i_ctry_b_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_b_24/$this->i_ctry_tot_24) : 0;

            // Clicks last 24 hours
            $this->c_raw_24 = $stats24[$i++];
            $this->c_uniq_24 = $stats24[$i++];
            $this->c_proxy_24 = $stats24[$i++];
            $this->c_trades_24 = $stats24[$i++];
            $this->c_again_24 = $stats24[$i++];
            $this->c_ctry_g_24 = $stats24[$i++];
            $this->c_ctry_n_24 = $stats24[$i++];
            $this->c_ctry_b_24 = $stats24[$i++];
            $this->c_ctry_tot_24 = $this->c_ctry_g_24 + $this->c_ctry_n_24 + $this->c_ctry_b_24;
            $this->c_ctry_g_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_g_24/$this->c_ctry_tot_24) : 0;
            $this->c_ctry_n_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_n_24/$this->c_ctry_tot_24) : 0;
            $this->c_ctry_b_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_b_24/$this->c_ctry_tot_24) : 0;

            // Out last 24 hours
            $this->o_raw_24 = $stats24[$i++];
            $this->o_uniq_24 = $stats24[$i++];
            $this->o_proxy_24 = $stats24[$i++];
            $this->o_ctry_g_24 = $stats24[$i++];
            $this->o_ctry_n_24 = $stats24[$i++];
            $this->o_ctry_b_24 = $stats24[$i++];
            $this->o_ctry_tot_24 = $this->o_ctry_g_24 + $this->o_ctry_n_24 + $this->o_ctry_b_24;
            $this->o_ctry_g_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_g_24/$this->o_ctry_tot_24) : 0;
            $this->o_ctry_n_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_n_24/$this->o_ctry_tot_24) : 0;
            $this->o_ctry_b_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_b_24/$this->o_ctry_tot_24) : 0;

            // Forces last 24 hours
            $this->f_instant_24 = $stats24[$i++];
            $this->f_hourly_24 = $stats24[$i++];

            // Productivity (CLICKS/RAW INCOMING)
            $this->prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_raw_24/$this->i_raw_24) : 0;

            // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
            $this->t_prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_trades_24/$this->i_raw_24) : 0;

            // Skim (CLICKS TO TRADES / CLICKS)
            $this->skim_24 = $this->c_raw_24 > 0 ? format_float_to_percent(1 - $this->c_trades_24 / $this->c_raw_24) : 0;

            // Return (OUT/RAW INCOMING)
            $this->return_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->o_raw_24/$this->i_raw_24) : 0;
        }
    }

    function AddStats($s)
    {
        if( $s->i_raw_60 === STATS_UNKNOWN )
        {
            return;
        }

        // In last 60 minutes
        $this->i_raw_60 += $s->i_raw_60;
        $this->i_uniq_60 += $s->i_uniq_60;
        $this->i_uniq_pct_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->i_uniq_60/$this->i_raw_60) : 0;
        $this->i_proxy_60 += $s->i_proxy_60;
        $this->i_ctry_g_60 += $s->i_ctry_g_60;
        $this->i_ctry_n_60 += $s->i_ctry_n_60;
        $this->i_ctry_b_60 += $s->i_ctry_b_60;
        $this->i_ctry_tot_60 = $this->i_ctry_g_60 + $this->i_ctry_n_60 + $this->i_ctry_b_60;
        $this->i_ctry_g_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_g_60/$this->i_ctry_tot_60) : 0;
        $this->i_ctry_n_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_n_60/$this->i_ctry_tot_60) : 0;
        $this->i_ctry_b_pct_60 = $this->i_ctry_tot_60 > 0 ? format_float_to_percent($this->i_ctry_b_60/$this->i_ctry_tot_60) : 0;

        // Clicks last 60 minutes
        $this->c_raw_60 += $s->c_raw_60;
        $this->c_uniq_60 += $s->c_uniq_60;
        $this->c_proxy_60 += $s->c_proxy_60;
        $this->c_trades_60 += $s->c_trades_60;
        $this->c_again_60 += $s->c_again_60;
        $this->c_ctry_g_60 += $s->c_ctry_g_60;
        $this->c_ctry_n_60 += $s->c_ctry_n_60;
        $this->c_ctry_b_60 += $s->c_ctry_b_60;
        $this->c_ctry_tot_60 = $this->c_ctry_g_60 + $this->c_ctry_n_60 + $this->c_ctry_b_60;
        $this->c_ctry_g_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_g_60/$this->c_ctry_tot_60) : 0;
        $this->c_ctry_n_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_n_60/$this->c_ctry_tot_60) : 0;
        $this->c_ctry_b_pct_60 = $this->c_ctry_tot_60 > 0 ? format_float_to_percent($this->c_ctry_b_60/$this->c_ctry_tot_60) : 0;

        // Out last 60 minutes
        $this->o_raw_60 += $s->o_raw_60;
        $this->o_uniq_60 += $s->o_uniq_60;
        $this->o_proxy_60 += $s->o_proxy_60;
        $this->o_ctry_g_60 += $s->o_ctry_g_60;
        $this->o_ctry_n_60 += $s->o_ctry_n_60;
        $this->o_ctry_b_60 += $s->o_ctry_b_60;
        $this->o_ctry_tot_60 = $this->o_ctry_g_60 + $this->o_ctry_n_60 + $this->o_ctry_b_60;
        $this->o_ctry_g_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_g_60/$this->o_ctry_tot_60) : 0;
        $this->o_ctry_n_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_n_60/$this->o_ctry_tot_60) : 0;
        $this->o_ctry_b_pct_60 = $this->o_ctry_tot_60 > 0 ? format_float_to_percent($this->o_ctry_b_60/$this->o_ctry_tot_60) : 0;

        // Forces last 60 minutes
        $this->f_instant_60 += $s->f_instant_60;
        $this->f_hourly_60 += $s->f_hourly_60;

        // Productivity (CLICKS/RAW INCOMING)
        $this->prod_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->c_raw_60/$this->i_raw_60) : 0;

        // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
        $this->t_prod_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->c_trades_60/$this->i_raw_60) : 0;

        // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
        $this->skim_60 = $this->c_raw_60 > 0 ? format_float_to_percent(1 - $this->c_trades_60/$this->c_raw_60) : 0;

        // Return (OUT/RAW INCOMING)
        $this->return_60 = $this->i_raw_60 > 0 ? format_float_to_percent($this->o_raw_60/$this->i_raw_60) : 0;



        // In last 24 hours
        $this->i_raw_24 += $s->i_raw_24;
        $this->i_uniq_24 += $s->i_uniq_24;
        $this->i_uniq_pct_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->i_uniq_24/$this->i_raw_24) : 0;
        $this->i_proxy_24 += $s->i_proxy_24;
        $this->i_ctry_g_24 += $s->i_ctry_g_24;
        $this->i_ctry_n_24 += $s->i_ctry_n_24;
        $this->i_ctry_b_24 += $s->i_ctry_b_24;
        $this->i_ctry_tot_24 = $this->i_ctry_g_24 + $this->i_ctry_n_24 + $this->i_ctry_b_24;
        $this->i_ctry_g_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_g_24/$this->i_ctry_tot_24) : 0;
        $this->i_ctry_n_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_n_24/$this->i_ctry_tot_24) : 0;
        $this->i_ctry_b_pct_24 = $this->i_ctry_tot_24 > 0 ? format_float_to_percent($this->i_ctry_b_24/$this->i_ctry_tot_24) : 0;

        // Clicks last 24 hours
        $this->c_raw_24 += $s->c_raw_24;
        $this->c_uniq_24 += $s->c_uniq_24;
        $this->c_proxy_24 += $s->c_proxy_24;
        $this->c_trades_24 += $s->c_trades_24;
        $this->c_again_24 += $s->c_again_24;
        $this->c_ctry_g_24 += $s->c_ctry_g_24;
        $this->c_ctry_n_24 += $s->c_ctry_n_24;
        $this->c_ctry_b_24 += $s->c_ctry_b_24;
        $this->c_ctry_tot_24 = $this->c_ctry_g_24 + $this->c_ctry_n_24 + $this->c_ctry_b_24;
        $this->c_ctry_g_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_g_24/$this->c_ctry_tot_24) : 0;
        $this->c_ctry_n_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_n_24/$this->c_ctry_tot_24) : 0;
        $this->c_ctry_b_pct_24 = $this->c_ctry_tot_24 > 0 ? format_float_to_percent($this->c_ctry_b_24/$this->c_ctry_tot_24) : 0;

        // Out last 24 hours
        $this->o_raw_24 += $s->o_raw_24;
        $this->o_uniq_24 += $s->o_uniq_24;
        $this->o_proxy_24 += $s->o_proxy_24;
        $this->o_ctry_g_24 += $s->o_ctry_g_24;
        $this->o_ctry_n_24 += $s->o_ctry_n_24;
        $this->o_ctry_b_24 += $s->o_ctry_b_24;
        $this->o_ctry_tot_24 = $this->o_ctry_g_24 + $this->o_ctry_n_24 + $this->o_ctry_b_24;
        $this->o_ctry_g_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_g_24/$this->o_ctry_tot_24) : 0;
        $this->o_ctry_n_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_n_24/$this->o_ctry_tot_24) : 0;
        $this->o_ctry_b_pct_24 = $this->o_ctry_tot_24 > 0 ? format_float_to_percent($this->o_ctry_b_24/$this->o_ctry_tot_24) : 0;

        // Forces last 24 hours
        $this->f_instant_24 += $s->f_instant_24;
        $this->f_hourly_24 += $s->f_hourly_24;

        // Productivity (CLICKS/RAW INCOMING)
        $this->prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_raw_24/$this->i_raw_24) : 0;

        // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
        $this->t_prod_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->c_trades_24/$this->i_raw_24) : 0;

        // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
        $this->skim_24 = $this->c_raw_24 > 0 ? format_float_to_percent(1 - $this->c_trades_24 / $this->c_raw_24) : 0;

        // Return (OUT/RAW INCOMING)
        $this->return_24 = $this->i_raw_24 > 0 ? format_float_to_percent($this->o_raw_24/$this->i_raw_24) : 0;
    }

    function SetUnknown()
    {
        // In last 60 minutes
        $this->i_raw_60 = STATS_UNKNOWN;
        $this->i_uniq_60 = STATS_UNKNOWN;
        $this->i_uniq_pct_60 = STATS_UNKNOWN;
        $this->i_proxy_60 = STATS_UNKNOWN;
        $this->i_ctry_g_60 = STATS_UNKNOWN;
        $this->i_ctry_n_60 = STATS_UNKNOWN;
        $this->i_ctry_b_60 = STATS_UNKNOWN;
        $this->i_ctry_tot_60 = STATS_UNKNOWN;
        $this->i_ctry_g_pct_60 = 0;
        $this->i_ctry_n_pct_60 = 0;
        $this->i_ctry_b_pct_60 = 0;

        // Clicks last 60 minutes
        $this->c_raw_60 = STATS_UNKNOWN;
        $this->c_uniq_60 = STATS_UNKNOWN;
        $this->c_proxy_60 = STATS_UNKNOWN;
        $this->c_trades_60 = STATS_UNKNOWN;
        $this->c_again_60 = STATS_UNKNOWN;
        $this->c_ctry_g_60 = STATS_UNKNOWN;
        $this->c_ctry_n_60 = STATS_UNKNOWN;
        $this->c_ctry_b_60 = STATS_UNKNOWN;
        $this->c_ctry_tot_60 = STATS_UNKNOWN;
        $this->c_ctry_g_pct_60 = 0;
        $this->c_ctry_n_pct_60 = 0;
        $this->c_ctry_b_pct_60 = 0;

        // Out last 60 minutes
        $this->o_raw_60 = STATS_UNKNOWN;
        $this->o_uniq_60 = STATS_UNKNOWN;
        $this->o_proxy_60 = STATS_UNKNOWN;
        $this->o_ctry_g_60 = STATS_UNKNOWN;
        $this->o_ctry_n_60 = STATS_UNKNOWN;
        $this->o_ctry_b_60 = STATS_UNKNOWN;
        $this->o_ctry_tot_60 = STATS_UNKNOWN;
        $this->o_ctry_g_pct_60 = 0;
        $this->o_ctry_n_pct_60 = 0;
        $this->o_ctry_b_pct_60 = 0;

        // Forces last 60 minutes
        $this->f_instant_60 = STATS_UNKNOWN;
        $this->f_hourly_60 = STATS_UNKNOWN;

        // Productivity (CLICKS/RAW INCOMING)
        $this->prod_60 = STATS_UNKNOWN;

        // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
        $this->t_prod_60 = STATS_UNKNOWN;

        // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
        $this->skim_60 = STATS_UNKNOWN;

        // Return (OUT/RAW INCOMING)
        $this->return_60 = STATS_UNKNOWN;



        // In last 24 hours
        $this->i_raw_24 = STATS_UNKNOWN;
        $this->i_uniq_24 = STATS_UNKNOWN;
        $this->i_uniq_pct_24 = STATS_UNKNOWN;
        $this->i_proxy_24 = STATS_UNKNOWN;
        $this->i_ctry_g_24 = STATS_UNKNOWN;
        $this->i_ctry_n_24 = STATS_UNKNOWN;
        $this->i_ctry_b_24 = STATS_UNKNOWN;
        $this->i_ctry_tot_24 = STATS_UNKNOWN;
        $this->i_ctry_g_pct_24 = 0;
        $this->i_ctry_n_pct_24 = 0;
        $this->i_ctry_b_pct_24 = 0;

        // Clicks last 24 hours
        $this->c_raw_24 = STATS_UNKNOWN;
        $this->c_uniq_24 = STATS_UNKNOWN;
        $this->c_proxy_24 = STATS_UNKNOWN;
        $this->c_trades_24 = STATS_UNKNOWN;
        $this->c_again_24 = STATS_UNKNOWN;
        $this->c_ctry_g_24 = STATS_UNKNOWN;
        $this->c_ctry_n_24 = STATS_UNKNOWN;
        $this->c_ctry_b_24 = STATS_UNKNOWN;
        $this->c_ctry_tot_24 = STATS_UNKNOWN;
        $this->c_ctry_g_pct_24 = 0;
        $this->c_ctry_n_pct_24 = 0;
        $this->c_ctry_b_pct_24 = 0;

        // Out last 24 hours
        $this->o_raw_24 = STATS_UNKNOWN;
        $this->o_uniq_24 = STATS_UNKNOWN;
        $this->o_proxy_24 = STATS_UNKNOWN;
        $this->o_ctry_g_24 = STATS_UNKNOWN;
        $this->o_ctry_n_24 = STATS_UNKNOWN;
        $this->o_ctry_b_24 = STATS_UNKNOWN;
        $this->o_ctry_tot_24 = STATS_UNKNOWN;
        $this->o_ctry_g_pct_24 = 0;
        $this->o_ctry_n_pct_24 = 0;
        $this->o_ctry_b_pct_24 = 0;

        // Forces last 24 hours
        $this->f_instant_24 = STATS_UNKNOWN;
        $this->f_hourly_24 = STATS_UNKNOWN;

        // Productivity (CLICKS/RAW INCOMING)
        $this->prod_24 = STATS_UNKNOWN;

        // Trade productivity (CLICKS TO TRADES/RAW INCOMING)
        $this->t_prod_24 = STATS_UNKNOWN;

        // Skim (CLICKS - CLICKS TO TRADES/RAW INCOMING)
        $this->skim_24 = STATS_UNKNOWN;

        // Return (OUT/RAW INCOMING)
        $this->return_24 = STATS_UNKNOWN;
    }
}

class StatsDetailed
{
    var $trade;

    // Browser detection
    var $browser_regex = '~(MSIE \d+\.\d+|Firefox/\d+\.\d+|Chrome/\d+\.\d+|Opera/\d+\.\d+|iPhone OS|Android \d+\.\d+|Version/\d+\.\d+|Safari/\d+|Blackberry\d+|Konqueror/\d+\.\d+)~i';
    var $browser_search = array('MSIE', 'Version/', 'Firefox/', 'Opera/', 'Safari/', 'Chrome/', 'Konqueror/');
    var $browser_replace = array('Internet Explorer', 'Safari ', 'Firefox ', 'Opera ', 'Safari ', 'Chrome ', 'Konqueror ');

    // In
    var $i_total = 0;
    var $i_ip = array();
    var $i_proxy = array();
    var $i_agent = array();
    var $i_ctry = array();
    var $i_ref = array();
    var $i_land = array();
    var $i_lang = array();

    // Out
    var $o_total = 0;
    var $o_ip = array();
    var $o_proxy = array();
    var $o_agent = array();
    var $o_ctry = array();
    var $o_page = array();
    var $o_link = array();
    var $o_lang = array();

    // Clicks
    var $c_total = 0;
    var $c_ip = array();
    var $c_proxy = array();
    var $c_agent = array();
    var $c_ctry = array();
    var $c_page = array();
    var $c_link = array();
    var $c_lang = array();

    function StatsDetailed($trade, $amount = 10)
    {
        $db = get_trade_db($trade);
        $stats_dir = get_trade_stats_dir($trade);
        $this->trade = $db->Retrieve($trade);


        // Process in log
        $fp = fopen("$stats_dir/$trade-in", 'r');
        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);

            $this->i_total++;
            $this->i_ip[$data[1]]++;
            $this->i_ctry[$data[4]]++;
            $this->i_ref[$data[5]]++;
            $this->i_land[$data[6]]++;
            $this->i_lang[$data[7]]++;

            if( $data[2] )
            {
                $this->i_proxy[$data[1]]++;
            }

            if( preg_match($this->browser_regex, $data[3], $matches) )
            {
                $browser = str_replace($this->browser_search, $this->browser_replace, $matches[1]);
                $this->i_agent[$browser]++;
            }
            else
            {
                $this->i_agent[$data[3]]++;
            }
        }
        fclose($fp);

        arsort($this->i_ip);
        arsort($this->i_proxy);
        arsort($this->i_agent);
        arsort($this->i_ctry);
        arsort($this->i_ref);
        arsort($this->i_land);
        arsort($this->i_lang);

        $this->i_ip = $this->_array_slice($this->i_ip, $amount);
        $this->i_proxy = $this->_array_slice($this->i_proxy, $amount);
        $this->i_agent = $this->_array_slice($this->i_agent, $amount);
        $this->i_ctry = $this->_array_slice($this->i_ctry, $amount);
        $this->i_ref = $this->_array_slice($this->i_ref, $amount);
        $this->i_land = $this->_array_slice($this->i_land, $amount);
        $this->i_lang = $this->_array_slice($this->i_lang, $amount);


        // Process out log
        $fp = fopen("$stats_dir/$trade-out", 'r');
        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);

            $this->o_total++;
            $this->o_ip[$data[1]]++;
            $this->o_ctry[$data[4]]++;
            $this->o_page[$data[5]]++;
            $this->o_link[$data[6]]++;
            $this->o_lang[$data[7]]++;

            if( $data[2] )
            {
                $this->o_proxy[$data[1]]++;
            }

            if( preg_match($this->browser_regex, $data[3], $matches) )
            {
                $browser = str_replace($this->browser_search, $this->browser_replace, $matches[1]);
                $this->o_agent[$browser]++;
            }
            else
            {
                $this->o_agent[$data[3]]++;
            }
        }
        fclose($fp);

        arsort($this->o_ip);
        arsort($this->o_proxy);
        arsort($this->o_agent);
        arsort($this->o_ctry);
        arsort($this->o_page);
        arsort($this->o_link);
        arsort($this->o_lang);

        $this->o_ip = $this->_array_slice($this->o_ip, $amount);
        $this->o_proxy = $this->_array_slice($this->o_proxy, $amount);
        $this->o_agent = $this->_array_slice($this->o_agent, $amount);
        $this->o_ctry = $this->_array_slice($this->o_ctry, $amount);
        $this->o_page = $this->_array_slice($this->o_page, $amount);
        $this->o_link = $this->_array_slice($this->o_link, $amount);
        $this->o_lang = $this->_array_slice($this->o_lang, $amount);

        // Process clicks log
        $fp = fopen("$stats_dir/$trade-clicks", 'r');
        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);

            $this->c_total++;
            $this->c_ip[$data[1]]++;
            $this->c_ctry[$data[4]]++;
            $this->c_page[$data[5]]++;
            $this->c_link[$data[6]]++;
            $this->c_lang[$data[7]]++;

            if( $data[2] )
            {
                $this->c_proxy[$data[1]]++;
            }

            if( preg_match($this->browser_regex, $data[3], $matches) )
            {
                $browser = str_replace($this->browser_search, $this->browser_replace, $matches[1]);
                $this->c_agent[$browser]++;
            }
            else
            {
                $this->c_agent[$data[3]]++;
            }
        }
        fclose($fp);

        arsort($this->c_ip);
        arsort($this->c_proxy);
        arsort($this->c_agent);
        arsort($this->c_ctry);
        arsort($this->c_page);
        arsort($this->c_link);
        arsort($this->c_lang);

        $this->c_ip = $this->_array_slice($this->c_ip, $amount);
        $this->c_proxy = $this->_array_slice($this->c_proxy, $amount);
        $this->c_agent = $this->_array_slice($this->c_agent, $amount);
        $this->c_ctry = $this->_array_slice($this->c_ctry, $amount);
        $this->c_page = $this->_array_slice($this->c_page, $amount);
        $this->c_link = $this->_array_slice($this->c_link, $amount);
        $this->c_lang = $this->_array_slice($this->c_lang, $amount);
    }

    function _array_slice($input, $length = 10)
    {
        if( version_compare(PHP_VERSION, '5.0.2') >= 0 )
        {
            return array_slice($input, 0, $length, true);
        }

        if( count($input) <= $length )
        {
            return $input;
        }

        $result = array();
        $counter = 0;

        foreach( $input as $key => $value )
        {
            if( ++$counter > $length )
            {
                break;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}



define('STATS_HISTORY_REGEX_REPLACE_DAILY', '$1-$2-$3');
define('STATS_HISTORY_REGEX_REPLACE_MONTHLY', '$1-$2');
define('STATS_HISTORY_REGEX_REPLACE_YEARLY', '$1');

class StatsHistory
{
    var $trade;
    var $history_file;
    var $stats;

    function StatsHistory($trade, $start, $end, $regex_replace)
    {
        $this->trade = $trade;
        $this->history_file = empty($trade) ? DIR_DATA . '/history' : (is_system_trade($trade) ? DIR_SYSTEM_STATS : DIR_TRADE_STATS) . "/$trade-history";
        $this->_read_stats($start, $end, $regex_replace);
    }

    function _read_stats($start, $end, $regex_replace)
    {
        $this->stats = $this->_create_zeroed_stats($start, $end, $regex_replace);

        $fp = fopen($this->history_file, 'r');
        while( !feof($fp) )
        {
            $line = trim(fgets($fp));

            if( empty($line) )
            {
                continue;
            }

            $data = explode('|', $line);
            $stats_date = array_shift($data);

            if( $stats_date >= $start && $stats_date <= $end )
            {
                $key = preg_replace('~^(\d\d\d\d)(\d\d)(\d\d)$~', $regex_replace, $stats_date);

                foreach( $data as $i => $amt )
                {
                    $this->stats[$key][$i] += $amt;
                }
            }
        }
        fclose($fp);
    }

    function _create_zeroed_stats($start, $end, $regex_replace)
    {
        $ts_start = strtotime(join('-', array(substr($start, 0, 4), substr($start, 4, 2), substr($start, 6, 2))) . ' 12:00:00');
        $ts_end = strtotime(join('-', array(substr($end, 0, 4), substr($end, 4, 2), substr($end, 6, 2))) . ' 12:00:00');

        // Swap start and end if necessary
        if( $ts_start > $ts_end )
        {
            list($ts_start, $ts_end) = array($ts_end, $ts_start);
        }

        $stats = array();
        for( $i = $ts_start; $i <= $ts_end; $i += SECONDS_PER_DAY )
        {
            $stats_date = date('Ymd', $i);
            $key = preg_replace('~^(\d\d\d\d)(\d\d)(\d\d)$~', $regex_replace, $stats_date);

            if( !isset($stats[$key]) )
            {
                $stats[$key] = array_fill(0, 22, 0);
            }
        }

        return $stats;
    }
}

?>