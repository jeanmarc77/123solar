<?php
/**
 * /srv/http/123solar/admin/fperms.php
 *
 * @package default
 */


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>meterN Debug</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
</head>
<body>
<table width="95%" height="80%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr><td>
<?php
include 'secure.php';
$CURDIR = dirname(dirname(__FILE__));
$whoami = exec('whoami');
$whoamig = exec("groups $whoami");


/**
 *
 * @param unknown $dir
 * @param unknown $who
 * @return unknown
 */
function listFolderFiles($dir, $who) {
	global $cnt;
	$ffs = scandir($dir);
	unset($ffs[array_search('.', $ffs, true)]);
	unset($ffs[array_search('..', $ffs, true)]);

	foreach ($ffs as $ff) {
		$arr = posix_getpwuid(fileowner($dir.'/'.$ff));
		$user = $arr['name'];
		$arr = posix_getgrgid(filegroup($dir.'/'.$ff));
		$grp = $arr['name'];
		if ($user!=$who) {
			$cnt++;
			echo "<li>$dir/$ff <font color='#8B0000'>($user:$grp)</font></li>";
		}

		if (is_dir($dir.'/'.$ff)) {
			listFolderFiles($dir.'/'.$ff, $who);
		}

	}
	return $cnt;
}


echo "Checking $CURDIR files permissions, you are $whoami user ($whoamig)<br><br>";
$ret = listFolderFiles($CURDIR, $whoami);

if ($ret>0) {
	echo "<br><img src='../images/24/sign-error.png' width=24 height=24 border=0><b>-NOT- OK</b> (chown -R $whoami:$whoami $CURDIR/)";
} else {
	echo "<br><img src='../images/24/sign-check.png' width=24 height=24 border=0>All OK $ret";
}

?>
<div align=center><br><INPUT TYPE='button' onClick="location.href='help.php'" value='Back'></div>
</tr></td>
</table>
</body>
</html>
