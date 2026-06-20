<!DOCTYPE html>
<html lang="vi" class="admin-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Sapiens House</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    @include('components.admin.sidebar')

    {{-- Main --}}
    <div class="flex flex-col flex-1 overflow-hidden">
        @include('components.admin.topbar')

        <main class="admin-main flex-1 overflow-y-auto p-6">
            @include('components.flash-messages')
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
