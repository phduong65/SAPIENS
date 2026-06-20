# CLAUDE.md — Sapiens House Website

> AI assistant instructions for this project. Read this file before every implementation task.

---

## 1. Project Overview

**Brand:** Sapiens House – Eatery & Drinks  
**Slogan:** A Modern Cave for Modern Humans  
**Concept:** Fusion Japanese Eatery & Bistro Bar — inspired by the book *Sapiens* (Yuval Noah Harari)  
**Address:** Tầng 4, 44 Nguyễn Huệ, Quận 1, TP.HCM  
**Hours:** Working Space 11:00–17:00 | Bistro Bar 18:00–01:00  
**Stack:** Laravel 13 · Laravel Blade · Tailwind CSS v4 · Vanilla JS · GSAP 3 + ScrollTrigger · Canvas 2D · SQLite · SMTP Mail  
**Animation Libs:** GSAP 3.12.5 (CDN) · ScrollTrigger · Canvas 2D Particle System  
**Fonts:** PaperCrease (custom, local `/fonts/PAPER%20CREASE.TTF`) · Cormorant Garamond (Google, italic serif) · DM Sans (Google, clean sans-serif)

---

## 2. Design System

### Color Palette
```
--color-cave-black:   #0A0A08   // Primary background (deepest dark)
--color-cave-dark:    #0F0F0D   // Panels / surfaces
--color-cave-mid:     #1A1A18   // Grid separators
--color-cave-border:  #2E2E2A   // Borders
--color-sand:         #C9B99A   // Primary body text
--color-sand-light:   #E5D9C8   // Headings
--color-sand-muted:   #8C7E6A   // Muted / secondary text
--color-gold:         #B8925A   // Primary accent/CTA
--color-gold-light:   #D4A96A   // Hover state
```

### Typography
- **Display / Headings:** `PaperCrease` (custom, `/fonts/PAPER%20CREASE.TTF`) — class: `.font-display`
- **Decorative Serif (italic):** `Cormorant Garamond` (Google Fonts) — taglines, quotes
- **UI / Body:** `DM Sans` (Google Fonts) — all interface text, buttons, labels

### Animation System
- **Library:** GSAP 3.12.5 + ScrollTrigger (CDN, loaded in `layouts/client.blade.php`)
- **Loading screen:** `#sp-loader` — GSAP timeline, ~2.8s total, exits and triggers `initPageAnimations()`
- **Scroll reveals:** `data-reveal`, `data-reveal-slow`, `data-stagger`, `data-lines`/`data-line`
- **Parallax:** `data-parallax="0.15"` (number = yPercent speed factor)
- **Canvas particles:** Hero section (`#sp-hero-canvas`) — dust + smoke blobs, 60fps rAF
- **CSS animations:** `breathe` (spotlight pulse), `orbDrift` (ambient orbs), `scrollPulse` (scroll hint)

### UI Components
- **Navbar:** `#sp-nav` — transparent by default, glassmorphism on scroll (`.scrolled` class)
- **Buttons:** `.sp-btn-primary` (gold fill), `.sp-btn-ghost` (outlined), `.sp-btn-lg`, `.sp-btn-sm`
- **Menu cards:** `.sp-menu-card` — horizontal drag-scroll carousel (`#sp-menu-track`)
- **Glass panel:** `.sp-glass-panel` — `backdrop-filter: blur(24px)` with gold border

### Font Setup (app.css)
```css
@font-face {
  font-family: 'PaperCrease';
  src: url('/fonts/PAPER CREASE.TTF') format('truetype');
  font-weight: normal;
  font-display: swap;
}
```

### Tailwind Config Extensions
```js
// tailwind.config.js
fontFamily: {
  display: ['"PaperCrease"', 'serif'],
  body: ['Inter', 'sans-serif'],
},
colors: {
  cave: {
    black: '#1A1A18',
    dark: '#242420',
    mid: '#2E2E2A',
  },
  sand: {
    DEFAULT: '#C9B99A',
    light: '#E5D9C8',
    muted: '#8C7E6A',
  },
  gold: {
    DEFAULT: '#B8925A',
    light: '#D4A96A',
  },
},
```

