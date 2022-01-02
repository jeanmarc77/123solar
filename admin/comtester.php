<?php
/**
 * /srv/http/123solar/admin/comtester.php
 *
 * @package default
 */


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>comtester</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
</head>
<body>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr><td>
<?php
include 'secure.php';
date_default_timezone_set($DTZ);

if (!empty($_POST['invtnum']) && is_numeric($_POST['invtnum'])) {
	$invtnum = $_POST['invtnum'];
} else {
	$invtnum = 1;
}
include "../config/config_invt$invtnum.php";

if (!empty($_POST['PROTOCOLx']) && is_string($_POST['PROTOCOLx'])) {
	$PROTOCOLx = htmlspecialchars($_POST['PROTOCOLx'], ENT_QUOTES, 'UTF-8');
} else {
	$PROTOCOLx = ${'PROTOCOL'.$invtnum};
}
if (!empty($_POST['PHASEx']) && is_string($_POST['PHASEx'])) {
	$PHASEx = htmlspecialchars($_POST['PHASEx'], ENT_QUOTES, 'UTF-8');
} else {
	$PHASEx = "${'PHASE'.$invtnum}";
}
if (!empty($_POST['PORTx']) && is_string($_POST['PORTx'])) {
	$PORTx = htmlspecialchars($_POST['PORTx'], ENT_QUOTES, 'UTF-8');
} else {
	$PORTx =  ${'PORT'.$invtnum};
}
if (!empty($_POST['COMOPTIONx']) && is_string($_POST['COMOPTIONx'])) {
	$COMOPTIONx = htmlspecialchars($_POST['COMOPTIONx'], ENT_QUOTES, 'UTF-8');
} else {
	$COMOPTIONx =  ${'COMOPTION'.$invtnum};
}
if (!empty($_POST['ADRx']) && is_string($_POST['ADRx'])) {
	$ADRx = htmlspecialchars($_POST['ADRx'], ENT_QUOTES, 'UTF-8');
} else {
	$ADRx =  ${'ADR'.$invtnum};
}
if (!empty($_POST['bntsubmit'])) {
	$bntsubmit = $_POST['bntsubmit'];
} else {
	$bntsubmit = null;
}

echo "<br>
<div align=center><b>Test your com app reliability hence you can adjust the parameters</b><br></div>
<br>
<table border=1 cellspacing=0 cellpadding=5 width='80%' align='center'>
<tr><td>
<form method='POST' action='comtester.php'><select name='invtnum' onchange='this.form.submit()'>";
for ($i = 1; $i <= $NUMINV; $i++) {
	include "../config/config_invt$i.php";
	if ($invtnum == $i) {
		echo "<option SELECTED value=$i>";
	} else {
		echo "<option value=$i>";
	}
	echo "Inverter $i</option>";
}
echo '</select>';

echo " <b> ${'PROTOCOL' . $invtnum} protocol</b></form>
</td>
<td>
<form method='POST' action='comtester.php'>
 <select name='PHASEx'>";
if ($PHASEx == 'true') {
	echo "<option value='false'>Single</option><option SELECTED value='true'>Three</option>";
} else {
	echo "<option SELECTED value='false'>Single</option><option value='true'>Three</option>";
}
echo "
</select> phase</td>
<td>Port <input type='text' name='PORTx' size=10' value=\"$PORTx\"></td>
<td>RS485 | IP adress : <input type='text' name='ADRx' value=\"$ADRx\" style='width:80px'></td>
<td>Communication options : <input type='text' name='COMOPTIONx' value=\"$COMOPTIONx\" size=10></td>
</tr></table>
<div align=center><br><INPUT TYPE='button' onClick=\"location.href='help.php'\" value='Back'>&nbsp;<input type='submit' name='bntsubmit' value='Test communication'";
if (file_exists('../scripts/123solar.pid')) {
	echo "onclick=\"if(!confirm('123Solar will be stopped for this test, continue ?')){return false;}\"";
}
echo '></div>
</form>
<br>';
if ($bntsubmit == 'Test communication') {
	if (file_exists('../scripts/123solar.pid')) {
		$pid     = (int) file_get_contents('../scripts/123solar.pid');
		$command = exec("kill -9 $pid > /dev/null 2>&1 &");
		unlink('../scripts/123solar.pid');
		usleep(500000);
	}
	$try       = 10;
	$timemax   = 0;
	$timemin   = 10000000;
	$errcnt    = 0;

	$invt_num = 0;
	$PROTOCOL0  = ${'PROTOCOL' . $invtnum};
	if ($PHASEx == 'false') {
		$PHASE0 = false;
	} else {
		$PHASE0 = true;
	}
	$PORT0      = "$PORTx";
	$ADR0       = "$ADRx";
	$COMOPTION0 = "$COMOPTIONx";
	$DEBUG      = false;

	for ($i = 1; $i <= $try; $i++) {
		$start = microtime(true);
		$ret = include "../scripts/protocols/$PROTOCOLx.php";
		if ($ret && $RET == 'OK') {
			$time_elapsed_secs = microtime(true) - $start;
			if ($time_elapsed_secs > $timemax) {
				$timemax = $time_elapsed_secs;
			}
			if ($time_elapsed_secs < $timemin) {
				$timemin = $time_elapsed_secs;
			}
			$stamp = round(($time_elapsed_secs*1000), 2);
			echo "<font color='#888'>$i : $CMD_RETURN $stamp (ms)</font><br>";
		} else {
			$errcnt++;
			echo "<font color='#8B0000'>$i : $CMD_RETURN ERROR !</font><br>";
		}

	}

	$timemin = round(($timemin*1000), 2);
	$timemax = round(($timemax*1000), 2);
	$stamp = date('d/m/Y H:i:s');

	if ($errcnt != $try) {
		echo "<br><b>$stamp : $CMD_POOLING <br>Result : best $timemin ms - worst $timemax ms - $errcnt error(s)</b>";
	} else {
		echo "<br><font color='#8B0000'>Errors while testing : $CMD_POOLING</font>";
	}
}
?>

</tr></td>
</table>
</body>
</html>
