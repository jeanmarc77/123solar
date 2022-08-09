<?php
/**
 * /srv/http/123solar/scripts/distros/raspbian_ARM.php
 *
 * @package default
 */


// Commands
$PSCMD='ps -ef';
$UPTIME="uptime -p";
$CPUUSE="ps aux|awk -v nproc=`nproc` 'NR > 0 { s +=$3 }; END {print s/nproc}'";
$MEMTOT="free -m | grep 'Mem' | awk '{print $2}'";
$MEMUSE="free -m | grep 'Mem' | awk '{print $3}'";
$MEMFREE="free -m | grep 'Mem' | awk '{print $4}'";
$DISKUSE="df -h | grep root.*/$ | awk '{print $3}'";
$DISKFREE="df -h | grep root.*/$ | awk '{print $4}'";
$DISKTOT="df -h | grep root.*/$ | awk '{print $5}'";
?>