### Visual Mood
- Dark backgrounds (cave-black/cave-dark) throughout
- Warm sand/gold tones for all text
- Paper Crease font for all major headings — raw, human, textured feel
- Subtle grain/noise texture overlay on hero sections (CSS or SVG filter)
- Minimal animations — slow fades, no bouncing
- `High-end but approachable` — never cold/corporate

---

## 3. Project Structure

```
D:\SAPIENS\
├── .claude/
│   └── settings.local.json
├── docs/
│   ├── 00-project-init/
│   ├── 01-system-analysis/
│   ├── 02-system-design/
│   ├── 03-implementation/
│   ├── 04-testing/
│   ├── 05-deployment/
│   ├── 06-maintenance/
│   └── features/
├── source/                          ← Laravel root
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Client/
│   │   │   │   │   ├── HomeController.php
│   │   │   │   │   ├── MenuController.php
│   │   │   │   │   ├── ReservationController.php
│   │   │   │   │   ├── AboutController.php
│   │   │   │   │   └── EventController.php
│   │   │   │   └── Admin/
│   │   │   │       ├── DashboardController.php    ✅
│   │   │   │       ├── MenuItemController.php     ✅
│   │   │   │       ├── ReservationController.php  ✅
│   │   │   │       ├── EventController.php        ✅
│   │   │   │       └── SettingController.php      ❌ missing
│   │   │   ├── Requests/
│   │   │   │   └── ReservationRequest.php
│   │   │   └── Middleware/        ❌ empty — EnsureUserIsAdmin not created
│   │   ├── Models/
│   │   │   ├── MenuItem.php
│   │   │   ├── MenuCategory.php
│   │   │   ├── Reservation.php
│   │   │   └── Event.php
│   │   ├── Services/
│   │   │   └── ReservationService.php    ❌ missing
│   │   └── Mail/
│   │       └── ReservationConfirmation.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php
│   │       ├── MenuCategorySeeder.php
│   │       ├── MenuItemSeeder.php
│   │       └── AdminUserSeeder.php
│   ├── resources/
│   │   ├── css/
│   │   │   └── app.css
│   │   ├── js/
│   │   │   └── app.js
│   │   └── views/
│   │       ├── layouts/
│   │       │   ├── client.blade.php     ← public site layout
│   │       │   ├── admin.blade.php      ← admin panel layout
│   │       │   └── auth.blade.php
│   │       ├── components/
│   │       │   ├── navbar.blade.php          ✅
│   │       │   ├── footer.blade.php          ✅
│   │       │   ├── flash-messages.blade.php  ✅
│   │       │   ├── admin/
│   │       │   │   ├── sidebar.blade.php     ✅
│   │       │   │   └── topbar.blade.php      ✅
│   │       │   └── menu-card.blade.php       ❌ missing
│   │       ├── client/
│   │       │   ├── home.blade.php
│   │       │   ├── about.blade.php
│   │       │   ├── menu/
│   │       │   │   └── index.blade.php
│   │       │   ├── reservation/
│   │       │   │   └── index.blade.php
│   │       │   └── events/
│   │       │       └── index.blade.php
│   │       ├── admin/
│   │       │   ├── dashboard/
│   │       │   │   └── index.blade.php       ✅
│   │       │   ├── menu-items/
│   │       │   │   ├── index.blade.php       ✅
│   │       │   │   └── partials/
│   │       │   │       └── form.blade.php    ✅ (replaces 3 modal files)
│   │       │   ├── reservations/
│   │       │   │   └── index.blade.php       ✅
│   │       │   ├── events/
│   │       │   │   ├── index.blade.php       ✅
│   │       │   │   └── partials/
│   │       │   │       └── form.blade.php    ✅
│   │       │   └── settings/
│   │       │       └── index.blade.php       ❌ missing
│   │       ├── auth/
│   │       │   └── login.blade.php                       ✅
│   │       ├── emails/
│   │       │   └── reservation-confirmation.blade.php    ✅
│   │       └── errors/                                   ❌ empty (need 404, 500)
│   ├── routes/
│   │   ├── web.php           ✅ (contains client + auth + admin routes)
│   │   └── auth.php          ❌ missing (auth is in web.php instead)
│   ├── config/
│   └── tests/
│       ├── Unit/
│       └── Feature/
├── public/
│   └── fonts/
│       └── PAPER CREASE.TTF
├── docker/                    ❌ empty — Dockerfile, docker-compose, nginx not created
│   ├── Dockerfile
│   ├── docker-compose.yml
│   └── nginx/
├── CLAUDE.md
└── README.md
```

