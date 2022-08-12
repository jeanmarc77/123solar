<?php
/**
 * /srv/http/123solar/admin/updater.php
 *
 * @package default
 */


session_start();
$CURDIR = $_SESSION['CURDIR']; // /srv/http/123solar
$SRVDIR = $_SESSION['SRVDIR']; // /srv/http

if (empty($CURDIR) || !is_string($CURDIR) || empty($SRVDIR) || !is_string($SRVDIR) || basename(__DIR__) != '_INSTALL') {
	die('ERROR');
}
set_time_limit(600);
$time_start = microtime(true);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123solar Updater</title>
<style>
kbd {
    background-color: #eee;
    border-radius: 3px;
    border: 1px solid #b4b4b4;
    box-shadow: 0 1px 1px rgba(0, 0, 0, .2), 0 2px 0 0 rgba(255, 255, 255, .7) inset;
    color: #333;
    display: inline-block;
    font-size: .85em;
    font-weight: 700;
    line-height: 1;
    padding: 2px 4px;
    white-space: nowrap;
   }
</style>
</head>
<body>
<br>
<table width="70%" border=0 cellspacing=0 cellpadding=0 align="center">
<tr><td>
<?php
$error       = false;
$log         = '';
$destination = 'temp.tar.gz';

$json   = file_get_contents('https://raw.githubusercontent.com/jeanmarc77/123solar/main/misc/latest_version.json');
$data   = json_decode($json, true);
$lastv  = $data['LASTVERSION'];
$source = $data['LINK'];
$md5    = $data['md5'];

define('checkaccess', TRUE);
include "$CURDIR/config/config_main.php";
date_default_timezone_set($DTZ);


/**
 *
 * @param unknown $source
 * @param unknown $dest
 * @param unknown $permissions
 * @return unknown
 */
function xcopy($source, $dest, $permissions) {
	if (is_link($source)) { // Check for symlinks
		return symlink(readlink($source), $dest);
	}
	if (is_file($source)) { // Simple copy for a file
		return copy($source, $dest);
	}
	if (!is_dir($dest)) { // Make destination directory
		mkdir($dest, 0755);
	}
	$dir = dir($source); // Loop through the folder
	while (false !== $entry = $dir->read()) { // Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		xcopy("$source/$entry", "$dest/$entry", $permissions); // Deep copy directories
	}
	$dir->close();
	return true;
}


// Check free space
$bytes     = disk_free_space(".");
$si_prefix = array(
	'B',
	'KB',
	'MB',
	'GB',
	'TB',
	'EB',
	'ZB',
	'YB'
);
$base      = 1024;
$class     = min((int) log($bytes, $base), count($si_prefix) - 1);
if ($bytes < 32000000) {
	$error = true;
	$log .= "ERROR: You should have at least 32 MB left on your disk<br>";
} else {
	$log .= "OK: Checked free space of at least 32 MB<br>";
}

if ($CURDIR == $SRVDIR) {
	$error = true;
	$log .= "ERROR: 123solar should not be installed as websever's root directory<br>";
}

if (!$error) { // download
	$fp = fopen("$destination", 'w+');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "$source");
	curl_setopt($ch, CURLOPT_BUFFERSIZE,128);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOPROGRESS, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);

	if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
		$error = true;
		$log .= "ERROR: Downloading<br>";
	} else {
		$log .= "OK: Downloading<br>";
	}
	curl_close($ch);
	fclose($fp);
}

if (!$error) {
	exec("md5sum $destination", $output);
	$pieces = explode(' ', $output[0]);
	if (!$pieces[0] == $md5) {
		$error = true;
		$log .= "ERROR: md5sum<br>";
	} else {
		$log .= "OK: md5sum checked<br>";
	}
}

if (!$error) {
	exec("tar -xzvf $destination -C $SRVDIR/_INSTALL/", $output, $return); // Return will return non-zero upon an error
	if ($return) {
		$error = true;
		$log .= "ERROR: Extracting<br>";
	} else {
		$log .= "OK: Extracting<br>";
	}
}

