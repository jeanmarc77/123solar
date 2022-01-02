<?php
/**
 * /srv/http/123solar/programs/programcomparison.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set('UTC');

if (!empty($_GET['invtnum_f']) && is_numeric($_GET['invtnum_f'])) {
	$invtnum_f = $_GET['invtnum_f'];
} else {
	$invtnum_f = 0;
}
include '../languages/' . $LANG . '.php';

if (!empty($_GET['whichmonth']) && is_numeric($_GET['whichmonth'])) {
	$whichmonth = $_GET['whichmonth'];
} else {
	$whichmonth = date('n');
}
if (!empty($_GET['whichyear']) && is_numeric($_GET['whichyear'])) {
	$whichyear = $_GET['whichyear'];
} else {
	$whichyear = date('Y');
}
if (!empty($_GET['comparemonth']) && is_numeric($_GET['comparemonth'])) {
	$comparemonth = $_GET['comparemonth'];
} else {
	$comparemonth = date('n');
}
if (!empty($_GET['compareyear']) && is_string($_GET['compareyear'])) {
	$compareyear = htmlspecialchars($_GET['compareyear'], ENT_QUOTES, "UTF-8");
} else {
	$compareyear = 'expected';
}


/**
 *
 * @param unknown $selectmonth
 * @param unknown $selectyear
 * @param unknown $invtnum
 * @return unknown
 */
function getvalues($selectmonth, $selectyear, $invtnum) {
	include '../config/config_main.php';
	if ($invtnum == 0) { //all
		$startinv = 1;
		$uptoinv  = $NUMINV;
	} else {
		$startinv = $invtnum;
		$uptoinv  = $invtnum;
	}

	for ($invt_num = $startinv; $invt_num <= $uptoinv; $invt_num++) { // Multi
		include "../config/config_invt$invt_num.php";

		$dir = '../data/invt' . $invt_num . '/production/';
		if (file_exists($dir . 'energy' . $selectyear . '.csv')) {
			$line_num = 1;
			$thisfile = $dir . 'energy' . $selectyear . '.csv';
			if ($handle = fopen("$thisfile", 'r')) {
				$i = 0;
				while ($line = fgetcsv($handle, 1000, ',')) {
					$year  = substr($line[0], 0, 4);
					$month = substr($line[0], 4, 2);
					$day   = substr($line[0], 6, 2);

					if ($month == $selectmonth || $selectmonth == 13) {
						$date1 = strtotime($year . '-' . $month . '-' . $day);
						$date1 *= 1000; // in ms
						$month                             = (int) ($month);
						$day                               = (int) ($day);
						$prod_day[$invt_num][$month][$day] = round(($line[1] * ${'CORRECTFACTOR' . $invt_num}), 1);
					}
				} // end of looping through the file
			}
		}
		if ($selectyear == date('Y') && ($selectmonth == date('n') || $selectmonth == 13)) { // Add today
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
				$prod_day[$invt_num][$month][$day] = round(($val_last * ${'CORRECTFACTOR' . $invt_num}), 1);
			}
		} // end of today
	} // end of multi

	$i         = 0;
	$cumu_prod = 0; // Cumulative
	if ($selectmonth == 13) { // all year
		$selectmonth_start = 1;
		$selectmonth_stop  = 12;
	} else {
		$selectmonth_start = $selectmonth;
		$selectmonth_stop  = $selectmonth;
	}

	for ($k = $selectmonth_start; $k <= $selectmonth_stop; $k++) {
		$daythatm = cal_days_in_month(CAL_GREGORIAN, $k, $selectyear);
		for ($j = 1; $j <= $daythatm; $j++) {
			$date1 = strtotime($selectyear . '-' . $k . '-' . $j);
			$date1 *= 1000;
			for ($invt_num = $startinv; $invt_num <= $uptoinv; $invt_num++) { // Multi
				if (!isset($prod_day[$invt_num][$k][$j])) {
					$prod_day[$invt_num][$k][$j] = 0; // Filling blanks dates
				}
				$cumu_prod += $prod_day[$invt_num][$k][$j];
			} // end of multi
			$stack[$i] = array(
				$date1,
				$cumu_prod
			);
			$i++;
		}
	} // k selected month

	return $stack;
} // enf of fnct getvalues

