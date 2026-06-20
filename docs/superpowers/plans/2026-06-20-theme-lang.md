# Theme & Language System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add dark/light mode toggle and EN/VI language toggle to the client site, persisted in `localStorage`; restyle the admin panel with a permanent light theme using Deep Bronze accent.

**Architecture:** CSS custom properties on `<html data-theme>` drive all client colors; an inline `<script>` in `<head>` reads `localStorage` before Vite assets render to eliminate flash. Language swapping is pure JS using `data-en`/`data-vi` attributes for static strings and `.lang-en`/`.lang-vi` CSS classes for dynamic DB content. Admin panel uses `<html class="admin-mode">` with unconditional light CSS — no toggle.

**Tech Stack:** Laravel Blade · Tailwind CSS v4 · Vanilla JS · localStorage

---

## File Map

| File | Action | Responsibility |
|---|---|---|
| `source/resources/css/app.css` | Modify | Add light-mode vars, lang visibility, admin-mode vars, toggle styles |
| `source/resources/views/layouts/client.blade.php` | Modify | Add anti-flash inline script in `<head>` |
| `source/resources/views/components/navbar.blade.php` | Modify | Add lang + theme toggle buttons; add `data-en`/`data-vi` to links |
| `source/resources/views/components/footer.blade.php` | Modify | Add `data-en`/`data-vi` to static strings |
| `source/resources/js/app.js` | Modify | Add `initPreferences`, `applyTheme`, `applyLang`, event listeners |
| `source/resources/views/layouts/admin.blade.php` | Modify | Add `admin-mode` class to `<html>`; remove inline body styles |
| `source/resources/views/components/admin/sidebar.blade.php` | Modify | Replace inline styles with `.admin-mode` CSS classes |
| `source/resources/views/components/admin/topbar.blade.php` | Modify | Replace inline styles with `.admin-mode` CSS classes |

---

## Task 1: CSS — Light Mode Variables & Language Visibility

**Files:**
- Modify: `source/resources/css/app.css`

- [ ] **Step 1: Add light-mode custom property overrides after the `@theme` block**

Open `source/resources/css/app.css`. After the closing `}` of the `@theme { ... }` block (line ~20), add:

```css
/* ── LIGHT MODE OVERRIDE ──────────────────────────────────────── */
[data-theme="light"] {
    --color-cave-black:  #F5F0E8;
    --color-cave-dark:   #FAF8F5;
    --color-cave-mid:    #EDE8E0;
    --color-cave-border: #D4C9B4;
    --color-sand:        #2A2420;
    --color-sand-light:  #0A0A08;
    --color-sand-muted:  #6B5A48;
}

/* Light mode body override (body uses hardcoded hex, not var) */
[data-theme="light"] body {
    background-color: #F5F0E8;
    color: #2A2420;
}

/* ── LANGUAGE VISIBILITY ──────────────────────────────────────── */
[data-lang="en"] .lang-vi { display: none; }
[data-lang="vi"] .lang-en { display: none; }
```

- [ ] **Step 2: Add navbar toggle styles** — append after the `@media (max-width: 768px)` navbar block (around line ~197):

```css
/* ── LANG & THEME TOGGLES ─────────────────────────────────────── */
#sp-lang-toggle {
    display: flex; align-items: center; gap: 0.35rem;
    flex-shrink: 0;
}

.sp-lang-btn {
    background: none; border: none; cursor: pointer; padding: 0;
    font-family: 'DM Sans', sans-serif; font-size: 0.7rem;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: #8C7E6A; transition: color 0.25s;
}

.sp-lang-btn.active { color: #B8925A; font-weight: 600; }
.sp-lang-btn:hover  { color: #B8925A; }

#sp-lang-sep {
    color: #2E2E2A; font-size: 0.7rem; pointer-events: none; user-select: none;
}

#sp-theme-toggle {
    background: none; border: none; cursor: pointer; padding: 4px;
    color: #8C7E6A; transition: color 0.25s;
    display: flex; align-items: center; flex-shrink: 0;
}

#sp-theme-toggle:hover { color: #B8925A; }
#sp-theme-toggle svg   { width: 16px; height: 16px; display: block; }

/* sun icon: visible in dark mode; moon: visible in light mode */
#sp-theme-toggle .icon-sun  { display: block; }
#sp-theme-toggle .icon-moon { display: none;  }
[data-theme="light"] #sp-theme-toggle .icon-sun  { display: none;  }
[data-theme="light"] #sp-theme-toggle .icon-moon { display: block; }

/* Mobile toggles row in #sp-mobile-menu */
#sp-mobile-toggles {
    display: flex; align-items: center; gap: 1rem; margin-top: 1rem;
}

@media (max-width: 768px) {
    #sp-lang-toggle, #sp-theme-toggle { display: none; }
    #sp-mobile-toggles { display: flex; }
}

@media (min-width: 769px) {
    #sp-mobile-toggles { display: none; }
}
```

