<?php
/**
 * /srv/http/123solar/info.php
 *
 * @package default
 */


include 'styles/globalheader.php';
if (!empty($_POST['invtnum']) && is_numeric($_POST['invtnum'])) {
	$invtnum = $_POST['invtnum'];
} else {
	$invtnum = 1;
}
include "config/config_invt$invtnum.php";
include 'config/config_pvoutput.php';
include 'config/memory.php';
include 'scripts/version.php';
echo "
<script type='text/javascript'>
  function updateit() {

  $.getJSON('programs/programloggerinfo.php', function(data){
  json = eval(data);
  document.getElementById('cpu').value= json.cpuuse;
  document.getElementById('uptime').innerHTML = json.uptime;
  document.getElementById('cpuuse').innerHTML = json.cpuuse;
  document.getElementById('memtot').innerHTML = json.memtot;
  document.getElementById('mem').max= json.memtot;
  document.getElementById('mem').value= json.memuse;
  document.getElementById('mem').high = (json.memtot*0.85);
  document.getElementById('memfree').innerHTML = json.memfree;
  document.getElementById('diskuse').innerHTML = json.diskuse;
  document.getElementById('diskfree').innerHTML = json.diskfree;
  })
  }
  $(document).ready(function() {
  updateit();
  setInterval(updateit, 1000);
  })
</script>";

if ($NUMINV > 1) {
	$currentFile = $_SERVER["PHP_SELF"];
	echo "<table width='95%' border=0 align=center cellpadding=0 CELLSPACING=0>
<tr><td>
<form method='POST' action='$currentFile'><select name='invtnum' onchange='this.form.submit()'>";
	for ($i = 1; $i <= $NUMINV; $i++) {
		if ($invtnum == $i) {
			echo "<option SELECTED value=$i>";
		} else {
			echo "<option value=$i>";
		}
		echo "$lgINVT$i</option>";
	}
	echo '</select></form></td></tr></table>';
}

$PLANT_POWER = number_format(${'PLANT_POWER' . $invtnum}, 0, $DPOINT, $THSEP);

$KWHP = 0;
if (file_exists($LIVEMEMORY)) {
	$data     = file_get_contents($LIVEMEMORY);
	$livememarray = json_decode($data, true);

	if (isset($livememarray["KWHT$invtnum"])) {
		$KWHP = $livememarray["KWHT$invtnum"] * ${'CORRECTFACTOR' . $invtnum};
	}
}
$CO2 = (($KWHP / 1000) * 490);
if ($CO2 > 1000) {
	$CO2v = 't';
	$CO2  = number_format(($CO2 / 1000), 2, $DPOINT, $THSEP);
} else {
	$CO2v = 'kg';
	$CO2  = number_format(($CO2), 1, $DPOINT, $THSEP);
}
$carbon = $KWHP * 0.45;
if ($carbon > 1000) {
	$carbonv = 'kg';
	$carbon  = number_format(($carbon / 1000), 1, $DPOINT, $THSEP);
} else {
	$carbonv = 'g';
	$carbon  = number_format(($carbon), 0, $DPOINT, $THSEP);
}
$KWHP = number_format($KWHP, 1, $DPOINT, $THSEP);

$info     = '';
$filename = "data/invt$invtnum/infos/infos.txt";
if (file_exists($filename)) {
	$lines      = file($filename);
	$contalines = count($lines);
	for ($i = 0; $i < $contalines; $i++) {
		$isemptyline = trim($lines[$i]);
		if (!empty($isemptyline)) {
			$info .= nl2br($lines[$i]);
		}
	}
	$updtd = date('d M H:i', filemtime($filename));
} else {
	$info  = 'no info file found';
	$updtd = '';
}

$events   = '';
$filename = "data/invt$invtnum/infos/events.txt";
if (file_exists($filename)) {
	$handle = fopen($filename, 'r');
	$events = fread($handle, filesize($filename));
	fclose($handle);
} else {
	$events = 'no events file found';
}

