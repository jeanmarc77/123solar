<?php
/**
 * /srv/http/123solar/scripts/distros/Linux.php
 *
 * @package default
 */


// Commands
$PSCMD='ps -ef';
$UPTIME='uptime';
$CPUUSE="top -b -n 1 | awk '/^%Cpu/ { print $2+$4}'";
$MEMTOT="free --mebi | awk '/^Mem:/ { print $2}'";
$MEMUSE="free --mebi | awk '/^Mem:/ { print $3}'";
$MEMFREE="free --mebi | awk '/^Mem:/ { print $7}'";
$DISKUSE="df --human-readable / | awk 'NR > 1  {print $3}'";
$DISKFREE="df --human-readable / | awk 'NR > 1  {print $4}'";
?>
