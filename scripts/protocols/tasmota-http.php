<?php


// Contribution by Torsten Mueller : torsten@archesoft.de
// Paypal: torsten@archesoft.de

// smart plugs with tasmota flashed can measure
// voltage, ... and provide a http interface to get
// the values of this measurement

// so smart plugs are inverters? For sure not. But ...
// micro inverters often only have an app, that send
// the data to the china cloud. Probably they have an API
// too. But who wants to rely on an external cloud ?
// In this case it's better to do the measurements itself
// with the help of smart plugs, tasmota flashed.

// You should know the IP of your Tasmota device.
// Enter the IP into your webbrowser. Like:
// http://192.168.11.87/
// Do you see the Tasmota webinterface with energy values ?
// Great, next step is to check, if we get the energy values
// as better parsable values back. Please try
// http://192.168.11.87/cm?cmnd=status%208
// You should get back something like this:
// {"StatusSNS":{"Time":"2023-08-09T16:26:02","ENERGY":{"TotalStartTime":"2023-04-17T13:16:49","Total":437.074,"Yesterday":3.681,"Today":1.553,"Power":123,"ApparentPower":145,"ReactivePower":76,"Factor":0.85,"Voltage":296,"Current":0.488}}}
// Great, now you have the URL we can check.


// now configure 123solar
// choose as inverter protocol tasmota-http
// Communication options -> http://192.168.11.87/cm?cmnd=status%208
// it is the same URL you tested before


if (!defined('checkaccess')) {die('Direct access not permitted');}

$CMD_RETURN = ''; // Always initialize


$URL_to_get_values_from = ${'COMOPTION'.$invt_num};

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$URL_to_get_values_from");
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$CMD_RETURN = strip_tags(curl_exec($ch));
curl_close($ch);

// {"StatusSNS":{"Time":"2023-08-09T16:26:02","ENERGY":{"TotalStartTime":"2023-04-17T13:16:49","Total":437.074,"Yesterday":3.681,
// "Today":1.553,"Power":123,"ApparentPower":145,"ReactivePower":76,"Factor":0.85,"Voltage":296,"Current":0.488}}}

$split1 = '","Total":';
$w1 = explode("$split1", $CMD_RETURN);
$w2 = explode(",", $w1[1]);

$KWHT = (float) ($w2[0]); // yieldtotal

$split2 = ',"Power":';
$w3 = explode("$split2", $w1[1]);
$w4 = explode(",", $w3[1]);

$G1P = (float) ($w4[0]);

$split3 = ',"Voltage":';
$w5 = explode("$split3", $w1[1]);
$w6 = explode(",", $w5[1]);

$G1V = (float) ($w6[0]);

$split4 = ',"Current":';
$w7 = explode("$split4", $w1[1]);
$w8 = explode("}}", $w7[1]);

$G1A = (float) ($w8[0]);

if ($G1V > 0 || $KWHT > 0) {
        $RET = 'OK';
} else {
        $RET = 'NOK';
}

?>

