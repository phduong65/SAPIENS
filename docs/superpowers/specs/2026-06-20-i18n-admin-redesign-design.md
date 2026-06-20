# i18n System & Admin Panel Redesign — Design Spec
**Date:** 2026-06-20  
**Project:** Sapiens House Website (Laravel 13 · Blade · Tailwind v4 · SQLite)

---

## 1. Problem Statement

The current multilingual implementation uses `data-en`/`data-vi` HTML attributes swapped by JavaScript. This approach has several issues:
- Text is scattered across all Blade templates — no central management
- Admin cannot edit translations
- JS-only: emails, validation messages, and server-side rendering always render in the default language
- Adding a third language requires touching every Blade file

---

## 2. Goals

1. Admin can edit all UI and page-content translations via a panel
2. Server renders the correct language on initial load (SEO, email, validation)
3. Client-side toggle is instant (no page reload) using a lazy-loaded JSON dictionary
4. Architecture supports adding new locales without touching Blade templates
5. Admin panel gets a complete redesign: clean, blue & white, simple

---

## 3. i18n Architecture

### 3.1 Database Schema

**Table: `translation_strings`**

| Column   | Type          | Notes                              |
|----------|---------------|------------------------------------|
| id       | bigint PK     |                                    |
| group    | varchar(50)   | `ui` \| `pages` \| `emails`        |
| key      | varchar(200)  | dot-notation, e.g. `nav.home`      |
| locale   | varchar(10)   | `en`, `vi`, `ja` …                 |
| value    | text          |                                    |
| timestamps |             |                                    |

Unique constraint: `(group, key, locale)`

### 3.2 Lang Files (Runtime Source of Truth)

```
lang/
├── en/
│   ├── ui.php        ← nav, buttons, labels, footer, flash messages
│   ├── pages.php     ← hero, about, menu page headings & body copy
│   └── emails.php    ← reservation confirmation email strings
└── vi/
    ├── ui.php
    ├── pages.php
    └── emails.php
```

Blade templates use `__('ui.nav_home')`, `__('pages.hero_title')` etc.  
The database is the **edit layer**; lang files are the **runtime layer**.

### 3.3 File Generation Service

`App\Services\TranslationFileGenerator`

Keys in the database use dot-notation (`nav.home`, `nav.story`). The generator converts them to **nested PHP arrays** before writing, matching Laravel's official convention:

```php
// DB key: 'nav.home'  →  file: ['nav' => ['home' => 'Home']]

public function regenerate(string $group): void
{
    foreach ($this->locales() as $locale) {
        $rows = TranslationString::where('group', $group)
                    ->where('locale', $locale)
                    ->pluck('value', 'key');

        $nested = [];
        foreach ($rows as $dotKey => $value) {
            Arr::set($nested, $dotKey, $value);
        }

        $content = "<?php\nreturn " . var_export($nested, true) . ";\n";
        file_put_contents(lang_path("{$locale}/{$group}.php"), $content);
    }
    // No cache:clear — file overwrite is sufficient. Laravel reads from disk.
}
```

Example output (`lang/vi/ui.php`):
```php
<?php
return [
    'nav' => [
        'home'  => 'Trang chủ',
        'story' => 'Câu chuyện',
        'menu'  => 'Thực đơn',
    ],
    'hero' => [
        'cta_reserve' => 'Đặt bàn',
        'cta_menu'    => 'Xem thực đơn',
    ],
];
```

Blade usage: `__('ui.nav.home')` — standard Laravel dot-notation access into nested arrays.

> **Note:** No `Artisan::call('cache:clear')`. File overwrite is sufficient because Laravel's translation loader reads from disk on each request (unless `php artisan optimize` was run — in that case, `php artisan lang:clear` or similar is called only if needed).

### 3.4 Initial Seeding

**Order of operations (one-time setup):**
1. Manually create `lang/en/ui.php`, `lang/en/pages.php`, `lang/en/emails.php` (and `vi/` equivalents) with all translation keys extracted from current Blade templates.
2. Run `TranslationSeeder` — reads those files and inserts one row per `(group, key, locale)` into `translation_strings`.
3. From this point on, admin edits DB → `TranslationFileGenerator` regenerates the files. The files and DB stay in sync.

### 3.5 Dynamic Content

`menu_items` already has `name_en` / `name_vi` columns — kept as-is, not migrated into `translation_strings`. Future dynamic content (events with bilingual titles, etc.) follows the same `*_en` / `*_vi` column pattern or a separate `*_translations` table — outside this spec's scope.

