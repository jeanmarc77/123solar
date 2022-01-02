<?php
/**
 * /srv/http/123solar/scripts/protocols/piko.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	die('Direct access not permitted');
}
// For Piko stats http://sourceforge.net/projects/piko/
// With the help of Frank Ulbrich

if (!$DEBUG) {
	$CMD_POOLING = "piko --host=${'ADR'.$invt_num} -s -p -t -i -d";
} else {
	$CMD_POOLING = "piko --host=${'ADR'.$invt_num} -s -p -t -i -d";
}

$CMD_RETURN = '';
$dataarray = array();
$array = array();

exec($CMD_POOLING, $array);
//$array = array('Inverter Status : 3 (Running-MPP)', 'Inverter Error : 0', 'Total energy : 156794 Wh', 'Today energy : 5794 Wh', 'DC Power : 180 W',  'AC Power : 150 W', 'Efficiency : 60.1%', 'DC String 1 : 100.1 V 1.01 A 1 W T=0000 (99.99 C) S=0000 [8]', 'DC String 2 : 100.2 V 1.02 A 2 W T=0000 (99.99 C) S=0000' , 'DC String 3 : 100.3 V 1.03 A 3 W T=0000 (99.99 C) S=0000', 'AC Phase 1 : 230.1 V 0.01 A 1 W T=0000 (99.99 C)', 'AC Phase 2 : 230.2 V 0.02 A 2 W T=0000 (99.99 C)', 'AC Phase 3 : 230.3 V 0.03 A 3 W T=0000 (99.99 C)', 'AC Status : 0 (0000 -------)');
//print_r($array);

if (isset($array[13])) {
	$dataarray = preg_split('/[[:space:]]+/', $array[7]);
	$I1V       = (float) $dataarray[4];
	$I1A       = (float) $dataarray[6];
	$I1P       = (float) $dataarray[8];
	$dataarray = preg_split('/[[:space:]]+/', $array[8]);
	$I2V       = (float) $dataarray[4];
	$I2A       = (float) $dataarray[6];
	$I2P       = (float) $dataarray[8];
	$dataarray = preg_split('/[[:space:]]+/', $array[9]);
	$I3V       = (float) $dataarray[4];
	$I3A       = (float) $dataarray[6];
	$I3P       = (float) $dataarray[8];
	$dataarray = preg_split('/[[:space:]]+/', $array[10]);
	$G1V       = (float) $dataarray[4];
	$G1A       = (float) $dataarray[6];
	$G1P       = (float) $dataarray[8];
	$dataarray = preg_split('/[[:space:]]+/', $array[11]);
	$G2V       = (float) $dataarray[4];
	$G2A       = (float) $dataarray[6];
	$G2P       = (float) $dataarray[8];
	$dataarray = preg_split('/[[:space:]]+/', $array[12]);
	$G3V       = (float) $dataarray[4];
	$G3A       = (float) $dataarray[6];
	$G3P       = (float) $dataarray[8];
	$FRQ       = null;
	$EFF       = (float) preg_replace("/[^0-9.[:space:]]+/", '', $array[6]);
	$INVT      = null;
	$BOOT      = null;
	$KWHT      = (float) preg_replace("/[^0-9.[:space:]]+/", '', $array[2]);
	if ($KWHT > 0) {
		$KWHT/=1000;
		$RET = 'OK';
	} else {
		$RET = 'NOK';
	}
} else {
	$RET = '';
}
?>
