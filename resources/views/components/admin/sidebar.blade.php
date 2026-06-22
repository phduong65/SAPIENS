@php
    $route = request()->route()?->getName() ?? '';
    $pendingCount = \App\Models\Reservation::where('status', 'pending')->count();
@endphp

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
               class="adm-nav-link {{ str_starts_with($route, 'admin.reservations') ? 'active' : '' }}"
               style="display:flex; align-items:center; justify-content:space-between;">
                <span style="display:flex; align-items:center; gap:10px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Reservations
                </span>
                @if($pendingCount > 0)
                <span style="background:#3B82F6; color:#fff; font-size:10px; font-weight:700;
                              border-radius:10px; padding:2px 7px; min-width:20px; text-align:center;
                              line-height:1.5; flex-shrink:0;">
                    {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                </span>
                @endif
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
