<?php
/**
 * /srv/http/123solar/programs/programmultilive.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
include '../config/memory.php';
date_default_timezone_set($DTZ);

$GPTOT  = 0;
$nowUTC = strtotime(date('Ymd H:i:s'));

if (file_exists($LIVEMEMORY) && file_exists($MEMORY)) {
	$data     = file_get_contents($MEMORY);
	$memarray = json_decode($data, true);

	$data     = file_get_contents($LIVEMEMORY);
	$livememarray = json_decode($data, true);

	for ($invt_num = 1; $invt_num <= $NUMINV; $invt_num++) {
		include "../config/config_invt$invt_num.php";
		if (!isset($livememarray["SDTE$invt_num"])) {
			$livememarray["SDTE$invt_num"] = 0;
		}
		if (!isset($livememarray["G1P$invt_num"]) || $nowUTC - $livememarray["SDTE$invt_num"] > 30) {
			$livememarray["G1P$invt_num"] = 0;
		}
		if (!isset($livememarray["G2P$invt_num"]) || $nowUTC - $livememarray["SDTE$invt_num"] > 30) {
			$livememarray["G2P$invt_num"] = 0;
		}
		if (!isset($livememarray["G3P$invt_num"]) || $nowUTC - $livememarray["SDTE$invt_num"] > 30) {
			$livememarray["G3P$invt_num"] = 0;
		}
		$GPTOT += ($livememarray["G1P$invt_num"] + $livememarray["G2P$invt_num"] + $livememarray["G3P$invt_num"]) * ${'CORRECTFACTOR' . $invt_num};
	}

	if ($GPTOT > 1000) {
		$GPTOT = round($GPTOT, 0);
	} else {
		$GPTOT = round($GPTOT, 1);
	}

	if (!isset($memarray['pmotdmulti'])) {
		$memarray['pmotdmulti'] = 0;
	}
	if (!isset($memarray['pmotdtmulti'])) {
		$memarray['pmotdtmulti'] = 0;
	} else {
		$memarray['pmotdtmulti'] = date('H:i', $memarray['pmotdtmulti']);
	}

	$arr = array(
		'GPTOT' => floatval($GPTOT),
		'PMAXOTD' => floatval(round($memarray['pmotdmulti'], 0)),
		'PMAXOTDTIME' => ($memarray['pmotdtmulti'])
	);

	header("Content-type: application/json");
	echo json_encode($arr);
}
?>
