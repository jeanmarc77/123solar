<?php
/**
 * /srv/http/123solar/scripts/protocols/sdm120c.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// sdm120c is a command line program for reading the parameters out of EASTRON SDM120C ModBus Smart meter.
// http://github.com/gianfrdp/SDM120C

// Ask sdm120c:
//      - Voltage (-v)
//      - Power (-p)
//      - Current (-c)
//      - Frequency (-f)
//      - Imported energy (-i)
$CMD_RETURN  = '';
$CMD_POOLING = "sdm120c -a ${'ADR'.$invt_num} ${'COMOPTION'.$invt_num} -vpcfi -q ${'PORT'.$invt_num}";

if ($DEBUG) {
	$CMD_RETURN = exec("$CMD_POOLING 2>&1");
} else {
	$CMD_RETURN = exec($CMD_POOLING);
}
$dataarray = array();
$dataarray = preg_split('/[[:space:]]+/', $CMD_RETURN);

if (isset($dataarray[5])) {
	$G1V = (float) $dataarray[0];
	$G1A = (float) $dataarray[1];
	$G1P = (float) $dataarray[2];
	$FRQ = (float) $dataarray[3];
	$EFF  = (float) 0.0;
	$INVT = null;
	$BOOT = null;
	$KWHT = (float) $dataarray[4];
	$KWHT = $KWHT / 1000;
	$RET  = 'OK';
} else {
	$RET = 'NOK';
}
?>