---

## 4. Database Schema

### `menu_categories`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | varchar(100) | e.g. "Small Plates", "Signature Cocktails" |
| slug | varchar(100) | unique |
| type | enum('food','drink') | |
| sort_order | int | display ordering |
| is_active | boolean | default true |
| timestamps | | |

### `menu_items`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| menu_category_id | FK | |
| name_en | varchar(200) | English name |
| name_vi | varchar(200) | Vietnamese name |
| description_en | text nullable | |
| description_vi | text nullable | |
| price | int | in VND thousands (e.g. 195 = 195,000đ) |
| image_path | varchar nullable | |
| is_featured | boolean | for homepage Featured Menu |
| is_active | boolean | |
| sort_order | int | |
| timestamps | | |

### `reservations`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| code | varchar(20) | unique, e.g. SPH-20240619-001 |
| full_name | varchar(200) | |
| phone | varchar(20) | |
| email | varchar(200) | |
| reservation_date | date | |
| reservation_time | time | |
| guest_count | int | |
| seating_area | enum('indoor','outdoor','bar') | nullable |
| note | text nullable | general notes |
| food_allergy | text nullable | |
| is_birthday | boolean | default false |
| special_request | text nullable | |
| status | enum('pending','confirmed','cancelled') | default pending |
| confirmed_at | timestamp nullable | |
| cancelled_at | timestamp nullable | |
| timestamps | | |

### `events`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| title | varchar(200) | |
| slug | varchar(200) | unique |
| type | enum('event','guest_shift','workshop','special_night','community') | |
| description | text | |
| image_path | varchar nullable | |
| event_date | date | |
| event_time | time | |
| is_published | boolean | default false |
| timestamps | | |

### `users` (admin only)
Standard Laravel users table. **`role` column is NOT yet in migration** — needs to be added:
```php
// In a new migration: add_role_to_users_table
$table->enum('role', ['admin', 'staff'])->default('admin')->after('email');
```
Also update `AdminUserSeeder` to set `role: 'admin'` after migration added.

---

## 5. Menu Data (from PDF draft)

Seed these items into `menu_items`. All are **Small Plates / Food** unless noted.

| Name EN | Name VI | Price (000đ) | Ingredients |
|---------|---------|-------------|-------------|
| Trio Potato Mille-Feuille | Khoai Tây Ngàn Lớp | 195 | Smoked Iberico, Burrata, Bacon, Blueberry |
| Hunter's Roll | Bò Cuộn Thịt Xông Khói Phô Mai | 235 | Premium Beef, Bacon, Cheese |
| Not Squid | Khoai Môn và Nấm Kim Châm | 150 | Taro, Enoki, Spice (vegetarian) |
| Ocean Ruby | Cá Ngừ Khô Mè Rang | 285 | Seared Tuna, Sesame, Sauerkraut |
| Prawn Nachos & Burrata | Nachos Tôm & Burrata | 235 | Prawn, Charred Pepper |
| Ocean Greens Salad | Rong Nho | 99 | Sea Grapes, Sesame Glaze |
| Octopus Yakitori | Xiên Bạch Tuộc Cháy Cạnh | 250 | Madako, Wasabi, Sea Grapes |
| Aburi Salmon Pani Puri | Cá Hồi Pani Puri | 195 | Salmon Tartare, Sesame Leaf |
| Torched Salmon & Prawn Roulade | Cá Hồi Cuộn Tôm Phô Mai | 270 | Salmon, Prawn, Burrata |
| Ivory Cloud | Panna Cotta Chanh Dây | 140 | Passion Fruit Panna Cotta (**Dessert**) |

> Note: Drinks categories (Signature Mocktails, Highballs, Sake, Wine, Non-alcoholic) are to be added later. Seed empty categories with placeholder items.

**Price display:** Always show as `XXX,000đ` — never `XXXk`. VAT note: "Prices in 000 VND, subject to 10% VAT & 8% service charge."

---

## 6. Routes

