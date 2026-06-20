# i18n System & Admin Panel Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace attribute-based multilingual with key-based Laravel i18n (DB-backed lang files), add an admin Translation panel, and completely redesign the admin UI in blue/white.

**Architecture:** `translation_strings` table stores keys per locale; `TranslationFileGenerator` writes nested-array PHP lang files on admin save; `LocaleMiddleware` sets `App::locale` from cookie; client toggles language via `/translations/{locale}` JSON dictionary fetched once and cached in `sessionStorage`.

**Tech Stack:** Laravel 13 · Blade · Tailwind v4 · SQLite · Vanilla JS · PHPUnit

## Global Constraints

- Laravel 13 — middleware registered in `bootstrap/app.php` via `->withMiddleware()`, no `Kernel.php`
- Tailwind v4 — CSS-first, `@theme {}` in `app.css`, no `tailwind.config.js`
- SQLite database at `source/database/database.sqlite`
- Admin CSS vars scoped under `body[data-panel="admin"]`, never conflict with client `--sp-*` vars
- No `dark:` Tailwind prefix — everything via CSS custom properties
- No arbitrary hex values — use defined palettes only
- All Blade UI text via `__()` — no hardcoded English strings in templates after Task 7
- Run `php artisan test --no-coverage` after every task before committing

---

## File Map

**Create:**
- `database/migrations/2026_06_20_000001_create_translation_strings_table.php`
- `database/migrations/2026_06_20_000002_create_settings_table.php`
- `app/Models/TranslationString.php`
- `app/Models/Setting.php`
- `app/Services/TranslationFileGenerator.php`
- `app/Http/Middleware/LocaleMiddleware.php`
- `app/Http/Controllers/LocaleController.php`
- `app/Http/Controllers/Admin/TranslationController.php`
- `app/Http/Controllers/Admin/SettingController.php`
- `database/seeders/TranslationSeeder.php`
- `lang/en/ui.php`, `lang/en/pages.php`, `lang/en/emails.php`
- `lang/vi/ui.php`, `lang/vi/pages.php`, `lang/vi/emails.php`
- `resources/views/admin/translations/index.blade.php`
- `resources/views/admin/settings/index.blade.php`
- `tests/Unit/TranslationFileGeneratorTest.php`
- `tests/Feature/LocaleControllerTest.php`
- `tests/Feature/Admin/TranslationControllerTest.php`

**Modify:**
- `bootstrap/app.php` — register `LocaleMiddleware` on web group
- `routes/web.php` — `POST /locale`, `GET /translations/{locale}`, admin translation + settings routes
- `resources/css/app.css` — `body[data-panel="admin"]` vars + `.adm-*` classes
- `resources/js/app.js` — replace `applyLang()` with dictionary-fetch approach
- `resources/views/layouts/admin.blade.php` — new structure, `data-panel="admin"` on body
- `resources/views/components/admin/sidebar.blade.php` — complete rewrite
- `resources/views/components/admin/topbar.blade.php` — complete rewrite
- `resources/views/layouts/client.blade.php` — `lang="{{ app()->getLocale() }}"`, CSRF meta
- `resources/views/components/navbar.blade.php` — `__()` + `data-i18n`
- `resources/views/components/footer.blade.php` — `__()` + `data-i18n`
- `resources/views/client/home.blade.php` — `__()` for all static strings
- `resources/views/client/about.blade.php` — `__()`
- `resources/views/client/menu/index.blade.php` — `__()`
- `resources/views/client/reservation/index.blade.php` — `__()`
- `resources/views/client/events/index.blade.php` — `__()`
- `resources/views/admin/dashboard/index.blade.php` — `adm-*` classes
- `resources/views/admin/reservations/index.blade.php` — `adm-*` classes
- `resources/views/admin/menu-items/index.blade.php` — `adm-*` classes
- `resources/views/admin/events/index.blade.php` — `adm-*` classes

---

## Task 1: DB Migrations + Models

**Files:**
- Create: `database/migrations/2026_06_20_000001_create_translation_strings_table.php`
- Create: `database/migrations/2026_06_20_000002_create_settings_table.php`
- Create: `app/Models/TranslationString.php`
- Create: `app/Models/Setting.php`

**Interfaces:**
- Produces: `TranslationString` model with `group`, `key`, `locale`, `value`; `Setting::get(string $key, mixed $default)` and `Setting::set(string $key, mixed $value)` static helpers

- [ ] **Step 1: Write failing tests**

```php
// tests/Unit/Models/TranslationStringTest.php
<?php
namespace Tests\Unit\Models;

use App\Models\TranslationString;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationStringTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_translation_string(): void
    {
        $ts = TranslationString::create([
            'group'  => 'ui',
            'key'    => 'nav.home',
            'locale' => 'en',
            'value'  => 'Home',
        ]);
        $this->assertDatabaseHas('translation_strings', ['key' => 'nav.home', 'locale' => 'en']);
    }

    public function test_unique_constraint_on_group_key_locale(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home2']);
    }
}

// tests/Unit/Models/SettingTest.php
<?php
namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('en', Setting::get('default_locale', 'en'));
    }

    public function test_set_and_get(): void
    {
        Setting::set('default_locale', 'vi');
        $this->assertSame('vi', Setting::get('default_locale', 'en'));
    }
}
```

- [ ] **Step 2: Run tests — expect FAIL (classes not found)**

```
cd source && php artisan test --no-coverage --filter TranslationStringTest
cd source && php artisan test --no-coverage --filter SettingTest
```

Expected: Error — class not found.

- [ ] **Step 3: Create migration for translation_strings**

```php
// database/migrations/2026_06_20_000001_create_translation_strings_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translation_strings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);
            $table->string('key', 200);
            $table->string('locale', 10);
            $table->text('value');
            $table->timestamps();
            $table->unique(['group', 'key', 'locale']);
            $table->index(['group', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_strings');
    }
};
```

- [ ] **Step 4: Create migration for settings**

```php
// database/migrations/2026_06_20_000002_create_settings_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

- [ ] **Step 5: Create TranslationString model**

```php
// app/Models/TranslationString.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationString extends Model
{
    protected $fillable = ['group', 'key', 'locale', 'value'];
}
```

- [ ] **Step 6: Create Setting model**

```php
// app/Models/Setting.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::find($key);
        return $row ? $row->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
```

- [ ] **Step 7: Run migrations**

```
cd source && php artisan migrate
```

Expected: `translation_strings` and `settings` tables created.

- [ ] **Step 8: Run tests — expect PASS**

```
cd source && php artisan test --no-coverage --filter "TranslationStringTest|SettingTest"
```

Expected: 3 tests, 3 passed.

- [ ] **Step 9: Commit**

```
git add database/migrations/2026_06_20_000001_create_translation_strings_table.php \
        database/migrations/2026_06_20_000002_create_settings_table.php \
        app/Models/TranslationString.php \
        app/Models/Setting.php \
        tests/Unit/Models/TranslationStringTest.php \
        tests/Unit/Models/SettingTest.php
