<?php
/**
 * /srv/http/123solar/admin/admin_invt2.php
 *
 * @package default
 */


include 'secure.php';
include '../scripts/version.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<title>123Solar Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
</head>
<body>
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
if (!empty($_POST['invt_numx']) && is_numeric($_POST['invt_numx'])) {
	$invt_numx = $_POST['invt_numx'];
}
if (!empty($_POST['INVNAMEx']) && is_string($_POST['INVNAMEx'])) {
	$INVNAMEx = htmlspecialchars($_POST['INVNAMEx'], ENT_QUOTES, 'UTF-8');
} else {
	$INVNAMEx = '';
}
if (!empty($_POST['PLANT_POWERx']) && is_numeric($_POST['PLANT_POWERx'])) {
	$PLANT_POWERx = $_POST['PLANT_POWERx'];
} else {
	$PLANT_POWERx = 5000;
}
if (empty($_POST['PHASEx'])) {
	$PHASEx = 'false';
} else {
	$PHASEx = 'true';
}
if (!empty($_POST['CORRECTFACTORx']) && is_numeric($_POST['CORRECTFACTORx'])) {
	$CORRECTFACTORx = $_POST['CORRECTFACTORx'];
} else {
	$CORRECTFACTORx = 1;
}
if (!empty($_POST['PASSOx'])) {
	$PASSOx = $_POST['PASSOx'];
} else {
	$PASSOx = 0;
}
if (!empty($_POST['SRx']) && is_string($_POST['SRx'])) {
	$SRx = htmlspecialchars($_POST['SRx'], ENT_QUOTES, 'UTF-8');
} else {
	$SRx = 'no';
}

