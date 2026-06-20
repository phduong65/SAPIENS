<header class="adm-topbar">
    <p class="adm-breadcrumb">@yield('breadcrumb', 'Dashboard')</p>
    <div style="display:flex; align-items:center; gap:1rem;">
        <span class="adm-topbar-user">{{ auth()->user()?->name ?? 'Admin' }}</span>
    </div>
</header>
