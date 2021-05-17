<?php
/**
 * /srv/http/123solar/scripts/protocols/effekta.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}
// http://sourceforge.net/projects/modpoll/ By Mario Stuetz
$tmp_dir = "/tmp/";  //Speicherort/-ordner fuer akt. Werte -> am Ende ein / !!!
$script_name = "effekta.php";
$is_error_read = false;
$debug = false;
$debug_file = false;
$sleep_time = 1100000; //verzeogere Ausfuehrung und Filelesen um x ms!
$max_wait_time = 20; //max. Wartezeit auf Daten von modpoll in Sek. (sollte dem Takt von modpoll angepasst sein!)

openlog($script_name, LOG_PID | LOG_PERROR, LOG_LOCAL0);

if ($debug_file) $fp_debug_file = fopen("/tmp/effekta.log", "a");

if (!function_exists("readfromfile")) {


	/**
	 *
	 * @param unknown $filename
	 * @return unknown
	 */
	function readfromfile($filename) {
		global $is_error_read, $fp_debug_file, $debug_file;

		$v = file_get_contents($filename);
		if (strlen($v) > 0) $val = (float) $v;
		else $val = 0;

		if ($v === FALSE) {
			if (!$is_error_read) {
				syslog(LOG_ALERT, "Fehler beim Lesen von der Datei $filename Start (weitere Meldungen werden unterdrueckt)!");
				if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." Fehler beim Lesen von der Datei $filename Start (weitere Meldungen werden unterdrueckt)!\r\n");
			}
			$is_error_read = true;
		}
		else if ($is_error_read) {
			syslog(LOG_ALERT, "Fehler beim Lesen bereinigt!");
			if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." Fehler beim Lesen bereinigt!\r\n");
			$is_error_read = false;
		}

		return $val;
	}


}

if (!function_exists("readfromfile_string")) {


	/**
	 *
	 * @param unknown $filename
	 * @return unknown
	 */
	function readfromfile_string($filename) {
		global $is_error_read, $fp_debug_file, $debug_file;

		$v = file_get_contents($filename);
		if (strlen($v) > 0) $val = trim($v);
		else $val = 0;

		if (!$v) {
			if (!$is_error_read) {
				syslog(LOG_ALERT, "Fehler beim Lesen von der Datei $filename Start (weitere Meldungen werden unterdrueckt)!");
				if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." Fehler beim Lesen von der Datei $filename Start (weitere Meldungen werden unterdrueckt)!\r\n");
			}
			$is_error_read = true;
		}
		else if ($is_error_read) {
			syslog(LOG_ALERT, "Fehler beim Lesen bereinigt!");
			if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." Fehler beim Lesen bereinigt!\r\n");
			$is_error_read = false;
		}

		return $val;
	}


}

if (!function_exists("write2file")) {


	/**
	 *
	 * @param unknown $filename
	 * @param unknown $value
	 */
	function write2file($filename, $value) {
		global $is_error_read, $fp_debug_file, $debug_file;

		$fp2 = fopen($filename, "w");
		if (!$fp2 || !fwrite($fp2, (float) $value)) {
			if (!$is_error_read) {
				syslog(LOG_ALERT, "Fehler beim Schreiben in die Datei $filename Start (weitere Meldungen werden unterdrueckt)!", true);
				if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")."Fehler beim Schreiben in die Datei $filename Start (weitere Meldungen werden unterdrueckt)!\r\n");
			}
			$is_error_read = true;
		}
		else if ($is_error_read) {
			syslog(LOG_ALERT, "Fehler beim Schreiben bereinigt!", true);
			if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")."Fehler beim Schreiben bereinigt!\r\n");
			$is_error_read = false;
		}
		fclose($fp2);
	}


}
//synchronisiere Polling mit modpoll:
$mtime = filemtime($tmp_dir."pv_ges.txt");
$mtime_diff = time() - $mtime;

