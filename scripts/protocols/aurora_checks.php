<?php
/**
 * /srv/http/123solar/scripts/protocols/aurora_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For Aurora http://www.curtronics.com/Solar
// Riso iLeak test & Peak Powers

$CMD_RISOLEAK = "aurora -a ${'ADR'.$invt_num} -D -c ${'COMOPTION'.$invt_num} ${'PORT'.$invt_num}";
$alarmarray   = array();
$i            = 0;
$RET          = '';

while ($i <= 3 && $RET != 'OK') { // Try 3 times
	$datareturn = exec($CMD_RISOLEAK, $datareturn);
	$alarmarray = preg_split('/[[:space:]]+/', $datareturn);
	if (!isset($alarmarray[32])) {
		$alarmarray[32] = 'NOK';
	}
	if ($alarmarray[32] == 'OK') {
		settype($alarmarray[7], 'float');
		$ILEAK = round(($alarmarray[7] * 1000), 1); // ileak in mA
		if ($G1P < 10 && $ILEAK > 100) { // avoid false values
			$ILEAK = 0;
		}
		settype($alarmarray[8], 'float');
		$RISO = round($alarmarray[8], 1); // riso in Mohm
		settype($alarmarray[13], 'float');
		$PPEAK = round($alarmarray[13], 1); // peak power of all time
		settype($alarmarray[14], 'float');
		$PPEAKOTD = round($alarmarray[14], 1); // peak power of the day
		$RET      = 'OK';
	}
	$i++;
}

// State text file
exec("aurora -a ${'ADR'.$invt_num} ${'COMOPTION'.$invt_num} -s ${'PORT'.$invt_num}", $STATE);
$STATE = implode(PHP_EOL, $STATE);

// Alarm
exec("aurora -a ${'ADR'.$invt_num} ${'COMOPTION'.$invt_num} -A ${'PORT'.$invt_num}", $ALARM);
$ALARM = implode(PHP_EOL, $ALARM);
if (strpos($ALARM, 'W0')) {
	$MESSAGE = $ALARM;
} else {
	$MESSAGE = null;
}
if (strpos($ALARM, 'E0')) {
	$ALARM = $ALARM;
} else {
	$ALARM = null;
}

?>