<?php
/**
 * /srv/http/123solar/scripts/protocols/kaco.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

// For KACO Powador (work in progress)
$CMD_RETURN = ''; // Always initialize

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://${'ADR'.$invt_num}:8091/realtime.csv");
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = strip_tags(curl_exec($ch));
curl_close($ch);

$line = preg_split('/;/', $CMD_RETURN);
//print_r($line);

if (isset($line[13])) {
	// Grid values
	$G1V = (float) ($line[3] / (65535/1600));
	$G1V = round($G1V, 2);
	$G1A = (float) ($line[8] / (65535/200));
	$G1A = round($G1A, 2);
	$G1P = (float) ($line[11] / (65535/100000));
	$G1P = round($G1P, 2);

	$G2V = (float) ($line[4] / (65535/1600));
	$G2V = round($G2V, 2);
	$G2A = (float) ($line[9] / (65535/200));
	$G2A = round($G2A, 2);
	$G2P = round(($G2V*$G2A),2);

	$G3V = (float) ($line[5] / (65535/1600));
	$G3V = round($G3V, 2);
	$G3A = (float) ($line[10] / (65535/200));
	$G3A = round($G3A, 2);
	$G3P = round(($G3V*$G3A),2);

	$G1P = $G1P-$G2P-$G3P;
	
	$FRQ = null;

	// Strings
	$I1V = (float) ($line[1] / (65535/1600));
	$I1V = round($I1V, 2);
	$I1A = (float) ($line[6] / (65535/200));
	$I1A = round($I1A, 2);
	$I1P = null;

	$I2V = (float) ($line[2] / (65535/1600));
	$I2V = round($I2V, 2);
	$I2A = (float) ($line[7] / (65535/200));
	$I2A = round($I2A, 2);
	$I2P = (float) ($line[6] / (65535/200));
	$I2P = round($I2P, 2);

	$I3V = null;
	$I3A = null;
	$I3P = null;
	$I4V = null;
	$I4A = null;
	$I4P = null;

	// Inverter
	//$EFF = round(($G1P / $I1P) * 100, 1);
	$EFF  = null;
	$INVT = $G3A = (float) ($line[12] / 100);
	$BOOT = null;

	// Getting KWHT in Wh, the value should always increase !
	// Here's the hassle, your counter isn't precise enough

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://193.34.93.23:8091/eternal.csv');
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$CMD_RETURN = strip_tags(curl_exec($ch));
	curl_close($ch);

	$line = preg_split('/;/', $CMD_RETURN);
	//print_r($line);

	$KWHT = preg_replace('/[^0-9.]+/', '', $line[8]);
	if (!empty($KWHT)) {
		/*
        echo "
        G1V: $G1V V
        G1A: $G1A A
        G1P: $G1P W
        G2V: $G2V
        G2A: $G2A
        G2P: $G2P
        G3V: $G3V
        G3A: $G3A
        G3P: $G3P

        FRQ: $FRQ
        I1V: $I1V
        I1A: $I1A
        I1P: $I1P
        I2V: $I2V
        I2A: $I2A
        I2P: $I2P

        EFF: $EFF
        INVT: $INVT

        KWT: $KWHT Wh
        ";
*/
		$RET = 'OK';
	} else {
		$RET = 'NOK';
	}

} else {
	$RET = 'NOK';
}

?>
