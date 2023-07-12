<?php
/**
 * /srv/http/123solar/admin/admin.php
 *
 * @package default
 */


include 'secure.php';
include '../config/config_main.php';
include '../scripts/distros/' . $DISTRO . '.php';
include '../config/memory.php';
include '../scripts/version.php';
include '../scripts/links.php';
$url = 'https://raw.githubusercontent.com/jeanmarc77/123solar/main/misc/latest_version.json';

if (isset($_SERVER["PHP_AUTH_USER"])) {
	$me = $_SERVER["PHP_AUTH_USER"];
} else {
	$me = 'unknown';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123Solar Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
<?php
echo "<script src='$JSjquery'></script>";
?>
<script type='text/javascript'>
$(document).ready(function()
{
var vers='<?php
echo $VERSION;
?>';

$.ajax({
    url : '<?php
echo $url;
?>',
    dataType: 'json',
    type: 'GET',
    success: function(response){
	json =eval(response);
	lastvers =json['LASTVERSION'];

	if (vers!=lastvers) {
	document.getElementById('status').src = '../images/24/sign-warning.png';
	document.getElementById('msg').innerHTML = '<img src=\'../styles/default/images/sqe.gif\'><a href=\'update.php\'>Update</a>';
	} else {
	document.getElementById('status').src = '../images/24/sign-check.png';
	document.getElementById('msg').innerHTML = '';
	}
    },
    error: function(){
	document.getElementById('status').src = '../images/24/sign-question.png';
        document.getElementById('msg').innerHTML = '';
    },
    timeout: 3000
});

})
</script>
</head>
<body>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#FFFFFF" height="80">
  <td class="cadretopleft" width="128"><img src="../styles/default/images/sun12880.png" width="128" height="80" alt="123Solar"></td>
  <td class="cadretop" align="center"><b>123Solar Administration</font></td>
  <td class="cadretopright" width="128" align="right"></td>
  </tr>
  <tr bgcolor="#CCCC66">
<td COLSPAN="3" class="cadre" height="10">
&nbsp;
</td></tr>
<tr valign="top">
    <td COLSPAN="3" class="cadrebot" bgcolor="#d3dae2">
<!-- #BeginEditable "mainbox" -->
<br>
<div align=center><b>Welcome <?php
echo $me;
?></b></div>
<hr>
<br>&nbsp;
<div align=center><span id='messageSpan'></span></div>
<?php
$err_cfg = false;
$err_txt = '';
if ($cfgver < $CFGmain) {
	$err_txt .= " config_main";
	$err_cfg = true;
}
for ($i = 1; $i <= $NUMINV; $i++) {
	include "../config/config_invt" . $i . ".php";
	if ($cfgver < $CFGinvt) {
		$err_txt .= " config_invt$i";
		$err_cfg = true;
	}
}
include '../config/config_pvoutput.php';
if ($cfgver < $CFGpvo ) {
	$err_txt .= " config_pvoutput";
	$err_cfg = true;
}

date_default_timezone_set($DTZ);

if (!empty($_GET['startstop'])) {
	$startstop = $_GET['startstop'];
} else {
	$startstop = null;
}
$PIDd = 'stop';
if (file_exists('../scripts/123solar.pid')) {
	$PIDd = date("$DATEFORMAT H:i:s", filemtime('../scripts/123solar.pid'));
	$PID = (int) file_get_contents('../scripts/123solar.pid');
	exec("$PSCMD | grep $PID | grep 123solar.php", $ret);
	if (!isset($ret[1])) {
		$PID = null;
		unlink('../scripts/123solar.pid');
	}
} else {
	$PID = null;
}

if ($startstop == 'start' || $startstop == 'stop') {
	$now = date($DATEFORMAT . ' H:i:s');
	if ($startstop == 'start' && is_null($PID)) {
		if ($DEBUG) {
			$myFile     = '../data/123solar.err';
			$command    = 'php ../scripts/123solar.php' . ' >> ../data/123solar.err 2>&1 & echo $!; ';
			$PID        = exec($command);
			$stringData = "#* $now\tStarting 123Solar debug ($PID)\n\n";
			if (!file_put_contents('../scripts/123solar.pid', $PID)) {
				$stringData .= "\n\nCan't write scripts/123solar.pid, you might need to restart php\n\n";
				exec('pkill -f 123solar.php');
			}
			file_put_contents($myFile, $stringData, FILE_APPEND);
		} else {
			$output=null;
			exec("systemctl is-enabled 123solar.service",$output);
			if (is_dir('/run/systemd/system') && ($output[0] == "enabled")) {
				exec("$PSCMD | grep $PID | grep 123solar.php", $ret);
				if (!isset($ret[1])) { // avoid several instances
				$command = exec("sudo systemctl start 123solar.service");
				}
			} else {
				$command = 'php ../scripts/123solar.php' . ' > /dev/null 2>&1 & echo $!;';
				$PID     = exec($command);
				file_put_contents('../scripts/123solar.pid', $PID);
			}
		}
		for ($i = 1; $i <= $NUMINV; $i++) {
			if ($DEBUG) {
				$stringData = "#* $now\tStarting 123Solar debug ($PID)\n\n";
			} else {
				$stringData = "#* $now\tStarting 123Solar ($PID)\n\n";
			}
			if (${'SKIPMONITORING' . $i}) {
				$stringData .= "#$i $now\tInverter down for maintenance\n\n";
			}
			$INVTDIR = "../data/invt$i/";
			$stringData .= file_get_contents($INVTDIR . 'infos/events.txt');
			file_put_contents($INVTDIR . 'infos/events.txt', $stringData);
		}
	}
	if ($startstop == 'stop') {
		if (!is_null($PID)) {
			$output=null;
			exec("systemctl is-enabled 123solar.service",$output);
			if (is_dir('/run/systemd/system') && ($output[0] == "enabled")) {
				$command = exec("sudo systemctl stop 123solar.service");
			} else {
				$command = exec("kill $PID > /dev/null 2>&1 &");
				unlink('../scripts/123solar.pid');
			}
			if ($DEBUG) {
				$stringData = "#* $now\tStopping 123Solar debug ($PID)\n\n";
				$myFile     = '../data/123solar.err';
				file_put_contents($myFile, $stringData, FILE_APPEND);
			}
			for ($i = 1; $i <= $NUMINV; $i++) {
				$stringData = "#* $now\tStopping 123Solar ($PID)\n\n";
				$INVTDIR    = "../data/invt$i/";
				$stringData .= file_get_contents($INVTDIR . 'infos/events.txt');
				file_put_contents($INVTDIR . 'infos/events.txt', $stringData);
			}
		}
		$PID = null;
		unlink($LIVEMEMORY);
		$data               = file_get_contents($MEMORY);
		$memarray           = json_decode($data, true);
		$memarray['status'] = '123Solar stopped';
		$data               = json_encode($memarray);
		file_put_contents($MEMORY, $data);
	}
	echo "
<script type='text/javascript'>
  document.getElementById('messageSpan').innerHTML = \"...Please wait...<br><img src=\'../images/loading.gif\'>\";
  setTimeout(function () {
    window.location.href = 'admin.php?startstop=done';
  }, 1000);
</script>
";
}
echo "
<table border=0 align='center' width='80%'>
<tr><td align='left'>";

if ($startstop != 'start' && $startstop != 'stop') {
	echo "<form action='admin.php' method='GET'>";
	if (is_null($PID)) {
		echo "<input type='image' src='../images/off.png' value='' width=121 height=57>
		<input type='hidden' name='startstop' value='start'>";
	} else {
		echo "<input type='image' src='../images/on.png' value='' title='123s run as pid $PID since $PIDd' width=121 height=57 onclick=\"if(!confirm('Stop 123Solar ?')){return false;}\">
		<input type='hidden' name='startstop' value='stop'>";
	}
	if ($err_cfg) {
		echo "<br><img src='../images/24/sign-error.png' width='24' height='24' border='0'> Your config need to be updated ! Please, check and save : $err_txt file(s)";
	}
	echo "</form>
<br><img src='../styles/default/images/sqe.gif'><a href='admin_main.php'>Main configuration</a>
<br><br><img src='../styles/default/images/sqe.gif'><a href='admin_invt.php'>Inverter(s) configuration</a>
<br><br><img src='../styles/default/images/sqe.gif'><a href='admin_pvo.php'>PVoutput configuration</a> <a href='http://www.pvoutput.org/listteam.jsp?tid=317' target='_blank'><img src='../images/link.png' width=16 height=16 border=0></a>
<br><br><img src='../styles/default/images/sqe.gif'><a href='help.php'>Help and debugger</a>
<br><br><span id='msg'><span>
<br>
<br>
</tr></td>
</table>
<div align=center>
<INPUT TYPE='button' onClick=\"location.href='../'\" value='Back'>
</div>
<hr>
<table border=0 cellspacing=0 cellpadding=0 width='100%' align=center>
<tr valign=top><td></td>
<td width='33%'>
<div align=center><a href='kiva.html'>123solar is free !</a></div>
</td>
<td width='33%' align=right><a href='update.php'><img src='../images/24/sign-sync.png' id='status' width=24 height=24> $VERSION</a></td>
</tr>
</table>
";
}
?>
          <!-- #EndEditable -->
          </td>
          </tr>
</table>
</body>
</html>
