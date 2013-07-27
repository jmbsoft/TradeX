<?php

define('GEOIP_COUNTRY_EDITION', 106);
define('GEOIP_COUNTRY_BEGIN', 16776960);
define('STANDARD_RECORD_LENGTH', 3);

function geoip_country_id($ip_address)
{
    $fp = fopen(DIR_ASSETS . '/geoip.dat', 'rb');
    $long_ip = ip2long($ip_address);
    $offset = 0;
    $country_id = null;

    for( $depth = 31; $depth >= 0; --$depth )
    {
        fseek($fp, 2 * STANDARD_RECORD_LENGTH * $offset, SEEK_SET);
        $buf = fread($fp, 2 * STANDARD_RECORD_LENGTH);

        $x = array(0,0);

        for( $i = 0; $i < 2; ++$i )
        {
            for( $j = 0; $j < STANDARD_RECORD_LENGTH; ++$j )
            {
                $x[$i] += ord($buf[STANDARD_RECORD_LENGTH * $i + $j]) << ($j * 8);
            }
        }

        if( $long_ip & (1 << $depth) )
        {
            if( $x[1] >= GEOIP_COUNTRY_BEGIN )
            {
               $country_id = $x[1] - GEOIP_COUNTRY_BEGIN;
            }

            $offset = $x[1];
        }
        else
        {
            if( $x[0] >= GEOIP_COUNTRY_BEGIN )
            {
                $country_id = $x[0] - GEOIP_COUNTRY_BEGIN;
            }

            $offset = $x[0];
        }

        if( !empty($country_id) )
        {
            break;
        }
    }

    return $country_id;
}

?>