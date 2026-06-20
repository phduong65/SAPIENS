// ── Preferences: Theme & Language ─────────────────────────────

// Color maps: dark hex → light replacement (for inline-styled elements)
var DARK_TEXT_MAP = {
    '#E5D9C8': '#0A0A08',   // headings
    '#C9B99A': '#2A2420',   // body text
    '#8C7E6A': '#6B5A48',   // muted
    '#3A3A35': '#4A3F35',   // dim
    '#B8925A': '#8B6340',   // accent
};
var LIGHT_TEXT_MAP = {
    '#0A0A08': '#E5D9C8',
    '#2A2420': '#C9B99A',
    '#6B5A48': '#8C7E6A',
    '#4A3F35': '#3A3A35',
    '#8B6340': '#B8925A',
};

function patchInlineColors(theme) {
    var map = theme === 'light' ? DARK_TEXT_MAP : LIGHT_TEXT_MAP;
    // Target all elements with inline style= containing a color we know
    Object.keys(map).forEach(function(from) {
        var to = map[from];
        // color property
        document.querySelectorAll('[style*="color:' + from + '"]').forEach(function(el) {
            el.style.color = to;
        });
        // Store original so toggling back works — use data attr
    });
}

function patchHeroGradient(theme) {
    // The dark gradient overlay div inside #sp-hero is set as inline style;
    // CSS [data-theme="light"] handles it via class selector but this JS
    // backup ensures it applies on dynamic toggle without needing !important fight.
    var hero = document.getElementById('sp-hero');
    if (!hero) return;
    // Find the first direct-child <div> that has the background gradient
    var overlayDiv = hero.querySelector('div.absolute.inset-0:not(.grain-overlay)');
    if (!overlayDiv) return;
    if (theme === 'light') {
        overlayDiv.dataset.origBg = overlayDiv.dataset.origBg || overlayDiv.style.background;
        overlayDiv.style.background =
            'radial-gradient(ellipse 90% 70% at 50% 20%, rgba(139,99,64,0.05) 0%, transparent 65%), ' +
            'linear-gradient(180deg, #EEEAE2 0%, #F5F0E8 35%, #FAF8F5 70%, #F5F0E8 100%)';
    } else {
        if (overlayDiv.dataset.origBg) {
            overlayDiv.style.background = overlayDiv.dataset.origBg;
        }
    }
}

function patchSectionBackgrounds(theme) {
    var darkBgs = ['#0A0A08', '#050503', '#0F0F0D', '#080806'];
    var sections = document.querySelectorAll('#sp-main section');
    sections.forEach(function(sec) {
        var inlineStyle = sec.getAttribute('style') || '';
        var isDark = darkBgs.some(function(c) { return inlineStyle.indexOf(c) !== -1; });
        if (isDark) {
            if (theme === 'light') {
                sec.dataset.origBg = sec.dataset.origBg || sec.style.background || sec.style.backgroundColor;
                sec.style.setProperty('background', 'var(--sp-bg)', 'important');
            } else {
                if (sec.dataset.origBg) {
                    sec.style.background = sec.dataset.origBg;
                    sec.style.removeProperty('background');
                    sec.style.background = sec.dataset.origBg;
                }
            }
        }
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('sp-theme', theme);

    patchHeroGradient(theme);
    patchSectionBackgrounds(theme);
    patchInlineColors(theme);

    // Sync icon visibility (both desktop + mobile toggles)
    document.querySelectorAll('#sp-theme-toggle, #sp-theme-toggle-mobile').forEach(function(btn) {
        if (!btn) return;
        var sun  = btn.querySelector('.icon-sun');
        var moon = btn.querySelector('.icon-moon');
        if (theme === 'light') {
            if (sun)  sun.style.display  = 'none';
            if (moon) moon.style.display = 'block';
        } else {
            if (sun)  sun.style.display  = 'block';
            if (moon) moon.style.display = 'none';
        }
    });
}

// ── i18n: Dictionary-based language switching ───────────────
var _i18nCache = {};

function loadDictionary(locale) {
    if (_i18nCache[locale]) {
        return Promise.resolve(_i18nCache[locale]);
    }
    var stored = sessionStorage.getItem('sp.dict.' + locale);
    if (stored) {
        try {
            _i18nCache[locale] = JSON.parse(stored);
            return Promise.resolve(_i18nCache[locale]);
        } catch (e) { /* corrupt cache — fetch fresh */ }
    }
    return fetch('/translations/' + locale)
        .then(function (r) { return r.json(); })
        .then(function (dict) {
            _i18nCache[locale] = dict;
            try { sessionStorage.setItem('sp.dict.' + locale, JSON.stringify(dict)); } catch (e) {}
            return dict;
        });
}

function applyDictionary(dict) {
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
        var key = el.dataset.i18n;
        if (dict[key] !== undefined) {
            el.textContent = dict[key];
        }
    });
}

function applyLang(lang) {
    document.documentElement.setAttribute('data-lang', lang);
    localStorage.setItem('sp-lang', lang);

    loadDictionary(lang).then(applyDictionary);

    document.querySelectorAll('.sp-lang-btn').forEach(function (btn) {
        btn.setAttribute('aria-pressed', String(btn.dataset.lang === lang));
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });

    // Persist to server cookie (background)
    var token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        fetch('/locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.content,
            },
            body: JSON.stringify({ locale: lang }),
        });
    }
}

function initPreferences() {
    var theme = localStorage.getItem('sp-theme') || 'dark';
    // Use server locale as initial lang (from <html lang=""> attr set by LocaleMiddleware)
    var lang  = document.documentElement.getAttribute('lang') || localStorage.getItem('sp-lang') || 'en';
    applyTheme(theme);
    // Don't fetch dictionary on load — server already rendered correct locale.
    // Only update button states.
    document.querySelectorAll('.sp-lang-btn').forEach(function (btn) {
        btn.setAttribute('aria-pressed', String(btn.dataset.lang === lang));
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });
    localStorage.setItem('sp-lang', lang);
}

document.addEventListener('DOMContentLoaded', function () {
    initPreferences();

    // Theme toggle (desktop)
    var themeBtn = document.getElementById('sp-theme-toggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            var current = localStorage.getItem('sp-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // Theme toggle (mobile)
    var themeBtnMobile = document.getElementById('sp-theme-toggle-mobile');
    if (themeBtnMobile) {
        themeBtnMobile.addEventListener('click', function() {
            var current = localStorage.getItem('sp-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // Language buttons (all — desktop and mobile share .sp-lang-btn class)
    document.querySelectorAll('.sp-lang-btn').forEach(function(btn) {
        btn.addEventListener('click', function() { applyLang(btn.dataset.lang); });
    });
});