if ($mtime_diff >= 0 && $mtime_diff <= ($max_wait_time + 1)) //Daten sind noch nicht so alt (innerhalb eines Zyklus von modpoll)
{
	$last_mtime = readfromfile_string($tmp_dir."last_mtime.txt"); //wann wurden Daten (von diesem Skript/worker) das letzte Mal gelesen?

	if ($last_mtime == $mtime) //Daten schon mal gelesen -> warte max. einen Zyklus von modpoll auf neue Daten
		{
		for ($i = 0; $i < ($max_wait_time + 1); $i++) //pruefe alle Sek., ob neue Daten vorliegen -> blockiere worker solange, bis Daten vorliegen (max. einen modpoll Zyklus)
			{
			sleep(1);

			clearstatcache();
			$new_mtime = filemtime($tmp_dir."pv_ges.txt"); //frage mtime erneut ab
			if ($new_mtime != $mtime)  //Daten wurden waehrenddessen in File geschrieben -> raus aus Schleife
				{
				//aktualisiere mtime in File schreiben:
				write2file($tmp_dir."last_mtime.txt", $new_mtime);
				//$fp_mtime = fopen($tmp_dir."last_mtime.txt","w");
				//fwrite($fp_mtime, $new_mtime);
				//fclose($fp_mtime);

				break; //raus aus Schleife, damit worker die neuen Werte bekommt!
			}
			else usleep($sleep_time); //keine Veraenderung der Daten erkannt -> warte
		}
	}
	else  //Daten neu -> Daten sofort lesen!
		{
		//usleep($sleep_time);

		//aktualisiere mtime in File:
		write2file($tmp_dir."last_mtime.txt", $mtime);
		//$fp_mtime = fopen($tmp_dir."last_mtime.txt","w");
		//fwrite($fp_mtime, $mtime);
		//fclose($fp_mtime);
	}
}
else usleep($sleep_time); //alte Daten (auszerhalb eines Zyklus von modpoll) -> standard Wartezeit und dann Daten lesen!

if ($debug_file) clearstatcache();
if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." filemtime ".filemtime($tmp_dir."pv_ges.txt")." ".(time() - filemtime($tmp_dir."pv_ges.txt"))." ".(time() - fileatime($tmp_dir."pv_ges.txt"))."\r\n");

$kw = readfromfile($tmp_dir."pv_ges.txt");
if (strlen($kw) > 0) $kwh = (float) $kw;
else $kwh = 0;

$KWHT = $kwh;
settype($KWHT, 'float');

$SDTE = readfromfile_string($tmp_dir."ts.txt"); //date("YMd-His");//Bsp: "20131201-09:00:38";
$G1P = readfromfile($tmp_dir."akt_power.txt");
$I1V = readfromfile($tmp_dir."DC1INV.txt");
$I1A = readfromfile($tmp_dir."DC1INC.txt");
$I1P = readfromfile($tmp_dir."INPA.txt");
$I2V = readfromfile($tmp_dir."DC2INV.txt");
$I2A = readfromfile($tmp_dir."DC2INC.txt");
$I2P = readfromfile($tmp_dir."INPB.txt");
$G1V = readfromfile($tmp_dir."ACVL1.txt");
$G1A = readfromfile($tmp_dir."ACOCL1.txt");
$FRQ = readfromfile($tmp_dir."ACF.txt");
$EFF = (float) 0.0;
$INVT = readfromfile($tmp_dir."INTEMP.txt");
$BOOT = readfromfile($tmp_dir."INHS.txt");

//$fp_debug_file = fopen("/tmp/tmpfs/effekta.log","a");
if ($debug_file) fwrite($fp_debug_file, date("Y-M-d-H:i:s")." $SDTE $G1P $KWHT\r\n");
if ($debug_file) fclose($fp_debug_file);

//Effizienz berechnen, da nicht uebergeben:
if (($I1P + $I2P) > 0) $EFF = (float) $G1P / ($I1P + $I2P) * 100;

if (($I1V * $I1A + $I2V * $I2A) > 0) $eff2 = (float) $G1P / ($I1V * $I1A + $I2V * $I2A) * 100;
else $eff2 = 0;

if ($EFF <= 0 || $EFF >= 100) {
	if ($eff2 > 0 && $eff2 < 100) $EFF = $eff2;
	else if ($EFF >= 100 && $eff2 >= 100) $EFF = 101;
	else $EFF = 0;
}

$I3V = null;
$I3A = null;
$I3P = null;
$I4V = null;
$I4A = null;
$I4P = null;
$G2V = null;
$G2A = null;
$G2P = null;
$G3V = null;
$G3A = null;
$G3P = null;

$RET = 'OK';

if ($debug) {
	echo "DEBUGGING:\r\n";
	echo "KWHT: $KWHT\r\n";
	echo "SDTE: $SDTE\r\n";
	echo "I1V : $I1V \r\n";
	echo "I1A : $I1A \r\n";
	echo "I1P : $I1P \r\n";
	echo "I2V : $I2V \r\n";
	echo "I2A : $I2A \r\n";
	echo "I2P : $I2P \r\n";
	echo "G1V  : $G1V  \r\n";
	echo "G1A  : $G1A  \r\n";
	echo "G1P  : $G1P  \r\n";
	echo "FRQ : $FRQ \r\n";
	echo "INVT: $INVT\r\n";
	echo "BOOT: $BOOT\r\n";
	echo "EFF:  $EFF \r\n";
	echo "EFF2: $eff2\r\n";
}

//sleep(1);

?>