echo "
<table width='95%' border=0 align=center cellpadding=0 CELLSPACING=20>
<tr valign='top'>
<td width='50%'><img src='images/24/pin.png' width=24 height=24 border=0><b>&nbsp;$lgPLANTINFO</b><br>
<hr align=left size=1 width='90%'>
$lgPLANTPOWER : <a href='http://maps.google.com/maps?q=$LATITUDE,$LONGITUDE'>$PLANT_POWER W</a><br>
${'PANELS1'.$invtnum}<br>
${'PANELS2'.$invtnum}<br>
</td>
<td width='50%'><img src='images/24/lightning.png' width=24 height=24 border=0><b>&nbsp;$lgCOUNTER</b>
<br><hr align=left size=1 width='90%'>
$lgTOTALPROD : $KWHP kWh<br>
$lgECOSAVE : $CO2 $CO2v"."CO<sub>2</sub>&nbsp;<img style='cursor:help;' src='images/info10.png' width=10 height=10 border=0 title='Gas turbine-electric 490gCO2eq/kWh (Source: IPCC 2014)'>
<br>
$lgECOPROD : $carbon $carbonv"."CO<sub>2</sub>&nbsp;<img style='cursor:help;' src='images/info10.png' width=10 height=10 border=0 title='Solar PV 45gCO2eq/kWh (Source: IPCC 2014)'>
</td></tr>
<tr valign='top'>
<td width='50%'>
<img src='images/24/monitor.png' width=24 height=24 border=0><b>&nbsp;$lgINVERTERINFO</b>&nbsp;<img style='cursor:help;' src='images/info10.png' width=10 height=10 border=0 title=\"$lgINVERTERINFOB $updtd)\"'>
<br><hr align=left size=1 width='90%'>
$info
</td>
<td width='50%'><img src='images/24/cog.png' width=24 height=24 border=0><b>&nbsp;$lgLOGGERINFO</b>
<br><hr align=left size=1 width='90%'>
Uptime : <span id='uptime'>--</span>
<br>OS: ";
echo exec('uname -ors');
echo "<br>System: ";
echo exec('uname -nmi');
echo exec("cat /proc/cpuinfo | grep 'Processor' | head -n 1");
echo "
<meter id='cpu' high=85 min=0 max=100></meter> <span id='cpuuse'>--</span>%
<br>Memory : <span id='memtot'>--</span>MB
<meter id='mem' min='0'></meter> <font size='-1'>(<span id='memfree'>--</span>MB free)</font>
<br>Disk Usage : <span id='diskuse'>--</span>/<span id='diskfree'>--</span> avail.
<br>Software : $VERSION
</td></tr>
<tr valign='top'><td>
<img src='images/24/calendar-clock.png' width=24 height=24 border=0><b>&nbsp;$lgEVENTS</b>
<br><hr align=left size=1 width='90%'>
<textarea style='resize: none;background-color: #DCDCDC' cols=70 rows=10>$events</textarea>";
if (${'LOGMAW' . $invtnum}) {
	echo "<br><a href='data/invt$invtnum/infos/checks_log.txt'>Measures log</a> | <a href='data/invt$invtnum/infos/checks_status.txt'>Alarms/warnings log</a>";
}
echo "
</td><td width='50%'>";
if ($NUMPVO > 0) {
	echo "<img src='images/24/globe.png' width=24 height=24 border=0><b>&nbsp;PVoutput</b> <font size='-1'><a href='http://pvoutput.org/listteam.jsp?tid=317'>(123Solar Team)</a></font>
<br><hr align=left size=1 width='90%'>
<br>
<script src='https://pvoutput.org/widget/inc.jsp'></script>";
	for ($i = 1; $i <= $NUMPVO; $i++) {
		if (${'PVOUTPUT' . $i . $invtnum}) {
			echo "<script src='https://pvoutput.org/widget/graph.jsp?sid=${'SYSID'.$i}&w=200&h=80&n=1&d=1&t=1&c=1'></script>";
			if ($i & 1) {
				echo "<br>";
			}
		}
	}
}
echo '
</td></tr>
</table>';
include "styles/$STYLE/footer.php";
?>
