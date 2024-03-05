<?php

// Contribution by Torsten Mueller : torsten@archesoft.de
// Paypal: torsten@archesoft.de

// this little helper builds the bridge between 123solar
// and Solaredge Inverters with activated modbus-TCP with
// the help of sunspec-monitor https://github.com/tjko/sunspec-monitor/
// so it is more a protocol-helper than it does implement a protocol itself

// first download sunspec-monitor to a fine place and make it execuatble
// example: git clone https://github.com/tjko/sunspec-monitor.git
// chmod +x /your/path/to/sunspec-status
// make a dry run /your/path/to/sunspec-status to see, if all Perl modules
// are installed, if not, install the missing modules

// Configure your Solaredge Inverter to accept modbus TCP request
// i have only inverters with LCD display, so i had to remove the cover
// of the inverter to access the buttons near the LCD display to
// access the installer settings. There in the communication settings
// you must enable modbus TCP and look for the port of communication
// on older inverters this is port 501, on newer inverters 1501
// if the inverter doesn't receive modbus request within a timeout frame,
// it closes the modbus tcp until restart. I think you can set the
// communication timeout too with the buttons.
// for inverters without a display you have to use your app or the
// webinterface to enable modbus TCP access
// So you need to know the IP address, port and modbus ID (standard is 1)

// check, if you get values back from the inverter by issuing
// /your/path/to/sunspec-status --port=502 --address=1 --meter=0 --verbose 192.168.11.37
// You should see something like this:

/*
INVERTER:
             Model: SolarEdge  SE11400
  Firmware version: 3.1968
     Serial Number: 7Dxxxxxx

            Status: ON (MPPT)

 Power Output (AC):         8014 W
  Power Input (DC):         8136 W
        Efficiency:        98.50 %
  Total Production:      148.122 kWh
      Voltage (AC):       239.50 V (59.95 Hz)
      Current (AC):        33.64 A
      Voltage (DC):       360.60 V
      Current (DC):        22.56 A
       Temperature:        53.97 C (heatsink)
*/

// next we test csv format with
// /your/path/to/sunspec-status --port=502 --address=1 --meter=0 192.168.11.37
// example output:
// 2023-08-12 03:48:50,SLEEPING,0,0,6520263,0.00,0.00,0.00,0.00,0.00,0,0,0,0

// now configure 123solar
// choose as inverter protocol solaredge-modbustcp
// Communication options -> /your/path/to/sunspec-status --port=502 --address=1 --meter=0 192.168.11.37
// it is the same command you tested before to get csv output

// Changes
// 2024-03-05 fix division by 0 error (efficency) if no production yet

if (!defined('checkaccess')) {die('Direct access not permitted');}

$CMD_RETURN = ''; // Always initialize


$CMD_to_get_values_from_SE = ${'COMOPTION'.$invt_num};

$CMD_RETURN = system($CMD_to_get_values_from_SE);

// 2024-03-05 08:37:48,ON (MPPT),44,44,508895,399.70,1.61,751.30,0.06,13.28,0,0,0,0   small production
// 2024-03-05 08:38:13,ON (MPPT),0,0,263264,399.20,1.60,751.30,0.00,19.99,0,0,0,0     no production yet (morning)

// we switch to csv, as i can't fix json decode errors

$werte = explode(",", $CMD_RETURN);

//      0  "timestamp": "2023-08-03 07:44:54",
//      1  "status": "ON (MPPT)",
//      2  "ac_power": 1052,
//      3  "dc_power": 1068,
//      4  "total_production": 6211297,
//      5  "ac_voltage": 400.90,
//      6  "ac_current": 4.72,
//      7  "dc_voltage": 747.40,
//      8  "dc_current": 1.43,
//      9  "temperature": 45.02,
//      10 "exported_energy": 0,
//      11 "imported_energy": 0,
//      12 "exported_energy_m2": 0,
//      13 "imported_energy_m2": 0

if ($werte[4] > 0 || $werte[5] > 0) {
        $I1V = (float) $werte[7]; // DC part
        $I1A = (float) $werte[8];
        $I1P = (float) $werte[3];

        $G1V = (float) $werte[5]; // AC part
        $G1A = round($werte[6],2);
        $G1P = (float) $werte[2];

        $KWHT = (float) ($werte[4]/1000); // yieldtotal
        $INVT = (float) $werte[9];
        // fix division by 0 error
        if ($I1P > 0) {
            $EFF = round(($G1P/($I1P/100)),2);
        } else {
            $EFF = 0;
        }
        $RET = 'OK';
} else {
        $RET = 'NOK';
}

?>

