(function () {
  var root = document.documentElement;
  var themes = ['dark', 'light', 'sunset'];
  var icons = { dark: '🌙', light: '☀️', sunset: '🌅' };
  var labels = { dark: 'Dark', light: 'Light', sunset: 'Sunset' };

  function savedTheme() {
    try {
      var saved = localStorage.getItem('beyond-theme');
      return themes.includes(saved) ? saved : 'dark';
    } catch (error) {
      return 'dark';
    }
  }

  function applyTheme(theme) {
    if (!themes.includes(theme)) theme = 'dark';
    root.dataset.theme = theme;
    var next = themes[(themes.indexOf(theme) + 1) % themes.length];
    document.querySelectorAll('.theme-toggle').forEach(function (button) {
      button.textContent = icons[theme];
      button.title = labels[theme] + ' theme · switch to ' + labels[next];
      button.setAttribute('aria-label', 'Current theme ' + labels[theme] + '. Switch to ' + labels[next] + ' theme');
    });
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', theme === 'light' ? '#f4f6fc' : theme === 'sunset' ? '#32113d' : '#050817');
  }

  function bindControls() {
    applyTheme(savedTheme());
    document.querySelectorAll('.theme-toggle:not([data-theme-bound])').forEach(function (button) {
      button.dataset.themeBound = 'true';
      button.addEventListener('click', function () {
        var current = themes.includes(root.dataset.theme) ? root.dataset.theme : 'dark';
        var next = themes[(themes.indexOf(current) + 1) % themes.length];
        try { localStorage.setItem('beyond-theme', next); } catch (error) {}
        applyTheme(next);
      });
    });

    document.querySelectorAll('#localePicker:not([data-locale-bound])').forEach(function (picker) {
      picker.dataset.localeBound = 'true';
      var locale = 'en';
      try { locale = localStorage.getItem('beyond-locale') || 'en'; } catch (error) {}
      if (Array.from(picker.options).some(function (option) { return option.value === locale; })) picker.value = locale;
      root.lang = picker.value;
      picker.addEventListener('change', function () {
        root.lang = picker.value;
        try { localStorage.setItem('beyond-locale', picker.value); } catch (error) {}
        document.dispatchEvent(new CustomEvent('beyond:locale-change', { detail: { locale: picker.value } }));
      });
    });
  }

  applyTheme(savedTheme());
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindControls);
  else bindControls();
})();
