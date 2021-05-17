<?php
/**
 * /srv/http/123solar/scripts/protocols/aurora_startup.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// Daily info text file
$CMD_INFO = "aurora -a ${'ADR'.$invt_num} ${'COMOPTION'.$invt_num} -p -n -f -g -m -v ${'PORT'.$invt_num}";
// Daily Sync time command
$CMD_SYNC ="aurora -a ${'ADR'.$invt_num} ${'COMOPTION'.$invt_num} -L5 ${'PORT'.$invt_num}"; //if drift >5sec from computer
?>
