<?php
/**
 * /srv/http/123solar/programs/programlastdays.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set('UTC');

if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
} else {
	$invtnum = 1;
}
include "../config/config_invt$invtnum.php";

$stack = array();

$dir    = '../data/invt' . $invtnum . '/production/';
$output = glob($dir . '*.csv');

$yesterd = ((strtotime(date('Ymd')) - 86400) * 1000);
$thisyear = date('Y');
if (isset($output[0])) {
	$j       = 0;
	$day_num = 0;
	
	while ($day_num < 20) { // Digging
		$filename          = $dir . 'energy'. $thisyear . '.csv';
		if (file_exists($filename)) {
			$lines       = file($filename);
			$countalines = count($lines);
			$array = preg_split('/,/', $lines[$countalines - $j - 1]);
			if(isset($array[0]) && isset($array[1])) {
				$year  = substr($array[0], 0, 4);
				$month = substr($array[0], 4, 2);
				$day   = substr($array[0], 6, 2);
				$UTCdate = ((strtotime($year . '-' . $month . '-' . $day))*1000);

				if ($yesterd - $UTCdate < (86400000 * 20)) {
					$production = round((floatval($array[1]) * ${'CORRECTFACTOR' . $invtnum}), 1);

					$stack[$day_num] = array(
						$UTCdate,
						$production
					);
					$day_num++;
				}
			}
			$j++;

			if ($countalines == $j || $j == 20) {
				if ($thisyear == date('Y')) {
					$thisyear--; //Takes older file
					$j = 0;
				} else {
					$day_num = 20; //Stop
				}
			}
		} else {
			$day_num = 20; //Stop
		} // file exist
	} // digging

} else {
	$stack[0] = array(
		$yesterd,
		0
	);
}

$cnt = count($stack);
if ($cnt == 0) {
	$stack[0] = array(
		$yesterd,
		0
	);
}

sort($stack);
$data = array(
	'data' => $stack
);

header("Content-type: application/json");
echo json_encode($stack);
?>
