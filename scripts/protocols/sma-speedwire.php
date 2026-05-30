<?php
/**
 * /srv/http/123solar/scripts/protocols/sma-speedwire.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For SMA https://github.com/StefanLSA/py-sma-modbus2/

//$invt_num = 1;
//$PHASE1=false;

$SMAIP = '192.168.50.127';

$CMD_POOLING = "sma_pool -live";

$dataarray = array();
$ret = array();

exec($CMD_POOLING, $ret, $CMD_RETURN);
if (!isset($ret[0])) {
	$RET = 'NOK';
	exit;
} else {
	$json_texte = $ret[0];
}

$dataarray = json_decode($json_texte, true);

if ($CMD_RETURN === null || (!isset($dataarray[13]))) {
   $RET = 'NOK';
   exit;
}

if ($CMD_RETURN === 0 && isset($dataarray[13])) {
	//$SDTE = $dataarray[0]; // 20150719-11:31:02
	$I1V  = round($dataarray[2],1);
	$I1A  = round($dataarray[1],1);
	$I1P  = round($dataarray[3],1);
	$I2V  = round($dataarray[10],1);
	$I2A  = round($dataarray[9],1);
	$I2P  = round($dataarray[11],1);
	$FRQ  = round($dataarray[7],2);
//	$EFF  = 0;
	$INVT = round($dataarray[8],1);
	$BOOT = round($dataarray[13],1);
	$KWHT = round($dataarray[0]/1000,3);

	if (!${'PHASE' . $invt_num}) {
		$G1V = round($dataarray[6],1);
		$G1A = round($dataarray[12],1);
		$G1P = round($dataarray[4],2);
		$I3V = null;
		$I3A = null;
		$I3P = null;
		$I4V = null;
		$I4A = null;
		$I4P = null;
		$G2V = null;
		$G2A = null;
		$G2P = null;
		$G3V = null;
		$G3A = null;
		$G3P = null;
		if ($I1P > 0 && $G1P > 0) {
		$EFF  = ($G1P/($I1P+$I2P))*100;
		} else {
		$EFF = 0;
		}
	} else {

	}
	if ($KWHT!=0) {
	$RET= 'OK';
	} else {
	$RET= 'NOK';	
	}
} else {
	$RET= 'NOK';
}

?>
