<?php
/**
 * /srv/http/123solar/scripts/protocols/delta.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	// comment this line to test from command line
	die('Direct access not permitted');
}
// For Delta Inverters using https://github.com/rsltrifork/DeltaPVOutput

$CMD_RETURN = '';

$CMD_POOLING = 'python DeltaPVOutput.py -v';
$CMD_RETURN = exec($CMD_POOLING);

// Test lines
/*
$CMD_RETURN = "Date: d=20150719, Time: t=10:36
Energy Today: v1=8142Wh, Instantaneous Power: v2=2570W
Volts: v6=239, Temp: v5=47 oC
OK 200: Added Status";

$CMD_RETURN ="Date: d=20150830, Time: t=11:53
Energy Today: v1=11240Wh, Instantaneous Power: v2=2985W
Volts: v6=238, Temp: v5=52 oC
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100    68  100    20  100    48     24     58 --:--:-- --:--:-- --:--:--   116";

*/

$dataarray = preg_split('/[[:space:]]+/', $CMD_RETURN);
// uncomment this line to test
//print_r($dataarray);

if ($dataarray[16] == 'OK') {

	$KWHT = $dataarray[7];
	$KWHT = substr($KWHT, 3);
	settype($KWHT, 'float');

	$G1P = $dataarray[10];
	$G1P = substr($G1P, 3);
	settype($G1P, 'float');

	$G1V = $dataarray[12];
	$G1V = substr($G1V, 3);
	settype($G1V, 'float');

	$INVT = $dataarray[14];
	$INVT = substr($INVT, 3);
	settype($INVT, 'float');
	// uncomment this line to test
	//echo "kwh $KWHT g1p $G1P g1v $G1V temp $INVT ";
	$RET = 'OK';
} else {
	$RET = 'NOK';
}
?>