### Client (public)
```
GET  /                          → HomeController@index           ✅ name: home
GET  /about                     → AboutController@index          ✅ name: about
GET  /menu                      → MenuController@index           ✅ name: menu
GET  /reservation               → ReservationController@index    ✅ name: reservation
POST /reservation               → ReservationController@store    ✅ name: reservation.store
GET  /community                 → EventController@index          ✅ name: community
                                  ⚠️  spec says /events but actual route is /community
```

### Admin (authenticated — `auth` middleware only, ⚠️ no role check)
```
GET  /admin                     → redirect /admin/dashboard      ✅
GET  /admin/dashboard           → Admin\DashboardController@index ✅
GET  /admin/reservations        → Admin\ReservationController@index ✅
POST /admin/reservations/{id}/confirm → Admin\ReservationController@confirm ✅
POST /admin/reservations/{id}/cancel  → Admin\ReservationController@cancel  ✅
GET  /admin/menu-items          → Admin\MenuItemController@index ✅
POST /admin/menu-items          → Admin\MenuItemController@store ✅
PUT  /admin/menu-items/{id}     → Admin\MenuItemController@update ✅
DEL  /admin/menu-items/{id}     → Admin\MenuItemController@destroy ✅
GET  /admin/events              → Admin\EventController@index    ✅
POST /admin/events              → Admin\EventController@store    ✅
PUT  /admin/events/{id}         → Admin\EventController@update   ✅
DEL  /admin/events/{id}         → Admin\EventController@destroy  ✅
GET  /admin/settings            → Admin\SettingController@index  ❌ NOT registered
POST /admin/settings            → Admin\SettingController@update ❌ NOT registered
```

### Auth
```
GET  /login                     → show login form
POST /login                     → authenticate
POST /logout                    → logout
```

---

## 7. Client Pages — Section Breakdown

### `/` — Homepage

1. **Hero Section**
   - Full-viewport height
   - Background: video (`.mp4` in `public/videos/`) or dark cinematic image fallback
   - Grain/noise overlay (CSS `background-image: url("data:image/svg+xml,...")`)
   - Center-aligned: Logo SVG → Brand name (PaperCrease) → Slogan → Two CTAs
   - CTAs: `Book a Table` (gold filled) | `Explore Menu` (ghost/outline)

2. **About Sapiens** (short teaser)
   - Left: Pull quote / tagline in PaperCrease large
   - Right: 2–3 paragraphs about the Sapiens book inspiration, Modern Cave concept
   - CTA: `Read Our Story →`

3. **Experience Section**
   - Two side-by-side cards (full width on mobile = stacked)
   - **Working Space** card: 11:00–17:00, description, dark image
   - **Bistro Bar** card: 18:00–01:00, description, dark moody image
   - Gold divider line between cards

4. **Featured Menu**
   - Title: "From Our Kitchen" (PaperCrease)
   - Grid of 4–6 `is_featured = true` menu items
   - Each card: image, name, price, short description
   - CTA: `View Full Menu →`

5. **Community Section**
   - Title: "Built for Human Connection" (PaperCrease, large)
   - 2–3 paragraph brand story about community
   - Masonry or 3-col image grid of people/gatherings
   - No CTA — this is a pure storytelling section

6. **Reservation CTA Banner**
   - Full-width dark section
   - Large text: "Reserve Your Spot" (PaperCrease)
   - Sub text: "Join us at Tầng 4, 44 Nguyễn Huệ, Quận 1"
   - Button: `Book a Table` (gold)

7. **Footer**
   - Logo + brand name
   - Address + Google Maps link: `https://maps.app.goo.gl/U4srxx72PFPQruoP7`
   - Hours: Working Space 11:00–17:00 | Bistro Bar 18:00–01:00
   - Social links (Instagram, Facebook — placeholder `#`)
   - Hotline placeholder | Email placeholder
   - Copyright

### `/about` — About Page
- Full storytelling layout (not a standard content page)
- Hero: Large PaperCrease headline — "We Are Sapiens"
- Sections: The Book → The Cave → The Community → The Vision
- Timeline or alternating text/image layout
- End CTA: reservation

### `/menu` — Menu Page
- Sticky tab/filter bar: Food | Drinks (then sub-categories)
- JavaScript-powered filtering (no page reload)
- Each item card: image, EN name, VI name, ingredients, price
- Price note banner at top: "Prices in 000 VND · +10% VAT · +8% Service"
- Category sections with PaperCrease category titles

