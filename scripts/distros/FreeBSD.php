<?php
/**
 * /srv/http/metern/scripts/distros/FreeBSD.php
 *
 * @package default
 */


// Commands
$PSCMD='ps -auxw';
$UPTIME='uptime';
$CPUUSE="ps aux|awk 'NR > 0 { s +=$3 }; END {print \"cpu %\",s}' | awk '{ print $3 }'";
$MEMTOT="sysctl -a | grep 'real memory'";
$MEMUSE="sysctl -a | grep 'mem used'";
$MEMFREE="sysctl -a | grep 'avail memory'";
$DISKUSE="df -h | grep '/$' | awk '{print $2}'";
$DISKFREE="df -h | grep '/$' | awk '{print $4}'";
?>
