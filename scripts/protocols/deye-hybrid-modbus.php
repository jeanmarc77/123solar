<?php
# Stand-alone protocol for Deye Hybrid inverter
# The KWHT has not enough precision so we calculate it based on mi600-webif.php.
# License GPL-v3+
#
# Please supply 'Modbus Serial Device' for your inverter in Admin -> Inverter(s) configuration
# Use the field 'Communication options'
# We use mbpoll and Modbus RTU there and NOT modpoll

if (!defined('checkaccess')) {die('Direct access not permitted');}
$CMD_RETURN = ''; // always initialize
$MATCHES = '';
$ERR = "0";
$SDTE = date("Ymd H:i:s");
$OPTIONS = ${'COMOPTION'.$invt_num};
list ($DEV) = explode(" ", $OPTIONS, 1);
$LOGFILE = "$INVTDIR/errors/deyeHybridModbus.err";
$LAST_KWHTOTAL_FILE = "$INVTDIR/errors/lastKWHtotal.dat";

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
$LastP = 'DEYEHYBRIDINVT'.$invt_num.'_LASTP';
$LastPTS = 'DEYEHYBRIDINVT'.$invt_num.'_LASTPTS';
$LastKWHT = 'DEYEHYBRIDINVT'.$invt_num.'_LASTKWHT';
// other needed variables
$MaxRetryCount = 5;
$RetryCounter = 0;
$P = (float) 0;
$Dt = 0;
$MinSecondsBetweenMeasurements = 10;
$Now = time();
$KWHTDifference = (float) 0; // difference between the calculated KWHT-value and the (real) KWHT-value from inverters webif
$KWHTCorrectionFactor = (float) 0; // standard behaviour, no correction needed

// initializing process variables
if (!isset($P)) $P = 0;
if (!isset($$LastP)) $$LastP = (float) 0; // last determined power value for this inverter
if (!isset($$LastPTS)) $$LastPTS = $Now - $MinSecondsBetweenMeasurements; // last determination time for the power value of this inverter
if (!isset($$LastKWHT)) {
  $$LastKWHT = (float) 0;  // last KWH-total value of this inverter
  // In case LastKWHT is not properly set (i.e. after an 123solar restart) we try to load the stored value from disk.
  $StoredTotalKWH = (float) exec("cat ".$LAST_KWHTOTAL_FILE);
  if ($StoredTotalKWH > $$LastKWHT) {
    $$LastKWHT = $StoredTotalKWH;
  }
}

// the first measurement should be taken immediately, the following ones at the earliest after $MinSecondsBetweenMeasurements seconds
$SecondsElapsed = ($Now - $$LastPTS);
if ($SecondsElapsed < $MinSecondsBetweenMeasurements) {
  // we have to wait at least n sconds
  sleep($MinSecondsBetweenMeasurements - $SecondsElapsed);
}

// connect to inverter
$CMD_RETVAL = 1;
unset($CMD_RETURN);
if (!$DEBUG) {
    $CMD_POOLING = "mbpoll -a 1 -b 9600 -0 -P none $DEV -1 -c 23 -r 661 2>/dev/null";
} else {
    $CMD_POOLING = "mbpoll -a 1 -b 9600 -0 -P none $DEV -1 -c 23 -r 661 > /tmp/modpoll.err 2>&1";
}

while (($RetryCounter < $MaxRetryCount) && ($CMD_RETVAL != 0)) {
    exec($CMD_POOLING, $CMD_RETURN, $CMD_RETVAL);
    $RetryCounter++;
    if ($CMD_RETVAL != 0 ) {
	// wait a second and try again
        sleep (1);
    }
}
$RETURN = array();
foreach ($CMD_RETURN as $value) {
	$valuearray = explode(']:', $value);

	if (isset($valuearray[1])) {
		$key = substr($valuearray[0], 1);
		$RETURN[$key]  = trim($valuearray[1]);
	}
}
if (isset($CMD_RETURN)) {
    // String 1
    $I1V = (((float) ($RETURN[676])) / 10);
    $I1A = $RETURN[677];
    $I1P = $RETURN[672];
    // String 2
    $I2V = (((float) ($RETURN[678])) / 10);
    $I2A = $RETURN[679];
    $I2P = $RETURN[673];
    // GenPort
    $I3V = 230;
    $I3A = 1;
    $I3P = $RETURN[667];
    // Other strings are not present 
    $I4V = null;
    $I4A = null;
    $I4P = null;

} else {
    $RET        = 'NOK';
    $CMD_RETURN = implode(',', $CMD_RETURN);
    $CMD_RETURN = "Error on Modpoll: 1st command $CMD_RETURN";
}

