<?php
/**
 * /srv/http/123solar/programs/programdetailed.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set('UTC');

if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
}
include "../config/config_invt$invtnum.php";
include '../languages/' . $LANG . '.php';

$nbryaxis = 0;

if (!empty($_GET['date1']) && is_string($_GET['date1'])) {
	$date1 = htmlspecialchars($_GET['date1'], ENT_QUOTES, 'UTF-8');
} else {
	$date1 = FALSE;
}

$checkPROD     = htmlspecialchars($_GET['checkPROD'], ENT_QUOTES, 'UTF-8');
$checkPERF     = htmlspecialchars($_GET['checkPERF'], ENT_QUOTES, 'UTF-8');
$checkavgpower = htmlspecialchars($_GET['checkavgpower'], ENT_QUOTES, 'UTF-8');
$checkIV1      = htmlspecialchars($_GET['checkIV1'], ENT_QUOTES, 'UTF-8');
$checkIA1      = htmlspecialchars($_GET['checkIA1'], ENT_QUOTES, 'UTF-8');
$checkIP1      = htmlspecialchars($_GET['checkIP1'], ENT_QUOTES, 'UTF-8');
$checkPERF1    = htmlspecialchars($_GET['checkPERF1'], ENT_QUOTES, 'UTF-8');
$checkIV2      = htmlspecialchars($_GET['checkIV2'], ENT_QUOTES, 'UTF-8');
$checkIA2      = htmlspecialchars($_GET['checkIA2'], ENT_QUOTES, 'UTF-8');
$checkIP2      = htmlspecialchars($_GET['checkIP2'], ENT_QUOTES, 'UTF-8');
$checkPERF2    = htmlspecialchars($_GET['checkPERF2'], ENT_QUOTES, 'UTF-8');
$checkIV3      = htmlspecialchars($_GET['checkIV3'], ENT_QUOTES, 'UTF-8');
$checkIA3      = htmlspecialchars($_GET['checkIA3'], ENT_QUOTES, 'UTF-8');
$checkIP3      = htmlspecialchars($_GET['checkIP3'], ENT_QUOTES, 'UTF-8');
$checkPERF3    = htmlspecialchars($_GET['checkPERF3'], ENT_QUOTES, 'UTF-8');
$checkIV4      = htmlspecialchars($_GET['checkIV4'], ENT_QUOTES, 'UTF-8');
$checkIA4      = htmlspecialchars($_GET['checkIA4'], ENT_QUOTES, 'UTF-8');
$checkIP4      = htmlspecialchars($_GET['checkIP4'], ENT_QUOTES, 'UTF-8');
$checkPERF4    = htmlspecialchars($_GET['checkPERF4'], ENT_QUOTES, 'UTF-8');
$checkG1V      = htmlspecialchars($_GET['checkG1V'], ENT_QUOTES, 'UTF-8');
$checkG1A      = htmlspecialchars($_GET['checkG1A'], ENT_QUOTES, 'UTF-8');
$checkG1P      = htmlspecialchars($_GET['checkG1P'], ENT_QUOTES, 'UTF-8');
$checkG2V      = htmlspecialchars($_GET['checkG2V'], ENT_QUOTES, 'UTF-8');
$checkG2A      = htmlspecialchars($_GET['checkG2A'], ENT_QUOTES, 'UTF-8');
$checkG2P      = htmlspecialchars($_GET['checkG2P'], ENT_QUOTES, 'UTF-8');
$checkG3V      = htmlspecialchars($_GET['checkG3V'], ENT_QUOTES, 'UTF-8');
$checkG3A      = htmlspecialchars($_GET['checkG3A'], ENT_QUOTES, 'UTF-8');
$checkG3P      = htmlspecialchars($_GET['checkG3P'], ENT_QUOTES, 'UTF-8');
$checkFRQ      = htmlspecialchars($_GET['checkFRQ'], ENT_QUOTES, 'UTF-8');
$checkEFF      = htmlspecialchars($_GET['checkEFF'], ENT_QUOTES, 'UTF-8');
$checkINVT     = htmlspecialchars($_GET['checkINVT'], ENT_QUOTES, 'UTF-8');
$checkBOOT     = htmlspecialchars($_GET['checkBOOT'], ENT_QUOTES, 'UTF-8');
$checkSR       = htmlspecialchars($_GET['checkSR'], ENT_QUOTES, 'UTF-8');

$log = '../data/invt' . $invtnum . '/csv/' . $date1;
if (file_exists($log)) {
	$year  = substr($date1, 0, 4);
	$month = substr($date1, 4, 2);
	$day   = substr($date1, 6, 2);

	$line_num = 1;
	$handle   = fopen($log, 'r');
	while ($line = fgetcsv($handle, 1000, ',')) {
		if ($line_num > 1) {
			$SDTE[$line_num] = $line[0];
			$I1V             = $line[1];
			$I1A             = $line[2];
			$I1P             = $line[3];
			$I2V             = $line[4];
			$I2A             = $line[5];
			$I2P             = $line[6];
			$I3V             = $line[7];
			$I3A             = $line[8];
			$I3P             = $line[9];
			$I4V             = $line[10];
			$I4A             = $line[11];
			$I4P             = $line[12];
			$G1V             = $line[13];
			$G1A             = $line[14];
			$G1P             = $line[15];
			$G2V             = $line[16];
			$G2A             = $line[17];
			$G2P             = $line[18];
			$G3V             = $line[19];
			$G3A             = $line[20];
			$G3P             = $line[21];
			$FRQ             = $line[22];
			$EFF             = $line[23];
			$INVT            = $line[24];
			$BOOT            = $line[25];
			$SSR             = $line[26];
			$KWHT[$line_num] = $line[27];

			$hour    = substr($SDTE[$line_num], 0, 2);
			$minute  = substr($SDTE[$line_num], 3, 2);
			$seconde = substr($SDTE[$line_num], 6, 2);

			$epochdate = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $seconde);
			$UTCdate   = $epochdate * 1000;

			$countstack = 0; // Count the numbers of Yaxis

			if ($checkavgpower == 'on') {
				if ($line_num > 2) {
					$pastline_num = $line_num - 1;
					$pasthour     = substr($SDTE[$pastline_num], 0, 2);
					$pastminute   = substr($SDTE[$pastline_num], 3, 2);
					$pastseconde  = substr($SDTE[$pastline_num], 6, 2);
					//calculate average Power between 2 pooling
					$diffUTCdate  = strtotime($year . '-' . $month . '-' . $day . ' ' . $pasthour . ':' . $pastminute . ':' . $pastseconde);
					$diffTime     = $epochdate - $diffUTCdate;
					$AvgPOW       = round((((($KWHT[$line_num] - $KWHT[$pastline_num]) * 3600) / $diffTime) * 1000), 1);
				} else {
					$AvgPOW = 0;
				}
				$stackname[$countstack]        = $lgDPOWERAVG;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					$AvgPOW
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
			}

			for ($i = 1; $i < 5; $i++) {
				if (${'checkIP' . $i } == 'on') {
					$stackname[$countstack]        = "$lgARRAY $i $lgDPOWER";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'I' . $i . 'P'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}

			for ($i = 1; $i < 4; $i++) {
				if (${'checkG' . $i . 'P'} == 'on') {
					$stackname[$countstack]        = "$lgGRID $lgDPOWER $lgDPHASE $i";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'G' . $i . 'P'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}

			if ($checkavgpower == 'on' || $checkIP1 == 'on' || $checkIP2 == 'on' || $checkIP3 == 'on' || $checkIP4 == 'on' || $checkG1P == 'on' || $checkG2P == 'on' || $checkG3P == 'on') {
				$nbryaxis++;
			}

			if ($checkPROD == 'on') {
				if ($line_num > 2) {
					$PROD = round((($KWHT[$line_num] - $KWHT[2]) * ${'CORRECTFACTOR' . $invtnum}), 1);
				} else {
					$PROD = 0;
				}
				$stackname[$countstack]        = $lgPROD;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					$PROD
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'areaspline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
				$nbryaxis++;
			}

			if ($checkPERF == 'on') {
				if ($line_num > 2) {
					$pastline_num = $line_num - 1;
					$pasthour     = substr($SDTE[$pastline_num], 0, 2);
					$pastminute   = substr($SDTE[$pastline_num], 3, 2);
					$pastseconde  = substr($SDTE[$pastline_num], 6, 2);
					$diffUTCdate  = strtotime($year . '-' . $month . '-' . $day . ' ' . $pasthour . ':' . $pastminute . ':' . $pastseconde);
					$diffTime     = $epochdate - $diffUTCdate;
					$PERF         = round(((($KWHT[$line_num] - $KWHT[$pastline_num]) * 3600 * 1000 * 1000) / ($diffTime * ${'PLANT_POWER' . $invtnum})), 1); //
				} else {
					$PERF = 0;
				}
				$stackname[$countstack]        = $lgDPERF;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					$PERF
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
			}

			for ($i = 1; $i < 5; $i++) {
				if (${'checkPERF' . $i} == 'on') {
					if (${'ARRAY' . $i . "_POWER$invtnum"} > 0) {
						$PERF                          = round((${'I' . $i . 'P'} / ${'ARRAY' . $i . "_POWER$invtnum"}) * 1000, 1);
					} else {
						$PERF = 0;
					}
					$stackname[$countstack]        = "$lgARRAY $i $lgDPERF";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						$PERF
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}

			if ($checkPERF == 'on' || $checkPERF1 == 'on' || $checkPERF2 == 'on' || $checkPERF3 == 'on' || $checkPERF4 == 'on') {
				$nbryaxis++;
			}

			for ($i = 1; $i < 5; $i++) {
				if (${'checkIV' . $i} == 'on') {
					$stackname[$countstack]        = "$lgARRAY $i $lgDVOLTAGE";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'I' . $i . 'V'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}

			}
			for ($i = 1; $i < 4; $i++) {
				if (${'checkG' . $i . 'V'} == 'on') {
					$stackname[$countstack]        = "$lgGRID $lgDVOLTAGE $lgDPHASE $i";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'G' . $i . 'V'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}
			if ($checkIV1 == 'on' || $checkIV2 == 'on' || $checkIV3 == 'on' || $checkIV4 == 'on' || $checkG1V == 'on' || $checkG2V == 'on' || $checkG3V == 'on') {
				$nbryaxis++;
			}

			for ($i = 1; $i < 5; $i++) {
				if (${'checkIA' . $i } == 'on') {
					$stackname[$countstack]        = "$lgARRAY $i $lgDCURRENT";
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'I' . $i . 'A'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}
			for ($i = 1; $i < 4; $i++) {
				if (${'checkG' . $i . 'A'} == 'on') {
					$stackname[$countstack] = "$lgGRID $lgDCURRENT $lgDPHASE $i";
					;
					$stack[$countstack][$line_num] = array(
						$UTCdate,
						round(${'G' . $i . 'A'}, 2)
					);
					$yaxis[$countstack]            = $nbryaxis;
					$type[$countstack]             = 'spline';
					$dashStyle[$countstack]        = 'Solid';
					$countstack++;
				}
			}
			if ($checkIA1 == 'on' || $checkIA2 == 'on' || $checkIA3 == 'on' || $checkIA4 == 'on' || $checkG1A == 'on' || $checkG2A == 'on' || $checkG3A == 'on') {
				$nbryaxis++;
			}

			if ($checkFRQ == 'on') {
				$stackname[$countstack]        = $lgDFREQ;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					round($FRQ, 2)
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$nbryaxis++;
				$countstack++;
			}
			if ($checkEFF == 'on') {
				$stackname[$countstack]        = $lgDEFF;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					round($EFF, 1)
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$nbryaxis++;
				$countstack++;
			}

			if ($checkINVT == 'on') {
				$stackname[$countstack]        = "$lgDINVERTER $lgDTEMP";
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					round($INVT, 1)
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
			}
			if ($checkBOOT == 'on') {
				$stackname[$countstack]        = "$lgDBOOSTER $lgDTEMP";
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					round($BOOT, 1)
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
			}

			if ($checkSR == 'on') {
				$stackname[$countstack]        = $lgSENSOR;
				$stack[$countstack][$line_num] = array(
					$UTCdate,
					round($SSR, 2)
				);
				$yaxis[$countstack]            = $nbryaxis;
				$type[$countstack]             = 'spline';
				$dashStyle[$countstack]        = 'Solid';
				$countstack++;
			}

			$nbryaxis = 0;
		}
		$line_num++;
	} // end of looping through the file
	fclose($handle);

	$line_num--;
	if (!isset($KWHT[2])) {
		$KWHT[2] = 0;
	}
	if (!isset($KWHT[$line_num])) {
		$KWHT[$line_num] = 0;
	}
	$KWHD = round((($KWHT[$line_num] - $KWHT[2]) * ${'CORRECTFACTOR' . $invtnum}), 2);
	$KWHD = number_format($KWHD, 2, $DPOINT, $THSEP);

	if ($invtnum == 0 || $NUMINV == 1) {
		$parttitle = '';
	} else {
		$parttitle = "$lgINVT$invtnum - ";
	}

	$dday = date($DATEFORMAT, mktime(0, 0, 0, $month, $day, $year));

	settype($titledate, 'string');
	$title = "$parttitle $lgDETAILEDOFTITLE $dday $titledate ($KWHD kWh)";

	$data = array();
	// Return datas via json
	for ($i = 0; $i < $countstack; $i++) {
		sort($stack[$i]);
		$data[$i] = array(
			'name' => $stackname[$i],
			'data' => $stack[$i],
			'yAxis' => $yaxis[$i],
			'type' => $type[$i],
			'dashStyle' => $dashStyle[$i]
		);
	}

	$jsonreturn = array(
		'data' => $data,
		'title' => $title
	);
} else {
	$jsonreturn = array(
		'data' => null,
		'title' => 'No Data'
	);

}
header("Content-type: application/json");
echo json_encode($jsonreturn);
?>
