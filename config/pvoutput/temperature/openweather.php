<?php
//error_reporting(0);
// OpenWeather
$wkey='yourkey';
$json=null;
$ctx = stream_context_create(array('http'=>
    array(
        'timeout' => 3
    )
));

$json = file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=chastre,be&units=metric&appid=$wkey", false, $ctx);
$weatherData = json_decode($json,true);
if (isset($weatherData['main']['temp'])) {
$TEMP=round($weatherData['main']['temp'],1);
$tempmem=$TEMP; // Remember last temperature if the request fails
} else {
$TEMP=$tempmem;
}
?>
