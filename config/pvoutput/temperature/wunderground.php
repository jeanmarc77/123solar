<?php
error_reporting(0);
// http://www.wunderground.com/weather/api/

$wkey='yourpersonalkey';
$wstationID= 'IRGIONWA74';

// Wunderground
$json=null;
$ctx = stream_context_create(array('http'=>
    array(
        'timeout' => 3
    )
));

$json = file_get_contents("http://api.wunderground.com/api/$wkey/conditions/q/pws:$wstationID.json", false, $ctx);
$weatherData = json_decode($json,true);

if (isset($weatherData['current_observation']['temp_c'])) {
$TEMP=round($weatherData['current_observation']['temp_c'],1);
$tempmem=$TEMP; // Remember last temperature if the request fails
} else {
$TEMP=$tempmem;
}
?>
