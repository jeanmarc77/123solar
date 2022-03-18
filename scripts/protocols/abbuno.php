<?php
/**
 * /srv/http/123solar/scripts/protocols/abbuno.php
 *
 * @package default
 */

if (!defined('checkaccess')) {
    die('Direct access not permitted');
}

// For TCP Modbus https://www.modbusdriver.com/modpoll.html

$RET = '';
unset($CMD_RETURN);

if (!$DEBUG) {
    $CMD_POOLING = "modpoll -m tcp -r 41123 -c 26 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
} else {
    $CMD_POOLING = "modpoll -m tcp -r 41123 -c 26 -0 -a 1 -i -1 ${'ADR'.$invt_num} 2> /tmp/modpoll.err";
}
exec($CMD_POOLING, $CMD_RETURN);

if (isset($CMD_RETURN[13])) {
    // String 1
    $I1V = ((float) (substr($CMD_RETURN[12], 9)) / 10);
    $I1A = ((float) (substr($CMD_RETURN[11], 9)) / 10);
    $I1P = ((float) (substr($CMD_RETURN[13], 9)) * 10);
    // String 2
    $I2V = ((float) (substr($CMD_RETURN[32], 9)) / 10);
    $I2A = ((float) (substr($CMD_RETURN[31], 9)) / 10);
    $I2P = ((float) (substr($CMD_RETURN[33], 9)) * 10);
    // Other strings are not present
    $I3V = null;
    $I3A = null;
    $I3P = null;
    $I4V = null;
    $I4A = null;
    $I4P = null;
} else {
    $RET        = 'NOK';
    $CMD_RETURN = implode(',', $CMD_RETURN);
    $CMD_RETURN = "Error on Modpoll: 1st command $CMD_RETURN";
}

// 2nd command to get grid parameters
if ($RET != 'NOK') {
    unset($CMD_RETURN);
    if (!$DEBUG) {
        $CMD_POOLING = "modpoll -m tcp -r 40072 -c 50 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
    } else {
        $CMD_POOLING = "modpoll -m tcp -r 40072 -c 50 -0 -a 1 -i -1 ${'ADR'.$invt_num} 2>> /tmp/modpoll.err";
    }
    exec($CMD_POOLING, $CMD_RETURN);
    
    if (isset($CMD_RETURN[45])) {
        // Grid
        if (!${'PHASE' . $invt_num}) {
            $G1V = ((float) (substr($CMD_RETURN[19], 9)) / 10);
            $G1A = ((float) (substr($CMD_RETURN[11], 9)) / 10);
            $G1P = ((float) (substr($CMD_RETURN[23], 9)) * 10);
            $G2V = null;
            $G2A = null;
            $G2P = null;
            $G3V = null;
            $G3A = null;
            $G3P = null;
        } else {
            $G1V = ((float) (substr($CMD_RETURN[19], 9)) / 10);
            $G1A = ((float) (substr($CMD_RETURN[12], 9)) / 10);
            $G1P = round(($G1V * $G1A), 3);
            $G2V = ((float) (substr($CMD_RETURN[20], 9)) / 10);
            $G2A = ((float) (substr($CMD_RETURN[13], 9)) / 10);
            $G2P = round(($G2V * $G2A), 3);
            $G3V = ((float) (substr($CMD_RETURN[21], 9)) / 10);
            $G3A = ((float) (substr($CMD_RETURN[14], 9)) / 10);
            $G3P = round(($G3V * $G3A), 3);
        }
        
        $FRQ  = ((float) (substr($CMD_RETURN[25], 9)) / 100);
        $INVT = ((float) (substr($CMD_RETURN[42], 9)) / 10);
        $BOOT = ((float) (substr($CMD_RETURN[45], 9)) / 10);
        if (!empty(substr($CMD_RETURN[40], 9))) {
            $EFF = round((100.0 * (float) (substr($CMD_RETURN[23], 9)) / (float) (substr($CMD_RETURN[40], 9))), 2);
        } else {
            $EFF = 0;
        }
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

// 3 command to get total energy
if ($RET != 'NOK') {
    unset($CMD_RETURN);
    if (!$DEBUG) {
        $CMD_POOLING = "modpoll -m tcp -t4:int -r 40094 -c 1 -0 -a 1 -i -1 ${'ADR'.$invt_num}";
    } else {
        $CMD_POOLING = "modpoll -m tcp -t4:int -r 40094 -c 1 -0 -a 1 -i -1 ${'ADR'.$invt_num} 2>> /tmp/modpoll.err";
    }
    exec($CMD_POOLING, $CMD_RETURN);
    
    if (isset($CMD_RETURN[11])) {
        $KWHT = ((float) (substr($CMD_RETURN[11], 9)) / 100);
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

if ($RET != 'NOK') {
    $RET        = 'OK';
    $CMD_RETURN = implode(',', $CMD_RETURN);
} else {
    $RET = 'NOK';
    if ($DEBUG) {
        $time = date('Ymd-H:i:s');
        exec('cp /tmp/modpoll.err ' . $INVTDIR . '/errors/modpoll' . $time . '.err');
        file_put_contents($INVTDIR . '/errors/out' . $time . '.txt', $CMD_RETURN);
    }
}
?>