git commit -m "feat: add translation_strings and settings tables with models"
```

---

## Task 2: Initial Lang Files

**Files:**
- Create: `lang/en/ui.php`, `lang/en/pages.php`, `lang/en/emails.php`
- Create: `lang/vi/ui.php`, `lang/vi/pages.php`, `lang/vi/emails.php`

**Interfaces:**
- Produces: Lang files readable by `require lang_path('en/ui.php')` returning nested arrays

- [ ] **Step 1: Create `lang/en/ui.php`**

```php
<?php
return [
    'nav' => [
        'home'          => 'Home',
        'story'         => 'Story',
        'menu'          => 'Menu',
        'community'     => 'Community',
        'reserve'       => 'Reserve',
        'our_story'     => 'Our Story',
        'reserve_table' => 'Reserve a Table',
    ],
    'footer' => [
        'tagline'       => 'A Modern Cave for Modern Humans',
        'find_us'       => 'Find Us',
        'hours'         => 'Hours',
        'working_space' => 'Working Space',
        'bistro_bar'    => 'Bistro Bar',
        'ws_hours'      => 'Mon – Sun  11:00 – 17:00',
        'bb_hours'      => 'Mon – Sun  18:00 – 01:00',
        'copyright'     => '© :year Sapiens House. All rights reserved.',
        'admin'         => 'Admin',
        'instagram'     => 'Instagram',
        'facebook'      => 'Facebook',
        'maps_link'     => '↗ Google Maps',
    ],
    'btn' => [
        'book_table'   => 'Reserve a Table',
        'explore_menu' => 'Explore Menu',
        'read_story'   => 'Read Our Story',
        'view_menu'    => 'Full Menu →',
    ],
    'flash' => [
        'success' => 'Done!',
        'error'   => 'Something went wrong.',
    ],
];
```

- [ ] **Step 2: Create `lang/vi/ui.php`**

```php
<?php
return [
    'nav' => [
        'home'          => 'Trang chủ',
        'story'         => 'Câu chuyện',
        'menu'          => 'Thực đơn',
        'community'     => 'Cộng đồng',
        'reserve'       => 'Đặt bàn',
        'our_story'     => 'Câu chuyện',
        'reserve_table' => 'Đặt một bàn',
    ],
    'footer' => [
        'tagline'       => 'Hang động hiện đại cho con người hiện đại',
        'find_us'       => 'Tìm Chúng Tôi',
        'hours'         => 'Giờ Mở Cửa',
        'working_space' => 'Không Gian Làm Việc',
        'bistro_bar'    => 'Bistro Bar',
        'ws_hours'      => 'T2 – CN  11:00 – 17:00',
        'bb_hours'      => 'T2 – CN  18:00 – 01:00',
        'copyright'     => '© :year Sapiens House. Bản quyền thuộc về chúng tôi.',
        'admin'         => 'Quản trị',
        'instagram'     => 'Instagram',
        'facebook'      => 'Facebook',
        'maps_link'     => '↗ Google Maps',
    ],
    'btn' => [
        'book_table'   => 'Đặt bàn',
        'explore_menu' => 'Xem thực đơn',
        'read_story'   => 'Xem câu chuyện',
        'view_menu'    => 'Thực đơn đầy đủ →',
    ],
    'flash' => [
        'success' => 'Thành công!',
        'error'   => 'Đã có lỗi xảy ra.',
    ],
];
```

- [ ] **Step 3: Create `lang/en/pages.php`**

```php
<?php
return [
    'hero' => [
        'tagline'     => 'A Modern Cave for Modern Humans',
        'cta_reserve' => 'Reserve a Table',
        'cta_menu'    => 'Explore Menu',
    ],
    'intro' => [
        'index'  => '001 · Origin',
        'label'  => 'Born From a Book',
        'line_1' => 'Every great gathering place',
        'line_2' => 'begins with an idea.',
        'body_1' => 'Sapiens House is inspired by Yuval Noah Harari\'s "Sapiens" — a journey through human history, from the cave to civilization.',
        'body_2' => 'Thousands of years ago, fire and the cave were where humans gathered — to tell stories, share, and build community. That\'s where civilization began.',
        'body_3' => 'We bring that into the 21st century: a modern cave — dark, warm, mysterious, yet full of life and connection.',
        'cta'    => 'Read Our Story',
    ],
    'experience' => [
        'ws_time'   => '11:00 — 17:00',
        'ws_title'  => 'Working Space',
        'ws_desc'   => 'A workspace for thinking, meeting, and coffee. Warm light, soft sound — perfect for focus and creativity.',
        'ws_feat_1' => 'High-speed WiFi',
        'ws_feat_2' => 'Specialty Coffee',
        'ws_feat_3' => 'Light Bites & Lunch',
        'bb_time'   => '18:00 — 01:00',
        'bb_title'  => 'Bistro Bar',
        'bb_desc'   => 'Japanese fusion cuisine, craft cocktails, and captivating music. A nocturnal space for endless conversation.',
        'bb_feat_1' => 'Japanese Fusion Cuisine',
        'bb_feat_2' => 'Craft Cocktails & Sake',
        'bb_feat_3' => 'Live Music Events',
    ],
    'menu_showcase' => [
        'label'     => 'From Our Kitchen',
        'title'     => 'Signature Dishes',
        'cta'       => 'Full Menu →',
        'drag_hint' => '← Drag to explore →',
        'vat_note'  => 'Prices in 000 VND · Subject to 10% VAT & 8% service charge',
        'empty'     => 'Menu coming soon.',
    ],
    'vibe' => [
        'label'          => 'The Vibe',
        'title_1'        => 'Built for',
        'title_2'        => 'Human Connection',
        'gather_title'   => 'Gather',
        'gather_text'    => 'Where modern humans meet and connect.',
        'share_title'    => 'Share',
        'share_text'     => 'Share meals, stories, and meaningful moments.',
        'create_title'   => 'Create',
        'create_text'    => 'A creative space for new ideas.',
        'discover_title' => 'Discover',
        'discover_text'  => 'Explore Japanese fusion cuisine and craft cocktails.',
        'belong_title'   => 'Belong',
        'belong_text'    => 'A sense of belonging — not just a customer.',
        'evolve_title'   => 'Evolve',
        'evolve_text'    => 'Sapiens always evolves — this is where it begins.',
    ],
    'gallery' => [
        'bistro_label' => 'Bistro Bar',
        'bistro_title' => 'An Evening Well Spent',
    ],
    'reservation_cta' => [
        'title' => 'Reserve Your Spot',
        'sub'   => 'Join us at Tầng 4, 44 Nguyễn Huệ, Quận 1',
        'btn'   => 'Book a Table',
    ],
    'menu_page' => [
        'title'        => 'Our Menu',
        'vat_note'     => 'Prices in 000 VND · +10% VAT · +8% Service Charge',
        'filter_all'   => 'All',
        'filter_food'  => 'Food',
        'filter_drink' => 'Drinks',
        'empty'        => 'No items in this category yet.',
    ],
    'reservation_page' => [
        'label'         => 'Reservations',
        'title'         => 'Reserve Your Spot',
        'sub'           => 'Fill in the form below and we will confirm within 24 hours. Reservation for Bistro Bar (18:00 – 01:00).',
        'bistro_label'  => 'Bistro Bar',
        'bistro_hours'  => '18:00 – 01:00 daily',
        'bistro_addr'   => 'Tầng 4, 44 Nguyễn Huệ, Q.1',
        'maps_link'     => '↗ View on Google Maps',
        'success_title' => 'Reservation Successful!',
        'success_body'  => 'We have received your reservation request.',
        'success_email' => 'A confirmation email has been sent to your inbox.',
        'code_label'    => 'Your code',
        'field_name'    => 'Full Name',
        'field_phone'   => 'Phone',
        'field_email'   => 'Email',
        'field_date'    => 'Date',
        'field_time'    => 'Time',
        'field_guests'  => 'Number of Guests',
        'field_area'    => 'Seating Area',
        'field_note'    => 'Note',
        'field_allergy' => 'Food Allergies',
        'field_birthday'=> 'Birthday Celebration',
        'field_special' => 'Special Request',
        'area_indoor'   => 'Indoor',
        'area_outdoor'  => 'Outdoor',
        'area_bar'      => 'Bar Counter',
        'submit'        => 'Confirm Reservation',
        'submitting'    => 'Sending...',
        'time_select'   => 'Select time',
    ],
    'about_page' => [
        'hero_label'       => 'Our Story',
        'hero_title'       => 'We Are Sapiens',
        'book_label'       => 'The Inspiration',
        'book_title'       => 'The Book That Started It All',
        'cave_label'       => 'The Space',
        'cave_title'       => 'The Modern Cave',
        'community_label'  => 'The People',
        'community_title'  => 'The Community',
        'vision_label'     => 'The Future',
        'vision_title'     => 'The Vision',
        'cta'              => 'Reserve a Table',
    ],
    'events_page' => [
        'label'               => 'Community',
        'title'               => 'Events & Gatherings',
        'sub'                 => 'Guest Shifts · Workshops · Special Nights',
        'empty'               => 'Stay tuned — something\'s brewing.',
        'badge_event'         => 'Event',
        'badge_guest_shift'   => 'Guest Shift',
        'badge_workshop'      => 'Workshop',
        'badge_special_night' => 'Special Night',
        'badge_community'     => 'Community',
    ],
];
```

- [ ] **Step 4: Create `lang/vi/pages.php`**

```php
<?php
return [
    'hero' => [
        'tagline'     => 'Hang Động Hiện Đại Cho Con Người Hiện Đại',
        'cta_reserve' => 'Đặt bàn',
        'cta_menu'    => 'Xem thực đơn',
    ],
    'intro' => [
        'index'  => '001 · Nguồn gốc',
        'label'  => 'Sinh Ra Từ Một Cuốn Sách',
        'line_1' => 'Mọi nơi tụ họp vĩ đại',
        'line_2' => 'đều bắt đầu từ một ý tưởng.',
        'body_1' => 'Sapiens House lấy cảm hứng từ cuốn sách "Sapiens" của Yuval Noah Harari — một hành trình qua lịch sử loài người, từ hang động đến nền văn minh.',
        'body_2' => 'Hàng nghìn năm trước, lửa và hang động là nơi con người tụ họp — kể chuyện, chia sẻ và xây dựng cộng đồng. Đó là nơi văn minh bắt đầu.',
        'body_3' => 'Chúng tôi mang điều đó vào thế kỷ 21: một hang động hiện đại — tối, ấm, bí ẩn, nhưng đầy sức sống và kết nối.',
        'cta'    => 'Xem câu chuyện',
    ],
    'experience' => [
        'ws_time'   => '11:00 — 17:00',
        'ws_title'  => 'Không Gian Làm Việc',
        'ws_desc'   => 'Không gian làm việc, gặp gỡ và cà phê. Ánh sáng ấm, âm thanh nhẹ — lý tưởng để tập trung và sáng tạo.',
        'ws_feat_1' => 'WiFi tốc độ cao',
        'ws_feat_2' => 'Cà phê đặc sản',
        'ws_feat_3' => 'Ăn nhẹ & Trưa',
        'bb_time'   => '18:00 — 01:00',
        'bb_title'  => 'Bistro Bar',
        'bb_desc'   => 'Ẩm thực fusion Nhật, cocktail thủ công và âm nhạc cuốn hút. Không gian về đêm cho những cuộc chuyện trò không hồi kết.',
        'bb_feat_1' => 'Ẩm thực Fusion Nhật',
        'bb_feat_2' => 'Cocktail & Sake thủ công',
        'bb_feat_3' => 'Âm nhạc trực tiếp',
    ],
    'menu_showcase' => [
        'label'     => 'Từ Bếp Của Chúng Tôi',
        'title'     => 'Món Đặc Trưng',
        'cta'       => 'Thực đơn đầy đủ →',
        'drag_hint' => '← Kéo để khám phá →',
        'vat_note'  => 'Giá tính theo 000 VND · Chưa bao gồm 10% VAT & 8% phí phục vụ',
        'empty'     => 'Thực đơn sắp ra mắt.',
    ],
    'vibe' => [
        'label'          => 'Không Khí',
        'title_1'        => 'Được xây dựng cho',
        'title_2'        => 'Sự Kết Nối',
        'gather_title'   => 'Tụ Họp',
        'gather_text'    => 'Nơi những con người hiện đại tụ họp và kết nối.',
        'share_title'    => 'Chia Sẻ',
        'share_text'     => 'Chia sẻ bữa ăn, câu chuyện và khoảnh khắc ý nghĩa.',
        'create_title'   => 'Sáng Tạo',
        'create_text'    => 'Không gian sáng tạo cho những ý tưởng mới.',
        'discover_title' => 'Khám Phá',
        'discover_text'  => 'Khám phá ẩm thực fusion Nhật và cocktail thủ công.',
        'belong_title'   => 'Thuộc Về',
        'belong_text'    => 'Cảm giác thuộc về — không chỉ là khách hàng.',
        'evolve_title'   => 'Tiến Hóa',
        'evolve_text'    => 'Sapiens luôn tiến hóa — đây là nơi khởi đầu.',
    ],
    'gallery' => [
        'bistro_label' => 'Bistro Bar',
        'bistro_title' => 'Một Buổi Tối Đáng Nhớ',
    ],
    'reservation_cta' => [
        'title' => 'Đặt Chỗ Của Bạn',
        'sub'   => 'Ghé thăm chúng tôi tại Tầng 4, 44 Nguyễn Huệ, Quận 1',
        'btn'   => 'Đặt bàn',
    ],
    'menu_page' => [
        'title'        => 'Thực Đơn',
        'vat_note'     => 'Giá tính theo 000 VND · +10% VAT · +8% Phí phục vụ',
        'filter_all'   => 'Tất cả',
        'filter_food'  => 'Đồ ăn',
        'filter_drink' => 'Đồ uống',
        'empty'        => 'Chưa có món trong danh mục này.',
    ],
    'reservation_page' => [
        'label'         => 'Đặt Bàn',
        'title'         => 'Đặt Chỗ Của Bạn',
        'sub'           => 'Điền thông tin bên dưới và chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ. Yêu cầu đặt bàn cho Bistro Bar (18:00 – 01:00).',
        'bistro_label'  => 'Bistro Bar',
        'bistro_hours'  => '18:00 – 01:00 hàng ngày',
        'bistro_addr'   => 'Tầng 4, 44 Nguyễn Huệ, Q.1',
        'maps_link'     => '↗ Xem trên Google Maps',
        'success_title' => 'Đặt Bàn Thành Công!',
        'success_body'  => 'Chúng tôi đã nhận được yêu cầu đặt bàn của bạn.',
        'success_email' => 'Email xác nhận đã được gửi đến hộp thư của bạn.',
        'code_label'    => 'Mã đặt bàn',
        'field_name'    => 'Họ và tên',
        'field_phone'   => 'Số điện thoại',
        'field_email'   => 'Email',
        'field_date'    => 'Ngày',
        'field_time'    => 'Giờ',
        'field_guests'  => 'Số lượng khách',
        'field_area'    => 'Khu vực ngồi',
        'field_note'    => 'Ghi chú',
        'field_allergy' => 'Dị ứng thực phẩm',
        'field_birthday'=> 'Tiệc sinh nhật',
        'field_special' => 'Yêu cầu đặc biệt',
        'area_indoor'   => 'Trong nhà',
        'area_outdoor'  => 'Ngoài trời',
        'area_bar'      => 'Quầy bar',
        'submit'        => 'Xác nhận đặt bàn',
        'submitting'    => 'Đang gửi...',
        'time_select'   => 'Chọn giờ',
    ],
    'about_page' => [
        'hero_label'       => 'Câu chuyện của chúng tôi',
        'hero_title'       => 'Chúng Ta Là Sapiens',
        'book_label'       => 'Nguồn cảm hứng',
        'book_title'       => 'Cuốn Sách Khởi Nguồn',
        'cave_label'       => 'Không gian',
        'cave_title'       => 'Hang Động Hiện Đại',
        'community_label'  => 'Con người',
        'community_title'  => 'Cộng Đồng',
        'vision_label'     => 'Tương lai',
        'vision_title'     => 'Tầm Nhìn',
        'cta'              => 'Đặt bàn',
    ],
    'events_page' => [
        'label'               => 'Cộng đồng',
        'title'               => 'Sự Kiện & Gặp Gỡ',
        'sub'                 => 'Ca Khách · Hội Thảo · Đêm Đặc Biệt',
        'empty'               => 'Hãy đón chờ — điều gì đó đang được chuẩn bị.',
        'badge_event'         => 'Sự kiện',
        'badge_guest_shift'   => 'Ca khách',
        'badge_workshop'      => 'Hội thảo',
        'badge_special_night' => 'Đêm đặc biệt',
        'badge_community'     => 'Cộng đồng',
    ],
];
```

- [ ] **Step 5: Create `lang/en/emails.php`**

```php
<?php
return [
    'reservation' => [
        'subject'       => '[Sapiens House] Reservation Confirmed – :code',
        'greeting'      => 'Dear :name,',
        'thank_you'     => 'Thank you for your reservation. We have received your booking request.',
        'details_title' => 'Your Booking Details',
        'code'          => 'Reservation Code',
        'date'          => 'Date',
        'time'          => 'Time',
        'guests'        => 'Number of Guests',
        'area'          => 'Seating Area',
        'note'          => 'Note',
        'confirm_msg'   => 'We will contact you to confirm within 24 hours.',
        'special_note'  => 'We have noted your special request.',
        'birthday_note' => 'We look forward to celebrating your birthday with you!',
        'footer_addr'   => 'Tầng 4, 44 Nguyễn Huệ, Quận 1, TP.HCM',
        'maps_link'     => 'Get Directions →',
    ],
];
```

- [ ] **Step 6: Create `lang/vi/emails.php`**

```php
<?php
return [
    'reservation' => [
        'subject'       => '[Sapiens House] Xác nhận đặt bàn – :code',
        'greeting'      => 'Kính gửi :name,',
        'thank_you'     => 'Cảm ơn bạn đã đặt bàn. Chúng tôi đã nhận được yêu cầu đặt bàn của bạn.',
        'details_title' => 'Thông Tin Đặt Bàn',
        'code'          => 'Mã đặt bàn',
        'date'          => 'Ngày',
        'time'          => 'Giờ',
        'guests'        => 'Số lượng khách',
        'area'          => 'Khu vực ngồi',
        'note'          => 'Ghi chú',
        'confirm_msg'   => 'Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ.',
        'special_note'  => 'Chúng tôi đã ghi nhận yêu cầu đặc biệt của bạn.',
        'birthday_note' => 'Chúng tôi mong chờ được cùng bạn kỷ niệm sinh nhật!',
        'footer_addr'   => 'Tầng 4, 44 Nguyễn Huệ, Quận 1, TP.HCM',
        'maps_link'     => 'Chỉ đường →',
    ],
];
```

- [ ] **Step 7: Verify lang files load**

```
cd source && php artisan tinker --execute="dd(__('ui.nav.home'), __('ui.nav.home', [], 'vi'));"
```

Expected: `"Home"` and `"Trang chủ"` — confirms Laravel finds the files.

- [ ] **Step 8: Commit**

```
git add lang/
git commit -m "feat: add initial en/vi lang files for ui, pages, emails groups"
```

---

## Task 3: TranslationSeeder

**Files:**
- Create: `database/seeders/TranslationSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: `TranslationString` model; `lang/{locale}/{group}.php` files from Task 2
- Produces: `translation_strings` table populated with all keys from lang files

