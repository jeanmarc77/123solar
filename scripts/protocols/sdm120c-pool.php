<?php
/**
 * /srv/http/123solar/scripts/protocols/sdm120c-pool.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// sdm120c is a command line program for reading the parameters out of EASTRON SDM120C ModBus Smart meter.
// http://github.com/gianfrdp/SDM120C


/**
 *
 * @param unknown $id
 * @param unknown $datareturn
 * @return unknown
 */
function is_valid($id, $datareturn) //  IEC 62056 data set structure
{
	$regexp = "/^$id\(-?[0-9\.]+\*[A-z0-9³²%°]+\)$/i"; //ID(VALUE*UNIT)
	if (preg_match($regexp, $datareturn)) {
		$datareturn = preg_replace("/^$id\(/i", '', $datareturn, 1); // VALUE*UNIT)
		$datareturn = preg_replace("/\*[A-z0-9³²%°]+\)$/i", '', $datareturn, 1); // VALUE
		settype($datareturn, 'float');
	} else {
		$datareturn = null;
	}
	return $datareturn;
}


$data = exec("cat /dev/shm/metern${'ADR'.$invt_num}.txt | egrep \"^${'ADR'.$invt_num}_1\(\" | egrep \"\*V\)$\"");
$id = "${'ADR'.$invt_num}_1";
$G1V = is_valid($id, $data);
settype($G1V, 'float');

$data = exec("cat /dev/shm/metern${'ADR'.$invt_num}.txt | egrep \"^${'ADR'.$invt_num}_2\(\" | egrep \"\*A\)$\"");
$id = "${'ADR'.$invt_num}_2";
$G1A = is_valid($id, $data);
settype($G1A, 'float');

$data = exec("cat /dev/shm/metern${'ADR'.$invt_num}.txt | egrep \"^${'ADR'.$invt_num}\(\" | egrep \"\*W\)$\"");
$id = "${'ADR'.$invt_num}";
$G1P = is_valid($id, $data);
settype($G1P, 'float');

$data = exec("cat /dev/shm/metern${'ADR'.$invt_num}.txt | egrep \"^${'ADR'.$invt_num}_3\(\" | egrep \"\*Hz\)$\"");
$id = "${'ADR'.$invt_num}_3";
$FRQ = is_valid($id, $data);
settype($FRQ, 'float');
$EFF = (float) 0.0;
$INVT = null;
$BOOT = null;

$data = exec("cat /dev/shm/metern${'ADR'.$invt_num}.txt | egrep \"^${'ADR'.$invt_num}\(\" | egrep \"\*Wh\)$\"");
$id = "${'ADR'.$invt_num}";
$KWHT = is_valid($id, $data);
settype($KWHT, 'float');
$KWHT = $KWHT/1000;

if ($KWHT != 0) {
	$RET = 'OK';
} else {
	$RET = 'NOK';
}

if ($DEBUG != 0) {
	echo "G1V = $G1V, G1A = $G1A, G1P = $G1P, FRQ = $FRQ, EFF = $EFF, KWHT = $KWHT";
}

?>
