<?php
/**
 * /srv/http/123solar/admin/admin_invt.php
 *
 * @package default
 */


include 'secure.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<title>123Solar Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
<script>
    function recalculateSum()
    {
    var num1 = parseInt(document.getElementById("Num1").value);
    var num2 = parseInt(document.getElementById("Num2").value);
	var num3 = parseInt(document.getElementById("Num3").value);
	var num4 = parseInt(document.getElementById("Num4").value);
	var num5 = parseInt(document.getElementById("Num5").value);
    var num6 = parseInt(document.getElementById("Num6").value);
	var num7 = parseInt(document.getElementById("Num7").value);
	var num8 = parseInt(document.getElementById("Num8").value);
	var num9 = parseInt(document.getElementById("Num9").value);
    var num10 = parseInt(document.getElementById("Num10").value);
	var num11 = parseInt(document.getElementById("Num11").value);
	var num12 = parseInt(document.getElementById("Num12").value);
    document.getElementById("Sum").innerHTML = num1 + num2 + num3+ num4+ num5+ num6+ num7+ num8+ num9+ num10+ num11+ num12;
    }
</script>
</head>
<body onLoad='recalculateSum()'>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#FFFFFF" height="80">
  <td class="cadretopleft" width="128"><img src="../styles/default/images/sun12880.png" width="128" height="80" alt="123Solar"></td>
  <td class="cadretop" align="center"><b>123Solar Administration</font></td>
  <td class="cadretopright" width="128" align="right"></td>
  </tr>
  <tr bgcolor="#CCCC66">
<td align=right COLSPAN="3" class="cadre" height="10">
&nbsp;
</td></tr>
<tr valign="top">
    <td COLSPAN="3" class="cadrebot" bgcolor="#d3dae2">
<!-- #BeginEditable "mainbox" -->
<?php
if (!empty($_POST['invt_num']) && is_numeric($_POST['invt_num'])) {
	$invt_num = $_POST['invt_num'];
} else {
	if (!empty($_GET['invt_num']) && is_numeric($_GET['invt_num'])) {
		$invt_num = ($_GET['invt_num']);
	} else {
		$invt_num = 1;
	}
}

// Allow fields
$portlist   = array(
	'aurora',
	'fronius-fslurp',
	'sdm120c',
	'jfyspot'
);
$adresslist = array(
	'aurora',
	'485solar-get',
	'SBFspot',
	'rklogger',
	'fronius-fslurp',
	'sdm120c',
	'sdm120c-pool',
	'fronius',
	'kaco',
	'piko',
	'solarlog',
	'abbuno'
);