$datareturn = getvalues($whichmonth, $whichyear, $invtnum_f); // Call fnct

if ($compareyear == $whichyear && $comparemonth == $whichmonth) { //Same req
	$datareturn2 = $datareturn;
	$xaxe        = 0;
} else {
	if ($compareyear != 'expected') { // Compare with
		$datareturn2 = getvalues($comparemonth, $compareyear, $invtnum_f);
		$xaxe        = 1;
	} else { // Expected
		if ($invtnum_f == 0) { //all
			$startinv = 1;
			$uptoinv  = $NUMINV;
		} else {
			$startinv = $invtnum_f;
			$uptoinv  = $invtnum_f;
		}
		$compareyear = $lgEXPECTED; //name
		$prod_exp    = array();
		for ($i = 1; $i <= 12; $i++) {
			$prod_exp[$i] = 0;
		}
		for ($invt_num = $startinv; $invt_num <= $uptoinv; $invt_num++) { // Multi
			include "../config/config_invt$invt_num.php";
			$prod_exp[1] += ${'EXPECT1_' . $invt_num};
			$prod_exp[2] += ${'EXPECT2_' . $invt_num};
			$prod_exp[3] += ${'EXPECT3_' . $invt_num};
			$prod_exp[4] += ${'EXPECT4_' . $invt_num};
			$prod_exp[5] += ${'EXPECT5_' . $invt_num};
			$prod_exp[6] += ${'EXPECT6_' . $invt_num};
			$prod_exp[7] += ${'EXPECT7_' . $invt_num};
			$prod_exp[8] += ${'EXPECT8_' . $invt_num};
			$prod_exp[9] += ${'EXPECT9_' . $invt_num};
			$prod_exp[10] += ${'EXPECT10_' . $invt_num};
			$prod_exp[11] += ${'EXPECT11_' . $invt_num};
			$prod_exp[12] += ${'EXPECT12_' . $invt_num};
		} // end of multi

		if ($comparemonth == 13) { // all year
			$selectmonth_start = 1;
			$selectmonth_stop  = 12;
			$startdate         = strtotime($whichyear . '-01-01');
		} else {
			$selectmonth_start = $comparemonth;
			$selectmonth_stop  = $comparemonth;
			$startdate         = strtotime($whichyear . '-' . $comparemonth . '-01');
		}
		$startdate *= 1000;
		$cumu_prod2 = 0;
		$i          = 0;
		//settype($cumu_prod2, "integer");
		for ($k = $selectmonth_start; $k <= $selectmonth_stop; $k++) {
			$daythatm     = cal_days_in_month(CAL_GREGORIAN, $k, $whichyear);
			$prodinexpday = round(($prod_exp[$k] / $daythatm), 1);
			for ($j = 0; $j < $daythatm; $j++) {
				$cumu_prod2 += $prodinexpday;
				$datareturn2[$i] = array(
					$startdate,
					$cumu_prod2
				);
				$startdate += 86400000; //next day
				$i++;
			}
		} // k selected month

		if ($comparemonth == $whichmonth) {
			$xaxe = 0;
		} else {
			$xaxe = 1;
		}
	}

} // end of same req

$data = array(
	0 => array(
		'name' => "$lgSMONTH[$whichmonth] $whichyear",
		'type' => 'areaspline',
		'data' => $datareturn,
		'xAxis' => 0
	),
	1 => array(
		'name' => "$lgSMONTH[$comparemonth] $compareyear",
		'type' => 'spline',
		'data' => $datareturn2,
		'xAxis' => $xaxe
	)
);

header("Content-type: application/json");
echo json_encode($data);
?>
