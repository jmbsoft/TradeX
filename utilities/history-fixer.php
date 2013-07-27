<?php

require_once 'lib/global.php';
require_once 'utility.php';

$regex_search = '~\|(\d*)(2010\d\d\d\d)\|~';
$regex_replace = "|$1\n$2|";

foreach( dir_read_files(DIR_SYSTEM) as $trade )
{
    $file = DIR_SYSTEM_STATS . "/$trade-history";
    $history = trim(file_get_contents($file));
    $history = preg_replace($regex_search, $regex_replace, $history);
    file_write($file, $history . "\n");
}

foreach( dir_read_files(DIR_TRADES) as $trade )
{
    $file = DIR_TRADE_STATS . "/$trade-history";
    $history = trim(file_get_contents($file));
    $history = preg_replace($regex_search, $regex_replace, $history);
    file_write($file, $history . "\n");
}

echo "DONE!\n";

?>