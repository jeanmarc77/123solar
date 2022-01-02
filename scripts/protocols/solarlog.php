<?php
// Experimental ! haven't been tested

if (!defined('checkaccess')) {die('Direct access not permitted');}

$CMD_RETURN = ''; // Always initialize

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://${'ADR'.$invt_num}/");
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = strip_tags(curl_exec($ch));
curl_close($ch);

$result = json_decode($CMD_RETURN);
//$result = '{„801":{„170":{„100":"31.03.14 10:42:15","101":0,"102":0,"103":0,"104":0,"105":0,"106":0,"107":3527647,"108":0,"109":0,"110":0,"111":0,"112":0,"113":1132434,"114":0,"115":0,"116":45000}}}';

if (json_last_error() === JSON_ERROR_NONE) {
	$I1V = (float) $result[104]; // udc

	$G1V = (float) $result[103]; // uac
	$G1P = (float) $result[102]; //pdc
	$G1A = round($G1P/$G1V,2);
	
	$KWHT = (float) $dataarray[109]; // yieldtotal
	$RET = 'OK';
} else {
	$RET = 'NOK';
}

?>