- [ ] **Step 1: Create TranslationSeeder**

```php
// database/seeders/TranslationSeeder.php
<?php
namespace Database\Seeders;

use App\Models\TranslationString;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $groups  = ['ui', 'pages', 'emails'];
        $locales = ['en', 'vi'];

        foreach ($groups as $group) {
            foreach ($locales as $locale) {
                $path = lang_path("{$locale}/{$group}.php");
                if (!file_exists($path)) {
                    continue;
                }

                $flat = Arr::dot(require $path);

                foreach ($flat as $key => $value) {
                    TranslationString::updateOrCreate(
                        ['group' => $group, 'key' => $key, 'locale' => $locale],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
```

- [ ] **Step 2: Add to DatabaseSeeder**

Open `database/seeders/DatabaseSeeder.php`. Add `TranslationSeeder` to the `run()` call list (after existing seeders):

```php
$this->call([
    MenuCategorySeeder::class,
    MenuItemSeeder::class,
    AdminUserSeeder::class,
    TranslationSeeder::class,  // add this line
]);
```

- [ ] **Step 3: Run seeder**

```
cd source && php artisan db:seed --class=TranslationSeeder
```

Expected: No errors. Rows visible in `translation_strings`.

- [ ] **Step 4: Verify row count**

```
cd source && php artisan tinker --execute="echo App\Models\TranslationString::count();"
```

Expected: > 100 rows.

- [ ] **Step 5: Commit**

```
git add database/seeders/TranslationSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: add TranslationSeeder to populate translation_strings from lang files"
```

---

## Task 4: TranslationFileGenerator Service

**Files:**
- Create: `app/Services/TranslationFileGenerator.php`
- Create: `tests/Unit/TranslationFileGeneratorTest.php`

**Interfaces:**
- Consumes: `TranslationString` model
- Produces: `TranslationFileGenerator::regenerate(string $group)` — writes `lang/{locale}/{group}.php` with nested arrays

- [ ] **Step 1: Write failing test**

```php
// tests/Unit/TranslationFileGeneratorTest.php
<?php
namespace Tests\Unit;

use App\Models\TranslationString;
use App\Services\TranslationFileGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationFileGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_regenerate_writes_nested_array_php_file(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.story', 'locale' => 'en', 'value' => 'Story']);
        TranslationString::create(['group' => 'ui', 'key' => 'btn.book', 'locale' => 'en', 'value' => 'Book']);

        app(TranslationFileGenerator::class)->regenerate('ui');

        $path = lang_path('en/ui.php');
        $this->assertFileExists($path);

        $result = require $path;
        $this->assertSame('Home', $result['nav']['home']);
        $this->assertSame('Story', $result['nav']['story']);
        $this->assertSame('Book', $result['btn']['book']);
    }

    public function test_regenerate_handles_multiple_locales(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        app(TranslationFileGenerator::class)->regenerate('ui');

        $en = require lang_path('en/ui.php');
        $vi = require lang_path('vi/ui.php');

        $this->assertSame('Home', $en['nav']['home']);
        $this->assertSame('Trang chủ', $vi['nav']['home']);
    }
}
```

