<?php
/**
 * /srv/http/123solar/scripts/protocols/rklogger_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// rklogger is a command line program for reading the parameters out of Danfoss inverters.
// http://www.petig.eu/rklogger/

$ILEAK = 0;
$RISO = 0;
$PPEAK = 0;
$PPEAKOTD = 0;
$RET = 'NOK'; // No output

?>
