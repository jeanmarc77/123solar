<?php
if (isset($_SERVER['REMOTE_ADDR'])) {
    //die('Direct access not permitted');
}
// For meterN Electrical household meter 
// You might need to set "Energy Tariffs" Export/Import Model as Net into PVoutput settings


$metnum  = 1; // meterN household meter number
$passomn = 100000; // pass-over of your meterN counter
$meterndir = '/srv/http/metern'; // meterN directory

// No edit should be needed bellow
$today = date('Ymd');
if (file_exists("$meterndir/data/csv/$today.csv")) {
    $lines       = file("$meterndir/data/csv/$today.csv");
    $first_array = preg_split('/,/', $lines[1]);
    $contalines  = count($lines);
    
    if ($contalines > 2) { // daily file
        $last_array = preg_split('/,/', $lines[$contalines - 1]);
        $prev_array = preg_split('/,/', $lines[$contalines - 2]);
    } else {
        $last_array = $first_array;
        $prev_array = $first_array;
    }
    $val_first = trim($first_array[$metnum]);
    $val_last  = trim($last_array[$metnum]);

	settype($val_first, 'int');
	settype($val_last, 'int');
	if ($val_last < $val_first && $passomn > 0) { // counter pass over
		$val_last += $passomn;
	}
	$CONSUMED_WHD = $val_last - $val_first; // Daily energy Consumption
    
    $hour        = substr($last_array[0], 0, 2);
    $minute      = substr($last_array[0], 3, 2);
    $prevhour    = substr($prev_array[0], 0, 2);
    $prevminute  = substr($prev_array[0], 3, 2);
    $UTCdate     = strtotime("$today" . $hour . ':' . $minute);
    $diffUTCdate = strtotime("$today" . $prevhour . ':' . $prevminute);
    $diffTime    = $UTCdate - $diffUTCdate;
    
    if ($diffTime >= 300) {
        settype($first_array[$metnum], 'int');
        settype($last_array[$metnum], 'int');
        if ($last_array[$metnum] < $prev_array[$metnum] && $passomn > 0) { // counter just pass over
            $last_array[$metnum] += $passomn;
        }
        $CONSUMED_W = round((($last_array[$metnum] - $prev_array[$metnum]) * 3600) / $diffTime, 0); // Avg Power Consumption
    } else {
        $CONSUMED_W = 0;
    }
} else {
    $CONSUMED_WHD = 0;
    $CONSUMED_W   = 0;
}
?>