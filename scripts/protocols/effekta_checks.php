<?php
/**
 * /srv/http/123solar/scripts/protocols/effekta_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

$moxa_ip = "192.168.123.240";
$moxa_port = 24272;
$moxa_timeout = 10;
$err = false;
$anz_values = 2;       //abzufragende Werte -> wird in Request mitgesendet
$debug = false;
$script_name = "effekta_checks.php";
$tmp_dir = "/tmp/";  //Speicherort/-ordner fuer akt. Werte -> am Ende ein / !!!
$log2console = false;
$is_error_read = false;

if (!function_exists("readfromfile")) {


	/**
	 *
	 * @param unknown $filename
	 * @return unknown
	 */
	function readfromfile($filename) {
		global $is_error_read;

		$v = file_get_contents($filename);
		if (strlen($v) > 0) $val = (float) $v;
		else $val = 0;

		if ($v === FALSE) {
			if (!$is_error_read) {
				syslog(LOG_ALERT, "Fehler beim Lesen von der Datei $filename Start (weitere Meldungen werden unterdrueckt)!");
			}
			$is_error_read = true;
		}
		else if ($is_error_read) {
			syslog(LOG_ALERT, "Fehler beim Lesen bereinigt!");
			$is_error_read = false;
		}

		return $val;
	}


}

//ALARME/ERRORS:
//entspricht ./modpoll -m enc -t4:hex -c 2 -1 -r 49153 -p 24272 192.168.123.240
$fp = fsockopen($moxa_ip, $moxa_port, $errno, $errstr, $moxa_timeout);

openlog($script_name, LOG_PID, LOG_LOCAL0);
if ($debug) syslog(LOG_DEBUG, "Skript gestartet");

if (!$fp) {
	syslog(LOG_ALERT, "Fehler beim Verbindungsaufbau: $errstr ($errno)");
	echo date("Y-M-d H:i:s").": Fehler beim Verbindungsaufbau: $errstr ($errno)<br />\n";
	$err = true;
	//exit(1);
}

//sende Request fuer Mess- Daten, siehe Protocol_V13.pdf Kapitel Measurements:
fwrite($fp, chr(0x01).chr(0x03).chr(0xc0).chr(0x00).chr(0x00).chr($anz_values).chr(0xf8).chr(0x0b));

$erg_values = array();
$index = 0;
while (!feof($fp)) {
	if (feof($fp)) break;
	$byte = fgetc($fp);

	if ($byte === FALSE)     //Fehler beim Empfang -> z.B. Verbindung abgebrochen!
		{
		syslog(LOG_ALERT, "Fehler: beim Empfang von Byte $index: ".bin2hex($byte));
		if ($log2console) echo date("Y-M-d H:i:s")." Fehler: beim Empfang von Byte $index: ".bin2hex($byte)."\r\n";
		$err = true;
		break;
	}
	if ($index == 0 && ord($byte) != 1)      //1. Byte = Index des WR -> wenn nicht 1 dann Fehler!
		{
		syslog(LOG_ALERT, "Fehler: Index des WR ist nicht gueltig: ".bin2hex($byte));
		if ($log2console) echo date("Y-M-d H:i:s")." Fehler: Index des WR ist nicht gueltig: ".bin2hex($byte)."\r\n";
		$err = true;
		break;
	}
	if ($index == 1 && ord($byte) != 3)      //Typ der Abfrage in Response pruefen, 0x03 = Abfragen
		{
		syslog(LOG_ALERT, "Fehler: Responsetype (sollte 0x03 sein) falsch: ".bin2hex($byte));
		if ($log2console) echo date("Y-M-d H:i:s")." Fehler: Responsetype (sollte 0x03 sein) falsch: ".bin2hex($byte)."\r\n";
		$err = true;
		break;
	}
	if ($index == 2 && ord($byte) < $anz_values)     //pruefe die Anz. der zurueckgegebenen Bytes mit den abgefragten!
		{
		syslog(LOG_ALERT, "Fehler: Anz. Bytes bei Response zu wenig: ".bin2hex($byte));
		if ($log2console) echo date("Y-M-d H:i:s")." Fehler: Anz. Bytes bei Response zu wenig: ".bin2hex($byte)."\r\n";
		$err = true;
		break;
	}

	if ($index == 2 && ord($byte) != 4)     //pruefe die Anz. der zurueckgegebenen Bytes mit den abgefragten!
		{
		syslog(LOG_ALERT, "Fehler: Anz. Bytes bei Response nicht gleich 4: ".bin2hex($byte));
		if ($log2console) echo date("Y-M-d H:i:s")." Fehler: Anz. Bytes bei Responsenicht gleich 4: ".bin2hex($byte)."\r\n";
		$err = true;
		break;
	}


	if ($log2console && $debug) echo "DEBUG: Byte (Hex): ".bin2hex($byte)." $byte\r\n";
	if ($debug) syslog(LOG_DEBUG, "Byte (Hex): ".bin2hex($byte)." $byte");

	array_push($erg_values, ord($byte));

	if (sizeof($erg_values) > ($anz_values * 2 + 2)) break;  //alle Daten empfangen -> Abbruch da Gegenstelle die Verb. nicht beendet!

	$index++;
}


