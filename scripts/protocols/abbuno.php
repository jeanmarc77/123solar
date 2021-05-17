<?php
/**
 * /srv/http/123solar/scripts/protocols/abbuno.php
 *
 * @package default
 */

if (!defined('checkaccess')) {
	die('Direct access not permitted');
}

// For TCP Modbus https://www.modbusdriver.com/modpoll.html

$RET = '';
unset ($CMD_RETURN);

$CMD_POOLING = "modpoll -m tcp -r 41123 -c 26 -0 -a 1 -i -1 ${'ADR'.$invt_num}";

exec($CMD_POOLING, $CMD_RETURN);
if ($DEBUG) 
	{
	var_dump($CMD_RETURN);
	}

if (isset($CMD_RETURN[11]))
{	

	// String 1
	$I1V = ((float) (substr($CMD_RETURN[12], 9)) / 10);
	$I1A = ((float) (substr($CMD_RETURN[11], 9)) / 10);
	$I1P = ((float) (substr($CMD_RETURN[13], 9)) * 10);

	// String 2
	$I2V = ((float) (substr ($CMD_RETURN[32], 9)) / 10);
	$I2A = ((float) (substr ($CMD_RETURN[31], 9)) / 10);
	$I2P = ((float) (substr ($CMD_RETURN[33], 9)) * 10);

	// Other strings are not present
	$I3V = null;
	$I3A = null;
	$I3P = null;
	$I4V = null;
	$I4A = null;
	$I4P = null;

	// 2nd command to get grid parameters
	
	unset ($CMD_RETURN);
	$CMD_POOLING = "modpoll -m tcp -r 40072 -c 50 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
		
	exec($CMD_POOLING, $CMD_RETURN);
	if ($DEBUG) 
		{
		var_dump($CMD_RETURN);
		}
	// Grid
	if (!${'PHASE' . $invt_num}) {
		$G1V = ((float) (substr ($CMD_RETURN[19], 9)) / 10);
		$G1A = ((float) (substr ($CMD_RETURN[11], 9)) / 10);
		$G1P = ((float) (substr ($CMD_RETURN[23], 9)) * 10);
		$G2V = null;
		$G2A = null;
		$G2P = null;
		$G3V = null;
		$G3A = null;
		$G3P = null;
	}
	else{
		$G1V = ((float) (substr ($CMD_RETURN[19], 9)) / 10);
		$G1A = ((float) (substr ($CMD_RETURN[12], 9)) / 10);
		$G1P = round(($G1V * $G1A), 3);
		$G2V = ((float) (substr ($CMD_RETURN[20], 9)) / 10);
		$G2A = ((float) (substr ($CMD_RETURN[13], 9)) / 10);
		$G2P = round(($G2V * $G2A), 3);
		$G3V = ((float) (substr ($CMD_RETURN[21], 9)) / 10);
		$G3A = ((float) (substr ($CMD_RETURN[14], 9)) / 10);
		$G3P = round(($G3V * $G3A), 3);
	}

	$FRQ  = ((float) (substr ($CMD_RETURN[25], 9)) / 100);
	$INVT = ((float) (substr ($CMD_RETURN[42], 9)) / 10);
	$BOOT = ((float) (substr ($CMD_RETURN[45], 9)) / 10);
	
	
	$EFF = round( (100.0 * (float) (substr ($CMD_RETURN[23], 9)) / (float) (substr ($CMD_RETURN[40], 9))) ,2 );

	
	if ($FRQ > 0 && $EFF > 0 && $EFF < 1.0) { // Avoid null values at early startup
			$RET = 'OK';
		} else {
			$RET = 'NOK';
		}
	
	// 3 command to get total energy
	
	unset ($CMD_RETURN);
	$CMD_POOLING = "modpoll -m tcp -t4:int -r 40094 -c 1 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
	
	exec($CMD_POOLING, $CMD_RETURN);
	if ($DEBUG) 
		{
		var_dump($CMD_RETURN);
		}
	
	$KWHT = ((float) (substr ($CMD_RETURN[11], 9)) / 100);

	if ($KWHT > 0) { // Avoid null values due to communication error
			$RET = 'OK';
		} else {
			$RET = 'NOK';
		}

	if ($DEBUG) {
	echo "DEBUGGING:\r\n";
	echo "KWHT: $KWHT\r\n";
	echo "I1V : $I1V \r\n";
	echo "I1A : $I1A \r\n";
	echo "I1P : $I1P \r\n";
	echo "I2V : $I2V \r\n";
	echo "I2A : $I2A \r\n";
	echo "I2P : $I2P \r\n";
	echo "G1V : $G1V  \r\n";
	echo "G2V : $G2V  \r\n";
	echo "G3V : $G3V  \r\n";
	echo "G1A : $G1A  \r\n";
	echo "G2A : $G2A  \r\n";
	echo "G3A : $G3A  \r\n";
	echo "G1P : $G1P  \r\n";
	echo "G2P : $G2P  \r\n";
	echo "G3P : $G3P  \r\n";
	echo "FRQ : $FRQ \r\n";
	echo "INVT: $INVT\r\n";
	echo "BOOT: $BOOT\r\n";
	echo "EFF:  $EFF \r\n";
}

	}
else
	{
	$RET = 'NOK';
	if ($DEBUG) 
		{
		var_dump(CMD_RETURN);
		}
	$CMD_RETURN = 'Error on Modpoll';
	}

?>
