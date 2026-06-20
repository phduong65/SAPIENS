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
