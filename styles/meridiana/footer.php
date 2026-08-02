<!-- #EndEditable -->
</main>

<footer class="meridiana-footer">
  <div class="meridiana-footer-inner">
    <a href="https://github.com/jeanmarc77/123solar" target="_blank" rel="noopener">Powered by 123Solar</a>
  </div>
</footer>

<script>
(function () {
  var themeBtn = document.getElementById('meridianaThemeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var body = document.body;
      var cur = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      var next = cur === 'dark' ? 'light' : 'dark';
      body.setAttribute('data-theme', next);
      document.cookie = 'color_scheme=' + next + '; path=/; max-age=31536000; samesite=lax';
      if (window.Highcharts) {
        /* Mirrors the palette set once at load time in header.php's
         * Highcharts.setOptions() call. That call only runs on page
         * load, so toggling afterwards needs to re-apply every themed
         * property here too - grid lines alone (the original version of
         * this handler) left title/legend/axis-label/tooltip text at
         * the OLD theme's color, invisible against the new background. */
        var dark = next === 'dark';
        var textColor = dark ? '#e7ebf3' : '#161b26';
        var mutedColor = dark ? '#94a1b8' : '#667085';
        var gridColor = dark ? '#253048' : '#e3e8ef';
        var tooltipBg = dark ? '#121a29' : '#ffffff';
        var themeOpts = {
          title: { style: { color: textColor } },
          subtitle: { style: { color: mutedColor } },
          xAxis: {
            gridLineColor: gridColor, lineColor: gridColor, tickColor: gridColor,
            labels: { style: { color: mutedColor } }
          },
          yAxis: {
            gridLineColor: gridColor,
            labels: { style: { color: mutedColor } },
            title: { style: { color: mutedColor } }
          },
          legend: { itemStyle: { color: textColor } },
          tooltip: { backgroundColor: tooltipBg, borderColor: gridColor, style: { color: textColor } }
        };
        Highcharts.setOptions(themeOpts);
        Highcharts.charts.forEach(function (chart) {
          if (!chart) { return; }
          chart.update(themeOpts, false);
          chart.redraw();
        });
      }
    });
  }

  var navBtn = document.getElementById('meridianaNavToggle');
  var nav = document.getElementById('meridianaNav');
  if (navBtn && nav) {
    navBtn.addEventListener('click', function () {
      var expanded = navBtn.getAttribute('aria-expanded') === 'true';
      navBtn.setAttribute('aria-expanded', String(!expanded));
      nav.classList.toggle('open');
    });
  }

  var invtSelect = document.getElementById('meridianaInvtSelect');
  if (invtSelect) {
    invtSelect.addEventListener('change', function () {
      if (this.value) { window.location.href = this.value; }
    });
  }
})();
</script>
</body>
</html>
