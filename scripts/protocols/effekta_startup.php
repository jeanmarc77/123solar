<?php
/**
 * /srv/http/123solar/scripts/protocols/effekta_startup.php
 *
 * @package default
 */


if (!defined('checkaccess')) {die('Direct access not permitted');}

$moxa_ip = "192.168.123.240";
$moxa_port = 24272;
$moxa_timeout = 10;
$err = false;
$anz_values = 28;       //abzufragende Werte -> wird in Request mitgesendet
$wrtype = "";
$wrname = "";
$wrserial = "";
$debug = false;
$script_name = "effekta_startup.php";
$log2console = false;

//WRType:
//./modpoll -m enc -t4:hex -c 28 -1 -r 49329 -p 24272 192.168.123.240
$fp = fsockopen($moxa_ip, $moxa_port, $errno, $errstr, $moxa_timeout);

openlog($script_name, LOG_PID, LOG_LOCAL0);
if ($debug) syslog(LOG_DEBUG, "Skript gestartet");

if (!$fp) {
	syslog(LOG_ALERT, "Fehler beim Verbindungsaufbau: $errstr ($errno)");
	if ($log2console) echo date("Y-M-d H:i:s").": Fehler beim Verbindungsaufbau: $errstr ($errno)<br />\n";
	$CMD_INFO = "echo \"Fehler beim Verbidungsaufbau zum WR: $errstr ($errno)\"";
	//exit(1);
}
else {
	//sende Request fuer Mess- Daten, siehe Protocol_V13.pdf Kapitel Measurements:
	fwrite($fp, chr(0x01).chr(0x03).chr(0xC0).chr(0xB0).chr(0x00).chr($anz_values).chr(0x79).chr(0xe4));

	$erg_values = array();
	$index = 0;
	while (!feof($fp)) {
		if (feof($fp)) break;
		$byte = fgetc($fp);

		if ($byte === FALSE)     //Fehler beim Empfang -> z.B. Verbindung abgebrochen!
			{
			$errstr = "Fehler: beim Empfang von Byte $index: ".bin2hex($byte);
			syslog(LOG_ALERT, $errstr);
			if ($log2console) echo date("Y-M-d H:i:s").$errstr."\r\n";
			$err = true;
			break;
		}
		if ($index == 0 && ord($byte) != 1)      //1. Byte = Index des WR -> wenn nicht 1 dann Fehler!
			{
			$errstr = "Fehler: Index des WR ist nicht gueltig: ".bin2hex($byte);
			syslog(LOG_ALERT, $errstr);
			if ($log2console) echo date("Y-M-d H:i:s").$errstr."\r\n";
			$err = true;
			break;
		}
		if ($index == 1 && ord($byte) != 3)      //Typ der Abfrage in Response pruefen, 0x03 = Abfragen
			{
			$errstr = "Fehler: Responsetype (sollte 0x03 sein) falsch: ".bin2hex($byte);
			syslog(LOG_ALERT, $errstr);
			if ($log2console) echo date("Y-M-d H:i:s").$errstr."\r\n";
			$err = true;
			break;
		}
		if ($index == 2 && ord($byte) < $anz_values)     //pruefe die Anz. der zurueckgegebenen Bytes mit den abgefragten!
			{
			$errstr = "Fehler: Anz. Bytes bei Response zu wenig: ".bin2hex($byte);
			syslog(LOG_ALERT, $errstr);
			if ($log2console) echo date("Y-M-d H:i:s").$errstr."\r\n";
			$err = true;
			break;
		}

		if ($index >= 7 && $index <= 12) {
			$wrtype .= $byte; //Name in ASCII zusammenbauen
		}

		if ($index >= 17 && $index <= 23) {
			$wrname .= $byte;
		}

		if ($index >= 27 && $index <= 34) {
			$wrname .= $byte;
		}

		if ($index >= 51 && $index <= 58) {
			$wrserial .= $byte;
		}

		if ($log2console && $debug) echo "DEBUG: Byte (Hex): ".bin2hex($byte)." $byte\r\n";
		if ($debug) syslog(LOG_DEBUG, "Byte (Hex): ".bin2hex($byte)." $byte");

		array_push($erg_values, ord($byte));

		if (sizeof($erg_values) > ($anz_values * 2 + 2)) break;  //alle Daten empfangen -> Abbruch da Gegenstelle die Verb. nicht beendet!

		$index++;
	}


	if ($err) {
		if ($log2console && $debug) echo date("Y-M-d H:i:s")." DEBUG: Fehler: $errstr\r\n";
		$CMD_INFO = "echo \"Fehler beim Abfragen der WR Infos: $errstr\"";

		//fclose($fp);
		//continue;
	}
	else {
		if ($log2console && $debug) echo date("Y-M-d H:i:s")." DEBUG: Ergebnisdaten (in Dezimal): \r\n";
		if ($log2console && $debug) print_r($erg_values);
		if ($debug) syslog(LOG_DEBUG, "Ergebnisdaten (in Dezimal): ".print_r($erg_values, true));

		if ($log2console) echo  "WRType: $wrtype<br>\r\n";

		if ($erg_values[4] == 0x28) $type = "WRType: Enersolis 4000";

		if ($log2console) echo $type."\r\n";

		if ($log2console) echo "WRName: $wrname<br>\r\n";
		if ($log2console) echo "WRSerial: $wrserial<br>\r\n";

		$CMD_INFO = "echo \"WRName: $wrname\r\n$type\r\nWRType: $wrtype\r\nWRSerial: $wrserial\"";

		if ($log2console) echo $CMD_INFO;
		if ($debug) syslog(LOG_DEBUG, "Ergebnisse: $CMD_INFO");
	}

	fclose($fp);
}

?>
