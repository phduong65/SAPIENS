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
                <p class="adm-error" style="color:var(--adm-muted); margin-top:0.375rem;">
                    Shown to visitors who have not set a language preference.
                </p>
            </div>
            <button type="submit" class="adm-btn adm-btn-primary">Save Settings</button>
        </form>
    </div>
</div>

@endsection