if ($NUMINV > 1) { //multi
	echo "
<table border='0' cellspacing=5 cellpadding=0 width='90%' align='center'><tr><td>
<form method='POST' action='admin_invt.php'>
  <div align=left><b>Select an inverter </b><select name='invt_num' onchange='this.form.submit()'>
";
	for ($i = 1; $i <= $NUMINV; $i++) {
		include "../config/config_invt" . $i . ".php";
		if ($invt_num == $i) {
			echo "<option value='$i' SELECTED>";
		} else {
			echo "<option value='$i'>";
		}
		echo "$i (${'INVNAME'.$i})</option>";
	}
	echo "
</select></div>
</form></td></tr></table>";
} else {
	echo '<br>';
} // multi
if (file_exists('../config/config_invt' . $invt_num . '.php')) {
	include "../config/config_invt" . $invt_num . ".php";
} else {
	include '../config/config_invt1.php';
	${'INVNAME' . $invt_num}        = $invt_num;
	${'PLANT_POWER' . $invt_num}    = $PLANT_POWER1;
	${'PHASE' . $invt_num}          = $PHASE1;
	${'CORRECTFACTOR' . $invt_num}  = 1;
	${'PASSO' . $invt_num}          = $PASSO1;
	${'SR' . $invt_num}             = 'no';
	${'PORT' . $invt_num}           = $PORT1;
	${'PROTOCOL' . $invt_num}       = $PROTOCOL1;
	${'ADR' . $invt_num}            = 0;
	${'COMOPTION' . $invt_num}      = '';
	${'SYNC' . $invt_num}           = false;
	${'SKIPMONITORING' . $invt_num} = false;
	${'LOGCOM' . $invt_num}         = false;
	${'YMAX' . $invt_num}           = $YMAX1;
	${'YINTERVAL' . $invt_num}      = $YINTERVAL1;
	${'PANELS1' . $invt_num}        = $PANELS11;
	${'PANELS2' . $invt_num}        = $PANELS21;
	${'ARRAY1_POWER' . $invt_num}   = $ARRAY1_POWER1;
	${'ARRAY2_POWER' . $invt_num}   = $ARRAY2_POWER1;
	${'ARRAY3_POWER' . $invt_num}   = $ARRAY3_POWER1;
	${'ARRAY4_POWER' . $invt_num}   = $ARRAY4_POWER1;
	${'EXPECT1_' . $invt_num}       = $EXPECT1_1;
	${'EXPECT2_' . $invt_num}       = $EXPECT2_1;
	${'EXPECT3_' . $invt_num}       = $EXPECT3_1;
	${'EXPECT4_' . $invt_num}       = $EXPECT4_1;
	${'EXPECT5_' . $invt_num}       = $EXPECT5_1;
	${'EXPECT6_' . $invt_num}       = $EXPECT6_1;
	${'EXPECT7_' . $invt_num}       = $EXPECT7_1;
	${'EXPECT8_' . $invt_num}       = $EXPECT8_1;
	${'EXPECT9_' . $invt_num}       = $EXPECT9_1;
	${'EXPECT10_' . $invt_num}      = $EXPECT10_1;
	${'EXPECT11_' . $invt_num}      = $EXPECT11_1;
	${'EXPECT12_' . $invt_num}      = $EXPECT12_1;
	${'EMAIL' . $invt_num}          = $EMAIL1;
	${'AWPOOLING' . $invt_num}      = $AWPOOLING1;
	${'DIGESTMAIL' . $invt_num}     = $DIGESTMAIL1;
	${'FILTER' . $invt_num}         = $FILTER1;
	${'MAILW' . $invt_num}          = $MAILW1;
	${'SENDALARMS' . $invt_num}     = $SENDALARMS1;
	${'SENDMSGS' . $invt_num}       = $SENDMSGS1;
	${'NORESPM' . $invt_num}        = $NORESPM1;
	${'LOGMAW' . $invt_num}         = $LOGMAW1;
	${'VGRIDUT' . $invt_num}        = $VGRIDUT1;
	${'VGRIDT' . $invt_num}         = $VGRIDT1;
	${'RISOT' . $invt_num}          = $RISOT1;
	${'ILEAKT' . $invt_num}         = $ILEAKT1;
	${'RPITOK' . $invt_num}         = $RPITOK1;
	${'POUKEY' . $invt_num}         = $POUKEY1;
}
echo "
<div align=center><form action='admin_invt2.php' method='post'>
<fieldset style='width:90%;'>
<legend><b>Inverter #$invt_num </b></legend>
<div align='left'> Short description name <input type='text' name='INVNAMEx' value=\"${'INVNAME'.$invt_num}\" size=10</div><br>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=5><b>Specs : </b></td></tr>
<tr>
<td>Plant Power <input type='number' name='PLANT_POWERx' value='${'PLANT_POWER'.$invt_num}' min=0 style='width:60px'> Wp</td>
<td>
  <select name='PHASEx' title='Single/Three phased'>";
if (${'PHASE' . $invt_num}) {
	echo "<option value=''>Single</option><option SELECTED value='true'>Three</option>";
} else {
	echo "<option SELECTED value=''>Single</option><option value='true'>Three</option>";
}
echo "
</select> phase
</td>
<td>Correction factor <input type='text' name='CORRECTFACTORx' value='${'CORRECTFACTOR'.$invt_num}' size=3 min='0' max='2' style='width:60px' title='If your inverter production is not equal with another calibrated counter, you may adujst this parameter
(If your inverter is 2% too optimist set it to 0.98)'>
</td>
<td>
Pass-over value <input type='number' name='PASSOx' value='${'PASSO'.$invt_num}' size=3 min=0 style='width:80px' title='Up to where your counter can count until it return to zero'> kWh
</td>
<td>
Sensor <select name='SRx' title='Solar radiation sensor'>";
$dir = '../config/sensor/';

if (${'SR' . $invt_num} == 'no') {
	echo '<option SELECTED>';
} else {
	echo '<option>';
}
echo 'no</option>';

$output = glob("$dir*.php");
sort($output);
$cnt = count($output);