if ($RET != 'NOK') {
    unset($CMD_RETURN);
    if (!$DEBUG) {
        $CMD_POOLING = "mbpoll -a 1 -b 9600 -0 -P none $DEV -1 -c 12 -r 609 2>/dev/null";
    } else {
	$CMD_POOLING = "mbpoll -a 1 -b 9600 -0 -P none $DEV -1 -c 12 -r 609 >> /tmp/modpoll.err 2>&1";
    }
    $RetryCounter = 0;
    $CMD_RETVAL = 1;
    while (($RetryCounter < $MaxRetryCount) && ($CMD_RETVAL != 0)) {
        exec($CMD_POOLING, $CMD_RETURN, $CMD_RETVAL);
        $RetryCounter++;
        if ($CMD_RETVAL != 0 ) {
            // wait a second and try again
            sleep (1);
        }
    }

    $RETURN = array();
    foreach ($CMD_RETURN as $value) {
	$valuearray = explode(']:', $value);

	if (isset($valuearray[1])) {
		$key = substr($valuearray[0], 1);
		$RETURN[$key]  = trim($valuearray[1]);
	}
    }
if (isset($CMD_RETURN[11])) {
    // Grid
    if (!${'PHASE' . $invt_num}) {
        $G1V = $I1V;
        $G1A = $I1A;
        $G1P = $I1P;
        $G2V = null;
        $G2A = null;
        $G2P = null;
        $G3V = null;
        $G3A = null;
        $G3P = null;
    } else {
        $G1V = $I1V;
        $G1A = $I1A;
        $G1P = $I1P;
        $G2V = $I2V;
        $G2A = $I2A;
        $G2P = $I2P;
        $G3V = $I3V;
        $G3A = $I3A;
	// we do not monitor the GEN port
	// the GEN port is used for mirco inverter
        //$G3P = $I3P;
        $G3P = 0;
   }

   $FRQ  = (((float) ($RETURN[609])) / 100);
   $INVT = 0;
   $BOOT = 0;
   $EFF = 0;
   if ($FRQ <= 0) { // Avoid null values at early startup
        $RET        = 'NOK';
        $CMD_RETURN = "Error on Modpoll: FRQ is null";
    }
 } else {
        $RET        = 'NOK';
        $CMD_RETURN = implode(',', $CMD_RETURN);
        $CMD_RETURN = "Error on Modpoll: 2nd command $CMD_RETURN";
 }
}