if (!$error) {
	if (!xcopy("$CURDIR/data/", "$SRVDIR/_INSTALL/123solar/data/", 0644)) {
		$error = true;
		$log .= "ERROR: Failed to import data<br>";
	} else {
		$log .= "OK: Imported data<br>";
	}
	if (!xcopy("$CURDIR/config/", "$SRVDIR/_INSTALL/123solar/config/", 0644)) {
		$error = true;
		$log .= "ERROR: Failed to import config<br>";
	} else {
		$log .= "OK: Imported config<br>";
	}
}

// Import user styles
$stylist = array(
    'default',
    'bluepanel',
    'jeanmarc',
    'mobile'
);

if (!$error) {
    $styldir = scandir("$CURDIR/styles/");
    $cnt     = count($styldir);
    for ($i = 0; $i < $cnt; $i++) {
        if (is_dir("$CURDIR/styles/$styldir[$i]/") && !in_array($styldir[$i], array('.','..')) && !in_array($styldir[$i], $stylist)) {
            if (!xcopy("$CURDIR/styles/$styldir[$i]/", "$SRVDIR/_INSTALL/123solar/styles/$styldir[$i]/", 0644)) {
                $error = true;
                $log .= "ERROR: Failed to import styles $styldir[$i]<br>";
            } else {
                $log .= "OK: Imported user style $styldir[$i]<br>";
            }
        }
    }
}

if (!$error) {
	if (file_exists("$CURDIR/scripts/123solar.pid")) {
		$pid     = (int) file_get_contents("$CURDIR/scripts/123solar.pid");
		$command = exec("kill -9 $pid > /dev/null 2>&1 &");
		unlink("$CURDIR/scripts/123solar.pid");
	}
}

if (!$error) { // Renaming
	$d = date('Ymd');
	exec("mv $CURDIR/ $CURDIR" . "$d/", $output, $return); // Return will return non-zero upon an error
	if ($return) {
		$error = true;
		$log .= "ERROR: mv $CURDIR/ $CURDIR.$d/<br>";
	} else {
		$log .= "OK: mv $CURDIR/ $CURDIR.$d/<br>";
	}
}

if (!$error) { // Installing new
	exec("mv $SRVDIR/_INSTALL/123solar/ $CURDIR/", $output, $return);
	if ($return) {
		$error = true;
		$log .= "ERROR: mv $SRVDIR/_INSTALL/123solar/ $CURDIR/<br>";
	} else {
		$log .= "OK: mv $SRVDIR/_INSTALL/123solar/ $CURDIR/<br>";
	}
}

if (file_exists($destination)) {
	if (!unlink("$destination")) {
		$log .= "ERROR: Can't remove $destination<br>";
	} else {
		$log .= "OK: Removed $destination<br>";
	}
}

// Sync
exec("sync", $output, $return);

if (!$error) { // Compressing older in background
	$d   = date('Ymd');
	$rnd = rand();
	shell_exec("tar -czPf $SRVDIR/_INSTALL/123s_backup_" . $d . '_' . $rnd . ".tar.gz $CURDIR" . "$d/ --remove-files 2>/dev/null >/dev/null &");
}

if (!$error) {
	echo "<br>
<font color='#228B22'><b>Update completed.</b></font>
<br><br>$SRVDIR/_INSTALL/123s_backup_" . $d . '_' . $rnd . ".tar.gz is being created. For security reason, move it away from your webserver directory !
<br><br>If you have graphical issue, force the refresh of your browser cache ( <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>R</kbd> )
<br><br>Please, take also this occasion to update your system.";
} else {
	$time_end       = microtime(true);
	$execution_time = (float) $time_end - $time_start;
	echo "<br><font color='#8B0000'><b>The update didn't complete as expected !</b></font>
<br><br>$log
<br>CURDIR: $CURDIR
<br>SRVDIR: $SRVDIR
<br>Total Execution Time: $execution_time secs
";
}

session_destroy();
$trimmed = str_replace($SRVDIR, '', $CURDIR);
echo "<br><br><INPUT TYPE='button' onClick=\"location.href='..$trimmed/admin/'\" value='Go back to admin'>";
?>
</tr></td>
</table>
</body>
</html>
<?php
unlink(__FILE__);
?>
