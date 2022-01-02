<?php
//Shared Memory usage
// Make sure you only use a tmpfs. Don't put a / at the end of the variable path. 
$TMPFS = '/dev/shm';

// live things
$LIVEMEMORY = $TMPFS . '/123s_LIVEMEMORY.json';
// awake + pmotd + pmotdt + pmotdmulti + pmotdtmulti +5minflagX + invtstatX + mailqX + AWt + AWriso + AWileak + peakotdX + peakoatX
$MEMORY   = $TMPFS . '/123s_MEMORY.json';
?>