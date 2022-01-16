<?php
/**
 * /srv/http/123solar/scripts/protocols/sunezy.php
 *
 * @package default
 * from https://github.com/lmillefiori/123solar
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// sunezy.py is a command line program for reading the status out of Sunezy inverters

$CMD_RETURN  = '';
$CMD_POOLING = "sunezy.py ${'PORT'.$invt_num}";

if ($DEBUG) {
	$CMD_RETURN = exec("$CMD_POOLING 2>&1");
} else {
	$CMD_RETURN = exec($CMD_POOLING);
}
$dataarray = array();
$dataarray = preg_split('/[[:space:]]+/', $CMD_RETURN);

if (isset($dataarray[7])) {
	$I1v = (float) $dataarray[10]; // Vpv
	$G1V = (float) $dataarray[9]; // Vac
	$G1A = (float) $dataarray[5]; // Iac
	$G1P = (float) $dataarray[7]; // Pac
	$FRQ = (float) $dataarray[3]; // Fac
	$EFF  = null;
	$INVT = (float) $dataarray[8]; // Temp-inv
	$BOOT = null;
	$KWHT = (float) $dataarray[1]; // E-Total
	$RET  = 'OK';
} else {
	$RET = 'NOK';
}
?>
