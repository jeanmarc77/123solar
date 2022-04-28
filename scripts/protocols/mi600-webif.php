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
$CURL_HANDLE = curl_init();
curl_setopt($CURL_HANDLE, CURLOPT_USERPWD, $USER. ":".$PASSWD); 
curl_setopt($CURL_HANDLE, CURLOPT_URL, $URL );
curl_setopt($CURL_HANDLE, CURLOPT_TIMEOUT, 15);
curl_setopt($CURL_HANDLE, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = (curl_exec($CURL_HANDLE));
// Checking if any error occured during request
if(curl_error($CURL_HANDLE)) {
    $CMD_RETURN = '';
    $DT = 0;
    $ERR=curl_error($CURL_HANDLE);
} else {
    // Decoding data
    if (preg_match("/webdata_now_p = .*/", $CMD_RETURN, $MATCHES)) {
        $Pnow = (float)preg_replace('/[^0-9.]+/', '', $MATCHES[0]); // keep only value
    } else {
        $ERR="parse error";
    }
    if (preg_match("/webdata_total_e = .*/", $CMD_RETURN, $MATCHES)) {
        if (!$Etotal) {
            $Etotal=(float)preg_replace('/[^0-9.]+/', '', $MATCHES[0]); // keep only value
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
                #file_put_contents("/var/www/html/dt.txt", $SDTE." Pnow=".$Pnow." P=".$P." dt=".$DT." E=".$Etotal."\r\n", FILE_APPEND);
            } else {
                $P0count++;
                #file_put_contents("/var/www/html/dt.txt", $SDTE."### P=".$P." dt=".$DT." E=".$Etotal."\r\n", FILE_APPEND);
            }
        }
    } else {
        $ERR="parse error";
    }
}
curl_close($CURL_HANDLE);
sleep (5);
file_put_contents("/var/www/html/dt.txt", $SDTE.": Pnow=".$Pnow." P=".$P." dt=".$DT." E=".$Etotal." Err=".$ERR."\r\n", FILE_APPEND);
if(!$P0count) $P=$Pnow; # check if valid actual value else keep last
if($P0count>12) {
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
if($ERR) {
    $RET = 'NOK';
} else {
    $RET = 'OK';
}



?>
