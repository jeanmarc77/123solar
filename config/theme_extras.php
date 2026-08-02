<?php
/**
 * config/theme_extras.php
 *
 * Optional, theme-only settings for the "meridiana" theme (and any other
 * theme that chooses to read it the same way).
 * Deliberately kept separate from config_main.php: it is not part of
 * 123solar's own config schema (see $CFGmain in config_main.php), so it
 * can never collide with a future upstream key, and the admin panel's
 * config editor cannot touch or wipe it. Read directly by
 * styles/<theme>/header.php, guarded by file_exists() there: deleting
 * this file is safe and simply hides the link below.
 *
 * @package default
 */

// URL of the meterN installation for this same plant, shown as a link in
// the top bar. Leave empty ('') to hide the link entirely. Both apps are
// served from the same Apache vhost here (see /etc/apache2/sites-enabled),
// hence the relative path.
$METERN_URL = '/metern/';
