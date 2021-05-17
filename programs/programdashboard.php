<?php
/**
 * /srv/http/123solar/programs/programdashboard.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include '../config/memory.php';
if (!empty($_GET['invtnum']) && is_numeric($_GET['invtnum'])) {
	$invtnum = $_GET['invtnum'];
}

$invtstat = '---';
$peakotd  = 0;
$peakoat  = 0;

if (file_exists($MEMORY)) {
	$data     = file_get_contents($MEMORY);
	$memarray = json_decode($data, true);
	$invtstat = $memarray["invtstat$invtnum"];
	$peakotd  = $memarray["peakotd$invtnum"];
	$peakoat  = $memarray["peakoat$invtnum"];
	$status   = $memarray['status'];
}

$ret = array(
	'STATE' => $invtstat,
	'PPEAK' => $peakoat,
	'PPEAKOTD' => $peakotd,
	'STATUS' => $status
);

header("Content-type: application/json");
echo json_encode($ret);
?>
