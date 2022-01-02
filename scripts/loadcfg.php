<?php
/**
 * /srv/http/123solar/scripts/loadcfg.php
 *
 * @package default
 */


// Load configuration
if (isset($_SERVER['REMOTE_ADDR'])) {
	die('Direct access not permitted');
}
if (php_sapi_name() == "cli") {
	//die('Abording. 123Solar cannot be in CLI');
}
// Few checks
$input = '{ "jsontest" : " <br>Json extension loaded" }';
$val   = json_decode($input, true);
if ($val["jsontest"] != "") {
} else {
	die("/!\ Json extension -NOT- loaded. Abording, please update php.ini.\n");
}

define('checkaccess', TRUE);
if (is_readable('../config/config_main.php')) {
	include '../config/config_main.php';
} else {
	die("Abording. Can't open config_main.php.\n");
}
if (is_readable('../config/config_pvoutput.php')) {
	include '../config/config_pvoutput.php';
} else {
	die("Abording. Can't open config_pvoutput.php.\n");
}
if (is_readable('../config/memory.php')) {
	include '../config/memory.php';
} else {
	die("Abording. Can't open memory.php.\n");
}
if (file_exists($MEMORY) && !is_writable($MEMORY)) {
	die("Abording. Can't write $MEMORY.php.\n");
}
if (version_compare(phpversion(), '7.1', '>=')) { // json_encode() uses EG(precision)
	ini_set('serialize_precision', -1);
}
if (file_exists($MEMORY)) {
	$data     = file_get_contents($MEMORY);
	$memarray = json_decode($data, true);
}
if (file_exists($LIVEMEMORY)) {
	$data         = file_get_contents($LIVEMEMORY);
	$livememarray = json_decode($data, true);
}

date_default_timezone_set($DTZ);
// Date check
$output = array();
$output = glob('../data/invt1/csv/*.csv');
sort($output);
$xdays = count($output);

$nowutc = strtotime(date('Ymd H:i:s'));
$todayUTC    = strtotime(date('Ymd'));
if ($xdays > 1) {
	$lastlog    = $output[$xdays - 1];
	$lines      = file($lastlog);
	$contalines = count($lines);
	$array      = preg_split('/,/', $lines[$contalines - 1]);
	$date1      = substr($output[$xdays - 1], -12);

	$year   = (int) substr($date1, 0, 4);
	$month  = (int) substr($date1, 4, 2);
	$day    = (int) substr($date1, 6, 2);
	$hour   = (int) substr($array[0], 0, 2);
	$minute = (int) substr($array[0], 3, 2);
	$fileUTCdate = strtotime(date("$year/$month/$day"));
	$epochdate = strtotime($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute);
} else {
	$contalines = 0;
	$fileUTCdate = 0;
	$epochdate = 1242975600;
}
$i = 1;
while ($nowutc < $epochdate) {
	$nowutc = strtotime(date('Ymd H:i:s'));
	echo "Computer or PHP time is not correct, trying again in $i sec\n";
	sleep("$i");
	$i++;
	if ($i > 1200) {
		die("Abording..\n");
	}
}

// Initialize variables
$minlist = array(
	'00',
	'05',
	'10',
	'15',
	'20',
	'25',
	'30',
	'35',
	'40',
	'45',
	'50',
	'55'
);

$DATADIR = dirname(dirname(__FILE__)) . '/data/';
if (!isset($memarray['awake'])) {
	$memarray['awake'] = false;
}

$GOPVO  = 1; // Go to feed PVoutput
$daemon = false;

$daemonlist = array(
	'485solar-get',
	'fronius-fslurp'
);

