<?php
// Experimental ! haven't been tested

if (!defined('checkaccess')) {die('Direct access not permitted');}

$CMD_RETURN = ''; // Always initialize
$match = '';
$err = "no error";
$SDTE = date("Ymd H:i:s");
$options = ${'COMOPTION'.$invt_num};
list ($host, $username, $password) = explode(" ", $options,3);
$url = "http://".$host."/status.html";
$ch = curl_init();
curl_setopt($ch, CURLOPT_USERPWD, $username. ":".$password); 
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = (curl_exec($ch));
// Checking if any error occured during request
if(curl_error($ch)) {
    $CMD_RETURN = '';
    $dt = 0;
    $err=curl_error($ch);
} else {
    // Decoding data
    if (preg_match("/webdata_now_p = .*/", $CMD_RETURN, $match)) {
        $Pnow = (float)preg_replace('/[^0-9.]+/', '', $match[0]); // keep only value
    } else {
        $err="parse error";
    }
    if (preg_match("/webdata_total_e = .*/", $CMD_RETURN, $match)) {
        if (!$Etotal) {
            $Etotal=(float)preg_replace('/[^0-9.]+/', '', $match[0]); // keep only value
        } else {
            if($Pnow>0) {
                $tstamp = time();
                if ($otstamp && !$P0count) {
                    $dt = $tstamp - $otstamp;
                } else {
                    $dt = 0;
                }
                $otstamp = $tstamp;
                $P0count = 0;
                if($dt && $P) {
                    $Etotal = $Etotal + $P*$dt*0.000000278;
                }
                #file_put_contents("/var/www/html/dt.txt", $SDTE." Pnow=".$Pnow." P=".$P." dt=".$dt." E=".$Etotal."\r\n", FILE_APPEND);
            } else {
                $P0count++;
                #file_put_contents("/var/www/html/dt.txt", $SDTE."### P=".$P." dt=".$dt." E=".$Etotal."\r\n", FILE_APPEND);
            }
        }
    } else {
        $err="parse error";
    }
}
curl_close($ch);
sleep (5);
file_put_contents("/var/www/html/dt.txt", $SDTE.": Pnow=".$Pnow." P=".$P." dt=".$dt." E=".$Etotal." Err=".$err."\r\n", FILE_APPEND);
if(!$P0count) $P=$Pnow; # check if valid actual value else keep last
if($P0count>12) {
    $P=(float) 0; # multiple times connection to mi600 failed set P=0
    $dt=0;
}


$INVT= 30; // Temperature inverter
$BOOT = 30; // Temperature dc/dc booster

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

$I1V = (float) 34; // udc
$I1P = (float) $P;
$I1A = (float) round($I1P/$I1V,2);
$G1P= (float) $P; //pac
$G1V= (float) 230; // uac
$G1A = (float) round($G1P/$G1V,2);
$FRQ= (float) 50;
$KWHT= (float) $Etotal;
$RET = 'OK';


?>