- [ ] **Step 2: Run — expect FAIL**

```
cd source && php artisan test --no-coverage --filter TranslationFileGeneratorTest
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement TranslationFileGenerator**

```php
// app/Services/TranslationFileGenerator.php
<?php
namespace App\Services;

use App\Models\TranslationString;
use Illuminate\Support\Arr;

class TranslationFileGenerator
{
    public function regenerate(string $group): void
    {
        $locales = TranslationString::where('group', $group)
            ->distinct()
            ->pluck('locale')
            ->toArray();

        foreach ($locales as $locale) {
            $rows = TranslationString::where('group', $group)
                ->where('locale', $locale)
                ->pluck('value', 'key');

            $nested = [];
            foreach ($rows as $dotKey => $value) {
                Arr::set($nested, $dotKey, $value);
            }

            $dir = lang_path($locale);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $content = "<?php\n\nreturn " . var_export($nested, true) . ";\n";
            file_put_contents("{$dir}/{$group}.php", $content);
        }
    }
}
```

- [ ] **Step 4: Run — expect PASS**

```
cd source && php artisan test --no-coverage --filter TranslationFileGeneratorTest
```

Expected: 2 tests, 2 passed.

- [ ] **Step 5: Commit**

```
git add app/Services/TranslationFileGenerator.php tests/Unit/TranslationFileGeneratorTest.php
git commit -m "feat: add TranslationFileGenerator service with nested array output"
```

---

## Task 5: Locale Infrastructure (Middleware + Controller + Routes)

**Files:**
- Create: `app/Http/Middleware/LocaleMiddleware.php`
- Create: `app/Http/Controllers/LocaleController.php`
- Create: `tests/Feature/LocaleControllerTest.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/web.php`
- Modify: `resources/views/layouts/client.blade.php`

**Interfaces:**
- Consumes: `Setting::get('default_locale', 'en')`; cookie `app_locale`
- Produces: `App::getLocale()` returns correct locale on every request; `POST /locale` sets cookie

- [ ] **Step 1: Write failing tests**

```php
// tests/Feature/LocaleControllerTest.php
<?php
namespace Tests\Feature;

use Tests\TestCase;

class LocaleControllerTest extends TestCase
{
    public function test_switch_sets_cookie_and_redirects(): void
    {
        $response = $this->post('/locale', ['locale' => 'vi']);

        $response->assertRedirect();
        $response->assertCookie('app_locale', 'vi');
    }

    public function test_switch_rejects_unsupported_locale(): void
    {
        $response = $this->post('/locale', ['locale' => 'ja']);

        $response->assertRedirect();
        $response->assertCookieMissing('app_locale');
    }

    public function test_locale_middleware_sets_locale_from_cookie(): void
    {
        $response = $this->withCookie('app_locale', 'vi')->get('/');

        $this->assertSame('vi', app()->getLocale());
    }
}
```

- [ ] **Step 2: Run — expect FAIL**

```
cd source && php artisan test --no-coverage --filter LocaleControllerTest
```

Expected: FAIL — route not found.

- [ ] **Step 3: Create LocaleMiddleware**

```php
// app/Http/Middleware/LocaleMiddleware.php
<?php
namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    private const SUPPORTED = ['en', 'vi'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('app_locale')
            ?? Setting::get('default_locale', config('app.locale', 'en'));

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
```

- [ ] **Step 4: Create LocaleController**

```php
// app/Http/Controllers/LocaleController.php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const SUPPORTED = ['en', 'vi'];

    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (!in_array($locale, self::SUPPORTED, true)) {
            return redirect()->back();
        }

        return redirect()->back()->withCookie(
            cookie()->forever('app_locale', $locale)
        );
    }
}
```

- [ ] **Step 5: Register middleware in bootstrap/app.php**

```php
// bootstrap/app.php — replace the withMiddleware closure with:
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\LocaleMiddleware::class,
    ]);
})
```

- [ ] **Step 6: Add route to routes/web.php**

Add after the existing `Route::get('/up', ...)` or at the top of the web routes section:

```php
// Locale switching
Route::post('/locale', [App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');
```

- [ ] **Step 7: Add CSRF meta tag to client layout**

Open `resources/views/layouts/client.blade.php`. Inside `<head>`, add if not present:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Also change `<html lang="vi">` to `<html lang="{{ app()->getLocale() }}">`.

- [ ] **Step 8: Run tests — expect PASS**

```
cd source && php artisan test --no-coverage --filter LocaleControllerTest
```

Expected: 3 tests, 3 passed.

- [ ] **Step 9: Commit**

```
git add app/Http/Middleware/LocaleMiddleware.php \
        app/Http/Controllers/LocaleController.php \
        tests/Feature/LocaleControllerTest.php \
        bootstrap/app.php \
        routes/web.php \
        resources/views/layouts/client.blade.php
git commit -m "feat: add LocaleMiddleware and POST /locale for server-side locale switching"
```

---

## Task 6: Translation JSON Endpoint + JS Dictionary Fetch

**Files:**
- Modify: `routes/web.php` — add `GET /translations/{locale}`
- Modify: `app/Http/Controllers/LocaleController.php` — add `dictionary()` method
- Create: `tests/Feature/TranslationDictionaryTest.php`
- Modify: `resources/js/app.js` — replace `applyLang()` with dictionary-fetch

**Interfaces:**
- Produces: `GET /translations/{locale}` returns flat JSON `{"nav.home":"Home",...}`; JS `applyLang(lang)` fetches dict and swaps `[data-i18n]` elements

- [ ] **Step 1: Write failing test**

```php
// tests/Feature/TranslationDictionaryTest.php
<?php
namespace Tests\Feature;

use App\Models\TranslationString;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationDictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_ui_group_as_flat_json(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);
        TranslationString::create(['group' => 'ui', 'key' => 'btn.book', 'locale' => 'vi', 'value' => 'Đặt bàn']);
        TranslationString::create(['group' => 'pages', 'key' => 'hero.title', 'locale' => 'vi', 'value' => 'Test']); // should NOT appear

        $response = $this->get('/translations/vi');

        $response->assertOk()
                 ->assertJsonStructure(['nav.home', 'btn.book'])
                 ->assertJsonMissing(['hero.title']);
    }

    public function test_rejects_unsupported_locale(): void
    {
        $this->get('/translations/ja')->assertStatus(400);
    }
}
```

- [ ] **Step 2: Run — expect FAIL**

```
cd source && php artisan test --no-coverage --filter TranslationDictionaryTest
```

Expected: FAIL — route not found.

- [ ] **Step 3: Add dictionary() method to LocaleController**

```php
// Add to app/Http/Controllers/LocaleController.php

use Illuminate\Http\JsonResponse;
use App\Models\TranslationString;
use Illuminate\Support\Facades\Cache;

public function dictionary(string $locale): JsonResponse
{
    if (!in_array($locale, self::SUPPORTED, true)) {
        return response()->json(['error' => 'Unsupported locale'], 400);
    }

    $data = Cache::remember("translations.dict.{$locale}", 3600, function () use ($locale) {
        return TranslationString::where('group', 'ui')
            ->where('locale', $locale)
            ->pluck('value', 'key')
            ->toArray();
    });

    return response()->json($data)
        ->header('Cache-Control', 'public, max-age=3600');
}
```

- [ ] **Step 4: Add route to routes/web.php**

```php
Route::get('/translations/{locale}', [App\Http\Controllers\LocaleController::class, 'dictionary'])
    ->name('translations.dictionary');
```

- [ ] **Step 5: Run — expect PASS**

```
cd source && php artisan test --no-coverage --filter TranslationDictionaryTest
```

Expected: 2 tests, 2 passed.

- [ ] **Step 6: Replace applyLang() in app.js**

Replace the entire `applyLang` function and the language button event listeners in `resources/js/app.js`:

```javascript
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
```

Also update the language button event listener (keep the rest of `DOMContentLoaded` unchanged, just update the lang listener):

```javascript
// Language buttons
document.querySelectorAll('.sp-lang-btn').forEach(function (btn) {
    btn.addEventListener('click', function () { applyLang(btn.dataset.lang); });
});
```

- [ ] **Step 7: Run all tests**

```
cd source && php artisan test --no-coverage
```

Expected: All green.

- [ ] **Step 8: Commit**

```
git add routes/web.php app/Http/Controllers/LocaleController.php \
        tests/Feature/TranslationDictionaryTest.php \
        resources/js/app.js
git commit -m "feat: add translation JSON endpoint and dictionary-based JS lang switching"
```

---

## Task 7: Client Blade Template Migration

**Files:**
- Modify: `resources/views/components/navbar.blade.php`
- Modify: `resources/views/components/footer.blade.php`
- Modify: `resources/views/client/home.blade.php`
- Modify: `resources/views/client/about.blade.php`
- Modify: `resources/views/client/menu/index.blade.php`
- Modify: `resources/views/client/reservation/index.blade.php`
- Modify: `resources/views/client/events/index.blade.php`

**Rule:** Replace every hardcoded UI string with `__('group.key')`. Add `data-i18n="key"` (flat, without group prefix) on nav/button elements so the JS dictionary can swap them. Long page body paragraphs use `__()` only (no `data-i18n` — they require a page reload to change, which is acceptable).

- [ ] **Step 1: Update navbar.blade.php**

Replace all `data-en`/`data-vi` attributes and hardcoded text. Key pattern:

```html
{{-- Desktop links --}}
<a href="{{ route('home') }}"
   class="sp-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
   data-i18n="nav.home">{{ __('ui.nav.home') }}</a>