### `/reservation` — Reservation Page
- Split layout: left = brand image/mood, right = form
- Form fields (see Section 9 below)
- On success: show inline success message + email sent notice (no redirect)
- Send confirmation email via SMTP

### `/events` — Events Page
- Card grid: event image, type badge, title, date/time, description excerpt
- Filter by type (optional, JS)
- Empty state: "Stay tuned — something's brewing."

---

## 8. Admin Panel

### Layout (`admin.blade.php`)
- Dark sidebar (cave-black) + top bar
- Sidebar items: Dashboard · Reservations · Menu Items · Events · Settings
- Active state: gold left border + sand text
- Topbar: breadcrumb + user name + logout

### Dashboard
- Stats cards: Total Reservations Today | Pending | Confirmed | Cancelled
- Recent reservations table (last 10)
- Quick actions: Go to pending reservations

### Reservations (`/admin/reservations`)
- Table: Code | Name | Phone | Date | Time | Guests | Status | Actions
- Filter: date range + status
- Actions per row: Confirm (POST) | Cancel (POST) — no modal needed, direct POST with confirmation via JS `confirm()`
- Status badges: pending=amber, confirmed=green, cancelled=red

### Menu Items (`/admin/menu-items`)
- Modal-first: all create/edit/delete in modals (no separate pages)
- Table: Image thumbnail | Name EN | Category | Price | Featured | Active | Actions
- Create/Edit modal: all fields including image upload
- Drag-to-reorder via `sort_order` (optional: use up/down buttons)

### Events (`/admin/events`)
- Modal-first CRUD
- Table: Title | Type | Date | Published | Actions
- Toggle publish/unpublish inline

---

## 9. Reservation Form & Email

### Form Fields
```
full_name        required | string | max:200
phone            required | string | regex:/^[0-9+\s\-]{9,15}$/
email            required | email
reservation_date required | date | after_or_equal:today
reservation_time required | in:18:00,18:30,19:00,...,00:00,00:30 (Bistro Bar hours)
guest_count      required | int | min:1 | max:50
seating_area     nullable | in:indoor,outdoor,bar
note             nullable | string | max:1000
food_allergy     nullable | string | max:500
is_birthday      boolean | default false
special_request  nullable | string | max:500
```

### Reservation Code Generation
```php
// in ReservationService::generateCode()
'SPH-' . now()->format('Ymd') . '-' . str_pad(Reservation::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT)
// e.g. SPH-20240619-001
```

### Confirmation Email (`ReservationConfirmation` Mailable)
- **Subject:** `[Sapiens House] Xác nhận đặt bàn – {code}`
- Template: `resources/views/emails/reservation-confirmation.blade.php`
- Content (bilingual VI/EN):
  - Logo + brand name header
  - "Cảm ơn {name}, chúng tôi đã nhận được yêu cầu đặt bàn của bạn."
  - Booking summary: code, date, time, guests, area
  - "Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ."
  - Note about special requests / birthday
  - Footer: address, hotline, Google Maps link
- Style: dark email template matching brand colors (inline CSS for email compatibility)
- Queue: `Mail::to($reservation->email)->send(new ReservationConfirmation($reservation))`

