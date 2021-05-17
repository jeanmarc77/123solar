<?php
/**
 * /srv/http/123solar/scripts/protocols/SBFspot_startup.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

$cfgdir = realpath('../../config/');
// Info file
$CMD_INFO = 'timeout --kill-after=10s 5s /usr/local/bin/sbfspot.3/SBFspot -finq -q -123s=INFO -cfg'.$cfgdir."SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}";
// Sync inverter
$CMD_SYNC = 'timeout --kill-after=10s 5s /usr/local/bin/sbfspot.3/SBFspot -finq -q -123s=SYNC -cfg'.$cfgdir."SBFspot_${'ADR'.$invt_num}.cfg ${'COMOPTION'.$invt_num}";
?>
