<?php
/**
 * /srv/http/123solar/scripts/boot123s.php
 *
 * @package default
 */


if ($_SERVER['SERVER_ADDR'] != '127.0.0.1' && $_SERVER['SERVER_ADDR'] != '::1') {
	die('Direct access not permitted');
}

define('checkaccess', TRUE);
include '../config/config_main.php';
date_default_timezone_set($DTZ);
$DATADIR = dirname(dirname(__FILE__)) . '/data/';
$pid    = null;
$now    = date($DATEFORMAT . ' H:i:s');

if (file_exists('123solar.pid')) {
	exec('pkill -f 123solar.php> /dev/null 2>&1 &'); // make sure there is only one instance
	usleep(500000);
	unlink('123solar.pid');
}

if ($DEBUG) {
	$command = 'php 123solar.php' . ' >> ../data/123solar.err 2>&1 & echo $!; ';
	$pid     = exec($command);
	file_put_contents('123solar.pid', $pid);
	$stringData = "#* $now\tStarting 123Solar on boot debug ($pid)\n\n";
	$myFile     = $DATADIR . '123solar.err';
	file_put_contents($myFile, $stringData, FILE_APPEND);
} else {
	$command    = 'php 123solar.php' . ' > /dev/null 2>&1 & echo $!;';
	$pid        = exec($command);
	file_put_contents('123solar.pid', $pid);
	$stringData = "#* $now\tStarting 123Solar on boot ($pid)\n\n";
}

for ($i = 1; $i <= $NUMINV; $i++) {
	$INVTDIR    = $DATADIR . "invt$i/";
	$stringData .= file_get_contents($INVTDIR . 'infos/events.txt');
	file_put_contents($INVTDIR . 'infos/events.txt', $stringData);
}
?>