<a href="{{ route('about') }}"
   class="sp-nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
   data-i18n="nav.story">{{ __('ui.nav.story') }}</a>
<a href="{{ route('menu') }}"
   class="sp-nav-link {{ request()->routeIs('menu') ? 'active' : '' }}"
   data-i18n="nav.menu">{{ __('ui.nav.menu') }}</a>
<a href="{{ route('community') }}"
   class="sp-nav-link {{ request()->routeIs('community') ? 'active' : '' }}"
   data-i18n="nav.community">{{ __('ui.nav.community') }}</a>

{{-- CTA --}}
<a href="{{ route('reservation') }}" class="sp-nav-cta"
   data-i18n="nav.reserve">{{ __('ui.nav.reserve') }}</a>

{{-- Mobile links --}}
<a href="{{ route('home') }}"        class="sp-mobile-link" data-i18n="nav.home">{{ __('ui.nav.home') }}</a>
<a href="{{ route('about') }}"       class="sp-mobile-link" data-i18n="nav.our_story">{{ __('ui.nav.our_story') }}</a>
<a href="{{ route('menu') }}"        class="sp-mobile-link" data-i18n="nav.menu">{{ __('ui.nav.menu') }}</a>
<a href="{{ route('community') }}"   class="sp-mobile-link" data-i18n="nav.community">{{ __('ui.nav.community') }}</a>
<a href="{{ route('reservation') }}" class="sp-mobile-link sp-mobile-cta"
   data-i18n="nav.reserve_table">{{ __('ui.nav.reserve_table') }}</a>

{{-- Language toggle: change buttons to POST form --}}
<form method="POST" action="{{ route('locale.switch') }}" style="display:contents">
    @csrf
    <div id="sp-lang-toggle" role="group" aria-label="Language">
        <button type="submit" name="locale" value="en"
                class="sp-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                aria-pressed="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}"
                data-lang="en">EN</button>
        <span id="sp-lang-sep" aria-hidden="true">·</span>
        <button type="submit" name="locale" value="vi"
                class="sp-lang-btn {{ app()->getLocale() === 'vi' ? 'active' : '' }}"
                aria-pressed="{{ app()->getLocale() === 'vi' ? 'true' : 'false' }}"
                data-lang="vi">VI</button>
    </div>
</form>
```

> The form wraps the lang buttons for progressive enhancement — JS intercepts the click and does instant swap + background fetch without page reload.

- [ ] **Step 2: Update footer.blade.php**

```html
<h4 class="font-display text-sm mb-5"
    style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
    data-i18n="footer.find_us">{{ __('ui.footer.find_us') }}</h4>

<h4 class="font-display text-sm mb-5"
    style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
    data-i18n="footer.hours">{{ __('ui.footer.hours') }}</h4>

<p ... data-i18n="footer.working_space">{{ __('ui.footer.working_space') }}</p>
<p>{{ __('ui.footer.ws_hours') }}</p>

<p ... data-i18n="footer.bistro_bar">{{ __('ui.footer.bistro_bar') }}</p>
<p>{{ __('ui.footer.bb_hours') }}</p>

<p style="color:#3A3A35; font-size:0.7rem; letter-spacing:0.08em;">
    {{ __('ui.footer.copyright', ['year' => date('Y')]) }}
</p>
<a href="{{ route('login') }}" ...>{{ __('ui.footer.admin') }}</a>
```

- [ ] **Step 3: Update home.blade.php — hero section**

```html
{{-- Hero tagline --}}
<p id="sp-hero-tagline" ...>{{ __('pages.hero.tagline') }}</p>

{{-- CTAs --}}
<a href="{{ route('reservation') }}" class="sp-btn-primary">
    <span data-i18n="btn.book_table">{{ __('ui.btn.book_table') }}</span>
    ...
</a>
<a href="{{ route('menu') }}" class="sp-btn-ghost"
   data-i18n="btn.explore_menu">{{ __('ui.btn.explore_menu') }}</a>
```

- [ ] **Step 4: Update home.blade.php — intro section**

```html
<p style="color:#3A3A35; ..." >{{ __('pages.intro.index') }}</p>

<p class="font-display sp-intro-label" ...>{{ __('pages.intro.label') }}</p>

<h2 class="font-display" ...>
    <span data-line style="display:block;">{{ __('pages.intro.line_1') }}</span>
    <span data-line style="display:block; color:#8C7E6A;">{{ __('pages.intro.line_2') }}</span>
</h2>

<p data-line ...>{{ __('pages.intro.body_1') }}</p>
<p data-line ...>{{ __('pages.intro.body_2') }}</p>
<p data-line ...>{{ __('pages.intro.body_3') }}</p>

<a href="{{ route('about') }}" class="sp-btn-ghost">{{ __('pages.intro.cta') }}</a>
```

- [ ] **Step 5: Update home.blade.php — experience section**

```html
{{-- Working Space --}}
<div class="sp-exp-time">{{ __('pages.experience.ws_time') }}</div>
<h3 class="font-display sp-exp-title">{{ __('pages.experience.ws_title') }}</h3>
<p class="sp-exp-desc">{{ __('pages.experience.ws_desc') }}</p>
<ul class="sp-exp-features">
    <li>{{ __('pages.experience.ws_feat_1') }}</li>
    <li>{{ __('pages.experience.ws_feat_2') }}</li>
    <li>{{ __('pages.experience.ws_feat_3') }}</li>
</ul>

{{-- Bistro Bar --}}
<div class="sp-exp-time">{{ __('pages.experience.bb_time') }}</div>
<h3 class="font-display sp-exp-title">{{ __('pages.experience.bb_title') }}</h3>
<p class="sp-exp-desc">{{ __('pages.experience.bb_desc') }}</p>
<ul class="sp-exp-features">
    <li>{{ __('pages.experience.bb_feat_1') }}</li>
    <li>{{ __('pages.experience.bb_feat_2') }}</li>
    <li>{{ __('pages.experience.bb_feat_3') }}</li>
</ul>
```

- [ ] **Step 6: Update home.blade.php — menu showcase, vibe, gallery, reservation CTA**

```html
{{-- Menu showcase header --}}
<p ...>{{ __('pages.menu_showcase.label') }}</p>
<h2 class="font-display" ...>{{ __('pages.menu_showcase.title') }}</h2>
<a href="{{ route('menu') }}" class="sp-btn-ghost sp-btn-sm">{{ __('pages.menu_showcase.cta') }}</a>
<p ...>{{ __('pages.menu_showcase.drag_hint') }}</p>
<p ...>{{ __('pages.menu_showcase.vat_note') }}</p>

{{-- Vibe section --}}
<p ...>{{ __('pages.vibe.label') }}</p>
<h2 class="font-display" ...>
    {{ __('pages.vibe.title_1') }}<br>
    <em style="color:#8C7E6A; font-style:italic;">{{ __('pages.vibe.title_2') }}</em>
</h2>

{{-- Replace $vibes PHP array with __() calls --}}
@php
$vibes = [
    ['kanji' => '集', 'title' => __('pages.vibe.gather_title'), 'text' => __('pages.vibe.gather_text')],
    ['kanji' => '語', 'title' => __('pages.vibe.share_title'),  'text' => __('pages.vibe.share_text')],
    ['kanji' => '創', 'title' => __('pages.vibe.create_title'), 'text' => __('pages.vibe.create_text')],
    ['kanji' => '発', 'title' => __('pages.vibe.discover_title'),'text' => __('pages.vibe.discover_text')],
    ['kanji' => '属', 'title' => __('pages.vibe.belong_title'), 'text' => __('pages.vibe.belong_text')],
    ['kanji' => '進', 'title' => __('pages.vibe.evolve_title'), 'text' => __('pages.vibe.evolve_text')],
];
@endphp

{{-- Gallery --}}
<p ...>{{ __('pages.gallery.bistro_label') }}</p>
<p ...>{{ __('pages.gallery.bistro_title') }}</p>

{{-- Reservation CTA --}}
<h2 class="font-display" ...>{{ __('pages.reservation_cta.title') }}</h2>
<p ...>{{ __('pages.reservation_cta.sub') }}</p>
<a href="{{ route('reservation') }}" class="sp-btn-primary">{{ __('pages.reservation_cta.btn') }}</a>
```

- [ ] **Step 7: Update reservation/index.blade.php**

Replace all hardcoded strings:

```html
{{-- Left panel --}}
<p ...>{{ __('pages.reservation_page.label') }}</p>
<h1 class="font-display ...">{{ __('pages.reservation_page.title') }}</h1>
<p ...>{{ __('pages.reservation_page.sub') }}</p>
<p ...>{{ __('pages.reservation_page.bistro_label') }}</p>
<p ...>{{ __('pages.reservation_page.bistro_hours') }}</p>
<p ...>{{ __('pages.reservation_page.bistro_addr') }}</p>
<a href="https://maps.app.goo.gl/U4srxx72PFPQruoP7" ...>{{ __('pages.reservation_page.maps_link') }}</a>

{{-- Success state --}}
<h3 class="font-display" ...>{{ __('pages.reservation_page.success_title') }}</h3>
<p ...>{{ __('pages.reservation_page.success_body') }}</p>
<p ...>{{ __('pages.reservation_page.success_email') }}</p>