---

## 4. Language Switching (Hybrid)

### 4.1 Server-Side

- **`LocaleMiddleware`** (applied to all web routes):  
  Reads cookie `app_locale` → `App::setLocale($locale)` → Blade renders `__()` in correct locale.

- **`LocaleController@switch`**:  
  `POST /locale` (body: `locale=vi`) → validates locale is supported → sets cookie (`app_locale`, 1 year) → `redirect()->back()`.

- **Default locale** is read from `settings` table key `default_locale` (falls back to `'en'` if not set). `LocaleMiddleware` uses this as the fallback when no cookie is present.

### 4.2 Client-Side (Instant Toggle, No Reload)

**Translation endpoint:**

```
GET /translations/{locale}
```

Returns JSON for the `ui` group only (page content is server-rendered; only UI strings need client-side swapping since those are the elements the toggle button changes):

```json
{
  "nav.home": "Trang chủ",
  "nav.story": "Câu chuyện",
  "nav.menu": "Thực đơn",
  ...
}
```

- Cached in `sessionStorage` keyed by locale after first fetch.
- Served with `Cache-Control: public, max-age=3600`.

**JS toggle flow:**

```
User clicks VI button
  → check sessionStorage['translations.vi'] exists?
      YES → apply dictionary immediately
      NO  → fetch('/translations/vi') → cache in sessionStorage → apply
  → set html[data-lang="vi"]  (CSS lang-en/lang-vi visibility)
  → POST '/locale' {locale:'vi'} in background (sets cookie for next page load)
```

**DOM update strategy:**  
Elements with `data-i18n="nav.home"` get their `textContent` replaced from the dictionary. Server renders the initial value (correct locale). JS only runs on toggle.

```html
<a href="/" data-i18n="nav.home">{{ __('ui.nav_home') }}</a>
```

### 4.3 SEO

- Initial render is server-side in the cookie locale → correct for crawlers.
- `<html lang="{{ app()->getLocale() }}">` is accurate.
- `<link rel="alternate" hreflang>` — deferred to a future pass.

---

## 5. Admin Panel Redesign

### 5.1 Color System

Admin CSS variables are defined in a separate `[data-panel="admin"]` scope (or in `admin.blade.php` `<style>`) so they never conflict with the client dark-mode palette:

```css
--adm-bg:            #F8FAFC;   /* page background */
--adm-surface:       #FFFFFF;   /* cards, sidebar */
--adm-border:        #E2E8F0;   /* dividers, card borders */
--adm-text:          #1E293B;   /* primary text */
--adm-muted:         #64748B;   /* secondary text */
--adm-primary:       #2563EB;   /* blue-600 — buttons, active */
--adm-primary-light: #EFF6FF;   /* blue-50 — hover bg */
--adm-primary-hover: #1D4ED8;   /* blue-700 — button hover */
--adm-success:       #10B981;
--adm-warning:       #F59E0B;
--adm-danger:        #EF4444;
```

### 5.2 Layout

```
┌────────────────────────────────────────────────────────┐
│  TOPBAR  h-14  white  border-bottom                    │
│  [≡ Logo]   Breadcrumb            [User ▾]  [Logout]   │
├───────────┬────────────────────────────────────────────┤
│           │                                            │
│  SIDEBAR  │   CONTENT AREA                             │
│  w-56     │   bg: --adm-bg                             │
│  white    │   p-6                                      │
│  border-r │   white cards, rounded-lg, bordered        │
│           │                                            │
│  Nav list │                                            │
│  (icons + │                                            │
│   labels) │                                            │
│           │                                            │
│  ──────── │                                            │
│  View Site│                                            │
└───────────┴────────────────────────────────────────────┘
```

### 5.3 Sidebar Nav Item States

| State   | Background     | Text         | Left border           |
|---------|----------------|--------------|-----------------------|
| Default | transparent    | slate-600    | none                  |
| Hover   | blue-50        | blue-700     | none                  |
| Active  | blue-50        | blue-700     | 3px solid blue-600    |

### 5.4 Sidebar Nav Items

```
Dashboard
Reservations
Menu Items
Events
──────────
Translations   ← NEW
Settings
──────────
View Website ↗
```

### 5.5 Common Admin Components

All admin pages use a consistent set of CSS classes (defined in `app.css` under `.adm-*` namespace):

