<?php
/**
 * /srv/http/123solar/programs/programproduction.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set('UTC');
include '../languages/' . $LANG . '.php';

if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
	include "../config/config_invt$invtnum.php";
	$strt    = $invtnum;
	$upto    = $invtnum;
	$INVNAME = ${'INVNAME' . $invtnum};
} else {
	$strt = 1;
	if ($NUMINV > 1) {
		$upto = $NUMINV;
	} else {
		$upto = 1;
	}
}

$list     = array();
$yearlist = array();
for ($invtnum = $strt; $invtnum <= $upto; $invtnum++) {
	include "../config/config_invt$invtnum.php";
	$list     = glob('../data/invt' . $invtnum . '/production/' . "energy*.csv");
	$yearscnt = count($list);
	for ($i = 0; $i < $yearscnt; $i++) {
		$year = (int) substr($list[$i], -8, 4);
		if (!in_array($year, $yearlist)) {
			array_push($yearlist, $year);
		}
	}
}
$yearscnt = count($yearlist);

if (empty($_GET['invtnum'])) {
	if ($NUMINV > 1) {
		$INVNAME = "$lgALL";
	} else {
		$INVNAME = $INVNAME1;
	}
}

$topseries[0] = array(
	'name' => "$INVNAME",
	'title' => "$INVNAME",
	'keys' => array(
		'x',
		'y',
		'drilldown',
		'title'
	)
);

$ncnt = 0;
for ($i = 0; $i < $yearscnt; $i++) {
	for ($invtnum = $strt; $invtnum <= $upto; $invtnum++) {
		$year = $yearlist[$i];
		$dir  = '../data/invt' . $invtnum . '/production/';
		if (file_exists($dir . 'energy' . $year . '.csv')) {
			$thefile    = file($dir . 'energy' . $year . '.csv');
			$contalines = count($thefile);
			$year       = (int) $year;

			for ($line_num = 0; $line_num < $contalines; $line_num++) {
				$array = preg_split("/,/", $thefile[$line_num]);
				$month = substr($array[0], 4, 2);
				$day   = substr($array[0], 6, 2);
				$month = (int) ($month);
				$day   = (int) ($day);
				if (!isset($prod_day[$year][$month][$day])) {
					$prod_day[$year][$month][$day] = 0;
				}
				$prod_day[$year][$month][$day] += (float) (floatval($array[1]) * ${'CORRECTFACTOR' . $invtnum});
			} // end of looping through the file
			if ($year == date('Y')) { // Add today
				$dir    = '../data/invt' . $invtnum . '/csv/';
				$output = glob($dir . '*.csv');
				rsort($output);
				if (isset($output[0])) {
					$file       = file($output[0]);
					$month      = (int) substr($output[0], -8, 2);
					$day        = (int) substr($output[0], -6, 2);
					$contalines = count($file);
					$prevarray  = preg_split('/,/', $file[1]);
					$linearray  = preg_split('/,/', $file[$contalines - 1]);
					$val_first  = (float) $prevarray[27];
					$val_last   = (float) $linearray[27];
					if (!empty($val_first) && !empty($val_last)) {
						if ($val_first <= $val_last) {
							$val_last -= $val_first;
						} else { // counter pass over
							$val_last += ${'PASSO' . $invtnum} - $val_first;
						}
					} else {
						$val_last = 0;
					}
					if (!isset($prod_day[$year][$month][$day])) {
						$prod_day[$year][$month][$day] = 0;
					}
					$prod_day[$year][$month][$day] += (float) ($val_last * ${'CORRECTFACTOR' . $invtnum});
				}
			} // end of today
		}
	} // each invt

	$prod_y = 0;
	for ($h = 1; $h <= 12; $h++) { // Fill blanks dates and drilldowndays
		$daythatm = cal_days_in_month(CAL_GREGORIAN, $h, $year);
		$day      = 0;
		for ($j = 1; $j <= $daythatm; $j++) {
			$epochdate = strtotime($h . '/' . $j . '/' . $year) * 1000;
			if (!isset($prod_day[$year][$h][$j])) {
				$prod_day[$year][$h][$j]        = 0;
				$drilldowndays[$year][$h][$day] = array(
					$epochdate,
					0
				);
			} else {
				$drilldowndays[$year][$h][$day] = array(
					$epochdate,
					$prod_day[$year][$h][$j]
				);
			}
			$day++;
		}
		$prod_y += array_sum($prod_day[$year][$h]);
	}

	$title = number_format($prod_y, 0, $DPOINT, $THSEP);
	$title = "$INVNAME $year: $title kWh";

	$epochdate                = strtotime('1/1/' . $year) * 1000;
	$topseries[0]['data'][$i] = array(
		$epochdate,
		$prod_y,
		"y$year",
		$title
	);

	$month = 0;
	for ($h = 1; $h <= 12; $h++) { // drilldownmonths
		if ($h == 1) {
			$return['series'][$ncnt]['id']   = 'y' . $year; //y2015
			$return['series'][$ncnt]['name'] = "$INVNAME $year";
			$thisy                           = $ncnt;
			$ncnt++;
		}

		$prod_m = round(array_sum($prod_day[$year][$h]), 1);
		$title  = "$INVNAME $lgMONTH[$h] $year: ";
		$title .= number_format($prod_m, 1, $DPOINT, $THSEP);
		$title .= ' kWh';

		$epochdate                      = strtotime($h . '/1/' . $year) * 1000;
		$drilldownmonths[$year][$month] = array(
			$epochdate,
			$prod_m,
			"m$year$h",
			$title
		);

		$return['series'][$ncnt]['id']   = 'm' . $year . $h; //m201508
		$return['series'][$ncnt]['name'] = "$lgSMONTH[$h] $year";
		$return['series'][$ncnt]['data'] = $drilldowndays[$year][$h];
		$return['series'][$ncnt]['keys'] = array(
			'x',
			'y',
			'drilldown'
		);
		$ncnt++;
		$month++;
	} // each month

	$return['series'][$thisy]['data'] = $drilldownmonths[$year];
	$return['series'][$thisy]['keys'] = array(
		'x',
		'y',
		'drilldown',
		'title'
	);
} // each year file

$jsonreturn = array(
	'series' => $topseries,
	'drilldown' => $return
);

header("Content-type: application/json");
echo json_encode($jsonreturn);
?>
