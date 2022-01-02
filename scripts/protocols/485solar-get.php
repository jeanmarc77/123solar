<?php
/**
 * /srv/http/123solar/scripts/protocols/485solar-get.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// For SMA-GET http://sourceforge.net/projects/solarget/
$CMD_RETURN = '';

if (!$DEBUG) {
	$CMD_POOLING = "485solar-get -d -n ${'ADR'.$invt_num}";
} else {
	$CMD_POOLING = "485solar-get -d -b -n ${'ADR'.$invt_num}";
}
$CMD_RETURN = exec($CMD_POOLING);
$array      = preg_split('/[[:space:]]+/', $CMD_RETURN);

if (!isset($array[21])) {
	$array[21] = 'NOK';
}

if ($array[21] == 'OK') {
	//$SDTE = $array[0];
	$I1V  = $array[1];
	settype($I1V, 'float');
	$I1A = $array[2];
	settype($I1A, 'float');
	$I1P = $array[3];
	settype($I1P, 'float');
	$I2V = $array[4];
	settype($I2V, 'float');
	$I2A = $array[5];
	settype($I2A, 'float');
	$I2P = $array[6];
	settype($I2P, 'float');
	$G1V = $array[7];
	settype($GV, 'float');
	$G1A = $array[8];
	settype($GA, 'float');
	$G1P = $array[9];
	settype($GP, 'float');
	$FRQ = $array[10];
	settype($FRQ, 'float');
	$EFF = $array[11];
	settype($EFF, 'float');
	$INVT = $array[12];
	settype($INVT, 'float');
	$BOOT = $array[13];
	settype($BOOT, 'float');
	$KWHT = $array[19];
	settype($KWHT, 'float');
	$RET = 'OK';
} else {
	$RET = 'NOK';
}
?>
