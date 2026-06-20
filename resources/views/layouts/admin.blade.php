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
