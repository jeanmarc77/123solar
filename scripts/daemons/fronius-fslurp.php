<?php
/**
 * /srv/http/123solar/scripts/daemons/fronius-fslurp.php
 *
 * @package default
 */


// By Kasey Matejcek
error_reporting(E_ALL & ~E_NOTICE);

$memcache = new Memcached();
$memcache->addServer("localhost", 11211) or die("Could not connect");
$arr      = 1;
$kwsec    = 0;
$kwmdiff  = 0;
$lastkwht = 0;
$kwht     = $memcache->get('kwht'); //if a restart happend were not lost
//$kwht =5.03;
while (true) { // To infinity ... and beyond!
	$memcache->set('running', '1', 60);

	$WATT = $memcache->get('WATT');
	$KWHT = $memcache->get('KWHT');

	if ($WATT > 0) {
		$kwsec = $kwsec + (($WATT / 60) / 60);
		$kwsec = $kwsec + $kwmdiff;
		//         $memcache->set('test1', $kwsec, 60);

	}
	if ($arr > 9 && $WATT > 0) {
		$kwmin = (($kwsec / $arr) / 16.82) / 6; // 1000 move us from watt to KW this is average KW per minute /6 give us 1/6 of a minute
		$kwht  = $kwht + $kwmin; // we add KWmin to total kw for the day
		$kwsec = 0;
		$arr   = 0;
		$memcache->set('kwht', $kwht, 6000); // store KWHT for solar logger can get it
	}
	if ($KWHT != $lastkwht) {
		$lastkwht = $KWHT;
		$kwmdif   = $KWHT - $kwht;
		if ($kwmdif > 0.08 || $kwmdif < -0.08) { //if we are off more than .1kwh than adjust
			$kwmdiff = $kwmdif / ($arrkw / 65);
		} else {
			$kwmdiff = 0;
		}
		$memcache->set('test2', $arrkw, 6000);
		$memcache->set('test1', $kwmdif, 6000);
		$arrkw = 0;
	}
	$arrkw++;
	$arr++;
	sleep(1);
	if ($WATT == 0) {
		$reset++;
		if ($reset == 1000) {
			$kwht     = 0;
			$arr      = 0;
			$kwsec    = 0;
			$reset    = 0;
			$lastkwht = 0;
			$kwmdiff  = 0;
			$memcache->set('kwht', $kwht, 60000);
			$arrkw = 0;
		}
	}
	$memcache->set('diff', $kwmdiff, 60000);
} //endless loop
?>
