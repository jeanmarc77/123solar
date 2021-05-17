<?php
/**
 * /srv/http/123solar/scripts/protocols/omnik_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

// State
$STATE = 'no invt status';

// Alarms
$ALARM = null;
$MESSAGE = null;

// Riso, iLeak - Peak Powers
$RISO = 0;
$ILEAK = 0;
$PPEAK = 0;
$PPEAKOTD = 0;
?>
