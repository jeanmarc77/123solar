<?php
/**
 * /srv/http/123solar/scripts/protocols/jfyspot_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

$timeout_setup = 'timeout --kill-after=10s 5s'; // TERM after 5" & KILL after 10"
// State
exec($timeout_setup." jfyspot ${'COMOPTION'.$invt_num} -d ${'PORT'.$invt_num} -check", $STATE);
$STATE = implode(PHP_EOL, $STATE);

// Alarms
// Functionality is not implemented in jfyspot yet
//$ALARM = exec("");

// Riso, iLeak - Peak Powers
// Functionality is not implemented in jfyspot yet
$CMD_RISOLEAK = '';
$RISO = 0;
$ILEAK = 0;
?>