{{-- Form labels --}}
<label ...>{{ __('pages.reservation_page.field_name') }}</label>
<label ...>{{ __('pages.reservation_page.field_phone') }}</label>
<label ...>{{ __('pages.reservation_page.field_date') }}</label>
<label ...>{{ __('pages.reservation_page.field_time') }}</label>
<option value="">{{ __('pages.reservation_page.time_select') }}</option>
<label ...>{{ __('pages.reservation_page.field_guests') }}</label>
<label ...>{{ __('pages.reservation_page.field_area') }}</label>
<option value="indoor">{{ __('pages.reservation_page.area_indoor') }}</option>
<option value="outdoor">{{ __('pages.reservation_page.area_outdoor') }}</option>
<option value="bar">{{ __('pages.reservation_page.area_bar') }}</option>
<label ...>{{ __('pages.reservation_page.field_note') }}</label>
<label ...>{{ __('pages.reservation_page.field_allergy') }}</label>
<label ...>{{ __('pages.reservation_page.field_birthday') }}</label>
<label ...>{{ __('pages.reservation_page.field_special') }}</label>
<button type="submit" ...>{{ __('pages.reservation_page.submit') }}</button>
```

Also update the JS `submitting` text in the submit handler at the bottom of the view:

```javascript
submitBtn.textContent = '{{ __("pages.reservation_page.submitting") }}';
```

- [ ] **Step 8: Update menu/index.blade.php, about.blade.php, events/index.blade.php**

Apply the same `__()` pattern for all static strings using the corresponding keys from `pages.php`. Menu page key pattern: `__('pages.menu_page.title')`, `__('pages.menu_page.vat_note')`, etc. Events page: `__('pages.events_page.title')`, `__('pages.events_page.empty')`, and for type badges: `__('pages.events_page.badge_' . $event->type)`. About page: `__('pages.about_page.hero_title')`, etc.

- [ ] **Step 9: Verify in browser**

Start dev server: `cd source && npm run dev` (separate terminal) + `php artisan serve`.
Visit `http://localhost:8000`. Click VI — nav items should swap instantly. Reload page — content should stay in VI (cookie persists). Click EN — switches back.

- [ ] **Step 10: Run all tests**

```
cd source && php artisan test --no-coverage
```

Expected: All green.

- [ ] **Step 11: Commit**

```
git add resources/views/components/ resources/views/client/
git commit -m "feat: migrate all client Blade templates to __() with data-i18n for instant toggle"
```

---

## Task 8: Admin CSS Design System

**Files:**
- Modify: `resources/css/app.css`

**Interfaces:**
- Produces: `body[data-panel="admin"]` CSS vars + `.adm-*` utility classes usable in all admin views

- [ ] **Step 1: Append admin design system to app.css**

Add at the end of `resources/css/app.css`:

```css
/* ══════════════════════════════════════════════════════════
   ADMIN DESIGN SYSTEM — scoped to body[data-panel="admin"]
══════════════════════════════════════════════════════════ */

body[data-panel="admin"] {
    --adm-bg:            #F8FAFC;
    --adm-surface:       #FFFFFF;
    --adm-border:        #E2E8F0;
    --adm-text:          #1E293B;
    --adm-muted:         #64748B;
    --adm-primary:       #2563EB;
    --adm-primary-light: #EFF6FF;
    --adm-primary-hover: #1D4ED8;
    --adm-success:       #10B981;
    --adm-warning:       #F59E0B;
    --adm-danger:        #EF4444;
    --adm-danger-light:  #FEF2F2;
    background-color: var(--adm-bg);
    color: var(--adm-text);
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

/* Layout */
.adm-layout        { display:flex; min-height:100vh; }
.adm-sidebar       { width:14rem; flex-shrink:0; background:var(--adm-surface);
                     border-right:1px solid var(--adm-border); display:flex;
                     flex-direction:column; }
.adm-content       { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.adm-main          { flex:1; overflow-y:auto; padding:1.5rem; background:var(--adm-bg); }

/* Topbar */
.adm-topbar        { height:3.5rem; background:var(--adm-surface);
                     border-bottom:1px solid var(--adm-border);
                     display:flex; align-items:center;
                     justify-content:space-between; padding:0 1.5rem;
                     flex-shrink:0; }
.adm-topbar-brand  { font-size:0.875rem; font-weight:600; color:var(--adm-primary); }
.adm-topbar-user   { font-size:0.8125rem; color:var(--adm-muted); }
.adm-breadcrumb    { font-size:0.8125rem; font-weight:500; color:var(--adm-text); }

/* Sidebar brand */
.adm-brand         { padding:1rem 1.25rem; border-bottom:1px solid var(--adm-border); }
.adm-brand-name    { font-size:0.9375rem; font-weight:700; color:var(--adm-primary); line-height:1.2; }
.adm-brand-sub     { font-size:0.6875rem; color:var(--adm-muted); margin-top:0.125rem; }

/* Sidebar nav */
.adm-nav           { flex:1; padding:0.75rem 0; overflow-y:auto; }
.adm-nav-section   { padding:0 0.75rem; margin-bottom:0.25rem; }
.adm-nav-link      { display:flex; align-items:center; gap:0.625rem;
                     padding:0.5rem 0.75rem; border-radius:0.375rem;
                     font-size:0.8125rem; font-weight:500;
                     color:#475569; text-decoration:none;
                     border-left:3px solid transparent;
                     transition:background-color 0.15s, color 0.15s; }
.adm-nav-link:hover { background:var(--adm-primary-light); color:var(--adm-primary); }
.adm-nav-link.active { background:var(--adm-primary-light); color:var(--adm-primary);
                        border-left-color:var(--adm-primary); }
.adm-nav-divider   { height:1px; background:var(--adm-border);
                     margin:0.5rem 0.75rem; }
.adm-nav-footer    { padding:0.75rem; border-top:1px solid var(--adm-border); }

/* Page header */
.adm-page-header   { margin-bottom:1.5rem; }
.adm-page-title    { font-size:1.375rem; font-weight:700; color:var(--adm-text); line-height:1.2; }
.adm-page-sub      { font-size:0.8125rem; color:var(--adm-muted); margin-top:0.25rem; }

/* Cards */
.adm-card          { background:var(--adm-surface); border:1px solid var(--adm-border);
                     border-radius:0.5rem; }
.adm-card-header   { display:flex; align-items:center; justify-content:space-between;
                     padding:1rem 1.25rem; border-bottom:1px solid var(--adm-border); }
.adm-card-title    { font-size:0.875rem; font-weight:600; color:var(--adm-text); }
.adm-card-body     { padding:1.25rem; }

/* Stats grid */
.adm-stats         { display:grid; grid-template-columns:repeat(auto-fit, minmax(10rem,1fr)); gap:1rem; margin-bottom:1.5rem; }
.adm-stat          { background:var(--adm-surface); border:1px solid var(--adm-border);
                     border-radius:0.5rem; padding:1.25rem; }
.adm-stat-label    { font-size:0.6875rem; font-weight:600; text-transform:uppercase;
                     letter-spacing:0.06em; color:var(--adm-muted); margin-bottom:0.5rem; }
.adm-stat-value    { font-size:2rem; font-weight:300; line-height:1; }

/* Tables */
.adm-table-wrap    { overflow-x:auto; }
.adm-table         { width:100%; border-collapse:collapse; }
.adm-th            { padding:0.625rem 1rem; text-align:left; font-size:0.6875rem;
                     font-weight:600; text-transform:uppercase; letter-spacing:0.06em;
                     color:var(--adm-muted); background:#F8FAFC;
                     border-bottom:1px solid var(--adm-border); white-space:nowrap; }
.adm-td            { padding:0.75rem 1rem; font-size:0.8125rem; color:var(--adm-text);
                     border-bottom:1px solid var(--adm-border); vertical-align:middle; }
.adm-tr:last-child .adm-td { border-bottom:none; }
.adm-tr:hover .adm-td      { background:#F8FAFC; }

/* Badges */
.adm-badge         { display:inline-flex; align-items:center; padding:0.2rem 0.625rem;
                     border-radius:9999px; font-size:0.6875rem; font-weight:600;
                     white-space:nowrap; }
.adm-badge-warn    { background:#FEF3C7; color:#92400E; }
.adm-badge-ok      { background:#D1FAE5; color:#065F46; }
.adm-badge-err     { background:#FEE2E2; color:#991B1B; }
.adm-badge-blue    { background:#DBEAFE; color:#1E40AF; }
.adm-badge-gray    { background:#F1F5F9; color:#475569; }

/* Buttons */
.adm-btn           { display:inline-flex; align-items:center; gap:0.375rem;
                     padding:0.5rem 1rem; border-radius:0.375rem;
                     font-size:0.8125rem; font-weight:500; cursor:pointer;
                     border:1px solid transparent; transition:all 0.15s;
                     text-decoration:none; white-space:nowrap; }
.adm-btn-primary   { background:var(--adm-primary); color:#fff; border-color:var(--adm-primary); }
.adm-btn-primary:hover { background:var(--adm-primary-hover); border-color:var(--adm-primary-hover); }
.adm-btn-ghost     { background:transparent; color:var(--adm-text);
                     border-color:var(--adm-border); }
.adm-btn-ghost:hover { background:#F1F5F9; }
.adm-btn-danger    { background:var(--adm-danger-light); color:var(--adm-danger);
                     border-color:#FECACA; }
.adm-btn-danger:hover { background:#FEE2E2; }
.adm-btn-sm        { padding:0.3125rem 0.625rem; font-size:0.75rem; }
.adm-btn-icon      { padding:0.375rem; line-height:1; }

/* Forms */
.adm-form-group    { margin-bottom:1rem; }
.adm-label         { display:block; font-size:0.8125rem; font-weight:500;
                     color:var(--adm-text); margin-bottom:0.375rem; }
.adm-input         { width:100%; padding:0.5rem 0.75rem; font-size:0.875rem;
                     color:var(--adm-text); background:var(--adm-surface);
                     border:1px solid var(--adm-border); border-radius:0.375rem;
                     transition:border-color 0.15s, box-shadow 0.15s; }
.adm-input:focus   { outline:none; border-color:var(--adm-primary);
                     box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.adm-textarea      { resize:vertical; min-height:5rem; }
.adm-select        { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748B' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
                     background-repeat:no-repeat; background-position:right 0.75rem center;
                     padding-right:2.25rem; }
.adm-checkbox      { width:1rem; height:1rem; border-radius:0.25rem;
                     border:1px solid var(--adm-border); accent-color:var(--adm-primary); }
.adm-error         { font-size:0.75rem; color:var(--adm-danger); margin-top:0.25rem; }

/* Modal */
.adm-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.45);
                      display:flex; align-items:center; justify-content:center;
                      z-index:50; padding:1rem; }
.adm-modal         { background:var(--adm-surface); border-radius:0.5rem;
                     border:1px solid var(--adm-border); width:100%; max-width:32rem;
                     max-height:90vh; overflow-y:auto; }
.adm-modal-header  { display:flex; align-items:center; justify-content:space-between;
                     padding:1rem 1.25rem; border-bottom:1px solid var(--adm-border); }
.adm-modal-title   { font-size:1rem; font-weight:600; color:var(--adm-text); }
.adm-modal-body    { padding:1.25rem; }
.adm-modal-footer  { display:flex; gap:0.75rem; justify-content:flex-end;
                     padding:1rem 1.25rem; border-top:1px solid var(--adm-border); }

/* Filters bar */
.adm-filters       { display:flex; gap:0.75rem; flex-wrap:wrap;
                     align-items:center; margin-bottom:1rem; }

/* Flash messages */
.adm-flash-ok      { background:#D1FAE5; border:1px solid #A7F3D0; color:#065F46;
                     padding:0.75rem 1rem; border-radius:0.375rem;
                     font-size:0.8125rem; margin-bottom:1rem; }
.adm-flash-err     { background:#FEE2E2; border:1px solid #FECACA; color:#991B1B;
                     padding:0.75rem 1rem; border-radius:0.375rem;
                     font-size:0.8125rem; margin-bottom:1rem; }

/* Thumbnail */
.adm-thumb         { width:2.5rem; height:2.5rem; object-fit:cover; border-radius:0.25rem;
                     border:1px solid var(--adm-border); }
```

