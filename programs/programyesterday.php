<?php
/**
 * /srv/http/123solar/programs/programyesterday.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set('UTC');
include '../languages/' . $LANG . '.php';

if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$strto = $_GET['invtnum'];
	$upto  = $_GET['invtnum'];
} else {
	if ($NUMINV > 1) {
		$strto = 1;
		$upto  = $NUMINV;
	} else {
		$strto = 1;
		$upto  = 1;
	}
}

$i          = 0;
$latestfile = null;
for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) { // Check files dates
	$dir    = '../data/invt' . $invt_num . '/csv/';
	$output = glob($dir . '*.csv');
	sort($output);
	$cnt = count($output);
	if ($cnt > 1) {
		$option          = $output[$cnt - 2];
		$option          = str_replace($dir, '', $option);
		$year            = substr($option, 0, 4);
		$month           = substr($option, 4, 2);
		$day             = substr($option, 6, 2);
		$fileUTCdate[$i] = strtotime($year . "-" . $month . "-" . $day);
	} else {
		$fileUTCdate[$i] = null;
	}
	if ($fileUTCdate[$i] > $latestfile) {
		$latestfile     = $fileUTCdate[$i];
		$latestfilename = $option; // $cnt ?
	}
	$i++;
}

if (isset($latestfile)) {
	$year  = date('Y', $latestfile);
	$month = date('m', $latestfile);
	$day   = date('d', $latestfile);

	$ystdayUTC = strtotime(date('Ymd', strtotime("-1 day")));

	$i = 0;
	for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) {
		$dir      = '../data/invt' . $invt_num . '/csv/';
		$filename = $dir . $latestfilename;
		if (file_exists($filename)) { // skip older files
			include "../config/config_invt$invt_num.php";
			$file       = file($filename);
			$contalines = count($file);

			for ($line_num = 1; $line_num < $contalines; $line_num++) {
				$array = preg_split('/,/', $file[$line_num]);

				$SDTE[$line_num] = $array[0];
				$KWHT[$line_num] = floatval($array[27]);

				if ($line_num == 1) {
					$pastline_num = 1;
				} else {
					$pastline_num = $line_num - 1;
				}

				$hour        = substr($SDTE[$line_num], 0, 2);
				$minute      = substr($SDTE[$line_num], 3, 2);
				$seconde     = substr($SDTE[$line_num], 6, 2);
				$pasthour    = substr($SDTE[$pastline_num], 0, 2);
				$pastminute  = substr($SDTE[$pastline_num], 3, 2);
				$pastseconde = substr($SDTE[$pastline_num], 6, 2);

				$UTCdate = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $seconde);

				$diffUTCdate = strtotime($year . '-' . $month . '-' . $day . ' ' . $pasthour . ':' . $pastminute . ':' . $pastseconde);
				$diffTime    = $UTCdate - $diffUTCdate;

				if ($diffTime != 0) {
					$AvgPOW = round((((($KWHT[$line_num] - $KWHT[$pastline_num]) * 3600) / $diffTime) * 1000), 1);
				} else {
					$AvgPOW = 0;
				}

				$UTCdate = $UTCdate * 1000;
				if (!isset($totstack[$hour . $minute][1])) {
					$totstack[$hour . $minute][1] = 0;
				}
				$totAvgPOW                 = $AvgPOW + $totstack[$hour . $minute][1];
				$totstack[$hour . $minute] = array(
					$UTCdate,
					$totAvgPOW
				); // minute precision
				if (!isset($MaxPow)) {
					$MaxPow = 0;
				}
				if ($totAvgPOW >= $MaxPow) { // Annotation max
					$MaxPow       = $totAvgPOW;
					$arr['xAxis'] = 0;
					$arr['yAxis'] = 0;
					$arr['x']     = $UTCdate;
					$arr['y']     = $MaxPow;

					$annotations[0]['labels'][0]['point'] = $arr;
					$annotations[0]['labels'][0]['text']  = "$MaxPow W";
				}
			}
			$contalines--;
			$KWHD[$invt_num] = round((($KWHT[$contalines] - $KWHT[1]) * ${'CORRECTFACTOR' . $invt_num}), 3);
		} // skip older file
		$i++;
	} // multi

	sort($totstack);
	$data[0] = array(
		'name' => 'Avgtot',
		'data' => $totstack,
		'type' => 'areaspline'
	);

	$KWHDt = array_sum($KWHD);
	if ($KWHDt > 1) {
		$KWHDt = number_format($KWHDt, 1, $DPOINT, $THSEP);
	} else {
		$KWHDt = number_format($KWHDt, 3, $DPOINT, $THSEP);
	}

	if ($latestfile == $ystdayUTC) {
		$title = stripslashes("$lgYESTERDAYTITLE ($KWHDt kWh)");
	} else {
		$dday  = date($DATEFORMAT, $latestfile);
		$title = stripslashes("$dday ($KWHDt kWh)");
	}

	$annotations[0]['labelOptions']['backgroundColor'] = 'rgba(255,255,255,0.2)';
	$annotations[0]['labelOptions']['borderColor']     = '#4572A7';

	$jsonreturn = array(
		'data' => $data,
		'title' => $title,
		'annotations' => $annotations
	);
} else {
	$stack      = null;
	$data       = array(
		0 => array(
			'name' => 'Avgtot',
			'type' => 'areaspline',
			'data' => $stack
		)
	);
	$title      = stripslashes("$lgYESTERDAYTITLE (--- kWh)");
	$jsonreturn = array(
		'data' => $data,
		'title' => $title
	);
}

header("Content-type: application/json");
echo json_encode($jsonreturn);
?>
