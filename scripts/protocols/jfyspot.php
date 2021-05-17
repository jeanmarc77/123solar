<?php
/**
 * /srv/http/123solar/scripts/protocols/jfyspot.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For JFYspot https://github.com/Grahamauld/JFYspot
// Use $COMOPTION for any fun command line switches... (jfyspot -help for a clue)

// Timeout setup : really just as a failsafe - JFYspot should timeout anyway
$CMD_RETURN = '';

$timeout_setup = 'timeout --kill-after=15s 10s'; // TERM after 10" & KILL after 15"

if (!$DEBUG) {
	$CMD_POOLING = $timeout_setup . " jfyspot ${'COMOPTION'.$invt_num} -d ${'PORT'.$invt_num}";
} else {
	$CMD_POOLING = $timeout_setup . " jfyspot ${'COMOPTION'.$invt_num} -d ${'PORT'.$invt_num} -v  2>/dev/shm/jfyspot.debug";
}

$CMD_RETURN = exec($CMD_POOLING);
$dataarray  = preg_split('/[[:space:]]+/', $CMD_RETURN);
if (isset($dataarray[24])) { // JFYspot shoudl not send trames shorter than 24
	if ($dataarray[24]=='>>>S123:OK') {
		$SDTE = $dataarray[0];
		$G1V  = $dataarray[1]; // GridMs.PhV.phsA
		settype($G1V, 'float');
		$G1A = $dataarray[2]; // GridMs.A.phsA
		settype($G1A, 'float');
		$G1P = $dataarray[3]; // GridMs.W.phsA
		settype($G1P, 'float');
		$G2V = $dataarray[4]; // GridMs.PhV.phsB
		settype($G2V, 'float');
		$G2A = $dataarray[5]; // GridMs.A.phsB
		settype($G2A, 'float');
		$G2P = $dataarray[6]; // GridMs.W.phsB
		settype($G2P, 'float');
		$G3V = $dataarray[7]; // GridMs.PhV.phsC
		settype($G3V, 'float');
		$G3A = $dataarray[8]; // GridMs.A.phsC
		settype($G3A, 'float');
		$G3P = $dataarray[9]; // GridMs.W.phsC
		settype($G3P, 'float');
		$FRQ = $dataarray[10]; // GridMs.Hz
		settype($FRQ, 'float');
		$EFF = $dataarray[11]; // Value computed by JFYspot ( JSI3000 dosn't provide PV I or W so it's impossible)
		settype($EFF, 'float');
		$INVT = $dataarray[12]; // Inverter temperature - n/a for SMA inverters
		settype($INVT, 'float');
		$BOOT = $dataarray[13]; // Booster temperature - n/a for JFY inverters
		settype($BOOT, 'float');
		$KWHT = $dataarray[14]; // Metering.Daily Total (kWh)
		settype($KWHT, 'float');
		$I1V = $dataarray[15]; // DcMs.Vol[A]
		settype($I1V, 'float');
		$I1A = $dataarray[16]; // DcMs.Amp[A]
		settype($I1A, 'float');
		$I1P = $dataarray[17]; // DcMs.Watt[A]
		settype($I1P, 'float');
		$I2V = $dataarray[18]; // DcMs.Vol[B]
		settype($I2V, 'float');
		$I2A = $dataarray[19]; // DcMs.Amp[B]
		settype($I2A, 'float');
		$I2P = $dataarray[20]; // DcMs.Watt[B]
		settype($I2P, 'float');
		if ($KWHT > 0) { // Avoid null values at early startup
			$RET = 'OK';
		} else {
			$RET = 'NOK';
		}
	} else {
		$RET='NOK';
	}
} else {
	$RET = 'NOK';
}
?>