### SMTP Config (`.env`)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@sapienshouse.vn
MAIL_FROM_NAME="Sapiens House"
```

---

## 10. Technical Rules

### Security (Non-negotiable)
| Risk | Rule |
|------|------|
| SQL Injection | Never concat into raw queries — always use bindings |
| N+1 | Always `->with(['relations'])` on collections |
| Missing index | Index every FK and every `WHERE`/`ORDER BY` column |
| Authorization | Admin routes must use `auth` + `role` middleware — Blade `@can` is UI-only |
| CSRF | All POST forms must include `@csrf` |
| File uploads | Validate `mimes`, `max` size; store in `storage/app/public` via `Storage::disk('public')` |
| XSS | Never `{!! $var !!}` unless content is known-safe (e.g. admin-entered richtext) |

### Coding Standards
| Scope | Convention |
|-------|-----------|
| PHP | PSR-12 |
| Tables/columns | `snake_case` |
| Methods/vars | `camelCase` |
| Classes | `PascalCase` |
| Routes | implicit binding `{reservation}` |
| Validation | Form Request classes only — never inline in controller |
| Auth check | Middleware — never `if (auth()->user()->role === 'admin')` inline |
| Redirect success | `redirect()->route('x.index')->with('success', '...')` |
| Redirect failure | `back()->withErrors($v)->withInput()` |
| Comments | Only when WHY is non-obvious |

### Theme System (Light / Dark Toggle)
- Default: **dark mode** (cave aesthetic)
- Toggle: `#sp-theme-toggle` (sun/moon icon in navbar, both desktop + mobile)
- Storage: `localStorage` key `sp-theme` → `'dark'` | `'light'`
- Applied to: `<html data-theme="dark|light">` — anti-flash inline script in `<head>`
- CSS vars override: `[data-theme="light"]` block in `app.css` redefines all `--sp-*` semantic vars
- JS patches: `applyTheme()` in `app.js` calls `patchHeroGradient()`, `patchSectionBackgrounds()`, `patchInlineColors()` for inline-styled elements CSS can't reach
- `No dark:` Tailwind prefix — everything uses `var(--sp-*)` custom properties instead

### Language Toggle (EN / VI)
- Toggle: `.sp-lang-btn[data-lang]` buttons in navbar (desktop + mobile)
- Storage: `localStorage` key `sp-lang` → `'en'` | `'vi'`
- Applied to: `<html data-lang="en|vi">`
- CSS hides: `[data-lang="en"] .lang-vi { display:none }` and vice versa
- JS swaps: elements with `data-en="..."` `data-vi="..."` attributes get textContent replaced

### Performance
- Lazy-load all images: `loading="lazy"` attribute
- Optimize images at upload: use `Intervention/Image` (max 1200px wide, 80% quality)
- Hero video: `autoplay muted loop playsinline`, preload poster image first
- Tailwind CSS purge configured — no unused styles in production

### Responsive Breakpoints
- Mobile-first
- `sm:` 640px | `md:` 768px | `lg:` 1024px | `xl:` 1280px

---

## 11. Laravel Packages

### Currently Installed
```json
"require": {
    "laravel/framework": "^13.8",
    "laravel/tinker": "^3.0"
}
"require-dev": {
    "laravel/pint": "^1.27",
    "laravel/pail": "^1.2.5",
    "phpunit/phpunit": "^12.5"
}
```
NPM (installed):
```json
"devDependencies": {
    "tailwindcss": "^4.0.0",
    "@tailwindcss/vite": "^4.0.0",
    "vite": "^8.0.0",
    "laravel-vite-plugin": "^3.1",
    "concurrently": "^9.0.1"
}
```
> **Note:** Tailwind v4 is used (CSS-first config via `@theme {}` in `app.css`) — no `tailwind.config.js`.
> `@tailwindcss/forms` and `@tailwindcss/typography` are NOT installed.

### Still Needed (not yet installed)
```json
"require": {
    "intervention/image": "^3.0",      ← image resize on upload
    "spatie/laravel-activitylog": "^4.0" ← audit logging
}
```
Install commands:
```bash
composer require intervention/image
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

---

## 12. Audit Log

All CRUD + reservation status changes MUST log via `spatie/laravel-activitylog`:

```php
activity()
    ->causedBy(auth()->user())
    ->performedOn($model)
    ->withProperties(['before' => $before, 'after' => $model->getChanges()])
    ->log('created'); // created | updated | deleted | confirmed | cancelled
