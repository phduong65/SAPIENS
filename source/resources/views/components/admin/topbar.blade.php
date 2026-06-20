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