for ($i = 0; $i < $cnt; $i++) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr_replace($output[$i], "", -4);
	if (${'SR' . $invt_num} == $option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo "
</select>
</td>
</tr>
</table>
<hr>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=5><b>Protocol : </b></td></tr>
<tr>
<td>Port <input type='text' name='PORTx' size=10";
if (!in_array(${'PROTOCOL' . $invt_num}, $portlist)) {
	echo " value='' disabled";
}
echo " value=\"${'PORT'.$invt_num}\"></td>
<td>
Protocol <select name='PROTOCOLx' onchange='this.form.submit()'>";

$dir    = '../scripts/protocols/';
$output = glob("$dir*.php");
sort($output);
$cnt = count($output);

for ($i = 0; $i < $cnt; $i++) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr_replace($output[$i], "", -4);
	if (!preg_match("/_checks/", $option) && !preg_match("/_startup/", $option)) {
		if (${'PROTOCOL' . $invt_num} == $option) {
			echo '<option SELECTED>';
		} else {
			echo '<option>';
		}
		echo "$option</option>";
	}
}
echo "
</select>
</td><td>";
if (${'PROTOCOL' . $invt_num} == '485solar-get' || ${'PROTOCOL' . $invt_num} == 'SBFspot') {
	echo 'SMA inverter num. 0-9 ';
} else {
	echo 'RS485 | IP adress ';
}
echo "
<input type='text' name='ADRx' value='${'ADR'.$invt_num}' style='width:80px' title='RS485 adress or IP'";
if (!in_array(${'PROTOCOL' . $invt_num}, $adresslist)) {
	echo " value='' disabled";
}
echo ">
</td>
<td>Communication options <input type='text' name='COMOPTIONx' value=\"${'COMOPTION'.$invt_num}\" size=10  title='Not available for all protocols : If you got com. errors, please read the manual for more details'></td>
</tr><tr>
<td>
Sync. inverter time daily ";
echo "
<select name='SYNCx' title='If available'>";
if (${'SYNC' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td>
Log com. errors
<select name='LOGCOMx' title='It will log communication errors in event'>";
if (${'LOGCOM' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td>
Skip monitoring <select name='SKIPMONITORINGx' title='If inverter is down for maintenance'>";
if ($NUMINV > 1) {
	if (${'SKIPMONITORING' . $invt_num}) {
		echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
	} else {
		echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
	}
} else {
	echo "<option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td><input type='submit' name='bntsubmit' value='Test communication' ";
if (file_exists('../scripts/123solar.pid')) {
	echo "onclick=\"if(!confirm('123Solar will be stopped for this test, continue ?')){return false;}\"";
}
echo ">
</td>
</tr>
</table>
<hr>
<table border='0' cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=3><b>Front page : </b></td></tr>
<tr><td>yAxis Maximum <input type='number' name='YMAXx' value='${'YMAX'.$invt_num}' min=500 style='width:60px'> W</td>
<td>yAxis Tick interval <input type='number' name='YINTERVALx' value='${'YINTERVAL'.$invt_num}' min=200 style='width:60px'> W</td>
<td></td>
</tr>
</table>
<hr>
<table border='0' cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td><b>Info details :</td></tr>
<tr><td><input type='text' name='PANELS1x' value=\"${'PANELS1'.$invt_num}\" size=60></td><td><input type='text' name='PANELS2x' value=\"${'PANELS2'.$invt_num}\" size=60></td></tr>
</table>
<hr>
<table border='0' cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=2><b>Dashboard : </b></td></tr>
<tr>
<td>Array 1 DC Power <input type='number' name='ARRAY1_POWERx' value='${'ARRAY1_POWER'.$invt_num}' min=0 style='width:60px' title='Leave empty to disable.'> Wp</td>
<td>Array 2 DC Power <input type='number' name='ARRAY2_POWERx' value='${'ARRAY2_POWER'.$invt_num}' min=0 style='width:60px' title='Leave empty to disable.'> Wp</td>
</td>
<td>Array 3 DC Power <input type='number' name='ARRAY3_POWERx' value='${'ARRAY3_POWER'.$invt_num}' min=0 style='width:60px' title='Leave empty to disable.'> Wp</td>
<td>Array 4 DC Power <input type='number' name='ARRAY4_POWERx' value='${'ARRAY4_POWER'.$invt_num}' min=0 style='width:60px' title='Leave empty to disable.'> Wp</td>
</td>
</tr>
</table>
<hr>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=2><b>Expected annual production : </b> (<span id='Sum'>--</span>kWh) </td></tr>
<tr><td>Jan. <input type='number' id='Num1' step='any' name='EXPECTJANx' value=\"${'EXPECT1_'.$invt_num}\" style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Apr. <input type='number' id='Num4' step='any' name='EXPECTAPRx' value='${'EXPECT4_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Jul. <input type='number' id='Num7' step='any' name='EXPECTJUIx' value='${'EXPECT7_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Oct. <input type='number' id='Num10' step='any' name='EXPECTOCTx' value='${'EXPECT10_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
</tr>
<tr><td>Feb. <input type='number' id='Num2' step='any' name='EXPECTFEBx' value='${'EXPECT2_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>May <input type='number' id='Num5' step='any' name='EXPECTMAYx' value='${'EXPECT5_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Aug. <input type='number' id='Num8' step='any' name='EXPECTAUGx' value='${'EXPECT8_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Nov. <input type='number' id='Num11' step='any' name='EXPECTNOVx' value='${'EXPECT11_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
</tr>
<tr><td>Mar. <input type='number' id='Num3' step='any' name='EXPECTMARx' value='${'EXPECT3_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Jun. <input type='number' id='Num6' step='any' name='EXPECTJUNx' value='${'EXPECT6_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Sep. <input type='number' id='Num9' step='any' name='EXPECTSEPx' value='${'EXPECT9_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
<td>Dec. <input type='number' id='Num12' step='any' name='EXPECTDECx' value='${'EXPECT12_'.$invt_num}' style='width:60px' onchange=\"recalculateSum();\"/> kWh</td>
</tr>
</table>
<hr>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td><b>Monthly  report : </b></td></tr>
<tr><td>Email <input type='email' name='EMAILx' value=\"${'EMAIL'.$invt_num}\" title='You need to configure a SMTP client for PHP. Leave empty to disable.'> <input type='submit' name='bntsubmit' value='Test mail'></td>
</tr>
</table>
<hr>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=4><b>Checks & Instant notification : </b></td></tr>
<tr>
<td>Check each
<input type='number' name='AWPOOLINGx' value='${'AWPOOLING'.$invt_num}' style='width:40px' min=1 max=10>
minute(s)
</td>
<td>Digest messages <input type='number' name='DIGESTMAILx' value='${'DIGESTMAIL'.$invt_num}' style='width:60px' min=0 max=60 title='The first alarm/warning will be send when detected, the following ones will be grouped. Set to 0 to send them all when detected'>
minute(s)</td>
<td>Notification filter <input type='text' name='FILTERx' value=\"${'FILTER'.$invt_num}\" title='If you wish to filter some messages, put words seperated by a comma.'></td>
<td>
Receive an email
<select name='MAILWx' title=''>";
if (${'MAILW' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
</tr>
<tr><td>Check inverter alarms
<select name='SENDALARMSx' title='If available'>";
if (${'SENDALARMS' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td>Check inverter messages
<select name='SENDMSGSx' title='If available'>";
if (${'SENDMSGS' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td>
Warn connection lost
<select name='NORESPMx' title='Check if the connection have been lost for more than 60 sec.'>";
if (${'NORESPM' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
<td>
Log all measures
<select name='LOGMAWx' title='Will log all measures even if they are between tolerances, as well as all alarms/warnings messages'>";
if (${'LOGMAW' . $invt_num}) {
	echo "<option SELECTED value='true'>Yes</option><option value=''>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value=''>No</option>";
}
echo "
</select>
</td>
</tr>
<tr>
<td>Grid tension range < <input type='number' step='any' name='VGRIDUTx' value='${'VGRIDUT'.$invt_num}' style='width:60px' min=80 max=380> > <input type='number' step='any' name='VGRIDTx' value='${'VGRIDT'.$invt_num}' style='width:60px' min=110 max=380> V</td>
<td>Resistance insulation  threshold < <input type='number' step='any' name='RISOTx' value='${'RISOT'.$invt_num}' style='width:60px' min=1 max=20 title='If available'> MOhm</td>
<td>Current leak threshold > <input type='number' step='any' name='ILEAKTx' value='${'ILEAKT'.$invt_num}' style='width:60px' min=0.1 max=500 title='If available'> mA</td>
<td></td>
</tr>
<tr>
<td>Pushover <a href='https://pushover.net/' target='_blank'><img src='../images/link.png' width='16' height='16' border='0'></a></td>
<td>API key <input type='text' size=42 name='POAKEYx' value='${'POAKEY'.$invt_num}' title='Leave empty to disable.'></td>
<td>User key <input type='text' size=42 name='POUKEYx' value='${'POUKEY'.$invt_num}' title='Leave empty to disable.'></td> <td><input type='submit' name='bntsubmit' value='Test Pushover'></td>
</tr><tr>
<td>Telegram <a href='https://telegram.me/botfather' target='_blank'><img src='../images/link.png' width='16' height='16' border='0'></a></td>
<td>Bot token <input type='text' size=42 name='TLGRTOKx' value='${'TLGRTOK'.$invt_num}' title='Leave empty to disable.'></td>
<td>Chat ID <input type='number' size=42 name='TLGRCIDx' value='${'TLGRCID'.$invt_num}' title='Leave empty to disable.'></td>
<td><input type='submit' name='bntsubmit' value='Test Telegram'></td>
</tr>
</table>
</fieldset>
<div align=center><br><INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>&nbsp;<input type='submit' value='Save inverter cfg.'></div>
<input type='hidden' name='invt_numx' value='$invt_num'>
</form>
";
?>
<br>
<br>
<!-- #EndEditable -->
          </td>
          </tr>
</table>
<br>&nbsp;
</body>
</html>
