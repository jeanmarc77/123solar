<?php
/**
 * /srv/http/123solar/admin/admin_main.php
 *
 * @package default
 */


include 'secure.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
echo "
<div align=center><br><form action='admin_main2.php' method='post'>
<fieldset style='width:80%;'>
<legend><b>Main configuration</b></legend>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td>Number of inverter(s) <input type='number' name='NUMINV2' value='$NUMINV' min='1' max='64' style='width:40px'></td>
<td>Linux distro
<select name='DISTRO2'>
";

$dir    = '../scripts/distros/';
$output = glob("$dir*.php");
sort($output);
$cnt = count($output);

for ($i = 0; $i < $cnt; $i++) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr($output[$i], 0, -4);
	if ($DISTRO == $option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo "
</select>
</td>
<td>
Debug
<select name='DEBUG2' title='Dont leave this option permanently.'>";
if ($DEBUG) {
	echo "<option SELECTED value='true'>Yes</option><option value='false'>No</option>";
} else {
	echo "<option value='true'>Yes</option><option SELECTED value='false'>No</option>";
}
echo "
</select>
</td>
</tr>
</table>
<table border=0 cellspacing=5 cellpadding=0 width='100%' align='center'>
<tr><td colspan=2><b>Localization : </b></td></tr>
<tr><td>
Longitude <input type='number' id='longval' step='any' name='LONGITUDE2' value='$LONGITUDE' maxlength=6 style='width:70px'> Latitude <input type='number' id='latval' step='any' name='LATITUDE2' value='$LATITUDE' maxlength=6 style='width:70px'> <a href=\"https://epsg.io/map#x=$LONGITUDE&y=$LATITUDE&z=12&layer=streets\" target='_blank'><img src='../images/link.png' width='16' height='16' border='0'></a></td>
<td>Date format
<input type='text' name='DATEFORMAT2' value='$DATEFORMAT' size=4 title='For events log and messages. It must be a in valid php format'>
</td></tr>
<tr><td>TimeZone
<select name='DTZ2'>";
$timezone_identifiers = DateTimeZone::listIdentifiers();
$cnt                  = count($timezone_identifiers);
for ($i = 0; $i < $cnt; $i++) {
	if ($DTZ == $timezone_identifiers[$i]) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$timezone_identifiers[$i]</option>";
}
echo "</select>
</td><td>
Decimal mark <select name='DPOINT2'>";
if ($DPOINT == ',') {
	echo "<option value='.'>dot</option>";
	echo "<option value=',' SELECTED>comma</option>";
} else {
	echo "<option value='.' SELECTED>dot</option>";
	echo "<option value=','>comma</option>";
}
echo "</select>
Thousands separator  <select name='THSEP2'>";
$THlist = array(
	"",
	".",
	",",
	" "
);
$cnt    = count($THlist);
for ($i = 0; $i < $cnt; $i++) {
	if ($THSEP == $THlist[$i]) {
		echo "<option SELECTED ";
	} else {
		echo "<option ";
	}
	if ($THlist[$i] == "") {
		echo "value=''>null</option>";
	} elseif ($THlist[$i] == " ") {
		echo "value=' '>space</option>";
	} else {
		echo "value='$THlist[$i]'>$THlist[$i]</option>";
	}
}

echo "</select>
</td></tr>
<tr><td colspan=2><b>Web pages : </b></td></tr>
<tr><td>Title <input type='text' name='TITLE2' value=\"$TITLE\" size=50></td>
<td>Subtitle <input type='text' name='SUBTITLE2' value=\"$SUBTITLE\" size=50></td>
</tr>
<tr><td>Style
<select name='STYLEx'>
";
$output = scandir('../styles/');
sort($output);
$cnt=count($output);
$j=0;
for ($i=0;$i<$cnt;$i++) {
	if ($output[$i]!='.' && $output[$i]!='..' && !preg_match('/\.php$/i', $output[$i]) ) {
		$style[$j]=$output[$i];
		$j++;
	}
}
sort($style);
$cnt=count($style);
for ($i = 0; $i < $cnt; $i++) {
	if ($STYLE==$style[$i]) {
		echo "<option selected>";
	} else {
		echo "<option>";
	}
	echo "$style[$i]</option>";
}
echo "
</select>
</td>
<td>Language
<select name='LANGx'>
";
$dir    = '../languages/';
$output = glob("$dir*.php");
sort($output);
$cnt = count($output);
for ($i = 0; $i < $cnt; $i++) {
	$output[$i] = str_replace("$dir", '', "$output[$i]");
	$option     = substr($output[$i], 0, -4);
	if ($LANG == $option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo "
</select>
</td></tr>
<tr><td colspan=2><b>Daily cleanup : </td></tr>
<tr></td><td>Keep <input type='number' name='KEEPDDAYS2' value='$KEEPDDAYS' maxlength='4' min='0' style='width:60px' title='0 is unlimited'>fully detailed days
<td>Maintain logs size to <input type='number' name='AMOUNTLOG2' value='$AMOUNTLOG' maxlength='4' min='1000' max='50000' style='width:60px'>lines</td></tr>
</table>
</fieldset>
<div align=center><br><INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>&nbsp;<input type='submit' value='Save main cfg.'></div>
</form>";
?>
<br>
<br>
          <!-- #EndEditable -->
          </td>
          </tr>
</table>
</body>
</html>
