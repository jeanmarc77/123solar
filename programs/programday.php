<?php
/**
 * /srv/http/123solar/programs/programday.php
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

$latestfile = null;
for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) { // Check files dates
	$fileUTCdate[$invt_num] = 0;
	$dir                    = '../data/invt' . $invt_num . '/csv/';
	$output                 = glob($dir . '*.csv');
	sort($output);
	$cnt = count($output);
	if ($cnt > 0) {
		$year                   = substr($output[$cnt - 1], -12, 4);
		$month                  = substr($output[$cnt - 1], -8, 2);
		$day                    = substr($output[$cnt - 1], -6, 2);
		$fileUTCdate[$invt_num] = strtotime($year . '-' . $month . '-' . $day);
		if ($fileUTCdate[$invt_num] > $latestfile) {
			$latestfile     = $fileUTCdate[$invt_num];
			$latestfilename = substr($output[$cnt - 1], -12);
		}
	}
}

if (isset($latestfile)) {
	$year     = date('Y', $latestfile);
	$month    = date('m', $latestfile);
	$day      = date('d', $latestfile);
	$todayUTC = strtotime(date("Ymd"));

	date_default_timezone_set($DTZ);
	$sun_info = date_sun_info(strtotime("$year-$month-$day"), $LATITUDE, $LONGITUDE);
	$subtitle = "$lgSUNRISE " . date("H:i", $sun_info['sunrise']) . " - $lgTRANSIT " . date("H:i", $sun_info['transit']) . " - $lgSUNSET " . date("H:i", $sun_info['sunset']);
	$sunset   = date("Ymd H:i", $sun_info['sunset']);
	date_default_timezone_set('UTC');
	$sunset = strtotime($sunset);

	$lastsample = 0;
	$MaxPow     = 0;
	for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) { // multi
		$AvgPOW[$invt_num] = 0;
		$dir               = '../data/invt' . $invt_num . '/csv/';
		$filename          = $dir . $latestfilename;
		if (file_exists($filename)) { // skip older files
			include "../config/config_invt$invt_num.php";
			$line_num = 1;
			if ($handle = fopen($filename, 'r')) {
				while ($line = fgetcsv($handle, 1000, ',')) {
					if ($line_num > 1) {
						$SDTE[$line_num] = $line[0];
						$KWHT[$line_num] = floatval($line[27]);

						if ($line_num == 2) {
							$pastline_num = 2;
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
							$AvgPOW[$invt_num] = round((((($KWHT[$line_num] - $KWHT[$pastline_num]) * 3600) / $diffTime) * 1000), 1);
						} else {
							$AvgPOW[$invt_num] = 0;
						}

						$UTCdate = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute); // minute precision !
						$UTCdate *= 1000;
						$stack[$invt_num][$line_num] = array(
							$UTCdate,
							$AvgPOW[$invt_num]
						);
						if (!isset($totAvgPOW[$hour . $minute])) {
							$totAvgPOW[$hour . $minute] = 0;
						}
						$totAvgPOW[$hour . $minute] += $AvgPOW[$invt_num];
						if ($totAvgPOW[$hour . $minute] >= $MaxPow) { // Annotation max
							$MaxTime      = $UTCdate;
							$MaxPow       = $totAvgPOW[$hour . $minute];
							$arr['xAxis'] = 0;
							$arr['yAxis'] = 0;
							$arr['x']     = $UTCdate;
							$arr['y']     = $MaxPow;
							$annotations[0]['labels'][0]['point'] = $arr;
							$annotations[0]['labels'][0]['text']  = "$MaxPow W";
						}
					}
					$line_num++;
				}
			}
			fclose($handle);
			$line_num--;
			$KWHD[$invt_num] = round((($KWHT[$line_num] - $KWHT[2]) * ${'CORRECTFACTOR' . $invt_num}), 3);

			$lastsampledate[$invt_num] = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute);
			if ($lastsampledate[$invt_num] > $lastsample) {
				$lastsample = $lastsampledate[$invt_num];
			}
			if ($lastsampledate[$invt_num] < $sunset - 600) {
				$line_num++;
				$stack[$invt_num][$line_num] = array(
					($lastsampledate[$invt_num] * 1000) + 1,
					0
				);
				$line_num++;
				$n                           = 300 * round($sunset / 300);
				$stack[$invt_num][$line_num] = array(
					$n * 1000,
					0
				);
			}

		} else { // skip older file
			$KWHD[$invt_num] = 0;
			$lastsampledate[$invt_num] = null;
		}
		if (isset($stack[$invt_num])) {
			sort($stack[$invt_num]);
		}
	} // multi

	$totAvgPOW = 0;
	$KWHDTOT   = 0;

	for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) {
		if ($fileUTCdate[$invt_num] == $latestfile) {
			$KWHDTOT += $KWHD[$invt_num];
		}
		if ($lastsample == $lastsampledate[$invt_num]) {
			$totAvgPOW += $AvgPOW[$invt_num]; //last sample
		}
	}

	$LastTime = $lastsample * 1000;

	if ($LastTime != $MaxTime && $totAvgPOW!=0) { // Annotation latest value
		$arr['xAxis'] = 0;
		$arr['yAxis'] = 0;
		$arr['x']     = $LastTime;
		$arr['y']     = $totAvgPOW;
		$annotations[0]['labels'][1]['point'] = $arr;
		$annotations[0]['labels'][1]['text']  = "$totAvgPOW W";
	}

	$KWHDt = array_sum($KWHD);
	if ($KWHDt > 1) {
		$KWHDt = number_format($KWHDt, 1, $DPOINT, $THSEP);
	} else {
		$KWHDt = number_format($KWHDt, 3, $DPOINT, $THSEP);
	}

	if ($latestfile == $todayUTC) {
		$title = stripslashes("$lgTODAYTITLE ($KWHDt kWh)");
	} elseif ($latestfile == strtotime(date("Ymd", strtotime("-1 day")))) {
		$title = stripslashes("$lgYESTERDAYTITLE ($KWHDt kWh)");
	} else {
		$dday  = date($DATEFORMAT, $latestfile);
		$title = stripslashes("$dday ($KWHDt kWh)");
	}

	$m = 0;
	for ($invt_num = $strto; $invt_num <= $upto; $invt_num++) {
		if ($fileUTCdate[$invt_num] == $latestfile) { // skip older files
			$data[$m] = array(
				'name' => "${'INVNAME'.$invt_num}",
				'data' => $stack[$invt_num],
				'type' => 'areaspline'
			);
		} else {
			$data[$m] = array(
				'name' => "${'INVNAME'.$invt_num}",
				'data' => array(
					array(
						$LastTime,
						0
					)
				),
				'type' => 'areaspline'
			);
		}
		$m++;
	}

	$annotations[0]['labelOptions']['backgroundColor'] = 'rgba(255,255,255,0.2)';
	$annotations[0]['labelOptions']['borderColor']     = '#4572A7';

	$jsonreturn = array(
		'data' => $data,
		'title' => $title,
		'subtitle' => $subtitle,
		'annotations' => $annotations
	);

} else {
	$data[0]    = array(
		'name' => "${'INVNAME'.$invt_num}",
		'data' => null,
		'type' => 'areaspline'
	);
	$jsonreturn = array(
		'data' => $data,
		'title' => stripslashes("$lgTODAYTITLE (--- kWh)"),
		'subtitle' => 'No data'
	);
}
header("Content-type: application/json");
echo json_encode($jsonreturn);
?>