if (!empty($_POST['PORTx']) && is_string($_POST['PORTx'])) {
	$PORTx = htmlspecialchars($_POST['PORTx'], ENT_QUOTES, 'UTF-8');
} else {
	$PORTx = '/dev/ttyUSB0';
}
if (!empty($_POST['PROTOCOLx']) && is_string($_POST['PROTOCOLx'])) {
	$PROTOCOLx = htmlspecialchars($_POST['PROTOCOLx'], ENT_QUOTES, 'UTF-8');
} else {
	$PROTOCOLx = 'aurora';
}
if (!empty($_POST['ADRx']) && is_string($_POST['ADRx'])) {
	$ADRx = htmlspecialchars($_POST['ADRx'], ENT_QUOTES, 'UTF-8');
} else {
	$ADRx = 0;
}
if (!empty($_POST['COMOPTIONx']) && is_string($_POST['COMOPTIONx'])) {
	$COMOPTIONx = htmlspecialchars($_POST['COMOPTIONx'], ENT_QUOTES, 'UTF-8');
} else {
	$COMOPTIONx = '';
}
if (empty($_POST['SYNCx'])) {
	$SYNCx = 'false';
} else {
	$SYNCx = 'true';
}
if (empty($_POST['SKIPMONITORINGx'])) {
	$SKIPMONITORINGx = 'false';
} else {
	$SKIPMONITORINGx = 'true';
}
if (empty($_POST['LOGCOMx'])) {
	$LOGCOMx = 'false';
} else {
	$LOGCOMx = 'true';
}
if (!empty($_POST['YMAXx']) && is_numeric($_POST['YMAXx'])) {
	$YMAXx = $_POST['YMAXx'];
} else {
	$YMAXx = 5000;
}
if (!empty($_POST['YINTERVALx']) && is_numeric($_POST['YINTERVALx'])) {
	$YINTERVALx = $_POST['YINTERVALx'];
} else {
	$YINTERVALx = 1000;
}
if (!empty($_POST['PANELS1x']) && is_string($_POST['PANELS1x'])) {
	$PANELS1x = htmlspecialchars($_POST['PANELS1x'], ENT_QUOTES, 'UTF-8');
} else {
	$PANELS1x = '';
}
if (!empty($_POST['PANELS2x']) && is_string($_POST['PANELS2x'])) {
	$PANELS2x = htmlspecialchars($_POST['PANELS2x'], ENT_QUOTES, 'UTF-8');
} else {
	$PANELS2x = '';
}
if (!empty($_POST['ARRAY1_POWERx']) && is_numeric($_POST['ARRAY1_POWERx'])) {
	$ARRAY1_POWERx = $_POST['ARRAY1_POWERx'];
} else {
	$ARRAY1_POWERx = 0;
}
if (!empty($_POST['ARRAY2_POWERx']) && is_numeric($_POST['ARRAY2_POWERx'])) {
	$ARRAY2_POWERx = $_POST['ARRAY2_POWERx'];
} else {
	$ARRAY2_POWERx = 0;
}
if (!empty($_POST['ARRAY3_POWERx']) && is_numeric($_POST['ARRAY3_POWERx'])) {
	$ARRAY3_POWERx = $_POST['ARRAY3_POWERx'];
} else {
	$ARRAY3_POWERx = 0;
}
if (!empty($_POST['ARRAY4_POWERx']) && is_numeric($_POST['ARRAY4_POWERx'])) {
	$ARRAY4_POWERx = $_POST['ARRAY4_POWERx'];
} else {
	$ARRAY4_POWERx = 0;
}
if (!empty($_POST['EXPECTJANx']) && is_numeric($_POST['EXPECTJANx'])) {
	$EXPECTJANx = $_POST['EXPECTJANx'];
} else {
	$EXPECTJANx = 0;
}
if (!empty($_POST['EXPECTFEBx']) && is_numeric($_POST['EXPECTFEBx'])) {
	$EXPECTFEBx = $_POST['EXPECTFEBx'];
} else {
	$EXPECTFEBx = 0;
}
if (!empty($_POST['EXPECTMARx']) && is_numeric($_POST['EXPECTMARx'])) {
	$EXPECTMARx = $_POST['EXPECTMARx'];
} else {
	$EXPECTMARx = 0;
}
if (!empty($_POST['EXPECTAPRx']) && is_numeric($_POST['EXPECTAPRx'])) {
	$EXPECTAPRx = $_POST['EXPECTAPRx'];
} else {
	$EXPECTAPRx = 0;
}
if (!empty($_POST['EXPECTMAYx']) && is_numeric($_POST['EXPECTMAYx'])) {
	$EXPECTMAYx = $_POST['EXPECTMAYx'];
} else {
	$EXPECTMAYx = 0;
}
if (!empty($_POST['EXPECTJUNx']) && is_numeric($_POST['EXPECTJUNx'])) {
	$EXPECTJUNx = $_POST['EXPECTJUNx'];
} else {
	$EXPECTJUNx = 0;
}
if (!empty($_POST['EXPECTJUIx']) && is_numeric($_POST['EXPECTJUIx'])) {
	$EXPECTJUIx = $_POST['EXPECTJUIx'];
} else {
	$EXPECTJUIx = 0;
}
if (!empty($_POST['EXPECTAUGx']) && is_numeric($_POST['EXPECTAUGx'])) {
	$EXPECTAUGx = $_POST['EXPECTAUGx'];
} else {
	$EXPECTAUGx = 0;
}
if (!empty($_POST['EXPECTSEPx']) && is_numeric($_POST['EXPECTSEPx'])) {
	$EXPECTSEPx = $_POST['EXPECTSEPx'];
} else {
	$EXPECTSEPx = 0;
}
if (!empty($_POST['EXPECTOCTx']) && is_numeric($_POST['EXPECTOCTx'])) {
	$EXPECTOCTx = $_POST['EXPECTOCTx'];
} else {
	$EXPECTOCTx = 0;
}
if (!empty($_POST['EXPECTNOVx']) && is_numeric($_POST['EXPECTNOVx'])) {
	$EXPECTNOVx = $_POST['EXPECTNOVx'];
} else {
	$EXPECTNOVx = 0;
}
if (!empty($_POST['EXPECTDECx']) && is_numeric($_POST['EXPECTDECx'])) {
	$EXPECTDECx = $_POST['EXPECTDECx'];
} else {
	$EXPECTDECx = 0;
}
if (!empty($_POST['EMAILx']) && is_string($_POST['EMAILx'])) {
	$EMAILx = htmlspecialchars($_POST['EMAILx'], ENT_QUOTES, 'UTF-8');
} else {
	$EMAILx = '';
}
if (!empty($_POST['AWPOOLINGx']) && is_numeric($_POST['AWPOOLINGx'])) {
	$AWPOOLINGx = $_POST['AWPOOLINGx'];
} else {
	$AWPOOLINGx = 10;
}
if (!empty($_POST['DIGESTMAILx']) && is_numeric($_POST['DIGESTMAILx'])) {
	$DIGESTMAILx = $_POST['DIGESTMAILx'];
} else {
	$DIGESTMAILx = 0;
}
if (!empty($_POST['FILTERx']) && is_string($_POST['FILTERx'])) {
	$FILTERx = htmlspecialchars($_POST['FILTERx'], ENT_QUOTES, 'UTF-8');
} else {
	$FILTERx = '';
}
if (empty($_POST['MAILWx'])) {
	$MAILWx = 'false';
} else {
	$MAILWx = 'true';
}
if (empty($_POST['SENDALARMSx'])) {
	$SENDALARMSx = 'false';
} else {
	$SENDALARMSx = 'true';
}
if (empty($_POST['SENDMSGSx'])) {
	$SENDMSGSx = 'false';
} else {
	$SENDMSGSx = 'true';
}
if (empty($_POST['NORESPMx'])) {
	$NORESPMx = 'false';
} else {
	$NORESPMx = 'true';
}
if (empty($_POST['LOGMAWx'])) {
	$LOGMAWx = 'false';
} else {
	$LOGMAWx = 'true';
}
if (!empty($_POST['VGRIDUTx']) && is_numeric($_POST['VGRIDUTx'])) {
	$VGRIDUTx = $_POST['VGRIDUTx'];
} else {
	$VGRIDUTx = 210;
}
if (!empty($_POST['VGRIDTx']) && is_numeric($_POST['VGRIDTx'])) {
	$VGRIDTx = $_POST['VGRIDTx'];
} else {
	$VGRIDTx = 251;
}
if (!empty($_POST['RISOTx']) && is_numeric($_POST['RISOTx'])) {
	$RISOTx = $_POST['RISOTx'];
} else {
	$RISOTx = 10;
}
if (!empty($_POST['ILEAKTx']) && is_numeric($_POST['ILEAKTx'])) {
	$ILEAKTx = $_POST['ILEAKTx'];
} else {
	$ILEAKTx = 20;
}
if (!empty($_POST['POAKEYx']) && is_string($_POST['POAKEYx'])) {
	$POAKEYx = htmlspecialchars($_POST['POAKEYx'], ENT_QUOTES, 'UTF-8');
} else {
	$POAKEYx = '';
}
if (!empty($_POST['POUKEYx']) && is_string($_POST['POUKEYx'])) {
	$POUKEYx = htmlspecialchars($_POST['POUKEYx'], ENT_QUOTES, 'UTF-8');
} else {
	$POUKEYx = '';
}
if (!empty($_POST['TLGRTOKx']) && is_string($_POST['TLGRTOKx'])) {
	$TLGRTOKx = htmlspecialchars($_POST['TLGRTOKx'], ENT_QUOTES, 'UTF-8');
} else {
	$TLGRTOKx = '';
}
if (!empty($_POST['TLGRCIDx']) && is_numeric($_POST['TLGRCIDx'])) {
	$TLGRCIDx = $_POST['TLGRCIDx'];
} else {
	$TLGRCIDx = 0;
}
if (!empty($_POST['bntsubmit'])) {
	$bntsubmit = $_POST['bntsubmit'];
} else {
	$bntsubmit = null;
}


