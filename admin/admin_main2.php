<?php
/**
 * /srv/http/123solar/admin/admin_main2.php
 *
 * @package default
 */


include 'secure.php';
include '../scripts/version.php';
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
if (!empty($_POST['NUMINV2']) && is_numeric($_POST['NUMINV2'])) {
	$NUMINV2 = $_POST['NUMINV2'];
}
if (!empty($_POST['DISTRO2']) && is_string($_POST['DISTRO2'])) {
	$DISTRO2 = htmlspecialchars($_POST['DISTRO2'], ENT_QUOTES, 'UTF-8');
}
if (!empty($_POST['DEBUG2'])) {
	$DEBUG2 = $_POST['DEBUG2'];
} else {
	$DEBUG2 = 'false';
}
if (!empty($_POST['LATITUDE2']) && is_numeric($_POST['LATITUDE2'])) {
	$LATITUDE2 = $_POST['LATITUDE2'];
} else {
	$LATITUDE2 = 50.61;
}
if (!empty($_POST['LONGITUDE2']) && is_numeric($_POST['LONGITUDE2'])) {
	$LONGITUDE2 = $_POST['LONGITUDE2'];
} else {
	$LONGITUDE2 = 4.635;
}
if (!empty($_POST['DTZ2']) && is_string($_POST['DTZ2'])) {
	$DTZ2 = htmlspecialchars($_POST['DTZ2'], ENT_QUOTES, 'UTF-8');
}
if (!empty($_POST['DATEFORMAT2']) && is_string($_POST['DATEFORMAT2'])) {
	$DATEFORMAT2 = htmlspecialchars($_POST['DATEFORMAT2'], ENT_QUOTES, 'UTF-8');
} else {
	$DATEFORMAT2 = 'd/m/Y';
}
if (!empty($_POST['DPOINT2']) && is_string($_POST['DPOINT2'])) {
	$DPOINT2 = htmlspecialchars($_POST['DPOINT2'], ENT_QUOTES, 'UTF-8');
} else {
	$DPOINT2 = ',';
}
if (!empty($_POST['THSEP2']) && is_string($_POST['THSEP2'])) {
	$THSEP2 = htmlspecialchars($_POST['THSEP2'], ENT_QUOTES, 'UTF-8');
} else {
	$THSEP2 = '.';
}
if (!empty($_POST['TITLE2']) && is_string($_POST['TITLE2'])) {
	$TITLE2 = htmlspecialchars($_POST['TITLE2'], ENT_QUOTES, 'UTF-8');
} else {
	$TITLE2 = '';
}
if (!empty($_POST['SUBTITLE2']) && is_string($_POST['SUBTITLE2'])) {
	$SUBTITLE2 = htmlspecialchars($_POST['SUBTITLE2'], ENT_QUOTES, 'UTF-8');
} else {
	$SUBTITLE2 = '';
}
if (!empty($_POST['STYLEx']) && is_string($_POST['STYLEx'])) {
	$STYLEx = htmlspecialchars($_POST['STYLEx'], ENT_QUOTES, 'UTF-8');
} else {
	$STYLEx = 'default';
}
if (!empty($_POST['LANGx']) && is_string($_POST['LANGx'])) {
	$LANGx = htmlspecialchars($_POST['LANGx'], ENT_QUOTES, 'UTF-8');
} else {
	$LANGx = 'English';
}
if (!empty($_POST['KEEPDDAYS2']) && is_numeric($_POST['KEEPDDAYS2'])) {
	$KEEPDDAYS2 = $_POST['KEEPDDAYS2'];
} else {
	$KEEPDDAYS2 = 0;
}
if (!empty($_POST['AMOUNTLOG2']) && is_numeric($_POST['AMOUNTLOG2'])) {
	$AMOUNTLOG2 = $_POST['AMOUNTLOG2'];
} else {
	$AMOUNTLOG2 = 2500;
}
if (!is_numeric($LATITUDE2)) {
	echo "LATITUDE value not correct<br>";
	$Err = true;
}
if (!is_numeric($LONGITUDE2)) {
	echo "LONGITUDE value not correct<br>";
	$Err = true;
}

if (!isset($Err)) {
	$Err = false;
}
if ($Err != true) {
	$myFile = '../config/config_main.php';
	$fh = fopen($myFile, 'w+') or die("<font color='#8B0000'><b>Can't open $myFile file. Configuration not saved !</b></font>");
	$stringData = "<?php
if(!defined('checkaccess')){die('Direct access not permitted');}
// ### GENERAL
\$NUMINV=$NUMINV2;
\$DISTRO='$DISTRO2';
\$DEBUG=$DEBUG2;

// ### LOCALIZATION
\$DTZ='$DTZ2';
\$LATITUDE=$LATITUDE2;
\$LONGITUDE=$LONGITUDE2;
\$DATEFORMAT='$DATEFORMAT2';
\$DPOINT='$DPOINT2';
\$THSEP='$THSEP2';

// ### WEB PAGE
\$TITLE=\"$TITLE2\";
\$SUBTITLE=\"$SUBTITLE2\";
\$STYLE=\"$STYLEx\";
\$LANG=\"$LANGx\";

// ### CLEANUP
\$KEEPDDAYS=$KEEPDDAYS2;
\$AMOUNTLOG=$AMOUNTLOG2;

\$cfgver=$CFGmain;
?>
";
	fwrite($fh, $stringData);
	fclose($fh);

	echo "
<br><div align=center><font color='#228B22'><b>Main configuration saved</b></font>
<br>&nbsp;
<br><br><font size='-1'>123Solar must be restarted for these changes to take effect</font>
<br>&nbsp;
<br>&nbsp;
<INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>
</div>
";
} else {
	echo "
<br><div align=center><font color='#8B0000'><b>Error configuration not saved !</b></font><br>
<INPUT type='button' value='Back' onclick='history.back()'>
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
