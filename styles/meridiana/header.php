<?php
/**
 * styles/meridiana/header.php
 *
 * Header for the "Meridiana" theme (123solar side - see the meterN tree
 * for the sibling implementation and the full rationale: responsive
 * navbar, light/dark cookie, Highcharts theming, official extension point
 * per styles/default/Explanation.txt). $TITLE, $SUBTITLE, $lgM*, $NUMINV
 * are already populated by styles/globalheader.php (config_main.php +
 * language file), which includes this file right after closing </head>.
 *
 */

// Always opens light unless the visitor has explicitly toggled to dark
// (cookie set by footer.php's toggle button): no system-preference
// auto-detection here, on purpose - a monitoring dashboard is as often
// read on a bright screen outdoors as on a phone at night, so the choice
// is left to an explicit, remembered click rather than guessed from the
// OS, which may not reflect the room the dashboard is actually read in.
$meridianaTheme = (isset($_COOKIE['color_scheme']) && $_COOKIE['color_scheme'] === 'dark') ? 'dark' : 'light';
$meridianaPage = basename(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH), '.php');
if ($meridianaPage === '') {
    $meridianaPage = 'index';
}
$meridianaSelInvt = isset($_GET['selectinvt']) ? (int) $_GET['selectinvt'] : 0;

// Optional cross-link to meterN, if installed for this plant. Kept in its
// own file (not config_main.php) so it can never collide with 123solar's
// own config schema - see config/theme_extras.php for the rationale.
$meridianaMeternUrl = '';
$meridianaThemeConfigFile = __DIR__ . '/../../config/theme_extras.php';
if (is_file($meridianaThemeConfigFile)) {
    include $meridianaThemeConfigFile;
    if (!empty($METERN_URL)) {
        $meridianaMeternUrl = $METERN_URL;
    }
}
?>
<body data-theme="<?php echo htmlspecialchars($meridianaTheme, ENT_QUOTES); ?>" class="page-<?php echo htmlspecialchars($meridianaPage, ENT_QUOTES); ?>">

<header class="meridiana-header">
  <div class="meridiana-header-inner">
    <a class="meridiana-brand" href="index.php">
      <span class="meridiana-brand-icon"><i class="fa-solid fa-solar-panel"></i></span>
      <span class="meridiana-brand-text">
        <strong><?php echo htmlspecialchars($TITLE, ENT_QUOTES); ?></strong>
        <small><?php echo htmlspecialchars(strip_tags($SUBTITLE), ENT_QUOTES); ?></small>
      </span>
    </a>

    <button type="button" class="meridiana-theme-toggle" id="meridianaThemeToggle" aria-label="Light / dark switch" title="Light / dark">
      <i class="fa-solid fa-sun"></i><i class="fa-solid fa-moon"></i>
    </button>
    <button type="button" class="meridiana-nav-toggle" id="meridianaNavToggle" aria-label="Menu" aria-expanded="false" aria-controls="meridianaNav">
      <i class="fa-solid fa-bars"></i>
    </button>

    <nav class="meridiana-nav" id="meridianaNav">
      <a href="index.php" class="<?php echo ($meridianaPage === 'index' && $meridianaSelInvt === 0) ? 'active' : ''; ?>"><i class="fa-solid fa-chart-line"></i><?php echo $lgMINDEX; ?></a>
<?php if (!empty($NUMINV) && $NUMINV > 1): ?>
      <div class="meridiana-invt-select">
        <i class="fa-solid fa-solar-panel" aria-hidden="true"></i>
        <select id="meridianaInvtSelect" aria-label="<?php echo htmlspecialchars($lgMINDEX, ENT_QUOTES); ?>">
          <option value="index.php"<?php echo $meridianaSelInvt === 0 ? ' selected' : ''; ?>>Tutti</option>
<?php for ($bi = 1; $bi <= $NUMINV; $bi++): ?>
          <option value="index.php?selectinvt=<?php echo $bi; ?>"<?php echo $meridianaSelInvt === $bi ? ' selected' : ''; ?>><?php echo htmlspecialchars(${'INVNAME' . $bi}, ENT_QUOTES); ?></option>
<?php endfor; ?>
        </select>
        <i class="fa-solid fa-chevron-down meridiana-invt-select-arrow" aria-hidden="true"></i>
      </div>
<?php endif; ?>
      <a href="production.php" class="<?php echo $meridianaPage === 'production' ? 'active' : ''; ?>"><i class="fa-solid fa-bolt"></i><?php echo $lgMPRODUCTION; ?></a>
      <a href="detailed.php" class="<?php echo $meridianaPage === 'detailed' ? 'active' : ''; ?>"><i class="fa-solid fa-table-list"></i><?php echo $lgMDETAILED; ?></a>
      <a href="comparison.php" class="<?php echo $meridianaPage === 'comparison' ? 'active' : ''; ?>"><i class="fa-solid fa-code-compare"></i><?php echo $lgMCOMPARISON; ?></a>
      <a href="info.php" class="<?php echo $meridianaPage === 'info' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i><?php echo $lgMINFO; ?></a>
<?php if ($meridianaMeternUrl !== ''): ?>
      <a href="<?php echo htmlspecialchars($meridianaMeternUrl, ENT_QUOTES); ?>" class="meridiana-nav-metern" target="_blank" rel="noopener"><i class="fa-solid fa-house-chimney-window"></i>meterN</a>
<?php endif; ?>
      <a href="admin/" class="meridiana-nav-admin"><i class="fa-solid fa-gear"></i>admin</a>
    </nav>
  </div>
</header>

<script>
/* Visual theme for Highcharts. Must run before the page's own script
 * (index.php etc.), which also calls Highcharts.setOptions() but only for
 * locale/decimal formatting: since it's a deep merge, these settings
 * survive (same finding as the meterN theme - see TODO.md point 9). */
if (window.Highcharts) {
  var meridianaDark = document.body.getAttribute('data-theme') === 'dark';
  Highcharts.setOptions({
    colors: ['#f59e0b', '#2563eb', '#16a34a', '#7c3aed', '#0891b2', '#ea580c'],
    chart: {
      backgroundColor: 'transparent',
      style: { fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif' }
    },
    title: { style: { color: meridianaDark ? '#e7ebf3' : '#161b26', fontWeight: '700' } },
    subtitle: { style: { color: meridianaDark ? '#94a1b8' : '#667085' } },
    xAxis: {
      gridLineColor: meridianaDark ? '#253048' : '#e3e8ef',
      lineColor: meridianaDark ? '#253048' : '#e3e8ef',
      tickColor: meridianaDark ? '#253048' : '#e3e8ef',
      labels: { style: { color: meridianaDark ? '#94a1b8' : '#667085' } }
    },
    yAxis: {
      gridLineColor: meridianaDark ? '#253048' : '#e3e8ef',
      labels: { style: { color: meridianaDark ? '#94a1b8' : '#667085' } },
      title: { style: { color: meridianaDark ? '#94a1b8' : '#667085' } }
    },
    legend: { itemStyle: { color: meridianaDark ? '#e7ebf3' : '#161b26' } },
    tooltip: {
      backgroundColor: meridianaDark ? '#121a29' : '#ffffff',
      borderColor: meridianaDark ? '#253048' : '#e3e8ef',
      style: { color: meridianaDark ? '#e7ebf3' : '#161b26' }
    }
  });
}
</script>

<main class="meridiana-main">
<!-- #BeginEditable "mainbox" -->
