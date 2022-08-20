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
//include 'secure.php';
set_time_limit(40);
$err = null;

echo "Checking files syntax<br><br>";

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('../'));
foreach ($iterator as $file) {
    if ($file->isDir())
        continue;
    $path = $file->getPathname();
    $ext  = pathinfo($path, PATHINFO_EXTENSION);
    // test PHP files
    if ($ext == 'php') {
        $output     = exec("php -l $path 2>&1", $datareturn, $result);
        $datareturn = implode('<br>', $datareturn);
        if ($result) {
            $err = true;
            echo "<b>$path</b>:<br>$datareturn<br>";
        }
    }
    
    // test csv files
    if ($ext == 'csv' || $ext == 'cst') {
        $csv     = fopen($path, 'r');
        $headers = fgetcsv($csv, 0, ',');
        $cnt     = count($headers);
        if ($cnt < 2) {
            $err = true;
            echo "<br><b>$path<b>: is corrupted<br>";
        } else {
            $rows       = array();
            $row_number = 0;
            while ($csv_row = fgetcsv($csv, 0, ',')) {
                $row_number++;
                $encoded_row = array_map('utf8_encode', $csv_row);
                if (count($encoded_row) != $cnt) {
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
    echo "<br><img src='../images/24/sign-check.png' width=24 height=24 border=0>All OK";
}
?>
<div align=center><br><br><INPUT TYPE='button' onClick="location.href='help.php'" value='Back'></div>
</tr></td>
</table>
</body>
</html>
