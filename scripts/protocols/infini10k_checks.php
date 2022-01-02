<?php
/**
 * /srv/http/123solar/scripts/protocols/infini10k_checks.php
 *
 * @package default
 */


$tmp_dir = "/tmp/inv1/";
$error_txt="";
if (file_exists($tmp_dir."ALARM.txt")) {
	$error_txt = file_get_contents($tmp_dir."ALARM.txt");
	if (strlen($error_txt) > 0) $error_txt .= "\r\n";

	//zwecks Debugging kuenstlich einen Fehler erzeugen:
	//$error_txt = "TestfehlerAmWR\r\n";

	$ILEAK = 0;     //current ileak in mA
	$RISO = 3000;   //r isulation in Mohm
	$PPEAK = 10000;  //peak power of all time
	$PPEAKOTD = 500;        //peak power of the day
	$RET = 'OK';

	if (strlen($error_txt) <= 0) $ALARM = null;
	else $ALARM = $error_txt;
	// WR-Modus auslesen
	$STATE  = file_get_contents($tmp_dir."STATE.txt");
}

?>