- [ ] **Step 3: Add admin-mode CSS block** — append at the end of `app.css`:

```css
/* ── ADMIN LIGHT MODE ─────────────────────────────────────────── */
.admin-mode body {
    background-color: #FAF8F5;
    color: #0A0A08;
    font-family: 'Inter', sans-serif;
}

.admin-sidebar {
    background-color: #FFFFFF;
    border-right: 1px solid #EDE8E0;
    min-height: 100vh;
}

.admin-sidebar-brand {
    padding: 1.25rem 1.25rem 0.875rem;
    border-bottom: 1px solid #EDE8E0;
    margin-bottom: 0.5rem;
}

.admin-sidebar-brand-name {
    font-family: 'PaperCrease', serif;
    color: #0A0A08;
    font-size: 0.875rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.admin-sidebar-brand-sub {
    color: #A89880;
    font-size: 0.65rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-top: 2px;
}

.admin-sidebar-divider {
    height: 1px;
    background: #EDE8E0;
    margin: 0.75rem 1rem;
}

.admin-sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #EDE8E0;
}

/* Override dark admin-sidebar-link for admin-mode */
.admin-mode .admin-sidebar-link {
    color: #6B5A48;
}

.admin-mode .admin-sidebar-link:hover {
    color: #0A0A08;
    background: #F0EAE0;
}

.admin-mode .admin-sidebar-link.active {
    color: #8B6340;
    border-left-color: #8B6340;
    background: rgba(139, 99, 64, 0.08);
}

.admin-topbar {
    background-color: #FFFFFF;
    border-bottom: 1px solid #EDE8E0;
    height: 64px;
}

.admin-topbar-breadcrumb {
    color: #6B5A48;
    font-size: 0.7rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.admin-topbar-user {
    color: #6B5A48;
    font-size: 0.8125rem;
}

.admin-main {
    background-color: #FAF8F5;
}

/* Admin accent buttons */
.admin-mode .btn-admin-primary {
    background: #8B6340;
    color: #FFFFFF;
    border: none;
    transition: background 0.2s;
}

.admin-mode .btn-admin-primary:hover { background: #7A5535; }

/* Admin badge overrides — keep readable on light bg */
.admin-mode .badge-pending   { background: rgba(180,120,30,0.12); color: #8B6340; }
.admin-mode .badge-confirmed { background: rgba(22,163,74,0.12);  color: #16a34a; }
.admin-mode .badge-cancelled { background: rgba(220,38,38,0.12);  color: #dc2626; }
```

- [ ] **Step 4: Light mode — fix navbar glassmorphism for light theme**

Add inside the CSS, after the `.admin-mode` block:

```css
/* Light mode navbar scrolled state */
[data-theme="light"] #sp-nav.scrolled {
    background: rgba(245, 240, 232, 0.88);
    border-bottom-color: rgba(212, 201, 180, 0.6);
}

/* Light mode scrollbar */
[data-theme="light"] ::-webkit-scrollbar-track { background: #F5F0E8; }
[data-theme="light"] ::-webkit-scrollbar-thumb { background: #D4C9B4; }
[data-theme="light"] ::-webkit-scrollbar-thumb:hover { background: #8B6340; }
```

- [ ] **Step 5: Verify CSS compiles — run Vite dev build**

```bash
cd source && npm run dev
```
Expected: No errors. Vite compiles and watches.

