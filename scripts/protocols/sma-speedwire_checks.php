<?php
/**
 * /srv/http/123solar/scripts/protocols/sma-speedwire_checks.php
 *
 * @package default
 */


if (!defined('checkaccess')) {
	//die('Direct access not permitted');
}

//Command that return inverter's status
$STATE = ''; 
$datareturn = [];
exec('sma_pool -alarm', $datareturn,$return_var);
if ($return_var !== 0 && !empty(trim($datareturn[0]))) {
$ALARM = "A $datareturn[0]";
} else {
$ALARM = null;
}
$datareturn = [];
exec('sma_pool -message', $datareturn,$return_var);
if ($return_var !== 0  && !empty(trim($datareturn[0]))) {
$MESSAGE = "M $datareturn[0]";
} else {
$MESSAGE = null;
}
$datareturn = [];
exec('sma_pool -ileak', $datareturn,$return_var);
if ($return_var !== 0) {
$ILEAK = (float)$datareturn[0];
}
$datareturn = [];
exec('sma_pool -riso', $datareturn,$return_var);
if ($return_var !== 0) {
$RISO = round($datareturn[0]/100000,1);
}
//$PPEAK = 1000;
//$PPEAKOTD = 10;

$RET = 'OK';

?>
