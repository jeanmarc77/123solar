<?php
/**
 * /srv/http/123solar/scripts/protocols/SBFspot.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For SBFspot https://sbfspot.codeplex.com/
// Use $COMOPTION for SBFspot CSV command switches (-ad# -am# -nocsv etc)

// Timeout setup : for SBFspot timeout management by 123Solar (relies on timeout command)
$CMD_RETURN = '';

$cfgdir = realpath('../../config/'); // /srv/http/123solar/config/SBFspot_X.cfg

if (!$DEBUG) {
	$CMD_POOLING = 'timeout --kill-after=15s 10s /usr/local/bin/sbfspot.3/SBFspot -finq -q -123s=DATA -cfg' . $cfgdir . "SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}";
} else {
	$CMD_POOLING = 'timeout --kill-after=15s 10s /usr/local/bin/sbfspot.3/SBFspot -finq -q -123s=DATA -cfg' . $cfgdir . "SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}";
	// This output is really verbose and does not respect the 123s data frame hence why you will had no data !
	//$CMD_POOLING = $timeout_setup . " SBFspot -finq -d5 -v5 -123s=DATA -cfg" . $cfgdir . "SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}";
}

$CMD_RETURN = exec($CMD_POOLING);
$dataarray  = preg_split('/[[:space:]]+/', $CMD_RETURN);
if (isset($dataarray[24])) { // SBFspot might send trames shorter than 24
	if ($dataarray[24] == '>>>S123:OK') {
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
		$EFF = $dataarray[11]; // Value computed by SBFspot
		settype($EFF, 'float');
		$INVT = $dataarray[12]; // Inverter temperature - n/a for SMA inverters
		settype($INVT, 'float');
		$BOOT = $dataarray[13]; // Booster temperature - n/a for SMA inverters
		settype($BOOT, 'float');
		$KWHT = $dataarray[14]; // Metering.TotWhOut (kWh)
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
		$RET = 'NOK';
	}
} else {
	$RET = 'NOK';
}
?>
