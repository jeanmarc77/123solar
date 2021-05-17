<?php
/**
 * /srv/http/123solar/scripts/protocols/485solar-get_startup.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// Info file
$CMD_INFO = "485solar-get -i -n ${'ADR'.$invt_num}";
// Sync
// unset no sync for sma $CMD_SYNC ="";
?>