- [ ] **Step 2: Run Vite build to verify no CSS errors**

```
cd source && npm run build
```

Expected: Build completes without errors.

- [ ] **Step 3: Commit**

```
git add resources/css/app.css
git commit -m "feat: add admin design system CSS vars and .adm-* component classes"
```

---

## Task 9: Admin Layout Redesign

**Files:**
- Rewrite: `resources/views/layouts/admin.blade.php`
- Rewrite: `resources/views/components/admin/sidebar.blade.php`
- Rewrite: `resources/views/components/admin/topbar.blade.php`

- [ ] **Step 1: Rewrite admin.blade.php**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Sapiens House</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-panel="admin">

<div class="adm-layout">
    @include('components.admin.sidebar')
    <div class="adm-content">
        @include('components.admin.topbar')
        <main class="adm-main">
            @if(session('success'))
                <div class="adm-flash-ok">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="adm-flash-err">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
```

- [ ] **Step 2: Rewrite sidebar.blade.php**

```html
@php $route = request()->route()?->getName() ?? ''; @endphp

<aside class="adm-sidebar">
    <div class="adm-brand">
        <p class="adm-brand-name">Sapiens House</p>
        <p class="adm-brand-sub">Admin Panel</p>
    </div>

    <nav class="adm-nav">
        <div class="adm-nav-section">
            <a href="{{ route('admin.dashboard') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.reservations.index') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.reservations') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Reservations
            </a>
            <a href="{{ route('admin.menu-items.index') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.menu-items') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
                Menu Items
            </a>
            <a href="{{ route('admin.events.index') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.events') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Events
            </a>
        </div>

        <div class="adm-nav-divider"></div>

        <div class="adm-nav-section">
            <a href="{{ route('admin.translations.index') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.translations') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 5h12M9 3v2m4.5 12.5L12 16l-4 5M5 8l-2 13h7M16 3l5 13h-2M16 3l-5 13"/>
                </svg>
                Translations
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="adm-nav-link {{ str_starts_with($route, 'admin.settings') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
                Settings
            </a>
        </div>

        <div class="adm-nav-divider"></div>

        <div class="adm-nav-section">
            <a href="{{ route('home') }}" class="adm-nav-link" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                    <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                View Website
            </a>
        </div>
    </nav>

    <div class="adm-nav-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="adm-nav-link" style="width:100%; background:none; border:none; cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
```

- [ ] **Step 3: Rewrite topbar.blade.php**

```html
<header class="adm-topbar">
    <p class="adm-breadcrumb">@yield('breadcrumb', 'Dashboard')</p>
    <div style="display:flex; align-items:center; gap:1rem;">
        <span class="adm-topbar-user">{{ auth()->user()?->name ?? 'Admin' }}</span>
    </div>
</header>
```

- [ ] **Step 4: Add missing admin routes to routes/web.php**

Ensure these routes exist (add if missing):

```php
Route::get('/admin/translations',        [Admin\TranslationController::class, 'index'])->name('admin.translations.index');
Route::post('/admin/translations',       [Admin\TranslationController::class, 'update'])->name('admin.translations.update');
Route::get('/admin/settings',            [Admin\SettingController::class, 'index'])->name('admin.settings.index');
Route::post('/admin/settings',           [Admin\SettingController::class, 'update'])->name('admin.settings.update');
```

- [ ] **Step 5: Verify admin panel loads without errors**

```
cd source && php artisan serve
```

Visit `http://localhost:8000/admin/dashboard`. Confirm white/blue layout renders, sidebar visible, no PHP errors.

- [ ] **Step 6: Commit**

```
git add resources/views/layouts/admin.blade.php \
        resources/views/components/admin/sidebar.blade.php \
        resources/views/components/admin/topbar.blade.php \
        routes/web.php
git commit -m "feat: complete admin panel redesign with blue/white layout and new sidebar"
```

---

## Task 10: Admin Translations Page

**Files:**
- Create: `app/Http/Controllers/Admin/TranslationController.php`
- Create: `tests/Feature/Admin/TranslationControllerTest.php`
- Create: `resources/views/admin/translations/index.blade.php`

**Interfaces:**
- Consumes: `TranslationString` model; `TranslationFileGenerator::regenerate()`
- Produces: `GET /admin/translations?group=ui` shows key table; `POST /admin/translations` saves + regenerates

- [ ] **Step 1: Write failing tests**

```php
// tests/Feature/Admin/TranslationControllerTest.php
<?php
namespace Tests\Feature\Admin;

use App\Models\TranslationString;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create());
    }

    public function test_index_shows_translation_keys(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        $this->actingAsAdmin()
             ->get('/admin/translations?group=ui')
             ->assertOk()
             ->assertSee('nav.home')
             ->assertSee('Home')
             ->assertSee('Trang chủ');
    }

    public function test_update_saves_translations_and_regenerates_files(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        $this->actingAsAdmin()->post('/admin/translations', [
            'group' => 'ui',
            'translations' => [
                'nav.home' => ['en' => 'Home Updated', 'vi' => 'Trang chủ mới'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('translation_strings', [
            'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home Updated',
        ]);

        $file = lang_path('en/ui.php');
        $this->assertFileExists($file);
        $result = require $file;
        $this->assertSame('Home Updated', $result['nav']['home']);
    }
}
```

- [ ] **Step 2: Run — expect FAIL**

```
cd source && php artisan test --no-coverage --filter TranslationControllerTest
```

Expected: FAIL — class not found.

- [ ] **Step 3: Create TranslationController**

```php
// app/Http/Controllers/Admin/TranslationController.php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TranslationString;
use App\Services\TranslationFileGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TranslationController extends Controller
{
    public function index(Request $request): View
    {
        $group  = $request->get('group', 'ui');
        $groups = ['ui', 'pages', 'emails'];

        $keys = TranslationString::where('group', $group)
            ->orderBy('key')
            ->get()
            ->groupBy('key')
            ->map(fn ($rows) => $rows->keyBy('locale'));

        $locales = TranslationString::where('group', $group)
            ->distinct()
            ->pluck('locale')
            ->toArray();

        return view('admin.translations.index', compact('keys', 'group', 'groups', 'locales'));
    }

    public function update(Request $request, TranslationFileGenerator $generator): RedirectResponse
    {
        $group   = $request->input('group', 'ui');
        $entries = $request->input('translations', []);

        foreach ($entries as $key => $locales) {
            foreach ($locales as $locale => $value) {
                TranslationString::updateOrCreate(
                    ['group' => $group, 'key' => $key, 'locale' => $locale],
                    ['value' => $value ?? '']
                );
            }
        }

        $generator->regenerate($group);

        return redirect()->route('admin.translations.index', ['group' => $group])
            ->with('success', 'Files regenerated. Changes are live.');
    }
}
```

- [ ] **Step 4: Create translations/index.blade.php**

