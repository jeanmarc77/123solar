<?php
/**
 * styles/meridiana/head.php
 *
 * Per-style <head> additions for the "Meridiana" theme, included by
 * styles/globalheader.php right before the theme's own stylesheet.
 *
 * Holds only the viewport meta tag, without which no amount of CSS makes
 * the pages usable on a phone: 123solar ships no viewport tag of its own,
 * so a mobile browser lays the page out at its default virtual width and
 * scales the result down.
 *
 * Deliberately kept out of styles/yourheader.php (the untouched, official
 * loose-file extension point): an admin.php-triggered update only
 * preserves custom style *directories*, not loose files directly under
 * styles/, so anything placed in yourheader.php is silently lost on the
 * next update. Everything a theme needs belongs inside the theme's own
 * directory instead.
 *
 * Bootstrap and Font Awesome are deliberately NOT linked here even though
 * this file is included before the theme stylesheet: they are @imported as
 * the first rule of css/style.css instead. Both orders would work today,
 * but keeping every external dependency in one place means the cascade is
 * decided by a single file, the one that also has to live with it.
 *
 * @package default
 */
?>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php
// vim: set ts=4 sw=4 noet:
