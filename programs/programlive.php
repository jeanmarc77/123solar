<?php
/**
 * /srv/http/123solar/programs/programlive.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/config_main.php';
include '../config/memory.php';
date_default_timezone_set($DTZ);

if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
}
include "../config/config_invt$invtnum.php";

$nowUTC = strtotime(date('Ymd H:i:s'));
$dday   = date($DATEFORMAT . ' H:i:s', $nowUTC);

if (file_exists($LIVEMEMORY) && file_exists($MEMORY)) {
	$data     = file_get_contents($MEMORY);
	$memarray = json_decode($data, true);

	$data     = file_get_contents($LIVEMEMORY);
	$livememarray = json_decode($data, true);

	if (!isset($livememarray["SDTE$invtnum"])) {
		$livememarray["SDTE$invtnum"] = 0;
	}
	if (!isset($livememarray["G1P$invtnum"])) {
		$livememarray["G1P$invtnum"] = 0;
	}
	if (!isset($livememarray["G2P$invtnum"])) {
		$livememarray["G2P$invtnum"] = 0;
	}
	if (!isset($livememarray["G3P$invtnum"])) {
		$livememarray["G3P$invtnum"] = 0;
	}
	if (!isset($livememarray["G1V$invtnum"])) {
		$livememarray["G1V$invtnum"] = 0;
	}
	if (!isset($livememarray["G2V$invtnum"])) {
		$livememarray["G2V$invtnum"] = 0;
	}
	if (!isset($livememarray["G3V$invtnum"])) {
		$livememarray["G3V$invtnum"] = 0;
	}
	if (!isset($livememarray["G1A$invtnum"])) {
		$livememarray["G1A$invtnum"] = 0;
	}
	if (!isset($livememarray["G2A$invtnum"])) {
		$livememarray["G2A$invtnum"] = 0;
	}
	if (!isset($livememarray["G3A$invtnum"])) {
		$livememarray["G3A$invtnum"] = 0;
	}
	if (!isset($livememarray["I1V$invtnum"])) {
		$livememarray["I1V$invtnum"] = 0;
	}
	if (!isset($livememarray["I1A$invtnum"])) {
		$livememarray["I1A$invtnum"] = 0;
	}
	if (!isset($livememarray["I1P$invtnum"])) {
		$livememarray["I1P$invtnum"] = 0;
	}
	if (!isset($livememarray["I2V$invtnum"])) {
		$livememarray["I2V$invtnum"] = 0;
	}
	if (!isset($livememarray["I2A$invtnum"])) {
		$livememarray["I2A$invtnum"] = 0;
	}
	if (!isset($livememarray["I2P$invtnum"])) {
		$livememarray["I2P$invtnum"] = 0;
	}
	if (!isset($livememarray["I3V$invtnum"])) {
		$livememarray["I3V$invtnum"] = 0;
	}
	if (!isset($livememarray["I3A$invtnum"])) {
		$livememarray["I3A$invtnum"] = 0;
	}
	if (!isset($livememarray["I3P$invtnum"])) {
		$livememarray["I3P$invtnum"] = 0;
	}
	if (!isset($livememarray["I4V$invtnum"])) {
		$livememarray["I4V$invtnum"] = 0;
	}
	if (!isset($livememarray["I4A$invtnum"])) {
		$livememarray["I4A$invtnum"] = 0;
	}
	if (!isset($livememarray["I4P$invtnum"])) {
		$livememarray["I4P$invtnum"] = 0;
	}

	if (!isset($livememarray["FRQ$invtnum"])) {
		$livememarray["FRQ$invtnum"] = 0;
	}
	if (!isset($livememarray["EFF$invtnum"])) {
		$livememarray["EFF$invtnum"] = 0;
	}
	if (!isset($livememarray["INVT$invtnum"])) {
		$livememarray["INVT$invtnum"] = 0;
	}
	if (!isset($livememarray["BOOT$invtnum"])) {
		$livememarray["BOOT$invtnum"] = 0;
	}
	if (!isset($livememarray["SSR$invtnum"])) {
		$livememarray["SSR$invtnum"] = 0;
	}
	if (!isset($livememarray["KWHT$invtnum"])) {
		$livememarray["KWHT$invtnum"] = 0;
	}
	if (!isset($memarray["pmotd$invtnum"])) {
		$memarray["pmotd$invtnum"] = 0;
	}
	if (!isset($memarray["pmotdt$invtnum"])) {
		$memarray["pmotdt$invtnum"] = 0;
	}
	if (!isset($memarray["AWt$invtnum"])) {
		$memarray["AWt$invtnum"] = 0;
	}
	if (!isset($memarray["AWriso$invtnum"])) {
		$memarray["AWriso$invtnum"] = 0;
	}
	if (!isset($memarray["AWileak$invtnum"])) {
		$memarray["AWileak$invtnum"] = 0;
	}

	// AC
	if ($livememarray["G1P$invtnum"] > 1000) { // Round power > 1000W
		$livememarray["G1P$invtnum"] = round($livememarray["G1P$invtnum"], 0);
	} else {
		$livememarray["G1P$invtnum"] = round($livememarray["G1P$invtnum"], 1);
	}
	if ($livememarray["G2P$invtnum"] > 1000) {
		$livememarray["G2P$invtnum"] = round($livememarray["G2P$invtnum"], 0);
	} else {
		$livememarray["G2P$invtnum"] = round($livememarray["G2P$invtnum"], 1);
	}
	if ($livememarray["G3P$invtnum"] > 1000) {
		$livememarray["G3P$invtnum"] = round($livememarray["G3P$invtnum"], 0);
	} else {
		$livememarray["G3P$invtnum"] = round($livememarray["G3P$invtnum"], 1);
	}
	// DC
	if ($livememarray["I1P$invtnum"] > 1000) {
		$livememarray["I1P$invtnum"] = round($livememarray["I1P$invtnum"], 0);
	} else {
		$livememarray["I1P$invtnum"] = round($livememarray["I1P$invtnum"], 1);
	}
	if ($livememarray["I2P$invtnum"] > 1000) {
		$livememarray["I2P$invtnum"] = round($livememarray["I2P$invtnum"], 0);
	} else {
		$livememarray["I2P$invtnum"] = round($livememarray["I2P$invtnum"], 1);
	}
	if ($livememarray["I3P$invtnum"] > 1000) {
		$livememarray["I3P$invtnum"] = round($livememarray["I3P$invtnum"], 0);
	} else {
		$livememarray["I3P$invtnum"] = round($livememarray["I3P$invtnum"], 1);
	}
	if ($livememarray["I4P$invtnum"] > 1000) {
		$livememarray["I4P$invtnum"] = round($livememarray["I4P$invtnum"], 0);
	} else {
		$livememarray["I4P$invtnum"] = round($livememarray["I4P$invtnum"], 1);
	}

	//Aw peak pmotd
	$awdate_t                   = date('H:i', $memarray["AWt$invtnum"]);
	$memarray["pmotdt$invtnum"] = date('H:i', $memarray["pmotdt$invtnum"]);

	if ($nowUTC - $livememarray["SDTE$invtnum"] < 30) {
		$arr = array(
			'SDTE' => $livememarray["SDTE$invtnum"] * 1000,
			'I1V' => floatval(round($livememarray["I1V$invtnum"], 1)),
			'I1A' => floatval(round($livememarray["I1A$invtnum"], 1)),
			'I1P' => floatval($livememarray["I1P$invtnum"]),
			'I2V' => floatval(round($livememarray["I2V$invtnum"], 1)),
			'I2A' => floatval(round($livememarray["I2A$invtnum"], 1)),
			'I2P' => floatval($livememarray["I2P$invtnum"]),
			'I3V' => floatval(round($livememarray["I3V$invtnum"], 1)),
			'I3A' => floatval(round($livememarray["I3A$invtnum"], 1)),
			'I3P' => floatval($livememarray["I3P$invtnum"]),
			'I4V' => floatval(round($livememarray["I4V$invtnum"], 1)),
			'I4A' => floatval(round($livememarray["I4A$invtnum"], 1)),
			'I4P' => floatval($livememarray["I4P$invtnum"]),
			'G1V' => floatval(round($livememarray["G1V$invtnum"], 1)),
			'G1A' => floatval(round($livememarray["G1A$invtnum"], 1)),
			'G1P' => floatval($livememarray["G1P$invtnum"]),
			'G2V' => floatval(round($livememarray["G2V$invtnum"], 1)),
			'G2A' => floatval(round($livememarray["G2A$invtnum"], 1)),
			'G2P' => floatval($livememarray["G2P$invtnum"]),
			'G3V' => floatval(round($livememarray["G3V$invtnum"], 1)),
			'G3A' => floatval(round($livememarray["G3A$invtnum"], 1)),
			'G3P' => floatval($livememarray["G3P$invtnum"]),
			'FRQ' => floatval(round($livememarray["FRQ$invtnum"], 1)),
			'EFF' => floatval(round($livememarray["EFF$invtnum"], 1)),
			'INVT' => floatval(round($livememarray["INVT$invtnum"], 1)),
			'BOOT' => floatval(round($livememarray["BOOT$invtnum"], 1)),
			'SSR' => floatval(round($livememarray["SSR$invtnum"], 1)),
			'KWHT' => floatval($livememarray["KWHT$invtnum"]),
			'PMAXOTD' => floatval(round($memarray["pmotd$invtnum"], 0)),
			'PMAXOTDTIME' => ($memarray["pmotdt$invtnum"]),
			'timestamp' => $dday,
			'riso' => floatval(round($memarray["AWriso$invtnum"], 2)),
			'ileak' => floatval(round($memarray["AWileak$invtnum"], 1)),
			'awdate' => $awdate_t
		);
	} else { // Too old data
		$arr = array(
			'SDTE' => $nowUTC * 1000,
			'I1V' => 0,
			'I1A' => 0,
			'I1P' => 0,
			'I2V' => 0,
			'I2A' => 0,
			'I2P' => 0,
			'I3V' => 0,
			'I3A' => 0,
			'I3P' => 0,
			'I4V' => 0,
			'I4A' => 0,
			'I4P' => 0,
			'G1V' => 0,
			'G1A' => 0,
			'G1P' => 0,
			'G2V' => 0,
			'G2A' => 0,
			'G2P' => 0,
			'G3V' => 0,
			'G3A' => 0,
			'G3P' => 0,
			'FRQ' => 0,
			'EFF' => 0,
			'INVT' => 0,
			'BOOT' => 0,
			'SSR' => 0,
			'PMAXOTD' => 0,
			'PMAXOTDTIME' => 0,
			'timestamp' => $dday,
			'riso' => 0,
			'ileak' => 0,
			'awdate' => '--:--'
		);
	}
	header("Content-type: application/json");
	echo json_encode($arr);
}
?>
