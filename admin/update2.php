<?php
/**
 * /srv/http/123solar/admin/update2.php
 *
 * @package default
 */


include 'secure.php';
set_time_limit(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
<title>123Solar Updater</title>
</head>
<body>
<br>
<table width="80%" border=0 cellspacing=0 cellpadding=0 align="center">
<tr><td>
<?php
if (!empty($_POST['bntsubmit']) && is_string($_POST['bntsubmit'])) {
	$bntsubmit = htmlspecialchars($_POST['bntsubmit'], ENT_QUOTES, 'UTF-8');
} else {
	$bntsubmit = null;
}
if ($bntsubmit != 'Update' && $bntsubmit != 'Continue') {
	die('Error');
}

$CURDIR = dirname(dirname(__FILE__)); // /srv/http/123solar
$SELFDIR = dirname($_SERVER['PHP_SELF']); // /123solar/admin
$SRVDIR = $_SERVER['DOCUMENT_ROOT']; // /srv/http
$error  = false;

session_start();
$_SESSION['CURDIR'] = $CURDIR;
$_SESSION['SELFDIR'] = $SELFDIR;
$_SESSION['SRVDIR'] = $SRVDIR;

date_default_timezone_set($DTZ);

if (!file_exists("$SRVDIR/_INSTALL/")) {
	if (!mkdir("$SRVDIR/_INSTALL/", 0777, true) || $CURDIR == $SRVDIR) {
		$error = true;
		echo "<img src='../images/24/sign-error.png' width=24 height=24> Can't create $SRVDIR/_INSTALL. Make sure you didn't install 123solar on web server's root directory !<br>";
	}
}
if (!is_writable("$SRVDIR")) {
	$error = true;
	echo "<img src='../images/24/sign-error.png' width=24 height=24> Can't write in $SRVDIR<br>";
}
if (!is_writable("$SRVDIR/_INSTALL/")) {
	$error = true;
	echo "<img src='../images/24/sign-error.png' width=24 height=24> Can't write in $SRVDIR/_INSTALL/ <br>";
}
if (!$error) {
	if (file_exists("$SRVDIR/_INSTALL/123solar/")) {
		rmdir("$SRVDIR/_INSTALL/123solar/");
	}
	touch("$SRVDIR/_INSTALL/index.html");
	exec("cp updater.php $SRVDIR/_INSTALL/", $output, $return);
	usleep(500000);
	header("Location: ../../_INSTALL/updater.php");
} else {
	echo "<br><br><INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Go back to admin'>";
}
?>
</td></tr>
</table>
</body>
</html>
