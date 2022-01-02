<?php
/**
 * /srv/http/123solar/scripts/protocols/jfyspot_startup.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

$timeout_setup = 'timeout --kill-after=10s 5s'; // TERM after 5" & KILL after 10"
// Info file
$CMD_INFO = $timeout_setup." jfyspot ${'COMOPTION'.$invt_num} -d ${'PORT'.$invt_num} -info";
// Sync inverter
$CMD_SYNC = $timeout_setup." jfyspot ${'COMOPTION'.$invt_num} -d ${'PORT'.$invt_num} -sync";
//JFY JSI-3000 model dosn't support RTC but jfyspot might work on other devices - untested!
?>
