<?php
# Stand-alone protocol for Marstek b2500d 
# There is no KWHT from battery so we calculate it and save it to file
# We use a local MQTT broker not the cloud
# License GPL-v3+
#
# Please supply 'hostname user password MAC in Admin -> Inverter(s) configuration -> Communication options
# hostname user password of your local MQTT broker.
# Important: Make sure you write down the MAC address displayed in the Marstek app! The WIFI MAC address of the battery is the wrong one.
# I use Model HMJ-2 Firmware v110, other (older) Models / Firmware maybe has a other MQTT topic see
# https://eu.hamedata.com/ems/mqtt/index.html

if (!defined('checkaccess')) {
    die('Direct access not permitted');
}

require_once(dirname(__FILE__) . '/../../misc/tools/phpMQTT.php');
use Bluerhinos\phpMQTT;

$OPTIONS = ${'COMOPTION'.$invt_num};
list ($HOST, $USER, $PASSWD, $MAC) = explode(" ", $OPTIONS, 4);
$LOGFILE = "$INVTDIR/errors/b2500d.err";
$CMD_RETURN = ''; // Always initialize
$stateFile = "$TMPFS/123s_b2500energy_state.json";

$port = 1883; // change if necessary
$clientId = uniqid(gethostname()."_client");
$topic = "hame_energy/HMJ-2/device/{$MAC}/ctrl";

$mqtt = new Bluerhinos\phpMQTT($HOST, $port, $clientId);

if (!$mqtt->connect(true, NULL, $USER, $PASSWD)) {
    $RET = 'NOK';
    exit(0);
}

$gotMessage = false;
$w1 = $w2 = 0;
$totalWh = 0.0;

// Callback: handle message and mark that we're done
$callback = function ($topic, $msg)  use (&$gotMessage, &$w1, &$w2) {

    // Parse payload like "w1=123,w2=456"
    $pairs = explode(",", $msg);
    $data = [];
    foreach ($pairs as $pair) {
        if (strpos($pair, "=") !== false) {
            list($key, $value) = explode("=", $pair);
            $data[trim($key)] = trim($value);
        }
    }

    if (isset($data['w1']) && isset($data['w2'])) {
        $w1 = intval($data['w1']);
        $w2 = intval($data['w2']);
    }

    $gotMessage = true;   // flag to stop the loop
};

// Subscribe
$mqtt->subscribe(
    [ $topic => ['qos' => 0, 'function' => $callback]]
);

while ($mqtt->proc()) {
    if ($gotMessage) break;
}

$mqtt->close();

/* ---------- Load KWTH ---------- */
$lastTimestamp = null;
$totalWh = 0.0;

if (file_exists($stateFile)) {
    $data = json_decode(file_get_contents($stateFile), true);
    if (is_array($data)) {
        $lastTimestamp = $data['timestamp'];
        $totalWh       = $data['wh'];
    }
}
else {
    $latestFile = null;
    $latestTime = 0;

    foreach (new DirectoryIterator("$INVTDIR/csv") as $file) {
        if ($file->isFile() && $file->getExtension() === 'csv') {
            $mtime = $file->getMTime();
            if ($mtime > $latestTime) {
                $latestTime = $mtime;
                $latestFile = $file->getPathname();
            }
        }
    }
    $lines = file($latestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lastLine = end($lines);
    $values   = str_getcsv($lastLine);
    $lastTimestamp = strtotime(date('Y-m-d') . ' ' . $values[0]);
    $totalWh       = end($values) * 1000;
}

/* ---------- KWTH calculation ---------- */
$now = time();

if ($lastTimestamp !== null) {
    $deltaSeconds = $now - $lastTimestamp;

    if ($deltaSeconds > 0) {
        // Wh = W * h
        $deltaWh = ($w1 + $w2) * ($deltaSeconds / 3600);
        $totalWh += $deltaWh;
    }
}

/* ---------- Save new state ---------- */
$tmpFile = $stateFile . '.tmp';

file_put_contents(
    $tmpFile,
    json_encode([
        'timestamp' => $now,
        'wh'        => round($totalWh, 10)
    ], JSON_PRETTY_PRINT)
);

rename($tmpFile, $stateFile);

//https://github.com/jeanmarc77/123solar/wiki/3)-Protocols#yourprotocolphp-this-script-is-called-as-much-as-possible
//String1
$I1V  = 36; // fixed dummy
$I1A  = 1; // fixed dummy
$I1P = (float) $w1;
	
//String2
$I2V  = 36; // fixed dummy
$I2A  = 1; // fixed dummy
$I2P = (float) $w2;

$INVT = 0; // temperature inverter fixed dummy
$BOOT = 0; // temperature dc/dc booster fixed dummy

$G1P  = (float) ($w1 + $w2); // P-AC
$G1V  = (float) 230; // U-AC fixed dummy
$G1A  = (float) round($G1P / $G1V, 2);

$FRQ  = (float) 50; // freq  fixed dummy
$KWHT = round($totalWh / 1000, 10);

$RET = 'OK';
?>
