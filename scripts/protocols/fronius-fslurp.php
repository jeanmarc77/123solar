<?php
/**
 * /srv/http/123solar/scripts/protocols/fronius-fslurp.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For Fronius https://sourceforge.net/projects/fslurp/ requires memcached
// With the help of Kasey Matejcek

$CMD_RETURN = '';

if ($DEBUG) {
	$CMD_POOLING = "fslurp -p ${'PORT'.$invt_num} -b 19200 -n ${'ADR'.$invt_num} -r all -d , -t %Y%m%d-%T 2> /tmp/de.err";
} else {
	$CMD_POOLING = "fslurp -p ${'PORT'.$invt_num} -b 19200 -n ${'ADR'.$invt_num} -r all -d , -t %Y%m%d-%T";
}
$dataarray = array();

$CMD_RETURN = exec($CMD_POOLING);
$dataarray  = preg_split('#(?<!\\\)\,#', $CMD_RETURN);

if ($dataarray[1] == ${'ADR' . $invt_num} && $dataarray[9] > 1) {
	// Strings
	$I1V = float($dataarray[9]);
	$I1A = float($dataarray[8]);
	$I1P = float($dataarray[4]);
	$I3V = null;
	$I3A = null;
	$I3P = null;
	$I4V = null;
	$I4A = null;
	$I4P = null;

	// Grid values
	$G1V = float($dataarray[6]);
	$G1A = float($dataarray[5]);
	$G1P = round($G1A * $G1V, 2);
	$FRQ = float($dataarray[7]);

	$G2V = null;
	$G2A = null;
	$G2P = null;
	$G3V = null;
	$G3A = null;
	$G3P = null;

	// Inverter
	$EFF  = round(($G1P / $I1P) * 100, 1);
	$KWHT = $dataarray[10];
	//$INTIME = $dataarray[0];
	//$INVT = null;
	$BOOT = null;

	if ($KWHT < 1) {
		$KWHT = 0.01;
	}
	error_reporting(E_ALL & ~E_NOTICE);

	$memcache = new Memcached();
	$memcache->addServer("localhost", 11211) or die("Could not connect");

	$memcache->set('WATT', $G1P, 60);
	$memcache->set('KWHT', $KWHT, 60);
	$KWHT    = $memcache->get('kwht');
	$INVT    = $memcache->get('it');
	$running = $memcache->get('running');
	if ($running != 1) {
		$output = exec('php ../daemons/fronius.php > /dev/null 2>&1 &');
	}
	$RET = 'OK';
} else {
	$RET = 'NOK';
	//    $memcache = new Memcached();
	//    $memcache->addServer("localhost", 11211)or die ("Could not connect");
	//    $memcache->set('KWHT', "0", 60);
}
?>
