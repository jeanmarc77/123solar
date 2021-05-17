<?php
/**
 * /srv/http/123solar/scripts/protocols/485solar-get_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// Riso iLeak test & Peak Powers

$CMD_RISOLEAK = "485solar-get -e -n ${'ADR'.$invt_num}";
$alarmarray = array();
$i=0;
$RET = 'NOK';

while ($i <= 3 && $RET!='OK' ) { // Try 3 times
	$datareturn = exec($CMD_RISOLEAK, $datareturn);
	$alarmarray = preg_split('/[[:space:]]+/', $datareturn);
	if (!isset($alarmarray[32])) {
		$alarmarray[32]='NOK';
	}
	if ($alarmarray[32]=='OK') {
		settype($alarmarray[7], 'float');
		$ILEAK = round(($alarmarray[7] * 1000), 1);
		settype($alarmarray[8], 'float');
		$RISO = round($alarmarray[8], 2);
		settype($alarmarray[13], 'float');
		$PPEAK = round($alarmarray[13], 1);
		settype($alarmarray[14], 'float');
		$PPEAKOTD = round($alarmarray[14], 1);
		$RET = 'OK';
	}
	$i++;
}

// State
// $CMD_STATE = '';
// Alarm
exec("485solar-get -a -n ${'ADR'.$invt_num}", $ALARM);
$ALARM = implode(PHP_EOL, $ALARM);

if (strstr($ALARM, '-------') || empty($ALARM)) {
	$ALARM = null;
}
$MESSAGE = null;
?>
