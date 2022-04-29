<?php
// Experimental ! haven't been tested


if (!defined('checkaccess')) {die('Direct access not permitted');}

$CMD_RETURN = ''; // Always initialize
$MATCHES = '';
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
    $Pnow =(float)exec("curl -s -u ".$USER.":".$PASSWD ." ".$HOST."/status.html | grep \"webdata_now_p = \" | awk -F '\"' '{print $2}'");
    if (!$Etotal) {
        sleep(5);
        $Etotal=(float)exec("curl -s -u ".$USER.":".$PASSWD ." ".$HOST."/status.html | grep \"webdata_total_e = \" | awk -F '\"' '{print $2}'");
        file_put_contents("/var/www/html/dt.txt", $SDTE." Etotal=".$Etotal."\r\n",FILE_APPEND);
    } else {
        if($Pnow>0) {
            $tstamp = time();
            if ($otstamp && !$P0count) {
                $DT = $tstamp - $otstamp;
            } else {
               $DT = 0;
            }
            $otstamp = $tstamp;
            $P0count = 0;
            if($DT && $P) {
                $Etotal = $Etotal + $P*$DT*0.000000278;
            }
        }
    }
} else {
    $ERR = "could not connect";
    $Pnow = (float) 0;
    $P0count++;
}
sleep (5);
file_put_contents("/var/www/html/dt.txt", $SDTE.": Pnow=".$Pnow." P=".$P." dt=".$DT." E=".$Etotal." Err=".$ERR."\r\n", FILE_APPEND);
if(!$P0count) $P=$Pnow; # check if valid actual value else keep last
if($P0count>4) {
    $P=(float) 0; # multiple times ~60s connection to mi600 failed set P=0
    $DT=0;
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
    $RET = 'OK';
} else {
    $RET = 'OK';
}



?>
