<?php
/**
 * /srv/http/123solar/admin/index.php
 *
 * @package default
 */


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123Solar Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
<?php
define('checkaccess', TRUE);
include '../config/config_main.php';
include '../scripts/links.php';
echo "<script src='$JSjquery'></script>
";
?>
<script type="text/javascript" src="../js/strength/strength.js"></script>
<link rel="stylesheet" type="text/css" href="../js/strength/strength.css">
</head>
<body>
<script>
$(document).ready(function($) {

$('#myPassword').strength({
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter',
            strengthButtonClass: 'button_strength',
            strengthButtonText: 'Show password',
            strengthButtonTextToggle: 'Hide password'
        });
});
</script>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#FFFFFF" height="80">
  <td class="cadretopleft" width="128"><img src="../styles/default/images/sun12880.png" width="128" height="80" alt="123Solar"></td>
  <td class="cadretop" align="center"><b>123Solar Administration</font></td>
  <td class="cadretopright" width="128" align="right">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCC66">
<td align=right COLSPAN="3" class="cadre" height="10">&nbsp;
</td></tr>
<tr valign="top">
    <td COLSPAN="3" class="cadrebot" bgcolor="#d3dae2">
<!-- #BeginEditable "mainbox" -->
<?php
$CURDIR      = dirname(dirname(__FILE__));
$processuser = posix_getpwuid(posix_geteuid());
$user        = $processuser['name'];
if (strstr($CURDIR, '_INSTALL')) {
	echo "$CURDIR is an install directory !";
} else if (!file_exists('../config/.htpasswd') && empty($_POST['login']) && empty($_POST['newpwd'])) {
	echo "<br><div align=center>
	<b>Thanks for using 123Solar !</b><br><br>
<table border=0 align='center' width='50%'>
<tr><td>
The application is running on top of a webserver, this folder must be owned by $user user: <b>chown -R $user:$user $CURDIR/</b><br><br>
You should also grant the access to communication(s) application(s) and peripherals port(s).<br><br>
Next, define a login and password :<br>
<br>
</td></tr></table>
<form action='index.php' method='post'>
<table border=0 align='center' width='50%'>
<tr><td valign='top' align='right'>Login:</td><td align='left'><input type='text' name='login' value='admin'></td></tr>
<tr><td valign='top' align='right'>Password:</td><td align='left'><input id='myPassword' type='password' name='newpwd' value=''></td></tr>
<tr><td align=left colspan=2>
<br>By clicking 'OK', you explicitly agree with softwares, libraries and content licenses included in this package.
<br><br>123Solar is released under the GNU GPLv3 license (General Public License).
This license allows you to freely integrate this library in your applications, modify the code and redistribute it in bundled packages as long as your application is also distributed with the GPL license.<br>
<br>The GPLv3 license description can be found at <a href='http://www.gnu.org/licenses/gpl.html'>http://www.gnu.org/licenses/gpl.html</a> <br>
<br>Highcharts, the javascript charting library is free for non-commercial use only. <a href='http://highcharts.com'>http://highcharts.com</a><br>
</tr>
</td>
<tr><td align=center colspan=2><br><input type='submit' value='OK'> <input type='button' onClick=\"location.href='http://www.wikipedia.org'\" value='Cancel'></td>
</tr></table>
</form>
";
} elseif (!file_exists('../config/.htpasswd') && !empty($_POST['login']) && !empty($_POST['newpwd'])) {
	$pw_file  = '../config/.htpasswd';
	$login    = trim($_POST['login']);
	$newpwd   = trim($_POST['newpwd']);
	$password = crypt($newpwd, base64_encode($newpwd));
	$pw_line  = "$login:$password\n";
	$pf       = fopen($pw_file, "w");
	fwrite($pf, $pw_line);
	fclose($pf);
	$doc_dir  = dirname(dirname(__FILE__));
	$acc_file = '../config/.htaccess';
	$af       = fopen($acc_file, "w");
	$new_acc  = "AuthUserFile $doc_dir/config/.htpasswd\n";
	$new_acc .= "AuthGroupFile /dev/null\n";
	$new_acc .= "AuthName \"123solar Password Protected Area\"\n";
	$new_acc .= "AuthType Basic\n";
	$new_acc .= "Require valid-user\n";
	fwrite($af, $new_acc);
	fclose($af);
	echo "<br><div align=center><font color='#228B22'><b>Password is saved for the $login user, don't forget it</b></font>
<br><br><INPUT TYPE='button' onClick=\"location.href='index.php'\" value='Continue'></div>";
} elseif (file_exists('../config/.htpasswd') && !isset($_SERVER["PHP_AUTH_USER"])) {
	$SRVDIR = $_SERVER['DOCUMENT_ROOT'];
	$CURDIR = dirname(dirname(__FILE__));
	$localip = $_SERVER['SERVER_ADDR'];
	$trimmed = str_replace($SRVDIR, '', $CURDIR);
	echo "<br><div align=center><img src='../images/24/sign-warning.png' width='24' height='24' border='0'><font color='#8B0000'><b> Your webserver don't allow HTTP authentication !</b></font>
<br><br>
For more security and to grant public IP access, configure your webserver : " . $_SERVER['SERVER_NAME'] . " (" . $_SERVER['SERVER_SOFTWARE'] . "). Then, refresh your browser.
<br><br>Credentials have been created as config/.htpasswd
<br>
<br>The administration access is currently only permitted from your local network :
<br><br><INPUT TYPE='button' onClick=\"location.href='http://$localip$trimmed/admin/admin.php'\" value='http://$localip'> or <INPUT TYPE='button' onClick=\"location.href='https://$localip$trimmed/admin/admin.php'\" value='https://$localip'></div>
";
} elseif (isset($_SERVER["PHP_AUTH_USER"])) {
	header('Location: admin.php');
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
