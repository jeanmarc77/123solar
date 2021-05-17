<?php
/**
 * /srv/http/123solar/scripts/protocols/SBFspot_checks.php
 *
 * @package default
 */

if (!defined('checkaccess')) {die('Direct access not permitted');}

$cfgdir = realpath('../../config/');
// State
exec('timeout --kill-after=10s 5s /usr/local/bin/sbfspot.3/SBFspot -finq -q -123s=STATE -cfg'.$cfgdir."SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}", $STATE);
$STATE = implode(PHP_EOL, $STATE);

// Alarms
// Functionality is not implemented in SBFspot yet
//$ALARM = exec("");

// Riso, iLeak - Peak Powers
// Functionality is not implemented in SBFspot yet
$CMD_RISOLEAK = '';
$RISO = 0;
$ILEAK = 0;
?>