---

## Task 2: Anti-Flash Inline Script in client.blade.php

**Files:**
- Modify: `source/resources/views/layouts/client.blade.php`

- [ ] **Step 1: Add inline script in `<head>` before `@vite`**

In `client.blade.php`, find line 22 (`@vite([...])`) and insert directly above it:

```html
    {{-- ── Anti-flash: apply stored theme & lang before CSS renders ── --}}
    <script>
    (function(){
        var t = localStorage.getItem('sp-theme') || 'dark';
        var l = localStorage.getItem('sp-lang')  || 'en';
        document.documentElement.setAttribute('data-theme', t);
        document.documentElement.setAttribute('data-lang', l);
    })();
    </script>
```

Result — the `<head>` section around that area should look like:

```html
    {{-- Anti-flash: apply stored theme & lang before CSS renders --}}
    <script>
    (function(){
        var t = localStorage.getItem('sp-theme') || 'dark';
        var l = localStorage.getItem('sp-lang')  || 'en';
        document.documentElement.setAttribute('data-theme', t);
        document.documentElement.setAttribute('data-lang', l);
    })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## Task 3: Navbar — Toggle Buttons & Bilingual Links

**Files:**
- Modify: `source/resources/views/components/navbar.blade.php`

- [ ] **Step 1: Replace nav link text with bilingual data attributes**

Replace the desktop nav links block (lines 13–18) with:

```html
        {{-- Desktop links --}}
        <div class="sp-nav-links" role="list">
            <a href="{{ route('home') }}"      class="sp-nav-link {{ request()->routeIs('home')      ? 'active' : '' }}" role="listitem" data-en="Home"      data-vi="Trang chủ">Home</a>
            <a href="{{ route('about') }}"     class="sp-nav-link {{ request()->routeIs('about')     ? 'active' : '' }}" role="listitem" data-en="Story"     data-vi="Câu chuyện">Story</a>
            <a href="{{ route('menu') }}"      class="sp-nav-link {{ request()->routeIs('menu')      ? 'active' : '' }}" role="listitem" data-en="Menu"      data-vi="Thực đơn">Menu</a>
            <a href="{{ route('community') }}" class="sp-nav-link {{ request()->routeIs('community') ? 'active' : '' }}" role="listitem" data-en="Community" data-vi="Cộng đồng">Community</a>
        </div>
```

- [ ] **Step 2: Add toggle buttons before `.sp-nav-cta`**

Replace the `{{-- CTA --}}` block (line 21) with:

```html
        {{-- Preference toggles --}}
        <div id="sp-lang-toggle" role="group" aria-label="Language">
            <button class="sp-lang-btn" data-lang="en" aria-pressed="true">EN</button>
            <span id="sp-lang-sep" aria-hidden="true">·</span>
            <button class="sp-lang-btn" data-lang="vi" aria-pressed="false">VI</button>
        </div>

        <button id="sp-theme-toggle" aria-label="Toggle theme">
            {{-- Sun: shown when dark mode is active --}}
            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            {{-- Moon: shown when light mode is active --}}
            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>

        {{-- CTA --}}
        <a href="{{ route('reservation') }}" class="sp-nav-cta" data-en="Reserve" data-vi="Đặt bàn">Reserve</a>
```

- [ ] **Step 3: Add mobile toggles inside `#sp-mobile-menu`**

In the mobile menu block, after the last `.sp-mobile-link` and before the closing `</div>`, add:

```html
        {{-- Mobile preference toggles --}}
        <div id="sp-mobile-toggles">
            <button class="sp-lang-btn" data-lang="en" aria-pressed="true"
                    style="font-size:1rem; color:#8C7E6A;">EN</button>
            <span style="color:#2E2E2A; font-size:1rem;">·</span>
            <button class="sp-lang-btn" data-lang="vi" aria-pressed="false"
                    style="font-size:1rem; color:#8C7E6A;">VI</button>
            <span style="color:#2E2E2A; font-size:1rem; margin: 0 0.25rem;">|</span>
            <button id="sp-theme-toggle-mobile" aria-label="Toggle theme"
                    style="background:none; border:none; cursor:pointer; color:#8C7E6A;">
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
        </div>
```