```

---

## 13. AI Assistant Rules (Golden Rules)

1. **Read this file before every task**
2. **Identify the phase** before proposing a solution
3. **Every new route → middleware → permission** (3 steps simultaneously)
4. **Modal-first in admin** — never create `/create` or `/edit` separate pages
5. **Run `php artisan test --no-coverage`** after implementation — never commit on test failure
6. **Every CRUD + workflow → audit log**
7. **Every reservation action → log + email**
8. **Font: PaperCrease for ALL headings** — check `@font-face` is loaded before styling
9. **Colors: always from the defined palette** — never arbitrary hex values
10. **Blade components over copy-paste** — extract repeated markup

---

## 14. Phase Tracker

| Phase | Status | Output |
|-------|--------|--------|
| 0 — Init | ✅ | Laravel 13 installed, Tailwind v4, SQLite DB, Vite build |
| 1 — Analysis | ✅ | This CLAUDE.md |
| 2 — Design | ✅ | ERD defined in §4, routes in §6, UI spec in §7–8 |
| 3 — Implementation | 🔄 | Core complete — see §17 for what remains |
| 4 — Testing | ☐ | Only ExampleTest stubs exist — no real tests yet |
| 5 — Deployment | ☐ | Docker folder empty, no Dockerfile/Nginx |
| 6 — Maintenance | ☐ | Not started |

**Current Phase: 3 — Implementation (partially complete)**

---

## 15. Context Commands

| Command | Action |
|---------|--------|
| `@status` | Read this file + `docs/` for current state |
| `@phase <n>` | Load phase-specific docs and constraints |
| `@docs <feature>` | Check `docs/features/<feature>.md` |
| `@init` | Reinitialize CLAUDE.md if structure changes |

---

## 16. Brand Voice (for copywriting in Blade templates)

- **Tone:** Warm, mysterious, intellectually curious — never corporate
- **Language:** Mix of English and Vietnamese naturally (bilingual brand)
- **Avoid:** "Welcome to our restaurant!", generic stock phrases, exclamation overuse
- **Use:** Evocative language tied to the cave/community/human connection narrative
- **Example headline:** "Where Modern Humans Gather" / "Một hang động cho con người hiện đại"

---

## 17. Implementation Audit (as of 2026-06-20)

### ✅ Implemented & Working

**Backend:**
- Laravel 13 + SQLite + Vite + Tailwind v4 installed and running
- All 4 domain models: `MenuItem`, `MenuCategory`, `Reservation`, `Event` + `User`
- All migrations run: `menu_categories`, `menu_items`, `reservations`, `events`, `users`
- Seeders: `MenuCategorySeeder`, `MenuItemSeeder`, `AdminUserSeeder`, `DatabaseSeeder`
- Admin credentials: `admin@sapienshouse.vn` / `sapiens2024!`
- All client controllers: `HomeController`, `AboutController`, `MenuController`, `ReservationController`, `EventController`
- Admin controllers: `DashboardController`, `MenuItemController`, `ReservationController`, `EventController`
- `ReservationRequest.php` form validation
- `ReservationConfirmation.php` mailable
- All routes in `routes/web.php` (client + auth + admin CRUD)
- Storage linked (`public/storage`)

**Frontend:**
- All 5 client pages: `/`, `/about`, `/menu`, `/reservation`, `/community`
- All admin pages: `/admin/dashboard`, `/admin/reservations`, `/admin/menu-items`, `/admin/events`
- `auth/login.blade.php`
- `emails/reservation-confirmation.blade.php`
- Components: `navbar.blade.php`, `footer.blade.php`, `flash-messages.blade.php`
- Loading screen (GSAP, ~2.8s), scroll reveals, parallax, canvas particles
- Light/Dark theme toggle with CSS semantic vars + JS DOM patching
- EN/VI language toggle with `data-en`/`data-vi` attribute swapping
- Hero section: video background (`images/sapiens/7956140970631.mp4`) + canvas particles
- Experience panels: real restaurant photos as backgrounds
- Photo gallery section on homepage (2-col asymmetric grid)
- Horizontal drag-scroll menu carousel with Fancybox lightbox
- Glassmorphism navbar (scrolled state)
- Laravel Pint installed (code formatting)

**Assets in `public/images/sapiens/`:**
| File | Used In |
|------|---------|
| `7956140970631.mp4` | Hero section video background |
| `z7956140941279_*.jpg` | Working Space panel background |
| `z7956140948857_*.jpg` | Bistro Bar panel background |
| `z7956140952723_*.jpg` | Photo gallery — right column |
| `z7956140953235_*.jpg` | Photo gallery — left column |
| `SAPIENS HOUSE_LOGO_*.png` | Navbar, footer, loader, mobile menu |

---

### ❌ Missing / Not Yet Built

**Backend — Critical:**
1. **`role` column missing from `users` table** — migration `0001_01_01_000000_create_users_table.php` has no `role` enum. Admin middleware relies on this. Need to add migration:
   ```php
   $table->enum('role', ['admin', 'staff'])->default('admin');
   ```
2. **No role-check middleware** — admin routes use only `auth` middleware, NOT role check. Anyone logged in can access admin. Need `EnsureUserIsAdmin` middleware or Gate policy.
3. **`ReservationService.php` missing** — code generation logic (`SPH-YYYYMMDD-NNN`) is currently inlined or missing. Should be in `app/Services/ReservationService.php`.
4. **`SettingController.php` missing** — `/admin/settings` route is NOT registered, view does not exist. Settings page (site name, hours, contact info) is unbuilt.
5. **`spatie/laravel-activitylog` NOT installed** — zero audit logging despite being required in spec.
6. **`intervention/image` NOT installed** — image uploads in admin are not resized/optimized.
7. **SMTP credentials are placeholder** — `.env` has `your@gmail.com` / `your-app-password`. Emails will fail until real credentials set.

**Backend — Minor:**
8. **`routes/auth.php` file missing** — spec shows it in project structure, but auth routes live in `web.php` (functionally OK, just structural discrepancy).
9. **`AdminUserSeeder` doesn't set role** — since `role` column doesn't exist yet, seeder needs updating after migration added.

**Frontend:**
10. **Admin Settings page** — no view at `resources/views/admin/settings/index.blade.php`.
11. **Admin menu-items modals split** — spec shows `create-modal.blade.php`, `edit-modal.blade.php`, `delete-modal.blade.php` but only `form.blade.php` exists. Currently works as-is but doesn't match spec structure.
12. **`menu-card.blade.php` component missing** — listed in spec but not created. Cards are inlined in `home.blade.php`.
13. **Error views missing** — `resources/views/errors/` folder is empty. No custom 404/500 pages.
14. **`public/videos/` folder is empty** — CLAUDE.md spec mentions `public/videos/` for hero video, but the `.mp4` is actually in `public/images/sapiens/`. Should either move the file or keep as-is.
15. **Reservation page — left image** — spec says "split layout: left = brand image/mood" but actual layout uses only form with dark background (no actual left image panel).
16. **About page images** — no photos used in `/about` page sections. The restaurant photos could enhance the storytelling sections.
17. **Community/Events page** — currently named route `community` but spec says `/events`. Page shows event cards but the nav labels it "Community".

**Not Started:**
18. **Docker/Nginx** — `docker/` directory is empty. `Dockerfile`, `docker-compose.yml`, `nginx/` config all missing.
19. **Feature tests** — only `ExampleTest` stubs. No tests for: reservation flow, email sending, admin CRUD, auth.
20. **Menu drinks data** — spec mentions Signature Mocktails, Highballs, Sake, Wine, Non-alcoholic categories with placeholder items. Only food is seeded.

---

### ⚠️ Spec Discrepancies (intentional changes)

| Spec Says | Actual Implementation | Reason |
|-----------|----------------------|--------|
| Tailwind v3.4 + `tailwind.config.js` | Tailwind **v4** with `@theme {}` in `app.css` | v4 was available and is CSS-first |
| Always dark, no toggle | Light/dark toggle added | User requested |
| English only (implied) | EN/VI language toggle | User requested |
| Laravel 11 | **Laravel 13** | Latest stable |
| `/events` route name | Named `community` | Route is `GET /community` → `EventController` |
| `docs/` folder has design artifacts | `docs/` folder is empty | Never populated |
| `admin/menu-items` separate modal files | Single `form.blade.php` | Simpler, works the same |
| `auth.php` separate routes file | All routes in `web.php` | Simpler, no functional difference |

---

### 🔜 Priority Build Order (what to do next)

1. **Security fix** — add `role` migration + role middleware (blocks auth bypass)
2. **ReservationService** — extract code generation, clean up controller
3. **Install packages** — `intervention/image` + `spatie/laravel-activitylog`
4. **Settings page** — controller + routes + view
5. **Error pages** — 404, 500, 503 custom views
6. **Configure SMTP** — real Gmail app password in `.env`
7. **Feature tests** — reservation flow, admin CRUD
8. **Docker setup** — Dockerfile + docker-compose.yml + Nginx config
9. **Reservation page** — add left-side image/mood panel
10. **About page** — incorporate restaurant photos into storytelling sections