/**
 *
 * @param unknown $adress
 * @return unknown
 */
function testemail($adress) {
	$Syntaxe = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
	if (preg_match($Syntaxe, $adress) || $adress == '')
		return true;
	else
		return false;
}


$Err = false;

if ($bntsubmit == 'Test mail') {
	if (!testemail($EMAILx) || empty($EMAILx)) {
		echo 'EMAIL is not correct<br>';
	} else {
		$sent = mail($EMAILx, "123Solar: Hello", "Hi,\r\n\r\nThanks for using 123Solar !", "From: \"123Solar\" <$EMAILx>");
		if ($sent) {
			echo "
    <br><div align=center><font color='#228B22'><b>Mail sent to $EMAILx</b></font>
    <br>&nbsp;
    <br>&nbsp;
    <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
    </div>";
		} else {
			echo "
    <br><div align=center><font color='#8B0000'><b>We encountered an error sending your mail</b></font>
    <br>&nbsp;
    <br>&nbsp;
    <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
    </div>";
		}
	}
} elseif ($bntsubmit == 'Test Pushover') {
	curl_setopt_array($ch = curl_init(), array(
			CURLOPT_URL => "https://api.pushover.net/1/messages.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => array(
				'token' => "$POAKEYx",
				'user' => "$POUKEYx",
				'title' => 'Hello',
				'message' => 'Thanks for using 123Solar !',
				'sound' => 'classical'
			)
		));
	$output = curl_exec($ch);
	curl_close($ch);

	if (preg_match('/"status":1/', $output)) {
		echo "
      <br><div align=center><font color='#228B22'><b>Push message send !</b></font>
      <br>&nbsp;
      <br>&nbsp;
      <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
      </div>";
	} else {
		echo "
      <br><div align=center><br><font color='#8B0000'><b>We encountered an error sending the message</b></font>
      <br>&nbsp;
      <br>$output;
      <br>&nbsp;
      <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
      </div>";
	}
} elseif ($bntsubmit == 'Test Telegram') {
	$msg = array('chat_id' => $TLGRCIDx, 'text' => 'Thanks for using 123Solar !');
	$ch = curl_init('https://api.telegram.org/bot'.$TLGRTOKx.'/sendMessage');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	$output = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($output, true);
	if ($output['ok']) {
		echo "
<br><div align=center><font color='#228B22'><b>Push message send !</b></font>
<br>
<br>
<INPUT TYPE='button' onClick=\"location.href='admin_invt.php?met_num=$invt_numx'\" value='Back'>
</div>";
	} else {
		echo "
      <br><div align=center><br><font color='#8B0000'><b>We encountered an error sending the message</b></font>
      <br>&nbsp;
	  <br>";
		print_r($output);
		echo "
	  <br>
      <br>&nbsp;
      <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'></div>";
	}
} elseif ($bntsubmit == 'Test communication') {
	if (file_exists('../scripts/123solar.pid')) {
		$pid     = (int) file_get_contents('../scripts/123solar.pid');
		$command = exec("kill -9 $pid > /dev/null 2>&1 &");
		unlink('../scripts/123solar.pid');
		usleep(500000);
	}

	$invt_num = 0;

	$RET  = '';
	$I1V  = null;
	$I1A  = null;
	$I1P  = null;
	$I2V  = null;
	$I2A  = null;
	$I2P  = null;
	$I3V  = null;
	$I3A  = null;
	$I3P  = null;
	$I4V  = null;
	$I4A  = null;
	$I4P  = null;
	$G1V  = null;
	$G1A  = null;
	$G1P  = null;
	$G2V  = null;
	$G2A  = null;
	$G2P  = null;
	$G3V  = null;
	$G3A  = null;
	$G3P  = null;
	$FRQ  = null;
	$EFF  = null;
	$INVT = null;
	$BOOT = null;
	$SSR  = null;
	$KWHT = null;

	if ($PHASEx == 'false') {
		$PHASE0 = false;
	} else {
		$PHASE0 = true;
	}

	$PORT0      = "$PORTx";
	$PROTOCOL0  = "$PROTOCOLx";
	$ADR0       = "$ADRx";
	$COMOPTION0 = "$COMOPTIONx";
	$DEBUG      = false;

	include "../scripts/protocols/$PROTOCOLx.php";

	if ($RET == 'OK') {
		echo "
    <div align=center>
    <br><br><font color='#228B22'><b>Communication is ok !</b></font>
    <br>&nbsp;
    <br>&nbsp;
    <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
    </div>
    <table width='40%' border=0 cellspacing=0 cellpadding=2 align='center'>
    <tr><td colspan= 3 align='left'><br><b>Command</b></td></tr>
    <tr><td colspan= 3 align='left'>$CMD_POOLING</td></tr>
    <tr><td colspan= 3 align='left'><br><b>Arrays values</b></td></tr>
    <tr><td> I1V : $I1V V </td><td> I1A : $I1A A </td><td> I1P : $I1P W</td></tr>
    <tr><td> I2V : $I2V V </td><td> I2A : $I2A A </td><td> I2P : $I2P W</td></tr>
    <tr><td> I3V : $I3V V </td><td> I3A : $I3A A </td><td> I3P : $I3P W</td></tr>
    <tr><td> I4V : $I4V V </td><td> I4A : $I4A A </td><td> I4P : $I4P W</td></tr>
    <tr><td colspan= 3 align='left'><br><b>Grid values</b></td></tr>
    <tr><td> G1V : $G1V V </td><td> G1A : $G1A A </td><td> G1P : $G1P W</td></tr>
    <tr><td> G2V : $G2V V </td><td> G2A : $G2A A </td><td> G2P : $G2P W</td></tr>
    <tr><td> G3V : $G3V V </td><td> G3A : $G3A A </td><td> G3P : $G3P W</td></tr>
    <tr><td colspan= 3 align='left'><br><b>Inverter values</b></td></tr>
    <tr><td> FRQ : $FRQ Hz </td><td> EFF : $EFF % </td><td> INVT : $INVT °</td></tr>
    <tr><td> BOOT : $BOOT °</td><td> KWHT : $KWHT kWh</td><td> Sensor : $SSR W/m²</td></tr>
    </table>";
	} else {
		echo "
    <br><div align=center>$CMD_POOLING return : $CMD_RETURN <br><br><font color='#8B0000'><b>error : /</b></font>
    <br>&nbsp;
    <br>&nbsp;
    <INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
    </div>";
	}

} else {
	if (!testemail($EMAILx)) {
		echo "EMAIL is not correct<br>";
		$Err = true;
	}

	if ($Err != true) {
		include "../config/config_invt" . $invt_numx . ".php";
		if (${'PROTOCOL' . $invt_numx} != $PROTOCOLx) { // Clearing options
			$COMOPTIONx = '';
		}

		$myFile = '../config/config_invt' . $invt_numx . '.php';
		$fh = fopen($myFile, 'w+') or die("<font color='#8B0000'><b>Can't open $myFile file. Configuration not saved !</b></font>");
		$stringData = "<?php
if(!defined('checkaccess')){die('Direct access not permitted');}

// ### GENERAL FOR INVERTER #$invt_numx
\$INVNAME$invt_numx=\"$INVNAMEx\";
// ### SPECS
\$PLANT_POWER$invt_numx=$PLANT_POWERx;
\$PHASE$invt_numx=$PHASEx;
\$CORRECTFACTOR$invt_numx=$CORRECTFACTORx;
\$PASSO$invt_numx=$PASSOx;
\$SR$invt_numx='$SRx';

// #### PROTOCOL
\$PORT$invt_numx='$PORTx';
\$PROTOCOL$invt_numx='$PROTOCOLx';
\$ADR$invt_numx='$ADRx';
\$COMOPTION$invt_numx='$COMOPTIONx';
\$SYNC$invt_numx=$SYNCx;
\$LOGCOM$invt_numx=$LOGCOMx;
\$SKIPMONITORING$invt_numx=$SKIPMONITORINGx;

// ### FRONT PAGE
\$YMAX$invt_numx=$YMAXx;
\$YINTERVAL$invt_numx=$YINTERVALx;

// ### INFO DETAILS
\$PANELS1$invt_numx=\"$PANELS1x\";
\$PANELS2$invt_numx=\"$PANELS2x\";

// ### DASHBOARD
\$ARRAY1_POWER$invt_numx=$ARRAY1_POWERx;
\$ARRAY2_POWER$invt_numx=$ARRAY2_POWERx;
\$ARRAY3_POWER$invt_numx=$ARRAY3_POWERx;
\$ARRAY4_POWER$invt_numx=$ARRAY4_POWERx;

// ### EXPECTED PRODUCTION
\$EXPECT1_$invt_numx=$EXPECTJANx;
\$EXPECT2_$invt_numx=$EXPECTFEBx;
\$EXPECT3_$invt_numx=$EXPECTMARx;
\$EXPECT4_$invt_numx=$EXPECTAPRx;
\$EXPECT5_$invt_numx=$EXPECTMAYx;
\$EXPECT6_$invt_numx=$EXPECTJUNx;
\$EXPECT7_$invt_numx=$EXPECTJUIx;
\$EXPECT8_$invt_numx=$EXPECTAUGx;
\$EXPECT9_$invt_numx=$EXPECTSEPx;
\$EXPECT10_$invt_numx=$EXPECTOCTx;
\$EXPECT11_$invt_numx=$EXPECTNOVx;
\$EXPECT12_$invt_numx=$EXPECTDECx;

// ### REPORT
\$EMAIL$invt_numx=\"$EMAILx\";

// ### CHECKS & NOTIFICATION
\$AWPOOLING$invt_numx=$AWPOOLINGx;
\$DIGESTMAIL$invt_numx=$DIGESTMAILx;
\$FILTER$invt_numx=\"$FILTERx\";
\$MAILW$invt_numx=$MAILWx;
\$SENDALARMS$invt_numx=$SENDALARMSx;
\$SENDMSGS$invt_numx=$SENDMSGSx;
\$NORESPM$invt_numx=$NORESPMx;
\$LOGMAW$invt_numx=$LOGMAWx;
\$VGRIDUT$invt_numx=$VGRIDUTx;
\$VGRIDT$invt_numx=$VGRIDTx;
\$RISOT$invt_numx=$RISOTx;
\$ILEAKT$invt_numx=$ILEAKTx;
\$POAKEY$invt_numx='$POAKEYx';
\$POUKEY$invt_numx='$POUKEYx';
\$TLGRTOK$invt_numx='$TLGRTOKx';
\$TLGRCID$invt_numx=$TLGRCIDx;

\$cfgver=$CFGinvt;
?>
";
		fwrite($fh, $stringData);
		fclose($fh);

		if (!file_exists('../data/invt' . $invt_numx . '/')) {
			mkdir("../data/invt$invt_numx", 0777);
		}
		chmod("../data/invt$invt_numx", 0777);
		if (!file_exists('../data/invt' . $invt_numx . '/csv')) {
			mkdir("../data/invt$invt_numx/csv", 0777);
		}
		chmod("../data/invt$invt_numx/csv", 0777);
		if (!file_exists('../data/invt' . $invt_numx . '/errors')) {
			mkdir("../data/invt$invt_numx/errors", 0777);
		}
		chmod("../data/invt$invt_numx/errors", 0777);
		if (!file_exists('../data/invt' . $invt_numx . '/infos')) {
			mkdir("../data/invt$invt_numx/infos", 0777);
		}
		chmod("../data/invt$invt_numx/infos", 0777);
		if (!file_exists('../data/invt' . $invt_numx . '/msgqueue')) {
			mkdir("../data/invt$invt_numx/msgqueue", 0777);
		}
		chmod("../data/invt$invt_numx/msgqueue", 0777);
		if (!file_exists('../data/invt' . $invt_numx . '/production')) {
			mkdir("../data/invt$invt_numx/production", 0777);
		}
		chmod("../data/invt$invt_numx/production", 0777);

		echo "
<br><div align=center><font color='#228B22'><b>Configuration for inverter #$invt_numx saved</b></font>
<br>&nbsp;
<br><font size='-1'>123Solar must be restarted for these changes to take effect</font>
<br>&nbsp;
<br>&nbsp;
<INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
</div>
";

	} else {
		echo "
<br><div align=center><font color='#8B0000'><b>Error configuration not saved !</b></font><br>
<INPUT TYPE='button' onClick=\"location.href='admin_invt.php?invt_num=$invt_numx'\" value='Back'>
</div>
";
	}

}
?>
<br>
<br>
<!-- #EndEditable -->
          </td>
          </tr>
</table>
</body>
</html>
