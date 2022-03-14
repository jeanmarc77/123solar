<?php
/**
 * /srv/http/metern/scripts/distros/arch_ARM.php
 *
 * @package default
 */


// Commands
$UPTIME="uptime";
$CPUUSE="ps aux|awk 'NR > 0 { s +=$3 }; END {print \"cpu %\",s}' | awk '{ print $3 }'";
$MEMTOT="free -m | grep 'Mem' | awk '{print $2}'";
$MEMUSE="free -m| grep 'Mem' | awk '{print $3}'";
$MEMFREE="free -m| grep 'Mem' | awk '{print $7}'";
$DISKUSE="df -h | grep '/$' | awk '{print $2}'";
$DISKFREE="df -h | grep '/$' | awk '{print $4}'";
?>
