<?php
/**
 * /srv/http/123solar/scripts/distros/ubuntu_x86.php
 *
 * @package default
 */


// Commands
$PSCMD='ps -ef';
$UPTIME='uptime';
$CPUUSE="ps aux|awk 'NR > 0 { s +=$3 }; END {print \"cpu %\",s}' | awk '{ print $3 }'";
$MEMTOT="free -t -m | grep 'Total' | awk '{print $2}'";
$MEMUSE="free -t -m | grep 'Total' | awk '{print $3}'";
$MEMFREE="free -t -m | grep 'Total' | awk '{print $4}'";
$DISKUSE="df -h ./ | grep % | awk '{print $2}'";
$DISKFREE="df -h ./ | grep % | awk '{print $4}'";
?>
