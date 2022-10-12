<?php
/**
 * /srv/http/123solar/scripts/protocols/fronius.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For Fronius https://github.com/victronenergy/dbus-fronius
$CMD_RETURN = ''; // Always initialize

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://${'ADR'.$invt_num}/solar_api/v1/GetInverterRealtimeData.cgi?Scope=Device&DeviceID=1&DataCollection=CommonInverterData");
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = strip_tags(curl_exec($ch));
curl_close($ch);

$dataarray = json_decode($CMD_RETURN, TRUE);
//  print_r($dataarray);

if (json_last_error() == JSON_ERROR_NONE && isset($dataarray['Body']['Data']['TOTAL_ENERGY']['Value'])) {
	// Grid values
	$G1V = $dataarray['Body']['Data']['UAC']['Value'];
	settype($G1V, 'float');
	$G1A = $dataarray['Body']['Data']['IAC']['Value'];
	settype($G1A, 'float');
	$G1P = $dataarray['Body']['Data']['PAC']['Value'];
	settype($G1P, 'float');
	$G2V = null;
	$G2A = null;
	$G2P = null;
	$G3V = null;
	$G3A = null;
	$G3P = null;
	$FRQ = $dataarray['Body']['Data']['FAC']['Value'];
	settype($FRQ, 'float');

	// Strings
	$I1V = $dataarray['Body']['Data']['UDC']['Value'];
	settype($I1V, 'float');
	$I1A = $dataarray['Body']['Data']['IDC']['Value'];
	settype($I1A, 'float');
	$I1P = round($I1V * $I1A, 2);
	settype($I1P, 'float');
	$I3V = null;
	$I3A = null;
	$I3P = null;
	$I4V = null;
	$I4A = null;
	$I4P = null;

	// Inverter
	$EFF = round(($G1P / $I1P) * 100, 1);
	$INVT = null;
	$BOOT = null;
	$KWHT = $dataarray['Body']['Data']['TOTAL_ENERGY']['Value'];
	$KWHT /= 1000;
	settype($KWHT, 'float');

	$RET = 'OK';
} else {
	$RET = 'NOK';
}
?>