| Component       | Class              | Description                              |
|-----------------|--------------------|------------------------------------------|
| Page header     | `.adm-page-header` | Title + subtitle row                     |
| Card            | `.adm-card`        | white, rounded-lg, border, shadow-sm     |
| Table           | `.adm-table`       | Full-width, striped rows                 |
| TH              | `.adm-th`          | slate-500, uppercase, text-xs            |
| TD              | `.adm-td`          | slate-700, text-sm, py-3 px-4            |
| Badge pending   | `.adm-badge-warn`  | amber bg                                 |
| Badge confirmed | `.adm-badge-ok`    | green bg                                 |
| Badge cancelled | `.adm-badge-err`   | red bg                                   |
| Primary button  | `.adm-btn-primary` | blue-600, white text, hover blue-700     |
| Ghost button    | `.adm-btn-ghost`   | border slate-300, slate-700 text         |
| Danger button   | `.adm-btn-danger`  | red-50, red-700 text, hover red-100      |
| Form input      | `.adm-input`       | border slate-200, focus ring blue-500    |
| Form label      | `.adm-label`       | slate-700, text-sm, font-medium          |

---

## 6. Translations Admin Page

**Route:** `GET /admin/translations` → `Admin\TranslationController@index`  
**Route:** `POST /admin/translations` → `Admin\TranslationController@update`

### Layout

```
Translations
[Tab: UI Strings] [Tab: Pages] [Tab: Emails]

[Search: filter by key...]                    [Save & Generate Files]

┌────────────────┬──────────────────────┬──────────────────────┐
│ Key            │ English              │ Tiếng Việt           │
├────────────────┼──────────────────────┼──────────────────────┤
│ nav.home       │ [input: Home       ] │ [input: Trang chủ  ] │
│ nav.story      │ [input: Story      ] │ [input: Câu chuyện ] │
│ hero.title     │ [textarea          ] │ [textarea           ] │
└────────────────┴──────────────────────┴──────────────────────┘
```

- Single-line `<input>` for short strings (< 100 chars), `<textarea>` for longer.
- "Save & Generate Files" POSTs all changed rows → `TranslationFileGenerator::regenerate($group)` for affected groups.
- Success flash: "Files regenerated. Changes are live."

---

## 7. Settings — Language Tab

Existing settings page gains a "Language" tab:

- **Default locale** dropdown: `English (en)` / `Tiếng Việt (vi)` → saved to `settings` table as `default_locale`
- **Active locales** checkboxes (future: enable/disable locales)

---

## 8. Key Counts (Estimated)

| Group   | Keys (approx) | Notes                                    |
|---------|---------------|------------------------------------------|
| ui      | 60–80         | Nav, buttons, labels, footer, flash msgs |
| pages   | 80–120        | Hero, about, menu page, reservation form |
| emails  | 20–30         | Reservation confirmation email           |

---

## 9. Files to Create / Modify

### New files
- `app/Services/TranslationFileGenerator.php`
- `app/Http/Controllers/LocaleController.php`
- `app/Http/Controllers/Admin/TranslationController.php`
- `app/Http/Middleware/LocaleMiddleware.php`
- `database/migrations/xxxx_create_translation_strings_table.php`
- `database/seeders/TranslationSeeder.php`
- `resources/views/admin/translations/index.blade.php`
- `lang/en/ui.php`, `lang/en/pages.php`, `lang/en/emails.php`
- `lang/vi/ui.php`, `lang/vi/pages.php`, `lang/vi/emails.php`

### Modified files
- `routes/web.php` — add `POST /locale`, `GET /translations/{locale}`, admin translation routes
- `app/Http/Kernel.php` (or bootstrap/app.php) — register `LocaleMiddleware`
- `resources/views/layouts/client.blade.php` — replace inline strings with `__()`
- `resources/views/layouts/admin.blade.php` — new structure + adm CSS vars
- `resources/views/components/admin/sidebar.blade.php` — new design
- `resources/views/components/admin/topbar.blade.php` — new design
- `resources/views/components/navbar.blade.php` — `data-i18n` attrs + `__()` render
- `resources/views/components/footer.blade.php` — `__()` render
- `resources/views/client/*.blade.php` — `__()` for all static strings
- `resources/css/app.css` — add `.adm-*` component classes
- `resources/js/app.js` — replace `applyLang()` with dictionary-fetch approach

### Deleted / replaced
- All `data-en`/`data-vi` attributes across Blade templates → replaced with `data-i18n` + server-rendered `__()`