Also update the mobile Reserve link to be bilingual:

```html
        <a href="{{ route('reservation') }}" class="sp-mobile-link sp-mobile-cta" data-en="Reserve a Table" data-vi="Đặt một bàn">Reserve a Table</a>
```

---

## Task 4: Footer — Bilingual Static Strings

**Files:**
- Modify: `source/resources/views/components/footer.blade.php`

- [ ] **Step 1: Add data-en/data-vi to footer headings and labels**

Replace the `Find Us` heading (line 19) with:

```html
                <h4 class="font-display text-sm mb-5" style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-en="Find Us" data-vi="Tìm Chúng Tôi">
                    Find Us
                </h4>
```

Replace the `Hours` heading (line 42) with:

```html
                <h4 class="font-display text-sm mb-5" style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-en="Hours" data-vi="Giờ Mở Cửa">
                    Hours
                </h4>
```

Replace the `Working Space` label (line 46) with:

```html
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.25rem;"
                       data-en="Working Space" data-vi="Không Gian Làm Việc">
                        Working Space
                    </p>
```

Replace the `Bistro Bar` label (line 50) with:

```html
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-top:0.75rem; margin-bottom:0.25rem;"
                       data-en="Bistro Bar" data-vi="Bistro Bar">
                        Bistro Bar
                    </p>
```

---

## Task 5: JavaScript — Preferences System

**Files:**
- Modify: `source/resources/js/app.js`

- [ ] **Step 1: Write the full preferences module** — replace the entire file content with:

```js
// ── Preferences: Theme & Language ─────────────────────────────
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('sp-theme', theme);

    // Sync icon visibility (both desktop + mobile toggles)
    document.querySelectorAll('#sp-theme-toggle, #sp-theme-toggle-mobile').forEach(btn => {
        if (!btn) return;
        const sun  = btn.querySelector('.icon-sun');
        const moon = btn.querySelector('.icon-moon');
        if (theme === 'light') {
            if (sun)  sun.style.display  = 'none';
            if (moon) moon.style.display = 'block';
        } else {
            if (sun)  sun.style.display  = 'block';
            if (moon) moon.style.display = 'none';
        }
    });
}

function applyLang(lang) {
    document.documentElement.setAttribute('data-lang', lang);
    localStorage.setItem('sp-lang', lang);

    // Swap text content for data-en / data-vi elements
    document.querySelectorAll('[data-en]').forEach(el => {
        const text = lang === 'vi' ? el.dataset.vi : el.dataset.en;
        if (text !== undefined) el.textContent = text;
    });

    // Update aria-pressed on all lang buttons (desktop + mobile)
    document.querySelectorAll('.sp-lang-btn').forEach(btn => {
        btn.setAttribute('aria-pressed', String(btn.dataset.lang === lang));
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });
}

function initPreferences() {
    const theme = localStorage.getItem('sp-theme') || 'dark';
    const lang  = localStorage.getItem('sp-lang')  || 'en';
    applyTheme(theme);
    applyLang(lang);
}

document.addEventListener('DOMContentLoaded', function () {
    initPreferences();

    // Theme toggle (desktop)
    const themeBtn = document.getElementById('sp-theme-toggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const current = localStorage.getItem('sp-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // Theme toggle (mobile)
    const themeBtnMobile = document.getElementById('sp-theme-toggle-mobile');
    if (themeBtnMobile) {
        themeBtnMobile.addEventListener('click', () => {
            const current = localStorage.getItem('sp-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // Language buttons (all — desktop and mobile share .sp-lang-btn class)
    document.querySelectorAll('.sp-lang-btn').forEach(btn => {
        btn.addEventListener('click', () => applyLang(btn.dataset.lang));
    });
});
```

---

## Task 6: Admin Layout — Apply admin-mode Class

**Files:**
- Modify: `source/resources/views/layouts/admin.blade.php`

- [ ] **Step 1: Add `admin-mode` class to `<html>` and remove inline body styles**

Replace:
```html
<html lang="vi">
```
With:
```html
<html lang="vi" class="admin-mode">
```

