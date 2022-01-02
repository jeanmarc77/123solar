<?php
/**
 * /srv/http/123solar/scripts/protocols/omnik.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// Omnik.php is a program for reading the parameters out via RS422 of Omnik inverters.
// by Gino Rosi

$SDTE = date("Ymd H:i:s");
$data = array();
$FRQ = 0;

include_once 'PhpSerial.php';

$serial = new PhpSerial;
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

$serial->deviceOpen();

$str = "\x3a\x3a\x01\x00\x00\x00\x10\x04\x00\x00\x89";
$serial->sendMessage($str);
usleep(100000);

$serial->sendMessage($str);
usleep(100000);

$serial->sendMessage($str);
usleep(100000);

$str = "\x3a\x3a\x01\x00\x00\x00\x10\x00\x00\x00\x85";
$serial->sendMessage($str);
$read = $serial->readPort();

$str = "\x3a\x3a\x01\x00\x00\x00\x10\x01\x11\x49\x54\x42\x4e\x33\x30\x32\x30\x31\x36\x37\x56\x33\x30\x32\x37\x10\x04\x59";
$serial->sendMessage($str);
$read = $serial->readPort();

$str = "\x3a\x3a\x01\x00\x00\x10\x11\x10\x00\x00\xa6";
$serial->sendMessage($str);
$read = $serial->readPort();

$data = bin2hex($read);

$INVT= hexdec(substr($data, 18, 4))/10;

$I1V= (hexdec(substr($data, 22, 4))+hexdec(substr($data, 26, 4)))/20;
if ($I1V < 100) {
	$I1V = 360;
}

$I1A= (hexdec(substr($data, 34, 4))+hexdec(substr($data, 38, 4)))/10;
$I1P = $I1V * $I1A;
$G1A= hexdec(substr($data, 46, 4))/10;
$G1V= hexdec(substr($data, 58, 4))/10;
$FRQ= hexdec(substr($data, 70, 4))/100;
$G1P= hexdec(substr($data, 74, 4));
$KWHT= hexdec(substr($data, 93, 5))/100;
$BOOT = 0;

if ($FRQ > 0) {
	$RET = 'OK';
	//print $FRQ;
}

?>
