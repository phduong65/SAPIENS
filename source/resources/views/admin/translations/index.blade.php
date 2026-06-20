@extends('layouts.admin')

@section('title', 'Translations')
@section('breadcrumb', 'Translations')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Translations</h1>
    <p class="adm-page-sub">Edit UI and page content translations. Save to regenerate language files immediately.</p>
</div>

{{-- Group tabs --}}
<div style="display:flex; gap:0; margin-bottom:1.5rem; border-bottom:1px solid var(--adm-border);">
    @foreach($groups as $g)
        <a href="{{ route('admin.translations.index', ['group' => $g]) }}"
           style="display:inline-block; padding:0.5rem 1.25rem; font-size:0.8125rem; font-weight:500;
                  text-decoration:none; border-bottom:2px solid {{ $g === $group ? 'var(--adm-primary)' : 'transparent' }};
                  color:{{ $g === $group ? 'var(--adm-primary)' : 'var(--adm-muted)' }};
                  margin-bottom:-1px; transition:color 0.15s;">
            {{ ucfirst($g) }}
        </a>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.translations.update') }}">
    @csrf
    <input type="hidden" name="group" value="{{ $group }}">

    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">
                {{ ucfirst($group) }} strings
                <span style="font-weight:400; color:var(--adm-muted);">— {{ count($keys) }} {{ Str::plural('key', count($keys)) }}</span>
            </span>
            <button type="submit" class="adm-btn adm-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save &amp; Generate Files
            </button>
        </div>

        @if(count($keys) > 0)
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-th" style="width:28%; min-width:180px;">Key</th>
                            @foreach($locales as $locale)
                                <th class="adm-th">{{ strtoupper($locale) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keys as $key => $byLocale)
                            <tr class="adm-tr">
                                <td class="adm-td" style="vertical-align:top; padding-top:0.875rem;">
                                    <code style="font-size:0.6875rem; color:var(--adm-muted);
                                                 background:#F1F5F9; padding:0.1875rem 0.4375rem;
                                                 border-radius:0.25rem; font-family:monospace;
                                                 white-space:nowrap; display:inline-block;">{{ $key }}</code>
                                </td>
                                @foreach($locales as $locale)
                                    <td class="adm-td" style="vertical-align:top;">
                                        @php $val = $byLocale->get($locale)?->value ?? ''; @endphp
                                        @if(strlen($val) > 80)
                                            <textarea name="translations[{{ $key }}][{{ $locale }}]"
                                                      class="adm-input adm-textarea"
                                                      rows="2"
                                                      style="font-size:0.8125rem; min-height:auto;">{{ $val }}</textarea>
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
                <button type="submit" class="adm-btn adm-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save &amp; Generate Files
                </button>
            </div>
        @else
            <div style="padding:3rem 1.25rem; text-align:center; color:var(--adm-muted);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                     style="margin:0 auto 0.75rem; display:block; opacity:0.4;">
                    <path d="M3 5h12M9 3v2m4.5 12.5L12 16l-4 5M5 8l-2 13h7M16 3l5 13h-2M16 3l-5 13"/>
                </svg>
                <p style="font-size:0.875rem;">No translation keys found for the <strong>{{ $group }}</strong> group.</p>
                <p style="font-size:0.8125rem; margin-top:0.375rem;">Seed the database or switch to another group tab above.</p>
            </div>
        @endif
    </div>
</form>

@endsection
