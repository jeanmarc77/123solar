<?php
/**
 * /srv/http/123solar/scripts/protocols/piko_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

// State
$output = array();
exec("piko --host=${'ADR'.$invt_num} -s", $output);
$STATE = implode(PHP_EOL, $output);

// Alarms
$dataarray  = preg_split('/[[:space:]]+/', $STATE);
if (isset($dataarray[8]) && $dataarray[8]!=0) {
	$ALARM = $dataarray[8];
} else {
	$ALARM = null;
}
$MESSAGE = null;

// Riso, iLeak - Peak Powers
$RISO = 0;
$ILEAK = 0;
$PPEAK = 0;
$PPEAKOTD = 0;
?>