if ($RET != 'NOK') {
    unset($CMD_RETURN);
    if (!$DEBUG) {
        $CMD_POOLING = "mbpoll -a 1 -b 9600 -0 -P none $DEV -1 -c 1 -r 534 2>/dev/null";
    } else {
        $CMD_POOLING = "$POLL -a 1 -b 9600 -0 -P none $DEV -1 -c 1 -r 534 >> /tmp/modpoll.err 2>&1";
    }
    $RetryCounter = 0;
    $CMD_RETVAL = 1;
    while (($RetryCounter < $MaxRetryCount) && ($CMD_RETVAL != 0)) {
        exec($CMD_POOLING, $CMD_RETURN, $CMD_RETVAL);
        $RetryCounter++;
        if ($CMD_RETVAL != 0 ) {
            // wait a second and try again
            sleep (1);
        }
    }
    $RETURN = array();
    foreach ($CMD_RETURN as $value) {
	$valuearray = explode(']:', $value);

	if (isset($valuearray[1])) {
		$key = substr($valuearray[0], 1);
		$RETURN[$key]  = trim($valuearray[1]);
	}
   }

   if (isset($CMD_RETURN[0])) {
        $KWHT = number_format((((float) ($RETURN[534])) / 10),1);
        if ($KWHT <= 0) { // Avoid null values due to communication error
            $RET        = 'NOK';
            $CMD_RETURN = "Error on Modpoll: KWHT is null";
        }
    } else {
        $RET        = 'NOK';
        $CMD_RETURN = implode(',', $CMD_RETURN);
        $CMD_RETURN = "Error on Modpoll: 3th command $CMD_RETURN";
    }
}
/*
echo "I1P $I1P :::\n";
echo "I2P $I2P :::\n";
echo "I3P $I3P :::\n";
echo "G1V $G1V :::\n";
echo "G2V $G2V :::\n";
echo "G3V $G3V :::\n";
echo "G1P $G1P :::\n";
echo "G2P $G2P :::\n";
echo "G3P $G3P :::\n";
echo "FRQ $FRQ :::\n";
echo "KWHT $KWHT :::\n";
 */
$PNowString = ($G1P+$G2P+$G3P);

if ($PNowString) {
    // The webif of the inverter could be read successfully.
    $P = (float) $PNowString;
    $$LastP = $P;
    $Now = time();
    $Dt = $Now - $$LastPTS;
    $$LastPTS = $Now;
    // If value for webdata_now_p could be read from webif it is sure that the value for webdata_total_e is also existing
    $TotalKWHStringWebIf = number_format((((float) ($RETURN[534])) / 10),1);
      if (!$$LastKWHT) {
        if ($TotalKWHStringWebIf) {
           $$LastKWHT = (float) $TotalKWHStringWebIf;
        }  
        $StoredTotalKWH = (float) exec("cat ".$LAST_KWHTOTAL_FILE);
        if ($StoredTotalKWH > $$LastKWHT) {
          $$LastKWHT = $StoredTotalKWH;
        }
      }
      if (($TotalKWHStringWebIf) && ($$LastKWHT - (float) $TotalKWHStringWebIf > 0.1)) {
        $KWHTDifference = $$LastKWHT - (float) $TotalKWHStringWebIf;
      }
} else {
      $RetryCounter++;
        $P = $$LastP;
        $Now = time();
        $Dt = $Now - $$LastPTS;
        $$LastPTS = $Now;
} 
if ($Dt && $P) {
    // total-KWHT value of this inverter has to be updated. 
    $NewKWHTDelta = $P * $Dt * ((1.0 / (60.0 * 60.0)) / 1000.0);
    if ($KWHTDifference > 0.1) {
      // The positive drift of KWHT is greater than 0.1, we shold calculate an correction factor.
      // Normally we have an average of 5 measurements per minute --> 300 per hour --> 3600 for twelve hours.
      // The goal should be that the drift is eliminated within a day. So we use 3600 as base for our correction 
      // target, nearly a day of solar-operation. On the other hand we don't want to see an intense sawtooth graph so 
      // we limit the correction factor to a maximum of 10 percent of $NewKWHTDelta. 
      $KWHTCorrectionTarget = $KWHTDifference / 3600.0;
      $KWHTCorrectionFactor = $NewKWHTDelta / 10.0;
      if ($KWHTCorrectionFactor > $KWHTCorrectionTarget) {
        // the correction factor does not need to be so high, so we limit it
        $KWHTCorrectionFactor = $KWHTCorrectionTarget;
      }
    }
    $$LastKWHT += ($NewKWHTDelta - $KWHTCorrectionFactor);
    // save the value in case we stop the webif and restart it again later. 
    file_put_contents("$LAST_KWHTOTAL_FILE", $$LastKWHT);    
} 
$INVT= 0; // temperature inverter fixed dummy
$BOOT = 0; // temperature dc/dc booster fixed dummy
$KWHT = $$LastKWHT; 

if ($ERR == "0") {
  $RET = 'OK';
} else {
  $RET = 'NOK';
}

?>