```html
@extends('layouts.admin')

@section('title', 'Translations')
@section('breadcrumb', 'Translations')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Translations</h1>
    <p class="adm-page-sub">Edit UI and page content translations. Click "Save" to regenerate lang files.</p>
</div>

{{-- Group tabs --}}
<div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; border-bottom:1px solid var(--adm-border); padding-bottom:0;">
    @foreach($groups as $g)
        <a href="{{ route('admin.translations.index', ['group' => $g]) }}"
           style="padding:0.5rem 1rem; font-size:0.8125rem; font-weight:500; text-decoration:none; border-bottom:2px solid {{ $g === $group ? 'var(--adm-primary)' : 'transparent' }}; color:{{ $g === $group ? 'var(--adm-primary)' : 'var(--adm-muted)' }}; margin-bottom:-1px;">
            {{ ucfirst($g) }}
        </a>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.translations.update') }}">
    @csrf
    <input type="hidden" name="group" value="{{ $group }}">

    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">{{ ucfirst($group) }} strings — {{ count($keys) }} keys</span>
            <button type="submit" class="adm-btn adm-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Save & Generate Files
            </button>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th class="adm-th" style="width:28%;">Key</th>
                        @foreach($locales as $locale)
                            <th class="adm-th">{{ strtoupper($locale) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($keys as $key => $byLocale)
                        <tr class="adm-tr">
                            <td class="adm-td">
                                <code style="font-size:0.75rem; color:var(--adm-muted); background:#F1F5F9; padding:0.125rem 0.375rem; border-radius:0.25rem;">{{ $key }}</code>
                            </td>
                            @foreach($locales as $locale)
                                <td class="adm-td">
                                    @php $val = $byLocale[$locale]->value ?? ''; @endphp
                                    @if(strlen($val) > 80)
                                        <textarea name="translations[{{ $key }}][{{ $locale }}]"
                                                  class="adm-input adm-textarea"
                                                  rows="2"
                                                  style="font-size:0.8125rem;">{{ $val }}</textarea>
                                    @else
                                        <input type="text"
                                               name="translations[{{ $key }}][{{ $locale }}]"
                                               value="{{ $val }}"
                                               class="adm-input"
                                               style="font-size:0.8125rem;">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:1rem 1.25rem; border-top:1px solid var(--adm-border); display:flex; justify-content:flex-end;">
            <button type="submit" class="adm-btn adm-btn-primary">Save & Generate Files</button>
        </div>
    </div>
</form>

@endsection
```

- [ ] **Step 5: Run tests — expect PASS**

```
cd source && php artisan test --no-coverage --filter TranslationControllerTest
```

Expected: 2 tests, 2 passed.

- [ ] **Step 6: Commit**

```
git add app/Http/Controllers/Admin/TranslationController.php \
        resources/views/admin/translations/index.blade.php \
        tests/Feature/Admin/TranslationControllerTest.php
git commit -m "feat: add admin Translations page with DB-backed editing and file generation"
```

---

## Task 11: Settings Page + Rebuild Admin Content Pages

**Files:**
- Create: `app/Http/Controllers/Admin/SettingController.php`
- Create: `resources/views/admin/settings/index.blade.php`
- Modify: `resources/views/admin/dashboard/index.blade.php`
- Modify: `resources/views/admin/reservations/index.blade.php`
- Modify: `resources/views/admin/menu-items/index.blade.php`
- Modify: `resources/views/admin/events/index.blade.php`

- [ ] **Step 1: Create SettingController**

```php
// app/Http/Controllers/Admin/SettingController.php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaultLocale = Setting::get('default_locale', 'en');
        return view('admin.settings.index', compact('defaultLocale'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_locale' => 'required|in:en,vi',
        ]);

        Setting::set('default_locale', $validated['default_locale']);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved.');
    }
}
```

- [ ] **Step 2: Create settings/index.blade.php**

```html
@extends('layouts.admin')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Settings</h1>
</div>

<div class="adm-card" style="max-width:32rem;">
    <div class="adm-card-header">
        <span class="adm-card-title">Language</span>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <div class="adm-form-group">
                <label class="adm-label" for="default_locale">Default Language</label>
                <select name="default_locale" id="default_locale" class="adm-input adm-select">
                    <option value="en" {{ $defaultLocale === 'en' ? 'selected' : '' }}>English (EN)</option>
                    <option value="vi" {{ $defaultLocale === 'vi' ? 'selected' : '' }}>Tiếng Việt (VI)</option>
                </select>
                <p style="font-size:0.75rem; color:var(--adm-muted); margin-top:0.375rem;">
                    Shown to visitors who have not set a language preference.
                </p>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">Save Settings</button>
        </form>
    </div>
</div>

@endsection
```

- [ ] **Step 3: Rebuild dashboard/index.blade.php**

Replace all inline `style="background-color:#242420; ..."` with `adm-*` classes. Full rewrite:

```html
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Dashboard</h1>
    <p class="adm-page-sub">{{ now()->format('l, d F Y') }}</p>
</div>

<div class="adm-stats">
    <div class="adm-stat">
        <p class="adm-stat-label">Today Total</p>
        <p class="adm-stat-value" style="color:var(--adm-text);">{{ $stats['today_total'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Pending</p>
        <p class="adm-stat-value" style="color:var(--adm-warning);">{{ $stats['today_pending'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Confirmed</p>
        <p class="adm-stat-value" style="color:var(--adm-success);">{{ $stats['today_confirmed'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Cancelled</p>
        <p class="adm-stat-value" style="color:var(--adm-danger);">{{ $stats['today_cancelled'] }}</p>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <span class="adm-card-title">Recent Reservations</span>
        <a href="{{ route('admin.reservations.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">View All →</a>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th class="adm-th">Code</th>
                    <th class="adm-th">Name</th>
                    <th class="adm-th">Date</th>
                    <th class="adm-th">Time</th>
                    <th class="adm-th">Guests</th>
                    <th class="adm-th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReservations as $r)
                <tr class="adm-tr">
                    <td class="adm-td"><code style="font-size:0.75rem;">{{ $r->code }}</code></td>
                    <td class="adm-td">{{ $r->full_name }}</td>
                    <td class="adm-td">{{ $r->reservation_date->format('d M Y') }}</td>
                    <td class="adm-td">{{ $r->reservation_time }}</td>
                    <td class="adm-td">{{ $r->guest_count }}</td>
                    <td class="adm-td">
                        @if($r->status === 'pending')
                            <span class="adm-badge adm-badge-warn">Pending</span>
                        @elseif($r->status === 'confirmed')
                            <span class="adm-badge adm-badge-ok">Confirmed</span>
                        @else
                            <span class="adm-badge adm-badge-err">Cancelled</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td class="adm-td" colspan="6" style="text-align:center; color:var(--adm-muted);">No reservations today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
```

- [ ] **Step 4: Rebuild reservations/index.blade.php**

Apply the same `adm-*` pattern. Replace all dark `style="background-color:#242420"` etc. with classes. Status badges use `.adm-badge-warn` / `.adm-badge-ok` / `.adm-badge-err`. Action buttons use `.adm-btn .adm-btn-ghost .adm-btn-sm` for Confirm and `.adm-btn .adm-btn-danger .adm-btn-sm` for Cancel. Table uses `.adm-table`, `.adm-th`, `.adm-td`. Filter form inputs use `.adm-input` and `.adm-select`.

- [ ] **Step 5: Rebuild menu-items/index.blade.php and partials/form.blade.php**

Same pattern. Table uses `adm-*` classes. Create/Edit modal backdrop uses `.adm-modal-backdrop`, modal body `.adm-modal`, header `.adm-modal-header`, form groups `.adm-form-group`, inputs `.adm-input`, submit `.adm-btn .adm-btn-primary`. Image thumbnail uses `.adm-thumb`. Featured/Active checkboxes use `.adm-checkbox`.

- [ ] **Step 6: Rebuild events/index.blade.php and partials/form.blade.php**

Same pattern as menu-items. Type badge uses `.adm-badge .adm-badge-blue`. Published toggle uses inline form with `.adm-btn .adm-btn-ghost .adm-btn-sm`.

- [ ] **Step 7: Run all tests**

```
cd source && php artisan test --no-coverage
```

Expected: All green.

- [ ] **Step 8: Smoke test admin in browser**

Visit `/admin/dashboard`, `/admin/reservations`, `/admin/menu-items`, `/admin/events`, `/admin/translations`, `/admin/settings`. Confirm: white background, blue sidebar active states, tables render correctly, modals open on Menu Items and Events.

- [ ] **Step 9: Commit**

```
git add app/Http/Controllers/Admin/SettingController.php \
        resources/views/admin/settings/ \
        resources/views/admin/dashboard/ \
        resources/views/admin/reservations/ \
        resources/views/admin/menu-items/ \
        resources/views/admin/events/
git commit -m "feat: add Settings page and rebuild all admin content pages with adm-* design system"
```

---

## Self-Review

**Spec coverage check:**

| Spec requirement | Task |
|---|---|
| `translation_strings` table with `(group, key, locale, value)` | Task 1 |
| `settings` table for `default_locale` | Task 1 |
| 6 lang files (`en`/`vi` × `ui`/`pages`/`emails`) | Task 2 |
| Seeder reads files → populates DB | Task 3 |
| `TranslationFileGenerator::regenerate()` writes nested arrays | Task 4 |
| `LocaleMiddleware` reads cookie → `App::setLocale()` | Task 5 |
| `POST /locale` sets cookie | Task 5 |
| `GET /translations/{locale}` returns flat JSON for `ui` group | Task 6 |
| JS dictionary-fetch with `sessionStorage` cache | Task 6 |
| All client Blade templates use `__()` + `data-i18n` on nav/buttons | Task 7 |
| Admin CSS vars `--adm-*` + `.adm-*` classes | Task 8 |
| Admin layout blue/white, sidebar, topbar | Task 9 |
| Admin Translations page with inline editing + file generation | Task 10 |
| Admin Settings page with `default_locale` | Task 11 |
| All admin content pages rebuilt with `adm-*` classes | Task 11 |

**No placeholders detected.** All steps contain actual code.

**Type consistency:** `TranslationFileGenerator::regenerate(string $group)` called consistently in Tasks 4, 10. `Setting::get()`/`Setting::set()` consistent in Tasks 1, 5, 11.
