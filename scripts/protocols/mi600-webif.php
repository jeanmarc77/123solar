<?php
// Experimental ! haven't been tested


if (!defined('checkaccess')) {die('Direct access not permitted');}
$LOGFILE = "/var/www/html/123solar/data/invt1/mi600.log";
$LASTTOTALE = "/var/www/html/123solar/data/invt1/lastEtot.dat";
$CMD_RETURN = ''; // Always initialize
$MATCHES = '';
if (!isset($P0count)) $P0count = 0;
if (!isset($P)) $P = 0;
if (!isset($DT)) $DT = 0;
if (!isset($Etotal)) $Etotal = '';
if (!isset($otstamp)) $otstamp= '';
$ERR = "0";
$SDTE = date("Ymd H:i:s");
$OPTIONS = ${'COMOPTION'.$invt_num};
list ($HOST, $USER, $PASSWD) = explode(" ", $OPTIONS, 3);
$URL = "http://".$HOST."/status.html";

// connect to mi600
$connected = @fsockopen($HOST, 80);
if ($connected){
    fclose($connected);
    // Decoding data
    $PnowString = exec("curl -s -u ".$USER.":".$PASSWD ." ".$HOST."/status.html | grep \"webdata_now_p = \" | awk -F '\"' '{print $2}'");
    if ($PnowString) {
        $Pnow = (float) $PnowString;
        $tstamp = time();
        $DT = $tstamp - $otstamp;
        $otstamp = $tstamp;
        $P0count = 0;
    } else {
        $ERR = "could not read webdata_now_p";
        $Pnow = (float) 0;
        $P0count++;
    }
    if (!$Etotal) {
        sleep(5);
        $EtotalString = exec("curl -s -u ".$USER.":".$PASSWD ." ".$HOST."/status.html | grep \"webdata_total_e = \" | awk -F '\"' '{print $2}'");
        $EtotalLast = (float) exec("cat ".$LASTTOTALE);
        $Etotal = (float) $EtotalLast;
        if ($EtotalString) {
            if ((float) $EtotalString > $EtotalLast) $Etotal = (float) $EtotalString; # avoid jumping Etotal 
            if ($DEBUG) file_put_contents("$LOGFILE", $SDTE." init local Etotal=".$Etotal."\r\n",FILE_APPEND);
            $DT = 0;
        } else {
            $ERR = "could not read webdata_total_e";
            $Etotal = '';
        }
    }
} else {
    $ERR = "could not connect to ".$HOST;
    $Pnow = (float) 0;
    $P0count++;
}
sleep (10); # if too long the power meter won't be updateing 
if($DT && $P) {
    $dE=$P*$DT*0.000000278;
    $Etotal = $Etotal + $dE;
    if ($DEBUG) file_put_contents("$LOGFILE", "                   dE=".$dE." dt=".$DT."\r\n",FILE_APPEND);
}
file_put_contents("$LASTTOTALE", $Etotal);
if ($DEBUG) file_put_contents("$LOGFILE", $SDTE.": Pnow=".$Pnow." P=".$P." E=".$Etotal." Err=".$ERR."\r\n", FILE_APPEND);
if($P0count==0) $P=(float) $Pnow; # check if valid actual value else keep last
if($P0count>5) {
    $P=(float) 0; # multiple times ~60s connection to mi600 failed set P=0
    $tstamp = time();
    $DT = $tstamp - $otstamp;
    $otstamp = $tstamp;
}


// strings
$I1V  = null;
$I1A  = null;
$I1P  = null;
$I2V  = null;
$I2A  = null;
$I2P  = null;
$I3V = null;
$I3A = null;
$I3P = null;
$I4V = null;
$I4A = null;
$I4P = null;

// inverter
$FRQ  = null;
$EFF  = null;
$INVT = null;
$BOOT = null;
$KWHT = null;

// grid
$G1V = null;
$G1A = null;
$G1P = null;
$G2V = null;
$G2A = null;
$G2P = null;
$G3V = null;
$G3A = null;
$G3P = null;

$INVT= 0; // Temperature inverter fixed dummy
$BOOT = 0; // Temperature dc/dc booster fixed dummy
$I1V = (float) 36; // udc fixed dummy
$I1P = (float) $P;
$I1A = (float) round($I1P/$I1V,2);
$G1P= (float) $P; //pac
$G1V= (float) 230; // uac fixed dummy
$G1A = (float) round($G1P/$G1V,2);
$FRQ= (float) 50; //freq  fixed dummy
$KWHT= (float) $Etotal;
if($ERR != "0") {
    $RET = 'OK'; #no NOK to enable automatic retry
} else {
    $RET = 'OK';
}



?>
