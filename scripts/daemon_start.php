<?php
/**
 * /srv/http/123solar/scripts/daemon_start.php
 *
 * @package default
 */


$numsma    = 0; // Num of SMA
$numfslurp = 0;
for ($i = 1; $i <= $NUMINV; $i++) {
	if (${'PROTOCOL' . $i} == '485solar-get' && !${'SKIPMONITORING' . $i}) {
		$numsma++;
	}
	if (${'PROTOCOL' . $i} == 'fronius-fslurp' && !${'SKIPMONITORING' . $i}) {
		$numfslurp++;
	}
}

// 485solar-get
if ($numsma > 0) {
	$datareturn = exec('485solar-get -r');
	if (strpos($datareturn, 'down') > 0) {
		for ($i = 1; $i <= $NUMINV; $i++) { // log on all SMA
			if (${'PROTOCOL' . $i} == '485solar-get' && !${'SKIPMONITORING' . $i}) {
				logevents($i, "#$i $now\tStart 485solar-get daemon\n\n");
			}
		}

		$i = $numsma - 1;
		if ($DEBUG) {
			$dir = 'tmp';
			exec("nohup 485solar-get -D -b -n $i >> $dir/sma_daemon.err 2>&1 &");
		} else {
			exec("nohup 485solar-get -D -n $i >/dev/null 2>&1 &");
		}
		sleep(5); // min is 3 secs
	}
}

// fronius-fslurp
if ($numfslurp > 0) {
	for ($i = 1; $i <= $NUMINV; $i++) {
		if (${'PROTOCOL' . $i} == 'fronius-fslurp' && !${'SKIPMONITORING' . $i}) {
			logevents($i, "#$i $now\tStart fronius daemon\n\n");
		}
	}
	if ($DEBUG) {
		$myFile = $DATADIR . '/123solar.err';
		file_put_contents($myFile, $stringData, FILE_APPEND);
		$output = exec('php ./daemons/fronius-fslurp.php >> ../data/123solar.err 2>&1 &');
	} else {
		$output = exec('php ./daemons/fronius-fslurp.php > /dev/null 2>&1 &');
	}
}
?>
