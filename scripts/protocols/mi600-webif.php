<?php
# Stand-alone protocol for Bosswerk MI600 (Deye SUN600G3-EU-230) inverter
# Is now also working with more than one inverter in a grid.
# Author: https://github.com/dr-ni/123solar_mi600, adapted by SOE135
# License GPL-v3+
#
# Please supply '<host> <user> <passwd>' for your MI600 in Admin -> Inverter(s) configuration
# Use the field 'Communication options'

if (!defined('checkaccess')) {die('Direct access not permitted');}
$CMD_RETURN = ''; // always initialize
$MATCHES = '';
$ERR = "0";
$SDTE = date("Ymd H:i:s");
$OPTIONS = ${'COMOPTION'.$invt_num};
list ($HOST, $USER, $PASSWD) = explode(" ", $OPTIONS, 3);
$URL = "http://".$HOST."/status.html";
$SRVDIR = $_SERVER['DOCUMENT_ROOT'];
if (!$SRVDIR) {
  // O.K. the server variable is not set, so we try another dirty hack to get the base-path.
  // Our script is located in directory /??/.../??/123solar/scripts/protocols. 
  // We now go up three directories in the path and that is our base path.
  $SRVDIR = dirname(__FILE__, 4);
}
$LOGFILE = "$SRVDIR/123solar/data/invt".$invt_num."/mi600.log";
$MI600_DATAFILE = "$SRVDIR/123solar/data/invt".$invt_num."/mi600.dat";
$LAST_KWHTOTAL_FILE = "$SRVDIR/123solar/data/invt".$invt_num."/lastKWHtotal.dat";
// strings
$I1V = null;
$I1A = null;
$I1P = null;
$I2V = null;
$I2A = null;
$I2P = null;
$I3V = null;
$I3A = null;
$I3P = null;
$I4V = null;
$I4A = null;
$I4P = null;
// inverters
$FRQ = null;
$EFF = null;
$INVT = null;
$BOOT = null;
$KWHT = null;
// grids
$G1V = null;
$G1A = null;
$G1P = null;
$G2V = null;
$G2A = null;
$G2P = null;
$G3V = null;
$G3A = null;
$G3P = null;
// variable names to store process values for the specific inverter
$LastP = 'MI600INVT'.$invt_num.'_LASTP';
$LastPTS = 'MI600INVT'.$invt_num.'_LASTPTS';
$LastKWHT = 'MI600INVT'.$invt_num.'_LASTKWHT';
// other needed variables
$MaxRetryCount = 5;
$RetryCounter = 0;
$DataOK = 0;
$P = (float) 0;
$Dt = 0;
$MinSecondsBetweenMeasurements = 10;
$Now = time();

// initializing process variables
if (!isset($P)) $P = 0;
if (!isset($$LastP)) $$LastP = (float) 0; // last determined power value for this inverter
if (!isset($$LastPTS)) $$LastPTS = $Now - $MinSecondsBetweenMeasurements; // last determination time for the power value of this inverter
if (!isset($$LastKWHT)) $$LastKWHT = (float) 0;  // last KWH-total value of this inverter

// the first measurement should be taken immediately, the following ones at the earliest after $MinSecondsBetweenMeasurements seconds
$SecondsElapsed = ($Now - $$LastPTS);
if ($SecondsElapsed < $MinSecondsBetweenMeasurements) {
  // we have to wait at least n sconds
  sleep($MinSecondsBetweenMeasurements - $SecondsElapsed);
}

// connect to mi600/deye
$Connected = @fsockopen($HOST, 80);
if ($Connected) {
  fclose($Connected);
  // get data from webif
  while (($RetryCounter < $MaxRetryCount) && ($DataOK == 0)) {
    $Res = exec("curl -s -u ".$USER.":".$PASSWD ." ".$URL." > ".$MI600_DATAFILE);
    // decoding the data
    $PNowString = exec("grep \"webdata_now_p = \"  ".$MI600_DATAFILE."| awk -F '\"' '{print $2}'");
    if ($PNowString) {
      // The webif of the inverter could be read successfully.
      $P = (float) $PNowString;
      $$LastP = $P;
      $DataOK = 1;
      $Now = time();
      $Dt = $Now - $$LastPTS;
      $$LastPTS = $Now;
      if (!$$LastKWHT) {
        // If value for webdata_now_p could be read from webif it is sure that the value for webdata_total_e is also existing
        $TotalKWHString = exec("grep \"webdata_total_e = \"  ".$MI600_DATAFILE."| awk -F '\"' '{print $2}'");
        if ($TotalKWHString) {
           $$LastKWHT = (float) $TotalKWHString;
        }  
        // check if our own calculated KWHT-value is higher then the value we read from inverters webif
        // in this case we use our own stored value to avoid jumping KWHT backwards due to the missing decimal places 
        // of the value from the webif
        $StoredTotalKWH = (float) exec("cat ".$LAST_KWHTOTAL_FILE);
        if ($StoredTotalKWH > $$LastKWHT) {
          $$LastKWHT = $StoredTotalKWH;
        }
      }
    } else {
      // sometimes the values are not filled correctly by the webif, imho a bug in the webif of the inverter
      $RetryCounter++;
      if ($RetryCounter < $MaxRetryCount) {
        // wait a second and try again
        sleep (1);
      } else {
        // the inverter is alive but the webif is currently not delivering the correct values, so we use the last known power value
        $P = $$LastP;
        $Now = time();
        $Dt = $Now - $$LastPTS;
        $$LastPTS = $Now;
      }
    }
  }  
  if ($Dt && $P) {
    // total-KWH value of this inverter has to be updated
    $$LastKWHT += ($P * $Dt * ((1.0 / (60.0 * 60.0)) / 1000.0));
    // save the value in case we stop the webif and restart it again later. 
    file_put_contents("$LAST_KWHTOTAL_FILE", $$LastKWHT);    
  } 
} else {
  $ERR = "could not connect to ".$HOST;
  $$LastP = (float) 0;
  $$LastPTS = time();
}

if ($DEBUG) {
  if ($RetryCounter > 0) {
    if ($RetryCounter < $MaxRetryCount) {
       file_put_contents("$LOGFILE", $SDTE.": ".$RetryCounter." retries needed to read the data from webif!\r\n", FILE_APPEND);
    } else {
       file_put_contents("$LOGFILE", $SDTE.": After ".$RetryCounter." retries data could not be read from webif, so last known power value is used!\r\n", FILE_APPEND);
    }  
  }
  file_put_contents("$LOGFILE", $SDTE.": P=".$P." P_LAST=".$$LastP." KWHTotal=".$$LastKWHT." Err=".$ERR."\r\n", FILE_APPEND);
}  

$INVT= 0; // temperature inverter fixed dummy
$BOOT = 0; // temperature dc/dc booster fixed dummy
$I1V = (float) 36; // udc fixed dummy
$I1P = (float) $P;
$I1A = (float) round($I1P / $I1V, 2);
$G1P= (float) $P; // P-AC
$G1V= (float) 230; // U-AC fixed dummy
$G1A = (float) round($G1P / $G1V, 2);
$FRQ= (float) 50; // freq  fixed dummy
$KWHT = $$LastKWHT; 

if ($ERR == "0") {
  $RET = 'OK';
} else {
  $RET = 'NOK';
}

?>
