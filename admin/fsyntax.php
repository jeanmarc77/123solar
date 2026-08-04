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
set_time_limit(40);
$err = null;

if (!empty($_POST['bntsubmit'])) {
	$bntsubmit = $_POST['bntsubmit'];
} else {
	$bntsubmit = null;
}

echo "
<form action='fsyntax.php' method='post'>
Checking
<select name='bntsubmit'>";
if ($bntsubmit=='csv') {
	echo "<option value='php'>PHP</option><option SELECTED value='csv'>CSV</option>";
} else {
	echo "<option SELECTED value='php'>PHP</option><option value='csv'>CSV</option>";
}
echo "</select> files <input type='submit' value='Test'>
</form>
<br>
";

if ($bntsubmit == 'php' || $bntsubmit == 'csv') {

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('../'));
foreach ($iterator as $file) {
    if ($file->isDir())
        continue;
    $path = $file->getPathname();
    $ext  = pathinfo($path, PATHINFO_EXTENSION);
    // test PHP files
    if ($bntsubmit == 'php' && $ext == 'php') {
        $output     = exec("php -l $path 2>&1", $datareturn, $result);
        $datareturn = implode('<br>', $datareturn);
        if ($result) {
            $err = true;
            echo "<b>$path</b>:<br>$datareturn<br>";
        }
    }
    
    // test csv files
    if ($bntsubmit == 'csv' && $ext == 'csv') {
        $csv     = fopen($path, 'r');
        $headers = fgetcsv($csv, 0, ',');
        $cnt     = count($headers);
        if ($cnt < 2) {
            $err = true;
            echo "<br><b>$path<b>: is corrupted<br>";
        } else {
            $row_number = 0;
            while ($csv_row = fgetcsv($csv, 0, ',')) {
                $row_number++;
                // utf8_encode() used to run over $csv_row here before this
                // count check, but array_map() never changes an array's
                // length - it had no effect on the check below, and its
                // result was not used anywhere else. Dropped rather than
                // replaced: utf8_encode()/utf8_decode() are deprecated as
                // of PHP 8.2, and there is nothing left for a replacement
                // to do.
                if (count($csv_row) != $cnt) {
                    $err = true;
                    echo "<br><b>$path</b>: length of row $row_number does not match the header length ($cnt)<br>";
                }
                if ($row_number > 400) {
                    $err = true;
                    echo "<br><b>$path</b>: file too big<br>";
                }
            }
        }
    }
    
}
if ($err) {
    echo "<br><img src='../images/24/sign-error.png' width=24 height=24 border=0><b>-NOT- OK</b>";
} else {
    echo "<br><img src='../images/24/sign-check.png' width=24 height=24 border=0>All $bntsubmit files are OK";
}

}
?>
<div align=center><br><br><INPUT TYPE='button' onClick="location.href='help.php'" value='Back'></div>
</tr></td>
</table>
</body>
</html>