// Invt config
for ($i = 1; $i <= $NUMINV; $i++) {
	if (is_readable("../config/config_invt$i.php")) {
		include "../config/config_invt$i.php";
	} else {
		die("Abording. Can't open config_invt$i.php.\n");
	}
	if (in_array(${'PROTOCOL' . $i}, $daemonlist)) {
		$daemon = true;
	}
	if (${'SKIPMONITORING' . $i}) {
		$GOPVO++;
	}
	${'comlost' . $i} = false;

	if ($fileUTCdate == $todayUTC) { // First of the day
		$array     = preg_split('/,/', $lines[1]);
		$memarray["First$i"] = $array[27]; //KWHT
	} else {
		$memarray["First$i"] = 0;
	}

	$nowUTCs                = strtotime(date('Ymd H:i:s'));
	$livememarray["SDTE$i"] = $nowUTCs;
	$livememarray["I1V$i"]  = 0;
	$livememarray["I1A$i"]  = 0;
	$livememarray["I1P$i"]  = 0;
	$livememarray["I2V$i"]  = 0;
	$livememarray["I2A$i"]  = 0;
	$livememarray["I2P$i"]  = 0;
	$livememarray["I3V$i"]  = 0;
	$livememarray["I3A$i"]  = 0;
	$livememarray["I3P$i"]  = 0;
	$livememarray["I4V$i"]  = 0;
	$livememarray["I4A$i"]  = 0;
	$livememarray["I4P$i"]  = 0;
	$livememarray["G1V$i"]  = 0;
	$livememarray["G1A$i"]  = 0;
	$livememarray["G1P$i"]  = 0;
	$livememarray["G2V$i"]  = 0;
	$livememarray["G2A$i"]  = 0;
	$livememarray["G2P$i"]  = 0;
	$livememarray["G3V$i"]  = 0;
	$livememarray["G3A$i"]  = 0;
	$livememarray["G3P$i"]  = 0;
	$livememarray["FRQ$i"]  = 0;
	$livememarray["EFF$i"]  = 0;
	$livememarray["INVT$i"] = 0;
	$livememarray["BOOT$i"] = 0;
	$livememarray["SSR$i"]  = 0;
	//$livememarray["KWHT$i"] = 0;
	if (!isset($memarray["pmotd$i"])) {
		$memarray["pmotd$i"]  = 0;
		$memarray["pmotdt$i"] = $nowUTCs;
	}
	if (!isset($memarray['pmotdmulti'])) {
		$memarray['pmotdmulti']  = 0;
		$memarray['pmotdtmulti'] = 0;
	}

	$memarray['status'] = '123Solar starting';
	if (!isset($memarray["invtstat$i"])) {
		$memarray["invtstat$i"] = '---';
	}
	if (!isset($memarray["5minflag$i"])) {
		$memarray["5minflag$i"] = false;
	}
	if (!isset($memarray["msgq$i"])) {
		$memarray["msgq$i"] = 0;
	}
	if (!isset($memarray["AWt$i"])) {
		$memarray["AWt$i"]     = $nowUTCs; // Reset AW msg
		$memarray["AWriso$i"]  = 0;
		$memarray["AWileak$i"] = 0;
		$memarray["peakotd$i"] = 0; // Peak
		$memarray["peakoat$i"] = 0;
	}
	// Alarms and Warnings flags
	$flagvolt[$i] = false;
	$flagriso[$i] = false;
	$flagleak[$i] = false;

	// Keep trace of previous msg
	$prevmsg[$i] = '';
}

// PVoutput config
$pvoconsu = false; // Consumption
for ($i = 1; $i <= $NUMPVO; $i++) {
	if (${'PVOC' . $i} != 'no') {
		$pvoconsu = true;
	}
}


/**
 *
 * @param unknown $aid
 * @param unknown $uid
 * @param unknown $title
 * @param unknown $msg
 */
function pushover($aid, $uid, $title, $msg) // Push-over
{
	curl_setopt_array($ch = curl_init(), array(
			CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_POSTFIELDS => array(
				'token' => "$aid",
				'user' => "$uid",
				'title' => "$title",
				'message' => "$msg"
			)
		));
	curl_exec($ch);
	curl_close($ch);
}


/**
 *
 * @param unknown $token
 * @param unknown $chatid
 * @param unknown $msg
 */
function telegram($token, $chatid, $msg) // Telegram
{
	$tosend = array('chat_id' => $chatid, 'text' => $msg);
	$ch = curl_init('https://api.telegram.org/bot'.$token.'/sendMessage');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tosend));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	$output = curl_exec($ch);
	curl_close($ch);
}


/**
 *
 * @param unknown $i
 * @param unknown $stringData
 */
function logevents($i, $stringData) // Log to events
{
	$dir = dirname(dirname(__FILE__)) . "/data/invt$i";
	$stringData .= file_get_contents($dir . '/infos/events.txt');
	file_put_contents($dir . '/infos/events.txt', $stringData);
}


$errfile= $DATADIR . '123solar.err';
if (file_exists($errfile)) {
	$lines = file($errfile);
	$cnt   = count($lines);
	if ($cnt >= $AMOUNTLOG) {
		$cnt   -= $AMOUNTLOG;
		array_splice($lines, 0, $cnt);
		$file2 = fopen($errfile, 'w');
		fwrite($file2, implode('', $lines));
		fclose($file2);
	}
}

/////  Main memory
$data = json_encode($memarray);
file_put_contents($MEMORY, $data);
/////  Live memory
$data = json_encode($livememarray);
file_put_contents($LIVEMEMORY, $data);
?>
