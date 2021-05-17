<?php
/**
 * /srv/http/123solar/styles/globalheader.php
 *
 * @package default
 */


define('checkaccess', TRUE);
include 'config/config_main.php';
include 'scripts/links.php';
date_default_timezone_set($DTZ);
include 'languages/' . $LANG . '.php';

echo '<!DOCTYPE html>
';
if ($DPOINT=='.') {
	echo "<html lang='en-150'>";
} else {
	echo '<html>';
}
echo "
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>$TITLE</title>
<META NAME='ROBOTS' CONTENT='NOINDEX, NOFOLLOW'>
<link rel='icon' type='image/x-icon' href='images/favicon.ico'>
<link rel='icon' type='image/png' href='images/favicon-32x32.png' sizes='32x32'>
<link rel='icon' type='image/png' href='images/favicon-16x16.png' sizes='16x16'>
<script src='$JSjquery'></script>
<script src='$JSjqui'></script>
<link rel='stylesheet' href='$JSjquit' type='text/css'>
<script type='text/javascript' src='$HC'></script>
<script type='text/javascript' src='$HCmore'></script>
<script type='text/javascript' src='$HCdd'></script>
<script type='text/javascript' src='$HCexp'></script>
<script type='text/javascript' src='$HCann'></script>
<link rel='stylesheet' href='styles/$STYLE/css/style.css' type='text/css'>
";
include 'styles/yourheader.php';
echo '</head>';
if ($DEBUG) {
	echo "(Debug mode shouldn't be left permanently)";
}
include "styles/$STYLE/header.php";
?>
