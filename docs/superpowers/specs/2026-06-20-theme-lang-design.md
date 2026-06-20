# Theme & Language System — Design Spec
**Date:** 2026-06-20
**Status:** Approved

---

## Overview

Add dark/light mode toggle and EN/VI language toggle to the Sapiens House website. Preferences persist in `localStorage` so they survive page navigation. Admin panel is always light with a distinct accent color.

---

## Decisions

| Question | Decision |
|---|---|
| Client light mode background | `#F5F0E8` (cream warm) |
| Client dark mode | Existing colors — no change |
| Toggle placement | Navbar, left of Reserve button |
| Language default | English (`en`) |
| Theme default | Dark (`dark`) |
| Implementation approach | CSS custom properties + vanilla JS (no server round-trip) |
| Admin theme | Always light — no toggle |
| Admin sidebar | `#FFFFFF` |
| Admin content area | `#FAF8F5` |
| Admin accent | Deep Bronze `#8B6340` |

---

## Architecture

### Theme System

CSS custom properties on `<html>` drive all colors. Dark mode = no attribute (default). Light mode = `data-theme="light"` on `<html>`.

An inline `<script>` in `<head>` (before Vite assets) reads `localStorage` and sets the attribute synchronously — prevents any flash of wrong theme on page load.

```
localStorage key: sp-theme
Values: 'dark' (default) | 'light'
```

### Language System

Static UI strings use `data-en` / `data-vi` attributes. A JS init function swaps `textContent` once on load. Dynamic DB content uses paired elements hidden/shown by CSS class.

```
localStorage key: sp-lang
Values: 'en' (default) | 'vi'
```

CSS approach for dynamic bilingual content:
```css
[data-lang="en"] .lang-vi { display: none; }
[data-lang="vi"] .lang-en { display: none; }
```

### Admin Panel

`<html class="admin-mode">` — always light, never reads theme preference. Separate CSS block overrides all colors unconditionally.

---

## CSS Variables — Light Mode

```css
/* app.css */
[data-theme="light"] {
  --color-cave-black:  #F5F0E8;
  --color-cave-dark:   #FAF8F5;
  --color-cave-mid:    #EDE8E0;
  --color-cave-border: #D4C9B4;
  --color-sand:        #2A2420;
  --color-sand-light:  #0A0A08;
  --color-sand-muted:  #6B5A48;
  /* gold unchanged — works on both themes */
}
```

## CSS Variables — Admin Mode

```css
/* app.css */
.admin-mode {
  --admin-bg-sidebar:  #FFFFFF;
  --admin-bg-content:  #FAF8F5;
  --admin-bg-card:     #FFFFFF;
  --admin-border:      #EDE8E0;
  --admin-text:        #0A0A08;
  --admin-text-muted:  #6B5A48;
  --admin-accent:      #8B6340;
  --admin-accent-hover:#7A5535;
}
```

---

## Inline Anti-Flash Script

Placed in `<head>` before `@vite`, inside `client.blade.php` only:

```html
<script>
(function(){
  var t = localStorage.getItem('sp-theme') || 'dark';
  var l = localStorage.getItem('sp-lang')  || 'en';
  document.documentElement.setAttribute('data-theme', t);
  document.documentElement.setAttribute('data-lang', l);
})();
</script>
```

---

## Navbar Toggle UI

Added to `navbar.blade.php` inside `.sp-nav-inner`, before `.sp-nav-cta`:

```html
<!-- Language toggle -->
<div id="sp-lang-toggle" role="group" aria-label="Language">
  <button class="sp-lang-btn" data-lang="en" aria-pressed="true">EN</button>
  <span aria-hidden="true">·</span>
  <button class="sp-lang-btn" data-lang="vi" aria-pressed="false">VI</button>
</div>

<!-- Theme toggle -->
<button id="sp-theme-toggle" aria-label="Toggle theme">
  <!-- sun SVG: shown when dark mode active -->
  <!-- moon SVG: shown when light mode active -->
</button>
```

Active lang button: `color: var(--color-gold)`, weight 600.
Inactive: `color: var(--color-sand-muted)`.
Theme icon: 16px, `color: var(--color-sand-muted)`, hover gold.

On mobile: both toggles appear inside `#sp-mobile-menu` at bottom.

---

## JavaScript (app.js additions)

```js
// init — runs on DOMContentLoaded
function initPreferences() {
  const theme = localStorage.getItem('sp-theme') || 'dark';
  const lang  = localStorage.getItem('sp-lang')  || 'en';
  applyTheme(theme);
  applyLang(lang);
}

function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('sp-theme', theme);
  // update toggle icon visibility
}

function applyLang(lang) {
  document.documentElement.setAttribute('data-lang', lang);
  localStorage.setItem('sp-lang', lang);
  // swap data-en / data-vi text nodes
  document.querySelectorAll('[data-en]').forEach(el => {
    el.textContent = lang === 'vi' ? el.dataset.vi : el.dataset.en;
  });
  // update aria-pressed on lang buttons
}

// event listeners on #sp-theme-toggle and .sp-lang-btn
```

---

## Strings to Translate (Phase 1 — Navbar & Footer)

| Element | EN | VI |
|---|---|---|
| Nav: Home | Home | Trang chủ |
| Nav: Story | Story | Câu chuyện |
| Nav: Menu | Menu | Thực đơn |
| Nav: Community | Community | Cộng đồng |
| Nav: Reserve (CTA) | Reserve | Đặt bàn |
| Mobile: Reserve a Table | Reserve a Table | Đặt một bàn |
| Footer: Working Space hours label | Working Space | Không gian làm việc |
| Footer: Bistro Bar hours label | Bistro Bar | Bistro Bar |

Dynamic content (menu item names, event titles, reservation labels) uses `name_en`/`name_vi` DB columns already — Blade renders both, CSS hides the inactive one.

---

## File Changes

| File | Change |
|---|---|
| `resources/css/app.css` | Add `[data-theme="light"]` block, `.lang-vi`/`.lang-en` visibility, `.admin-mode` block, navbar toggle styles |
| `resources/views/layouts/client.blade.php` | Add inline anti-flash script in `<head>`, add `data-theme`/`data-lang` via script (handled by inline script) |
| `resources/views/layouts/admin.blade.php` | Add `class="admin-mode"` to `<html>` |
| `resources/views/components/navbar.blade.php` | Add lang + theme toggle buttons, add `data-en`/`data-vi` to nav links |
| `resources/views/components/footer.blade.php` | Add `data-en`/`data-vi` to static strings |
| `resources/js/app.js` | Add `initPreferences()`, `applyTheme()`, `applyLang()`, event listeners |
| `resources/views/components/admin/sidebar.blade.php` | Replace inline styles with `.admin-mode` CSS classes |
| `resources/views/components/admin/topbar.blade.php` | Replace inline styles with `.admin-mode` CSS classes |
| `resources/views/layouts/admin.blade.php` | Replace inline styles with `.admin-mode` CSS classes |

---

## Out of Scope

- Translating hero/about/community section body copy (copywriting task)
- SEO meta tags per language
- URL-based routing (`/vi/`, `/en/`)
- Dark mode toggle in admin panel
