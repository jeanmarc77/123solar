<?php
/**
 * /srv/http/123solar/admin/admin_pvo.php
 *
 * @package default
 */


include 'secure.php';
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
echo "
<br>
<div align=center><form action='admin_pvo2.php' method='post'>
<fieldset style='width:90%;'>
<legend><b>PVoutput configuration</b></legend>
<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
<tr><td>Number of PVoutput system(s) <input type='number' name='NUMPVO2' value='$NUMPVO' min='0' max='$NUMINV' style='width:40px'></td></tr>
</table>
";
if ($NUMPVO > 0) {
	echo "
<hr>
<table border='0' cellspacing='5' cellpadding='0' width='100%' align='center'>
<tr><td>Sys. ID</td><td>API key</td><td>Consumption</td><td>Temperature</td><td>Extended data</td><td>Inverter(s)</td></tr>";

	for ($i = 1; $i <= $NUMPVO; $i++) {
		echo "
<tr><td><input type='text' size=3 name='SYSID2$i' value='${'SYSID' . $i}'></td>";
		echo "
<td><input type='text' size=42 name='APIKEY2$i' value=\"${'APIKEY' . $i}\" title='See in the PVoutput API settings'></td>
<td><select name='PVOC2$i' title='Feed PVoutput with consumption'>";
		$dir = '../config/pvoutput/consumption/';
		if (${'PVOC' . $i} == 'no') {
			echo "<option SELECTED>";
		} else {
			echo "<option>";
		}
		echo "no</option>";

		$output = glob("$dir*.php");
		sort($output);
		$cnt = count($output);

		for ($j = 0; $j < $cnt; $j++) {
			$output[$j] = str_replace("$dir", '', "$output[$j]");
			$option     = substr_replace($output[$j], "", -4);
			if (${'PVOC' . $i} == $option) {
				echo '<option SELECTED>';
			} else {
				echo '<option>';
			}
			echo "$option</option>";
		}
		echo "
</select>
</td>
<td><select name='PVOT2$i' title='Choose temperature'>";
		$dir    = '../config/pvoutput/temperature/';
		$output = glob("$dir*.php");
		sort($output);
		$cnt = count($output);

		for ($j = 0; $j < $cnt; $j++) {
			$output[$j] = str_replace("$dir", '', "$output[$j]");
			$option     = substr_replace($output[$j], "", -4);
			if (${'PVOT' . $i} == $option) {
				echo "<option SELECTED>";
			} else {
				echo "<option>";
			}
			echo "$option</option>";
		}
		echo "
</select>
</td>
<td>";
		if (${'PVOEXT' . $i}) {
			echo "<input type='checkbox' name='PVOEXT2$i' value='true' checked>";
		} else {
			echo "<input type='checkbox' name='PVOEXT2$i' value='false'>";
		}
		echo "
</td>
<td>";

		for ($j = 1; $j <= $NUMINV; $j++) {
			if (${'PVOUTPUT' . $i . $j}) {
				echo "<input type='checkbox' name='PVOUTPUT2$i$j' value='true' checked>$j";
			} else {
				echo "<input type='checkbox' name='PVOUTPUT2$i$j' value='false'>$j";
			}
		}
		echo "
</td></tr>
";
	}
	echo "
</table>";
}
echo "
</fieldset>
<div align=center><br><INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>&nbsp;<input type='submit' value='Save PVoutput cfg.'></div>
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
