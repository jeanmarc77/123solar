<?php
/**
 * /srv/http/123solar/scripts/daemon_stop.php
 *
 * @package default
 */


// 485solar-get
for ($i = 1; $i <= $NUMINV; $i++) {
	if (${'PROTOCOL' . $i} == '485solar-get' && !${'SKIPMONITORING' . $i}) {
		logevents($i, "#$i $now\tShutdown 485solar-get daemon\n\n");
		exec('485solar-get -x');
	}
}

// fronius-fslurp
for ($i = 1; $i <= $NUMINV; $i++) {
	if (${'PROTOCOL' . $i} == 'fronius-fslurp' && !${'SKIPMONITORING' . $i}) {
		logevents($i, "#$i $now\tShutdown fronius daemon\n\n");
		$output = exec('pkill -f fronius.php > /dev/null 2>&1 &');
	}
}
?>
