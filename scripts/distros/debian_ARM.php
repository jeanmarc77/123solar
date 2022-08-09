<?php
/**
 * /srv/http/123solar/scripts/distros/debian_ARM.php
 *
 * @package default
 */


// Commands
$PSCMD='ps -ef';
$UPTIME='uptime';
$CPUUSE="ps aux|awk 'NR > 0 { s +=$3 }; END {print \"cpu %\",s}' | awk '{ print $3 }'";
$MEMTOT="free -m | grep 'Mem' | awk '{print $2}'";
$MEMUSE="free -m| grep 'buffers/cache' | awk '{print $3}'";
$MEMFREE="free -m| grep 'buffers/cache' | awk '{print $4}'";
$DISKUSE="df -h | grep rootfs | awk '{print $3}'";
$DISKFREE="df -h | grep rootfs | awk '{print $4}'";
?>
