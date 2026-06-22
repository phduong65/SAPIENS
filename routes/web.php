<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Locale Switching ────────────────────────────────────────────────────────
Route::post('/locale', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/translations/{locale}', [\App\Http\Controllers\LocaleController::class, 'dictionary'])->name('translations.dictionary');

// ─── Client Routes ──────────────────────────────────────────────────────────
Route::get('/', [Client\HomeController::class, 'index'])->name('home');
Route::get('/about', [Client\AboutController::class, 'index'])->name('about');
Route::get('/menu', [Client\MenuController::class, 'index'])->name('menu');
Route::get('/community', [Client\EventController::class, 'index'])->name('community');
Route::get('/reservation', [Client\ReservationController::class, 'index'])->name('reservation');
Route::post('/reservation', [Client\ReservationController::class, 'store'])->name('reservation.store');
Route::get('/reservation/blocked-slots', [Client\ReservationController::class, 'blockedSlots'])->name('reservation.blocked-slots');

// ─── Auth Routes ─────────────────────────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/admin/dashboard');
    }
    return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->withInput();
})->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Reservations
    Route::get('/reservations', [Admin\ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{reservation}/confirm', [Admin\ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('/reservations/{reservation}/cancel', [Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{reservation}/send-deposit', [Admin\ReservationController::class, 'sendDeposit'])->name('reservations.send-deposit');
    Route::post('/reservations/{reservation}/resend-confirmation', [Admin\ReservationController::class, 'resendConfirmation'])->name('reservations.resend-confirmation');

    // Blocked Slots
    Route::post('/blocked-slots', [Admin\BlockedSlotController::class, 'store'])->name('blocked-slots.store');
    Route::delete('/blocked-slots/{blockedSlot}', [Admin\BlockedSlotController::class, 'destroy'])->name('blocked-slots.destroy');

    // Menu Items
    Route::resource('menu-items', Admin\MenuItemController::class)->except(['create', 'edit', 'show']);

    // Events
    Route::resource('events', Admin\EventController::class)->except(['create', 'edit', 'show']);

    // Translations (controller created in Task 10)
    Route::get('/translations', [Admin\TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [Admin\TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/add-key', [Admin\TranslationController::class, 'addKey'])->name('translations.add-key');

    // Settings (controller created in Task 11)
    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});
