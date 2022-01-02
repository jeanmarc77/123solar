<?php
/**
 * /srv/http/123solar/scripts/protocols/aurora.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For Aurora http://www.curtronics.com/Solar
$CMD_RETURN = '';

if (!${'PHASE' . $invt_num}) { // Monophased
	if ($DEBUG) {
		$CMD_POOLING = "aurora -b -a ${'ADR'.$invt_num} -c -T ${'COMOPTION'.$invt_num} -d0 -e ${'PORT'.$invt_num} 2> /tmp/de.err";
	} else {
		$CMD_POOLING = "aurora -a ${'ADR'.$invt_num} -c -T ${'COMOPTION'.$invt_num} -d0 -e ${'PORT'.$invt_num}";
	}
	$ok = 21;
} else { // Triphased
	if ($DEBUG) {
		$CMD_POOLING = "aurora -b -a ${'ADR'.$invt_num} -c -T ${'COMOPTION'.$invt_num} -d0 -e -3 ${'PORT'.$invt_num} 2> /tmp/de.err";
	} else {
		$CMD_POOLING = "aurora -a ${'ADR'.$invt_num} -c -T ${'COMOPTION'.$invt_num} -d0 -e -3 ${'PORT'.$invt_num}";
	}
	$ok = 31;
}
$dataarray = array();

$CMD_RETURN = exec($CMD_POOLING);
$dataarray  = preg_split('/[[:space:]]+/', $CMD_RETURN);

if (!isset($dataarray[$ok])) {
	$dataarray[$ok] = 'NOK';
}

if ($dataarray[$ok] == 'OK') {
	//$SDTE = $dataarray[0]; // 20150719-11:31:02
	$I1V  = (float) $dataarray[1];
	$I1A  = (float) $dataarray[2];
	$I1P  = (float) $dataarray[3];
	$I2V  = (float) $dataarray[4];
	$I2A  = (float) $dataarray[5];
	$I2P  = (float) $dataarray[6];
	$FRQ  = (float) $dataarray[10];
	$EFF  = (float) $dataarray[11];
	$INVT = (float) $dataarray[12];
	$BOOT = (float) $dataarray[13];
	$KWHT = (float) $dataarray[19];

	if (!${'PHASE' . $invt_num}) {
		$G1V = (float) $dataarray[7];
		$G1A = (float) $dataarray[8];
		$G1P = (float) $dataarray[9];
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
	} else {
		$I3V = null;
		$I3A = null;
		$I3P = null;
		$I4V = null;
		$I4A = null;
		$I4P = null;
		$G1V = (float) $dataarray[22];
		$G1A = (float) $dataarray[25];
		$G1P = round(($G1V * $G1A), 3);
		$G2V = (float) $dataarray[23];
		$G2A = (float) $dataarray[26];
		$G2P = round(($G2V * $G2A), 3);
		$G3V = (float) $dataarray[24];
		$G3A = (float) $dataarray[27];
		$G3P = round(($G3V * $G3A), 3);
	}

	if ($FRQ > 0) { // Avoid null values at early startup
		$RET = 'OK';
	} else {
		$RET = 'NOK';
	}
} else {
	$RET = 'NOK';
	if ($DEBUG) {
		$time = date('Ymd-H:i:s');
		exec('cp /tmp/de.err ' . $INVTDIR . '/errors/de' . $time . '.err');
		file_put_contents($INVTDIR . '/errors/out' . $time . '.txt', $CMD_RETURN);
	}
}
?>
