#!/usr/bin/php
<?php
if (isset($_SERVER['REMOTE_ADDR'])) {
    //die('Direct access not permitted');
}

//  chmod +x then ln -s /srv/http/comapps/sma-modbus/sma_pool.php /usr/bin/sma_pool

$SMAIP = '192.168.50.127';
$PYTHON_VENV = "/srv/http/comapps/sma-modbus/py-sma-modbus2/run/bin/python";
$SCRIPT_PATH = "/srv/http/comapps/sma-modbus/py-sma-modbus2/main.py";
$modbusregisters = "/srv/http/comapps/sma-modbus/sma-modbusregisters.txt";


if (!isset($argv[1])) {
	die("No command\n");
}

if ($argv[1] == '-live') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log -f $modbusregisters";
//$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -f $modbusregisters";
} elseif ($argv[1] == '-alarm') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30211";
} elseif ($argv[1] == '-message') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30213";
} elseif ($argv[1] == '-state') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30225";
} elseif ($argv[1] == '-ileak') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 31247";
} elseif ($argv[1] == '-riso') {
$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30225";
}
$poolarr = [];
$poolret = 0;
if ($argv[1] != '-info') {
exec($poolcmd, $poolarr, $poolret);
}
//print_r($poolarr);
if ($poolret === 0 && $argv[1] == '-live') {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($poolarr);
	exit;
} elseif ($poolret === 0 && $argv[1] == '-alarm') {
	if ($poolarr[0] !='keine (NonePrio)') {
	echo $poolarr[0];
	} else {
	echo '';
	}
} elseif ($poolret === 0 && $argv[1] == '-message') {
	if ($poolarr[0] !='keine (NoneMsg)') {
	echo $poolarr[0];
	} else {
	echo '';
	}
} elseif ($poolret === 0 && $argv[1] == '-state') {
	if ($poolarr[0] !='skeine (NoneMsg)') {
	echo $poolarr[0];
	} else {
	echo '';
	}
} elseif ($poolret === 0 && $argv[1] == '-ileak') {
	echo $poolarr[0];
} elseif ($poolret === 0 && $argv[1] == '-riso') {
	echo $poolarr[0];
} elseif ($argv[1] == '-info') {
	$poolarr = [];
	$poolret = 0;
	$ret = '';
	$poolcmd = "$PYTHON_VENV $SCRIPT_PATH -a " . escapeshellarg($SMAIP) . " -log 30057";
	exec($poolcmd, $poolarr, $poolret);
	if ($poolret === 0) {
	$ret = "serial $poolarr[0]";
	}
	echo $ret;
	
} else {
    echo "Error " . $poolret;
}
   

?>
