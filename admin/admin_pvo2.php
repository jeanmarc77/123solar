<?php
/**
 * /srv/http/123solar/admin/admin_pvo2.php
 *
 * @package default
 */


include 'secure.php';
include '../scripts/version.php';
include '../config/config_pvoutput.php';
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
if (!empty($_POST['NUMPVO2']) && is_numeric($_POST['NUMPVO2'])) {
	$NUMPVO2 = $_POST['NUMPVO2'];
} else {
	$NUMPVO2 = 0;
}

$SYSID2  = array();
$APIKEY2 = array();
$PVOC2   = array();
$PVOT2   = array();

for ($i = 1; $i <= $NUMPVO; $i++) {
	if (!empty($_POST["SYSID2$i"]) && is_numeric($_POST["SYSID2$i"])) {
		$SYSID2[$i] = $_POST["SYSID2$i"];
	} else {
		$SYSID2[$i] = '0';
	}
	if (!empty($_POST["APIKEY2$i"]) && is_string($_POST["APIKEY2$i"])) {
		$APIKEY2[$i] = htmlspecialchars($_POST["APIKEY2$i"], ENT_QUOTES, 'UTF-8');
	} else {
		$APIKEY2[$i] = '';
	}
	if (!empty($_POST["PVOC2$i"]) && is_string($_POST["PVOC2$i"])) {
		$PVOC2[$i] = htmlspecialchars($_POST["PVOC2$i"], ENT_QUOTES, 'UTF-8');
	} else {
		$PVOC2[$i] = 'no';
	}
	if (!empty($_POST["PVOT2$i"]) && is_string($_POST["PVOT2$i"])) {
		$PVOT2[$i] = htmlspecialchars($_POST["PVOT2$i"], ENT_QUOTES, 'UTF-8');
	} else {
		$PVOT2[$i] = 'no';
	}
	settype(${'PVOEXT2' . $i}, 'bool');
	if (!empty($_POST["PVOEXT2$i"])) {
		${'PVOEXT2' . $i} = true;
	} else {
		${'PVOEXT2' . $i} = false;
	}
	for ($j = 1; $j <= $NUMINV; $j++) {
		settype(${'PVOUTPUT2' . $i . $j}, 'bool');
		if (!empty($_POST["PVOUTPUT2$i$j"])) {
			${'PVOUTPUT2' . $i . $j} = true;
		} else {
			${'PVOUTPUT2' . $i . $j} = false;
		}
	}
}

$Err = false;
if ($Err != true) {
	$myFile = '../config/config_pvoutput.php';
	$fh = fopen($myFile, 'w+') or die("<font color='#8B0000'><b>Can't open $myFile file. Configuration not saved !</b></font>");
	$stringData = "<?php
if(!defined('checkaccess')){die('Direct access not permitted');}

// ### PVOUTPUT.org
\$NUMPVO=$NUMPVO2;";

	for ($i = 1; $i <= $NUMPVO; $i++) {
		$stringData .= "

\$SYSID$i='$SYSID2[$i]';
\$APIKEY$i='$APIKEY2[$i]';
\$PVOC$i='$PVOC2[$i]';
\$PVOT$i='$PVOT2[$i]';
";
		$stringData .= "\$PVOEXT$i=";
		if (${'PVOEXT2' . $i}) {
			$stringData .= 'true;';
			$extfile = "../config/pvoutput/extended/extended$i.php";
			if (!file_exists($extfile)) {
				$fhext         = fopen($extfile, 'w+');
				$stringData2 = "<?php
if(!defined('checkaccess')){die('Direct access not permitted');}
// ## Extended data for SYSID $SYSID2[$i]
\$pvo_v7=round(\$array2[16], 1);
\$pvo_v8=round(\$array2[19], 1);
\$pvo_v9=null;
\$pvo_v10=null;
\$pvo_v11=null;
\$pvo_v12=null;
?>";
				fwrite($fhext, $stringData2);
				fclose($fhext);
			}
		} else {
			$stringData .= 'false;';
		}
		$stringData .= "\n";
		for ($j = 1; $j <= $NUMINV; $j++) {
			$stringData .= "\$PVOUTPUT$i$j=";
			if (${'PVOUTPUT2' . $i . $j}) {
				$stringData .= 'true;';
			} else {
				$stringData .= 'false;';
			}
			$stringData .= "\n";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://pvoutput.org/service/r2/jointeam.jsp');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'X-Pvoutput-Apikey: ' . $APIKEY2[$i],
				'X-Pvoutput-SystemId: ' . $SYSID2[$i]
			));
		$params = 'tid=317';
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$pvo = curl_exec($ch);
		curl_close($ch);
	}
	$stringData .= "
\$cfgver=$CFGpvo;
?>
";
	fwrite($fh, $stringData);
	fclose($fh);

	echo "
<br><div align=center><font color='#228B22'><b>Configuration for PVoutput saved</b></font>
<br>&nbsp;
<br><font size='-1'>123Solar must be restarted for these changes to take effect</font>
<br>&nbsp;
<br>&nbsp;
<INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>
</div>
";
} else {
	echo "
<br><div align=center><font color='#8B0000'><b>Error configuration not saved !</b></font><br>
<INPUT TYPE='button' onClick=\"location.href='admin_pvo.php'\" value='Back'>
</div>
";
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
