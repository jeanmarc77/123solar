<?php
if (!defined('checkaccess')) {
    die('Direct access not permitted');
}

$CMD_RETURN = ''; // Always initialize
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://${'ADR'.$invt_num}/api/record/live");
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = strip_tags(curl_exec($ch));
curl_close($ch);

$result = json_decode($CMD_RETURN, true);
//print_r($result);

//https://github.com/jeanmarc77/123solar/wiki/3)-Protocols#yourprotocolphp-this-script-is-called-as-much-as-possible
if (json_last_error() === JSON_ERROR_NONE) {
  //String1
	$I1V = (float) $result['inverter'][0][0]['val']; // udc
	$I1A = (float) $result['inverter'][0][1]['val']; // idc
	$I1P = (float) $result['inverter'][0][2]['val']; // pdc
	
	//String2
	$I2V = (float) $result['inverter'][0][6]['val']; // udc
	$I2A = (float) $result['inverter'][0][7]['val']; // idc
	$I2P = (float) $result['inverter'][0][8]['val']; // pdc

	$G1V = (float) $result['inverter'][0][12]['val']; // uac
	$G1A = (float) $result['inverter'][0][13]['val']; // iac
	$G1P = (float) $result['inverter'][0][14]['val']; // pac
	
	$FRQ = (float) $result['inverter'][0][16]['val']; // frq
	$EFF = (float) $result['inverter'][0][23]['val']; // feff
	$BOOT = (float) $result['inverter'][0][18]['val']; // temp
	
	$KWHT = (float) $result['inverter'][0][21]['val']; // YieldTotal // yieldtotal
	$RET = 'OK';
} else {
	$RET = 'NOK';
}

?>