if ($err) {
	fclose($fp);

	$ILEAK = 0;     //current ileak in mA
	$RISO = 3000;   //r isulation in Mohm
	$PPEAK = 1000;  //peak power of all time
	$PPEAKOTD = 500;        //peak power of the day
	$RET = 'OK';

	//if(strlen($error_txt) > 0) $error_txt .= "&nbsp;&nbsp;&nbsp;&nbsp;".$error_txt;
	$STATE = "running";        //State text file shown in dashboad
	$ALARM = null;
}
else {

	//zum Testen der Bitauswertung:
	//$erg_values[4] = 160;

	$error_nr = $erg_values[5] * 0x1000000 + $erg_values[6] * 0x10000 + $erg_values[3] * 0x100 + $erg_values[4];


	if ($log2console && $debug) echo "ALARME/ERRORS:<br>\r\n";
	if ($log2console && $debug) echo date("Y-M-d H:i:s")." DEBUG: Ergebnisdaten (in Dezimal): \r\n".print_r($erg_values, true);

	if ($log2console && $debug) echo "Fehlerzahl: $error_nr<br>\r\n";

	$error_txt = "";
	/*
	if($error_nr & 00) $error_txt .= "E00 DC BUS Charge Fault\r\n";
	if($error_nr & 01) $error_txt .= "E01 Inverter Fault (SANYO DENKI)\r\n";
	if($error_nr & 02) $error_txt .= "E02 Reserve\r\n";
	if($error_nr & 03) $error_txt .= "E03 Inverter Fault\r\n";
	if($error_nr & 04) $error_txt .= "E04 Battery Weak or Bad\r\n";
	if(($error_nr & 05)) $error_txt .= "E05 Reserve\r\n";
	if($error_nr & 06) $error_txt .= "E06 EPO (Emergency Power Off Mode)\r\n";
	if($error_nr & 07) $error_txt .= "E07 DC BUS Voltage Over-Rang\r\n";
	if($error_nr & 08) $error_txt .= "E08 DC BUS Voltage Under-Rang\r\n";
	if($error_nr & 09) $error_txt .= "E09 Inverter output current Over-Rang\r\n";
	if($error_nr & 10) $error_txt .= "E10 Inverter temperature Over-Rang\r\n";
	if($error_nr & 11) $error_txt .= "E11 Inverter output power Over-Rang\r\n";
	if($error_nr & 12) $error_txt .= "E12 Charger Fault\r\n";
	if($error_nr & 13) $error_txt .= "E13 Inverter output Short-Circuit\r\n";
	if($error_nr & 14) $error_txt .= "E14 PLL(Phase-Locked Loop) Fault\r\n";
	if($error_nr & 15) $error_txt .= "E15 Reserve\r\n";
	if($error_nr & 16) $error_txt .= "E16 Reserve\r\n";
	if($error_nr & 17) $error_txt .= "E17 EEPROM Data Error ,Use Default Value\r\n";
	if($error_nr & 18) $error_txt .= "E18 Heatsink temperature Over-Rang\r\n";
	if($error_nr & 19) $error_txt .= "E19 DCBUS voltage don.t Discharge\r\n";
	if($error_nr & 20) $error_txt .= "E20 Reserve\r\n";
	if($error_nr & 21) $error_txt .= "E21 Reserve\r\n";
	if($error_nr & 22) $error_txt .= "E22 Inverter Relay Fault\r\n";
	if($error_nr & 23) $error_txt .= "E23 Reserve\r\n";
	if($error_nr & 24) $error_txt .= "E24 Inverter Current sense Fault\r\n";
	if($error_nr & 25) $error_txt .= "E25 Booster _1 - Input current Over-Rang\r\n";
	if($error_nr & 26) $error_txt .= "E26 Booster _2 - Input current Over-Rang\r\n";
	if($error_nr & 27) $error_txt .= "E27 Booster input Short-Circuit\r\n";
	if($error_nr & 28) $error_txt .= "E28 Charger Voltage Over-Rang\r\n";
	if($error_nr & 29) $error_txt .= "E29 Inverter Output Current Balance Over-Rang\r\n";
	if($error_nr & 30) $error_txt .= "E30 The Settings of Driver Board don.t match the EEPROM\r\n";
	if($error_nr & 31) $error_txt .= "E31 Reserve\r\n";
	if($error_nr & 32) $error_txt .= "E32 Memory Error\r\n";
	if($error_nr & 33) $error_txt .= "E33 Charger is self-locked\r\n";
	if($error_nr & 34) $error_txt .= "E34 Crystal damage\r\n";
	if($error_nr & 35) $error_txt .= "E35 Charger Voltage Under-Rang\r\n";
	if($error_nr & 36) $error_txt .= "E36 Bat. Over-heat\r\n";
	if($error_nr & 37) $error_txt .= "E37 Fan out of order\r\n";
	if($error_nr & 38) $error_txt .= "E38 AUTO Function Enable\r\n";
	if($error_nr & 39) $error_txt .= "E39 Failure in save\r\n";
	*/

	//Bituebersetzungstabelle:
	$bit2txt = array();
	$bit2txt[00] = "E00 DC BUS Charge Fault";
	$bit2txt[01] = "E01 Inverter Fault (SANYO DENKI)";
	$bit2txt[02] = "E02 Reserve";
	$bit2txt[03] = "E03 Inverter Fault";
	$bit2txt[04] = "E04 Battery Weak or Bad";
	//$bit2txt[05] = "E05 Reserve";  //Fehler nicht beachten, hat keine Bedeutung!
	$bit2txt[06] = "E06 EPO (Emergency Power Off Mode)";
	$bit2txt[07] = "E07 DC BUS Voltage Over-Rang";
	$bit2txt[08] = "E08 DC BUS Voltage Under-Rang";
	$bit2txt[09] = "E09 Inverter output current Over-Rang";
	$bit2txt[10] = "E10 Inverter temperature Over-Rang";
	$bit2txt[11] = "E11 Inverter output power Over-Rang";
	$bit2txt[12] = "E12 Charger Fault";
	$bit2txt[13] = "E13 Inverter output Short-Circuit";
	$bit2txt[14] = "E14 PLL(Phase-Locked Loop) Fault";
	$bit2txt[15] = "E15 Reserve";
	$bit2txt[16] = "E16 Reserve";
	$bit2txt[17] = "E17 EEPROM Data Error ,Use Default Value";
	$bit2txt[18] = "E18 Heatsink temperature Over-Rang";
	$bit2txt[19] = "E19 DCBUS voltage don.t Discharge";
	$bit2txt[20] = "E20 Reserve";
	$bit2txt[21] = "E21 Reserve";
	$bit2txt[22] = "E22 Inverter Relay Fault";
	$bit2txt[23] = "E23 Reserve";
	$bit2txt[24] = "E24 Inverter Current sense Fault";
	$bit2txt[25] = "E25 Booster _1 - Input current Over-Rang";
	$bit2txt[26] = "E26 Booster _2 - Input current Over-Rang";
	$bit2txt[27] = "E27 Booster input Short-Circuit";
	$bit2txt[28] = "E28 Charger Voltage Over-Rang";
	$bit2txt[29] = "E29 Inverter Output Current Balance Over-Rang";
	$bit2txt[30] = "E30 The Settings of Driver Board don.t match the EEPROM";
	$bit2txt[31] = "E31 Reserve";
	$bit2txt[32] = "E32 Memory Error";
	$bit2txt[33] = "E33 Charger is self-locked";
	$bit2txt[34] = "E34 Crystal damage";
	$bit2txt[35] = "E35 Charger Voltage Under-Rang";
	$bit2txt[36] = "E36 Bat. Over-heat";
	$bit2txt[37] = "E37 Fan out of order";
	$bit2txt[38] = "E38 AUTO Function Enable";
	$bit2txt[39] = "E39 Failure in save";

	//gehe alle Bits durch:
	for ($i = 0; $i < 32; $i++) {
		$tmp = $error_nr >> $i; //schiebe Bit an Stelle $i an 1. Stelle
		$tmp = $tmp & 1; //loesche alle Bits auszer an 1. Stelle

		if (!isset($bit2txt[$i])) continue; //keine Fehlerbeschreibung vorhanden -> ueberspringen!

		if ($tmp == 1)  //ist bit an 1. Stelle gesetzt?
			{
			if ($i == 7 || $i == 14 || $i == 10)  //Fehler 7, 10 und 14 nicht beachten wenn akt. Leistung 0 (Fehlalarme!)
				{
				$akt_power = readfromfile($tmp_dir."akt_power.txt");

				if ($akt_power <= 0) {
					continue;
				}
			}

			if (strlen($error_txt) > 0) $error_txt .= ",";
			$error_txt .= $bit2txt[$i];
		}
	}

	if (strlen($error_txt) > 0) $error_txt .= "\r\n";

	//zwecks Debugging kuenstlich einen Fehler erzeugen:
	//$error_txt = "TestfehlerAmWR\r\n";

	if ($log2console) echo "Fehlertext: $error_txt\r\n";

	$ILEAK = 0;     //current ileak in mA
	$RISO = 3000;   //r isulation in Mohm
	$PPEAK = 1000;  //peak power of all time
	$PPEAKOTD = 500;        //peak power of the day
	$RET = 'OK';

	//if(strlen($error_txt) > 0) $error_txt .= "&nbsp;&nbsp;&nbsp;&nbsp;".$error_txt;
	$STATE = "running";        //State text file shown in dashboad
	if (strlen($error_txt) <= 0) $ALARM = null;
	else $ALARM = $error_txt;        //Alarm command

	if ($debug) syslog(LOG_DEBUG, "Ergebnisse: $STATE,$error_nr,$ALARM");
}

@fclose($fp);

?>
