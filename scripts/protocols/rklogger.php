<?php
/**
 * /srv/http/123solar/scripts/protocols/rklogger.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// rklogger is a command line program for reading the parameters out of Danfoss inverters.
// http://www.petig.eu/rklogger/
// With the help of Gino Rosi

//$CMD_POOLING = "rklogger 11 1 ";
$datareturn = null;

$SDTE = date("Ymd H:i:s");

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 28 8");
$I1V = $a/10;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 2d 8");
$I1A = $a/1000;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 32 8");
$I1P = $a;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 29 8");
$I2V = $a/10;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 2e 8");
$I2A = $a/1000;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 33 8");
$I2P = $a;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 2a 8");
$I3V = $a/10;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 2f 8");
$I3A = $a/1000;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 34 8");
$I3P = $a;

$I4V = null;
$I4A = null;
$I4P = null;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 3c 8");
$G1V = $a/10;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 3d 8");
$G2V = $a/10;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 3e 8");
$G3V = $a/10;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 3f 8");
$G1A = $a/1000;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 40 8");
$G2A = $a/1000;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 41 8");
$G3A = $a/1000;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 42 8");
$G1P = $a;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 43 8");
$G2P = $a;
$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 44 8");
$G3P = $a;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 50 8");
$FRQ = $a/1000;

$EFF = round((($G1P+$G2P+$G3P)/($I1P+$I2P+$I3P)), 3);

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 53 8");
$INVT = $a;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 2 54 8");
$BOOT = $a;

$a = exec("rklogger 11 ${'ADR'.$invt_num} 1 2 8");
$KWHT = $a/1000;

if (isset($I1V)&&isset($I1A)&&isset($I1P)&&isset($I2V)&&isset($I2A)&&isset($I2P)&&isset($G1V)&&isset($G2V)&&isset($G3V)&&isset($G1A)&&isset($G2A)&&isset($G3A)&&isset($G1P)&&isset($G2P)&&isset($G3P)&&isset($FRQ)&&isset($EFF)&&isset($INVT)&&isset($BOOT)&&isset($KWHT)) {
	$RET = 'OK';
} else {
	$RET = 'NOK';
}
?>
