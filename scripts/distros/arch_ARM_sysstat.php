<?php
/**
 * /srv/http/123solar/scripts/distros/arch_ARM_sysstat.php
 *
 * @package default
 */


// Commands
$UPTIME="uptime";
$CPUUSE="sar 1 1 | tail -n1 | awk '{print $3+$5}'";
$MEMTOT="free -m | grep 'Mem' | awk '{print $2}'";
$MEMUSE="free -m| grep 'Mem' | awk '{print $3}'";
$MEMFREE="free -m| grep 'Mem' | awk '{print $7}'";
$DISKUSE="df -h | grep '/$' | awk '{print $3}'";
$DISKFREE="df -h | grep '/$' | awk '{print $4}'";
?>
