#!/usr/bin/php
<?php
if (isset($_SERVER['REMOTE_ADDR'])) {
    die('Direct access not permitted');
}

//  chmod +x then ln -s /srv/http/comapps/sma-modbus/sma_pool.php /usr/bin/sma_pool

$SMAIP = '192.168.50.127';
$PYTHON_VENV = "timeout 5s /srv/http/comapps/sma-modbus/py-sma-modbus2/run/bin/python";
$SCRIPT_PATH = "/srv/http/comapps/sma-modbus/py-sma-modbus2/main.py 2>/dev/null";
$modbusregisters = "/srv/http/comapps/sma-modbus/sma-modbusregisters.txt";

if (!isset($argv[1])) {
    die("No command\n");
}

$action = $argv[1];

if ($action == '-info') {
    $poolarr = [];
    $poolret = 0;
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30057";
    exec($poolcmd, $poolarr, $poolret);
    
    if ($poolret === 0 && !empty($poolarr)) {
        echo "serial " . $poolarr[0];
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["error" => "-info", "code" => $poolret]);
    }
    exit;
}

$poolcmd = '';
if ($action == '-live') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log -f $modbusregisters";
} elseif ($action == '-alarm') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30211";
} elseif ($action == '-message') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30213";
} elseif ($action == '-state') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30225";
} elseif ($action == '-ileak') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 31247";
} elseif ($action == '-riso') {
    $poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30225";
} else {
    die("Unknown command: $action\n");
}

$poolarr = [];
$poolret = 0;
exec($poolcmd, $poolarr, $poolret);

if ($poolret === 0 && isset($poolarr[0])) {
    if ($action == '-live') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($poolarr);
        exit;
    } elseif ($action == '-alarm') {
        echo ($poolarr[0] != 'keine (NonePrio)') ? $poolarr[0] : '';
    } elseif ($action == '-message') {
        echo ($poolarr[0] != 'keine (NoneMsg)') ? $poolarr[0] : '';
    } elseif ($action == '-state') {
        echo ($poolarr[0] != 'skeine (NoneMsg)') ? $poolarr[0] : '';
    } elseif ($action == '-ileak' || $action == '-riso') {
        echo $poolarr[0];
    }
} else {
	//header('Content-Type: application/json; charset=utf-8');
	//echo json_encode(["error" => "py", "code" => $poolret]);
	exit;
}
?>