Replace the `<body>` opening tag:
```html
<body style="background-color:#1A1A18; color:#C9B99A; font-family:'Inter',sans-serif;">
```
With:
```html
<body>
```

Replace the `<main>` tag:
```html
<main class="flex-1 overflow-y-auto p-6" style="background-color:#1A1A18;">
```
With:
```html
<main class="admin-main flex-1 overflow-y-auto p-6">
```

---

## Task 7: Admin Sidebar — Replace Inline Styles with CSS Classes

**Files:**
- Modify: `source/resources/views/components/admin/sidebar.blade.php`

- [ ] **Step 1: Replace the entire file** with the CSS-class-driven version:

```blade
@php
    $route = request()->route()?->getName() ?? '';
@endphp

<aside class="admin-sidebar w-60 flex-shrink-0 flex flex-col">

    {{-- Brand --}}
    <div class="admin-sidebar-brand">
        <a href="{{ route('home') }}" class="block">
            <p class="admin-sidebar-brand-name">Sapiens House</p>
            <p class="admin-sidebar-brand-sub">Admin Panel</p>
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-4 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="admin-sidebar-link {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.reservations.index') }}"
           class="admin-sidebar-link {{ str_starts_with($route, 'admin.reservations') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Reservations
        </a>
        <a href="{{ route('admin.menu-items.index') }}"
           class="admin-sidebar-link {{ str_starts_with($route, 'admin.menu-items') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            Menu Items
        </a>
        <a href="{{ route('admin.events.index') }}"
           class="admin-sidebar-link {{ str_starts_with($route, 'admin.events') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Events
        </a>

        <div class="admin-sidebar-divider"></div>

        <a href="{{ route('home') }}" class="admin-sidebar-link" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View Website
        </a>
    </nav>

    {{-- Logout --}}
    <div class="admin-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="admin-sidebar-link w-full" style="background:none; border:none; cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>
```

---

## Task 8: Admin Topbar — Replace Inline Styles with CSS Classes

**Files:**
- Modify: `source/resources/views/components/admin/topbar.blade.php`

- [ ] **Step 1: Replace the entire file:**

```blade
<header class="admin-topbar flex items-center justify-between px-6">

    <div>
        <p class="admin-topbar-breadcrumb">
            @yield('breadcrumb', 'Dashboard')
        </p>
    </div>

    <div class="flex items-center gap-4">
        <span class="admin-topbar-user">
            {{ auth()->user()?->name ?? 'Admin' }}
        </span>
    </div>
</header>
```

---

## Task 9: Smoke Test

- [ ] **Step 1: Start dev server**

```bash
cd source && php artisan serve
```

Open `http://localhost:8000` in browser.

- [ ] **Step 2: Verify dark mode (default)**
  - Page loads with dark background `#0A0A08`
  - No white flash before CSS applies
  - Sun icon visible in navbar

- [ ] **Step 3: Toggle to light mode**
  - Click sun icon → page switches to cream `#F5F0E8` instantly
  - Moon icon appears
  - Navbar links and text readable (dark text on light bg)
  - Reload page → cream background persists (no flash)

- [ ] **Step 4: Toggle language**
  - Click `VI` → nav links change: Home→Trang chủ, Story→Câu chuyện, Menu→Thực đơn, etc.
  - Click `EN` → reverts
  - Reload → language persists

- [ ] **Step 5: Cross-page persistence**
  - Set light + VI → navigate to `/menu` → still light + VI
  - Navigate to `/about` → still light + VI

- [ ] **Step 6: Verify admin panel**
  - Open `http://localhost:8000/admin/dashboard`
  - White sidebar, `#FAF8F5` content area
  - Active nav item uses Deep Bronze `#8B6340`
  - No dark mode — admin always light regardless of client preference

- [ ] **Step 7: Mobile**
  - Resize to < 768px
  - Open burger menu → see lang + theme toggles at bottom of mobile menu
  - Toggles work correctly

- [ ] **Step 8: Commit**

```bash
cd source && git add -A && git commit -m "feat: add dark/light theme toggle and EN/VI language switcher with localStorage persistence; admin panel always light with deep bronze accent"
```
