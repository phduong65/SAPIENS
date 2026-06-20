@extends('layouts.client')

@section('title', 'Sapiens House – Eatery & Drinks | A Modern Cave for Modern Humans')
@section('description',
    'Fusion Japanese Eatery & Bistro Bar. Working Space 11:00–17:00 · Bistro Bar 18:00–01:00. Tầng
    4, 44 Nguyễn Huệ, Quận 1.')

@section('content')

    {{-- ═══════════════════════════════════════════════════════════
     HERO SECTION — Canvas Particles + GSAP Text Reveal
═══════════════════════════════════════════════════════════ --}}
    <section id="sp-hero" class="relative flex items-center justify-center" style="min-height:100vh; overflow:hidden;">

        {{-- Video background --}}
        <video class="absolute inset-0 w-full h-full" style="z-index:0; object-fit:cover; opacity:0.45; pointer-events:none;"
            autoplay muted loop playsinline preload="auto">
            <source src="{{ asset('images/sapiens/7956140970631.mp4') }}" type="video/mp4">
        </video>

        {{-- Dark overlay gradient on top of video --}}
        <div class="absolute inset-0"
            style="z-index:1;
        background:
            radial-gradient(ellipse 90% 70% at 50% 20%, rgba(184,146,90,0.07) 0%, transparent 65%),
            radial-gradient(ellipse 50% 60% at 15% 80%, rgba(184,146,90,0.04) 0%, transparent 50%),
            linear-gradient(180deg, rgba(5,5,3,0.72) 0%, rgba(10,10,8,0.55) 35%, rgba(15,15,13,0.60) 70%, rgba(10,10,8,0.78) 100%);
    ">
        </div>

        {{-- Canvas: particles + smoke --}}
        <canvas id="sp-hero-canvas" class="absolute inset-0 w-full h-full" style="z-index:2; pointer-events:none;"></canvas>

        {{-- Warm spotlight (breathing) --}}
        <div id="sp-spotlight" class="absolute"
            style="
        z-index:3; pointer-events:none;
        width:800px; height:800px;
        top:50%; left:50%; transform:translate(-50%,-60%);
        background:radial-gradient(ellipse at center, rgba(184,146,90,0.055) 0%, transparent 65%);
        border-radius:50%;
        animation: breathe 6s ease-in-out infinite;
    ">
        </div>

        {{-- Grain overlay --}}
        <div class="absolute inset-0 grain-overlay" style="z-index:4; pointer-events:none;"></div>

        {{-- Hero Content --}}
        <div class="relative text-center px-6 max-w-5xl mx-auto" style="z-index:10;">

            {{-- Label --}}
            <p id="sp-hero-label"
                style="
            font-family:'DM Sans', sans-serif;
            color:#8C7E6A; font-size:0.65rem; letter-spacing:0.35em;
            text-transform:uppercase; margin-bottom:2.5rem;
            opacity:0; transform:translateY(20px);
        ">
                Tầng 4 · 44 Nguyễn Huệ · Quận 1 · TP.HCM
            </p>

            {{-- Logo PNG with texture (hero centrepiece) --}}
            <div id="sp-hero-logo-wrap" style="opacity:0; transform:translateY(30px); margin-bottom:2rem;">
                <img src="{{ asset('images/sapiens/SAPIENS HOUSE_LOGO_WITH TEXTURE.png') }}" alt="Sapiens House"
                    id="sp-hero-logo-img"
                    style="width:min(320px,60vw); height:auto; object-fit:contain; margin:0 auto; display:block;"
                    loading="eager">
            </div>

            {{-- Tagline --}}
            <p id="sp-hero-tagline"
                style="
            font-family:'Cormorant Garamond', Georgia, serif;
            font-style:italic; font-weight:300;
            font-size:clamp(1rem, 2.5vw, 1.4rem);
            color:#8C7E6A; letter-spacing:0.08em;
            margin:0.5rem 0 2.5rem;
            opacity:0; transform:translateY(20px);
        ">
                {{ __('pages.hero.tagline') }}
            </p>

            {{-- CTAs --}}
            <div id="sp-hero-ctas"
                style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; opacity:0; transform:translateY(20px);">
                <a href="{{ route('reservation') }}" class="sp-btn-primary">
                    <span data-i18n="btn.book_table">{{ __('ui.btn.book_table') }}</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="{{ route('menu') }}" class="sp-btn-ghost"
                   data-i18n="btn.explore_menu">{{ __('ui.btn.explore_menu') }}</a>
            </div>
        </div>

        {{-- Scroll indicator --}}
        {{-- <div id="sp-scroll-hint" class="absolute bottom-10 left-1/2" style="transform:translateX(-50%); z-index:10; opacity:0;">
        <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
            <span style="font-family:'DM Sans',sans-serif; color:#8C7E6A; font-size:0.6rem; letter-spacing:0.25em; text-transform:uppercase;">Scroll</span>
            <div class="sp-scroll-line"></div>
        </div>
    </div> --}}
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     INTRODUCTION — Cinematic Line-by-Line Reveal
═══════════════════════════════════════════════════════════ --}}
    <section class="py-32 lg:py-40 px-6" style="background:#0A0A08;">
        <div class="max-w-5xl mx-auto">

            <div class="sp-intro-grid">
                {{-- Left: Index --}}
                <div data-reveal style="padding-top:0.5rem;">
                    <p
                        style="color:#3A3A35; font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; writing-mode:vertical-rl; transform:rotate(180deg); display:inline-block;">
                        {{ __('pages.intro.index') }}</p>
                </div>

                {{-- Right: Text --}}
                <div data-lines>
                    <p class="font-display sp-intro-label" data-line
                        style="color:#B8925A; font-size:1rem; letter-spacing:0.3em; text-transform:uppercase; margin-bottom:2rem;">
                        {{ __('pages.intro.label') }}
                    </p>

                    <h2 class="font-display"
                        style="font-size:clamp(2.2rem, 5vw, 3.8rem); color:#E5D9C8; line-height:1.08; margin-bottom:2.5rem;">
                        <span data-line style="display:block;">{{ __('pages.intro.line_1') }}</span>
                        <span data-line style="display:block; color:#8C7E6A;">{{ __('pages.intro.line_2') }}</span>
                    </h2>

                    <div class="sp-divider"
                        style="width:80px; height:1px; background:linear-gradient(to right, #B8925A, transparent); margin-bottom:2.5rem; transform-origin:left;">
                    </div>

                    <div style="max-width:560px;">
                        <p data-line style="color:#8C7E6A; font-size:1rem; line-height:1.95; margin-bottom:1.2rem;">
                            {{ __('pages.intro.body_1') }}
                        </p>
                        <p data-line style="color:#8C7E6A; font-size:1rem; line-height:1.95; margin-bottom:1.2rem;">
                            {{ __('pages.intro.body_2') }}
                        </p>
                        <p data-line style="color:#8C7E6A; font-size:1rem; line-height:1.95;">
                            {{ __('pages.intro.body_3') }}
                        </p>
                    </div>

                    <div style="margin-top:3rem;" data-reveal>
                        <a href="{{ route('about') }}" class="sp-btn-ghost">{{ __('pages.intro.cta') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     EXPERIENCE — Split Panels with Cinematic Hover
═══════════════════════════════════════════════════════════ --}}
    <section style="background:#050503;">
        <div style="display:grid; grid-template-columns:1fr 1fr;">

            {{-- Working Space --}}
            <div class="sp-exp-panel" id="sp-exp-day" data-reveal>
                <div class="sp-exp-bg"
                    style="
                background-image:
                    linear-gradient(160deg, rgba(10,10,8,0.62) 0%, rgba(10,10,8,0.38) 55%, rgba(10,10,8,0.55) 100%),
                    url('{{ asset('images/sapiens/z7956140941279_4e54adbc05cc4398df4918c7719f59ee.jpg') }}');
                background-size: cover;
                background-position: center;
            ">
                </div>

                {{-- SVG art: Japanese minimalist day scene --}}
                <div class="sp-exp-art">
                    <svg viewBox="0 0 400 360" fill="none" xmlns="http://www.w3.org/2000/svg"
                        preserveAspectRatio="xMidYMid meet">
                        {{-- Shoji screen background panels --}}
                        <rect x="20" y="20" width="360" height="260" rx="2" stroke="#B8925A" stroke-width="0.4"
                            opacity="0.15" />
                        <line x1="20" y1="107" x2="380" y2="107" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.12" />
                        <line x1="20" y1="193" x2="380" y2="193" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.12" />
                        <line x1="113" y1="20" x2="113" y2="280" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.12" />
                        <line x1="207" y1="20" x2="207" y2="280" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.12" />
                        <line x1="300" y1="20" x2="300" y2="280" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.12" />
                        {{-- Low table --}}
                        <rect x="100" y="230" width="200" height="6" rx="1" fill="#B8925A"
                            opacity="0.18" />
                        <rect x="108" y="236" width="4" height="30" rx="1" fill="#B8925A"
                            opacity="0.12" />
                        <rect x="288" y="236" width="4" height="30" rx="1" fill="#B8925A"
                            opacity="0.12" />
                        {{-- Coffee cup --}}
                        <ellipse cx="185" cy="225" rx="22" ry="6" fill="#B8925A"
                            opacity="0.12" />
                        <path d="M163 210 Q163 225 185 225 Q207 225 207 210 Q207 200 185 200 Q163 200 163 210Z"
                            fill="#B8925A" opacity="0.1" />
                        <ellipse cx="185" cy="200" rx="22" ry="5" stroke="#B8925A"
                            stroke-width="0.5" opacity="0.25" fill="none" />
                        {{-- Steam lines --}}
                        <path d="M178 195 Q176 188 179 182" stroke="#B8925A" stroke-width="0.8" opacity="0.2"
                            stroke-linecap="round" fill="none" />
                        <path d="M185 193 Q183 185 186 178" stroke="#B8925A" stroke-width="0.8" opacity="0.2"
                            stroke-linecap="round" fill="none" />
                        <path d="M192 195 Q190 187 193 181" stroke="#B8925A" stroke-width="0.8" opacity="0.2"
                            stroke-linecap="round" fill="none" />
                        {{-- Laptop outline --}}
                        <rect x="215" y="205" width="70" height="44" rx="2" stroke="#B8925A"
                            stroke-width="0.5" opacity="0.2" fill="none" />
                        <rect x="208" y="249" width="84" height="4" rx="2" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.15" fill="none" />
                        {{-- Light beam from top --}}
                        <path d="M180 20 L150 200 L220 200 L190 20Z" fill="url(#daylightGrad)" opacity="0.06" />
                        <defs>
                            <linearGradient id="daylightGrad" x1="185" y1="0" x2="185"
                                y2="200" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#D4A96A" />
                                <stop offset="100%" stop-color="#B8925A" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        {{-- Japanese character "昼" (daytime) --}}
                        <text x="352" y="48" font-family="serif" font-size="18" fill="#B8925A" opacity="0.12"
                            text-anchor="middle">昼</text>
                    </svg>
                </div>

                <div class="sp-exp-content">
                    <div class="sp-exp-time">{{ __('pages.experience.ws_time') }}</div>
                    <h3 class="font-display sp-exp-title">{{ __('pages.experience.ws_title') }}</h3>
                    <p class="sp-exp-desc">{{ __('pages.experience.ws_desc') }}</p>
                    <ul class="sp-exp-features">
                        <li>{{ __('pages.experience.ws_feat_1') }}</li>
                        <li>{{ __('pages.experience.ws_feat_2') }}</li>
                        <li>{{ __('pages.experience.ws_feat_3') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Bistro Bar --}}
            <div class="sp-exp-panel sp-exp-panel--dark" id="sp-exp-night" data-reveal>
                <div class="sp-exp-bg"
                    style="
                background-image:
                    linear-gradient(160deg, rgba(8,8,6,0.58) 0%, rgba(8,8,6,0.32) 50%, rgba(8,8,6,0.55) 100%),
                    url('{{ asset('images/sapiens/z7956140948857_cc9c14025ea01f48794a107da0d39ee8.jpg') }}');
                background-size: cover;
                background-position: center;
            ">
                </div>

                {{-- SVG art: Bistro bar night scene --}}
                <div class="sp-exp-art" style="right:0; left:auto;">
                    <svg viewBox="0 0 400 360" fill="none" xmlns="http://www.w3.org/2000/svg"
                        preserveAspectRatio="xMidYMid meet">
                        {{-- Bar counter --}}
                        <rect x="20" y="250" width="360" height="8" rx="2" fill="#B8925A"
                            opacity="0.14" />
                        <rect x="20" y="258" width="360" height="80" rx="1" fill="#B8925A"
                            opacity="0.04" />
                        {{-- Bottles on shelf --}}
                        <rect x="50" y="160" width="80" height="2" fill="#B8925A" opacity="0.12" />
                        <rect x="60" y="120" width="8" height="40" rx="4" stroke="#B8925A"
                            stroke-width="0.5" opacity="0.25" fill="none" />
                        <ellipse cx="64" cy="118" rx="5" ry="3" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.2" fill="none" />
                        <rect x="78" y="128" width="6" height="32" rx="3" stroke="#B8925A"
                            stroke-width="0.5" opacity="0.2" fill="none" />
                        <rect x="94" y="134" width="7" height="26" rx="3.5" stroke="#B8925A"
                            stroke-width="0.5" opacity="0.2" fill="none" />
                        <rect x="112" y="138" width="5" height="22" rx="2.5" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.18" fill="none" />
                        {{-- Cocktail glass center --}}
                        <path d="M180 160 L160 230 L200 230Z" stroke="#B8925A" stroke-width="0.6" opacity="0.3"
                            fill="none" stroke-linejoin="round" />
                        <line x1="180" y1="230" x2="180" y2="250" stroke="#B8925A"
                            stroke-width="0.6" opacity="0.25" />
                        <line x1="165" y1="250" x2="195" y2="250" stroke="#B8925A"
                            stroke-width="0.8" opacity="0.2" />
                        {{-- Liquid in glass --}}
                        <path d="M168 215 L192 215 L188 230 L172 230Z" fill="#B8925A" opacity="0.08" />
                        {{-- Garnish --}}
                        <path d="M185 165 Q190 158 196 162" stroke="#B8925A" stroke-width="0.8" opacity="0.2"
                            fill="none" stroke-linecap="round" />
                        <circle cx="196" cy="162" r="3" fill="#B8925A" opacity="0.15" />
                        {{-- Ice cubes --}}
                        <rect x="172" y="218" width="8" height="8" rx="1" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.2" transform="rotate(-15 176 222)" fill="none" />
                        <rect x="183" y="220" width="7" height="7" rx="1" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.2" transform="rotate(10 186 223)" fill="none" />
                        {{-- Warm light glow --}}
                        <ellipse cx="220" cy="60" rx="40" ry="40" fill="url(#barGlow)"
                            opacity="0.5" />
                        <line x1="220" y1="20" x2="220" y2="60" stroke="#B8925A"
                            stroke-width="0.3" opacity="0.3" />
                        <defs>
                            <radialGradient id="barGlow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#D4A96A" stop-opacity="0.15" />
                                <stop offset="100%" stop-color="#B8925A" stop-opacity="0" />
                            </radialGradient>
                        </defs>
                        {{-- Sake bottle --}}
                        <path d="M290 180 Q286 190 286 220 L294 220 Q294 190 290 180Z" stroke="#B8925A" stroke-width="0.5"
                            opacity="0.2" fill="none" />
                        <rect x="287" y="170" width="6" height="12" rx="1" stroke="#B8925A"
                            stroke-width="0.4" opacity="0.18" fill="none" />
                        {{-- Japanese character "夜" (night) --}}
                        <text x="352" y="48" font-family="serif" font-size="18" fill="#B8925A" opacity="0.1"
                            text-anchor="middle">夜</text>
                        {{-- Stars/ambient dots --}}
                        <circle cx="310" cy="80" r="1" fill="#B8925A" opacity="0.2" />
                        <circle cx="340" cy="50" r="0.8" fill="#B8925A" opacity="0.15" />
                        <circle cx="360" cy="100" r="1.2" fill="#B8925A" opacity="0.18" />
                        <circle cx="330" cy="120" r="0.6" fill="#B8925A" opacity="0.12" />
                    </svg>
                </div>

                <div class="sp-exp-content" style="justify-content:flex-end;">
                    <div class="sp-exp-time">{{ __('pages.experience.bb_time') }}</div>
                    <h3 class="font-display sp-exp-title">{{ __('pages.experience.bb_title') }}</h3>
                    <p class="sp-exp-desc">{{ __('pages.experience.bb_desc') }}</p>
                    <ul class="sp-exp-features">
                        <li>{{ __('pages.experience.bb_feat_1') }}</li>
                        <li>{{ __('pages.experience.bb_feat_2') }}</li>
                        <li>{{ __('pages.experience.bb_feat_3') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     SIGNATURE MENU — Horizontal Scroll Showcase
═══════════════════════════════════════════════════════════ --}}
    <section class="sp-menu-section" style="background:#0A0A08; padding:6rem 0;">

        {{-- Header --}}
        <div class="px-6 lg:px-12 max-w-7xl mx-auto mb-10 flex justify-between items-end">
            <div data-reveal>
                <p
                    style="color:#B8925A; font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ __('pages.menu_showcase.label') }}</p>
                <h2 class="font-display" style="font-size:clamp(2rem,5vw,3.2rem); color:#E5D9C8; line-height:1.05;">
                    {{ __('pages.menu_showcase.title') }}</h2>
            </div>
            <a href="{{ route('menu') }}" class="sp-btn-ghost sp-btn-sm" data-reveal>{{ __('pages.menu_showcase.cta') }}</a>
        </div>

        {{-- Horizontal scroll track --}}
        <div class="sp-menu-track" id="sp-menu-track">
            <div class="sp-menu-rail" id="sp-menu-rail">

                @forelse($featuredItems as $item)
                    <article class="sp-menu-card">
                        {{-- Image — Fancybox lightbox --}}
                        <a href="{{ $item->image_url }}" data-fancybox="menu-gallery"
                            data-caption="{{ $item->name_en }} · {{ $item->name_vi }}"
                            class="sp-menu-card-img sp-menu-card-img--link">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name_en }}" loading="lazy">
                            <div class="sp-menu-card-img-overlay"></div>
                            <span class="sp-menu-card-zoom">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor"
                                        stroke-width="1.5" />
                                    <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M8 11h6M11 8v6" stroke="currentColor" stroke-width="1.2"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                        </a>

                        {{-- Content --}}
                        <div class="sp-menu-card-body">
                            @if ($item->category)
                                <span class="sp-menu-card-cat">{{ $item->category->name }}</span>
                            @endif
                            <h3 class="sp-menu-card-name">{{ $item->name_en }}</h3>
                            <p class="sp-menu-card-name-vi">{{ $item->name_vi }}</p>
                            @if ($item->price)
                                <p class="sp-menu-card-price">{{ number_format($item->price) }}K</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div style="padding:4rem; color:#8C7E6A;">{{ __('pages.menu_showcase.empty') }}</div>
                @endforelse

            </div>
        </div>

        {{-- Scroll drag hint --}}
        <div class="px-6 lg:px-12 max-w-7xl mx-auto mt-6" data-reveal>
            <p style="color:#3A3A35; font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase;">
                {{ __('pages.menu_showcase.drag_hint') }}</p>
            <p style="color:#8C7E6A; font-size:0.7rem; margin-top:4px;">{{ __('pages.menu_showcase.vat_note') }}</p>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     SPACE & VIBE — Concept Cards with Parallax
═══════════════════════════════════════════════════════════ --}}
    <section style="background:#050503; padding:6rem 1.5rem;">
        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-16" data-reveal>
                <p
                    style="color:#B8925A; font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; margin-bottom:1rem;">
                    {{ __('pages.vibe.label') }}</p>
                <h2 class="font-display"
                    style="font-size:clamp(2.5rem, 7vw, 5rem); color:#E5D9C8; line-height:0.95; max-width:700px; margin:0 auto;">
                    {{ __('pages.vibe.title_1') }}<br><em style="color:#8C7E6A; font-style:italic;">{{ __('pages.vibe.title_2') }}</em>
                </h2>
            </div>

            @php
                $vibes = [
                    ['kanji' => '集', 'title' => __('pages.vibe.gather_title'), 'text' => __('pages.vibe.gather_text')],
                    ['kanji' => '語', 'title' => __('pages.vibe.share_title'),  'text' => __('pages.vibe.share_text')],
                    ['kanji' => '創', 'title' => __('pages.vibe.create_title'), 'text' => __('pages.vibe.create_text')],
                    ['kanji' => '発', 'title' => __('pages.vibe.discover_title'), 'text' => __('pages.vibe.discover_text')],
                    ['kanji' => '属', 'title' => __('pages.vibe.belong_title'), 'text' => __('pages.vibe.belong_text')],
                    ['kanji' => '進', 'title' => __('pages.vibe.evolve_title'), 'text' => __('pages.vibe.evolve_text')],
                ];
            @endphp

            <div class="sp-vibe-grid" data-stagger>
                @foreach ($vibes as $v)
                    <div class="sp-vibe-card">
                        <div class="sp-vibe-kanji">{{ $v['kanji'] }}</div>
                        <h4 class="font-display sp-vibe-title">{{ $v['title'] }}</h4>
                        <p class="sp-vibe-text">{{ $v['text'] }}</p>
                        <div class="sp-vibe-line"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     PHOTO GALLERY — Cinematic Space Moments
═══════════════════════════════════════════════════════════ --}}
    <section style="background:#030302; padding:0;" aria-label="Gallery">

        {{-- Row 1: 2 ảnh cạnh nhau --}}
        <div style="display:flex; gap:2px; height:clamp(180px, 36vw, 560px);">

            {{-- Left --}}
            <div class="sp-gallery-item" style="flex:1.25; position:relative; overflow:hidden;">
                <img src="{{ asset('images/sapiens/z7956140953235_cba2933ce7b83badab1bbee35c332993.jpg') }}"
                    alt="Sapiens House – An evening at the bar" loading="lazy"
                    style="width:100%; height:100%; object-fit:cover; display:block;
                        transition:transform 0.9s ease, filter 0.5s ease;
                        filter:brightness(0.88) saturate(1.1);">
                <div
                    style="position:absolute; inset:0; pointer-events:none;
                background:linear-gradient(to right, rgba(0,0,0,0.25) 0%, transparent 60%);">
                </div>
                <div
                    style="position:absolute; bottom:0; left:0; right:0; padding:2.5rem; pointer-events:none;
                background:linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 70%);">
                    <p
                        style="font-family:'DM Sans',sans-serif; font-size:0.6rem; letter-spacing:0.3em;
                    text-transform:uppercase; color:#B8925A; margin-bottom:0.4rem;">
                        {{ __('pages.gallery.bistro_label') }}</p>
                    <p
                        style="font-family:'PaperCrease',serif; font-size:clamp(1.2rem,2.5vw,1.8rem);
                    color:#E5D9C8; line-height:1.1;">
                        {{ __('pages.gallery.bistro_title') }}</p>
                </div>
            </div>

            {{-- Right --}}
            <div class="sp-gallery-item" style="flex:0.75; position:relative; overflow:hidden;">
                <img src="{{ asset('images/sapiens/z7956140952723_9d27bb75ebe9eebb947726601d3c4126.jpg') }}"
                    alt="Sapiens House – Dinner and drinks" loading="lazy"
                    style="width:100%; height:100%; object-fit:cover; display:block;
                        transition:transform 0.9s ease, filter 0.5s ease;
                        filter:brightness(0.88) saturate(1.1);">
                <div
                    style="position:absolute; inset:0; pointer-events:none;
                background:linear-gradient(to left, rgba(0,0,0,0.2) 0%, transparent 60%);">
                </div>
                <div
                    style="position:absolute; bottom:0; left:0; right:0; padding:2rem; pointer-events:none;
                background:linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 70%);">
                    <p
                        style="font-family:'DM Sans',sans-serif; font-size:0.6rem; letter-spacing:0.3em;
                    text-transform:uppercase; color:#B8925A; margin-bottom:0.4rem;">
                        The Experience</p>
                    <p
                        style="font-family:'PaperCrease',serif; font-size:clamp(1rem,2vw,1.5rem);
                    color:#E5D9C8; line-height:1.1;">
                        Fusion &<br>Cocktails</p>
                </div>
            </div>

        </div>

        {{-- Row 2: full-width --}}
        <div style="margin-top:2px; height:clamp(140px, 28vw, 420px);">
            <div class="sp-gallery-item" style="position:relative; overflow:hidden; width:100%; height:100%;">
                <img src="{{ asset('images/sapiens/z7956140941279_4e54adbc05cc4398df4918c7719f59ee.jpg') }}"
                    alt="Sapiens House – The space" loading="lazy"
                    style="width:100%; height:100%; object-fit:cover; object-position:center 40%; display:block;
                        transition:transform 0.9s ease, filter 0.5s ease;
                        filter:brightness(0.82) saturate(1.1);">
                <div
                    style="position:absolute; inset:0; pointer-events:none;
                background:linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 55%);">
                </div>
                <div style="position:absolute; bottom:0; left:0; right:0; padding:2.5rem; pointer-events:none;">
                    <p
                        style="font-family:'DM Sans',sans-serif; font-size:0.6rem; letter-spacing:0.3em;
                    text-transform:uppercase; color:#B8925A; margin-bottom:0.4rem;">
                        Working Space</p>
                    <p
                        style="font-family:'PaperCrease',serif; font-size:clamp(1.2rem,2.5vw,2rem);
                    color:#E5D9C8; line-height:1.1;">
                        A Modern Cave for Modern Humans</p>
                </div>
            </div>
        </div>

        {{-- Hover zoom effect --}}
        <style>
            .sp-gallery-item:hover img {
                transform: scale(1.04);
                filter: brightness(0.95) saturate(1.15) !important;
            }

            @media (max-width: 640px) {
                .sp-gallery-grid {
                    grid-template-columns: 1fr !important;
                }

                .sp-gallery-item {
                    aspect-ratio: 4/3 !important;
                }
            }
        </style>

    </section>

    {{-- ═══════════════════════════════════════════════════════════
     RESERVATION CTA — Glassmorphism Banner
═══════════════════════════════════════════════════════════ --}}
    <section class="relative py-36 px-6 text-center overflow-hidden" style="background:#0A0A08;">

        {{-- Ambient orbs --}}
        <div class="sp-orb sp-orb-1"></div>
        <div class="sp-orb sp-orb-2"></div>

        {{-- Canvas smoke for this section --}}
        <canvas id="sp-cta-canvas" class="absolute inset-0 w-full h-full"
            style="pointer-events:none; opacity:0.7;"></canvas>

        <div class="relative max-w-3xl mx-auto grain-overlay py-16 px-10 sp-glass-panel" style="z-index:10;"
            data-reveal-slow>
            <p
                style="color:#B8925A; font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; margin-bottom:1.5rem;">
                {{ __('pages.reservation_cta.title') }}</p>

            <h2 class="font-display"
                style="font-size:clamp(2.5rem, 7vw, 4rem); color:#E5D9C8; line-height:1.0; margin-bottom:1.2rem;">
                {{ __('pages.reservation_cta.sub') }}<br><em style="color:#B8925A; font-style:italic;">{{ __('pages.reservation_cta.btn') }}</em>
            </h2>

            <p style="color:#8C7E6A; font-size:0.875rem; letter-spacing:0.1em; margin-bottom:2.5rem; line-height:1.8;">
                Tầng 4, 44 Nguyễn Huệ · Quận 1 · TP.HCM<br>
                <span style="color:#3A3A35;">——</span><br>
                Bistro Bar opens at 18:00
            </p>

            <a href="{{ route('reservation') }}" class="sp-btn-primary sp-btn-lg">
                <span>{{ __('pages.reservation_cta.btn') }}</span>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M4 9h10M9.5 4.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // ════════════════════════════════════════════════════════════
        // HERO CANVAS — Particles & Ambient Smoke
        // ════════════════════════════════════════════════════════════
        (function() {
            const canvas = document.getElementById('sp-hero-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let W, H, particles = [],
                smoke = [],
                raf;

            function resize() {
                W = canvas.width = window.innerWidth;
                H = canvas.height = window.innerHeight;
            }

            function randBetween(a, b) {
                return a + Math.random() * (b - a);
            }

            function createParticle() {
                return {
                    x: randBetween(0, W),
                    y: randBetween(0, H),
                    r: randBetween(0.4, 1.6),
                    vx: randBetween(-0.15, 0.15),
                    vy: randBetween(-0.35, -0.08),
                    alpha: randBetween(0.06, 0.28),
                    gold: Math.random() > 0.65,
                };
            }

            function createSmoke() {
                return {
                    x: randBetween(-100, W + 100),
                    y: randBetween(H * 0.55, H + 80),
                    r: randBetween(80, 200),
                    vx: randBetween(-0.08, 0.08),
                    vy: randBetween(-0.06, -0.02),
                    alpha: randBetween(0.008, 0.025),
                };
            }

            function init() {
                resize();
                particles = Array.from({
                    length: 90
                }, createParticle);
                smoke = Array.from({
                    length: 14
                }, createSmoke);
                window.addEventListener('resize', resize);
            }

            function draw() {
                ctx.clearRect(0, 0, W, H);

                // Smoke blobs
                smoke.forEach(s => {
                    const g = ctx.createRadialGradient(s.x, s.y, 0, s.x, s.y, s.r);
                    g.addColorStop(0, `rgba(184,146,90,${s.alpha})`);
                    g.addColorStop(1, 'rgba(184,146,90,0)');
                    ctx.beginPath();
                    ctx.fillStyle = g;
                    ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                    ctx.fill();

                    s.x += s.vx;
                    s.y += s.vy;
                    s.alpha += (Math.random() - 0.5) * 0.0008;
                    s.alpha = Math.max(0.004, Math.min(0.03, s.alpha));
                    if (s.y + s.r < 0) Object.assign(s, createSmoke(), {
                        y: H + s.r
                    });
                });

                // Dust particles
                particles.forEach(p => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = p.gold ?
                        `rgba(184,146,90,${p.alpha})` :
                        `rgba(201,185,154,${p.alpha})`;
                    ctx.fill();

                    p.x += p.vx;
                    p.y += p.vy;

                    if (p.y < -10) {
                        p.y = H + 10;
                        p.x = randBetween(0, W);
                    }
                    if (p.x < -10) p.x = W + 10;
                    if (p.x > W + 10) p.x = -10;
                });

                raf = requestAnimationFrame(draw);
            }

            init();
            draw();
        })();

        // ════════════════════════════════════════════════════════════
        // CTA CANVAS — Secondary smoke
        // ════════════════════════════════════════════════════════════
        (function() {
            const canvas = document.getElementById('sp-cta-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let W, H, blobs = [];

            function resize() {
                W = canvas.width = canvas.offsetWidth;
                H = canvas.height = canvas.offsetHeight;
            }

            function mkBlob() {
                return {
                    x: (Math.random() - 0.5) * W + W / 2,
                    y: H * 0.5 + Math.random() * H * 0.5,
                    r: 60 + Math.random() * 120,
                    vx: (Math.random() - 0.5) * 0.1,
                    vy: -(0.04 + Math.random() * 0.08),
                    a: 0.01 + Math.random() * 0.02,
                };
            }

            resize();
            window.addEventListener('resize', resize);
            blobs = Array.from({
                length: 8
            }, mkBlob);

            (function loop() {
                ctx.clearRect(0, 0, W, H);
                blobs.forEach(b => {
                    const g = ctx.createRadialGradient(b.x, b.y, 0, b.x, b.y, b.r);
                    g.addColorStop(0, `rgba(184,146,90,${b.a})`);
                    g.addColorStop(1, 'rgba(184,146,90,0)');
                    ctx.beginPath();
                    ctx.fillStyle = g;
                    ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
                    ctx.fill();
                    b.x += b.vx;
                    b.y += b.vy;
                    if (b.y + b.r < 0) Object.assign(b, mkBlob(), {
                        y: H + b.r
                    });
                });
                requestAnimationFrame(loop);
            })();
        })();

        // ════════════════════════════════════════════════════════════
        // HERO GSAP REVEAL (runs after loader exits)
        // ════════════════════════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', function() {
            // Hero reveal — wait for loader to exit (~2.6s)
            const heroLabel = document.getElementById('sp-hero-label');
            const heroLogo = document.getElementById('sp-hero-logo-wrap');
            const heroTagline = document.getElementById('sp-hero-tagline');
            const heroCtas = document.getElementById('sp-hero-ctas');
            const scrollHint = document.getElementById('sp-scroll-hint');

            const heroTl = gsap.timeline({
                delay: 2.5
            });

            heroTl
                .to(heroLabel, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out'
                })
                .to(heroLogo, {
                    opacity: 1,
                    y: 0,
                    duration: 1.0,
                    ease: 'power3.out'
                }, '-=0.2')
                .to(heroTagline, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power3.out'
                }, '-=0.4')
                .to(heroCtas, {
                    opacity: 1,
                    y: 0,
                    duration: 0.7,
                    ease: 'power3.out'
                }, '-=0.4')
                .to(scrollHint, {
                    opacity: 1,
                    duration: 0.8
                }, '-=0.2');
        });

        // ════════════════════════════════════════════════════════════
        // MENU DRAG-SCROLL
        // ════════════════════════════════════════════════════════════
        (function() {
            const track = document.getElementById('sp-menu-track');
            if (!track) return;

            let isDown = false,
                startX, scrollLeft;

            track.addEventListener('mousedown', e => {
                isDown = true;
                track.style.cursor = 'grabbing';
                startX = e.pageX - track.offsetLeft;
                scrollLeft = track.scrollLeft;
            });
            track.addEventListener('mouseleave', () => {
                isDown = false;
                track.style.cursor = 'grab';
            });
            track.addEventListener('mouseup', () => {
                isDown = false;
                track.style.cursor = 'grab';
            });
            track.addEventListener('mousemove', e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.6;
                track.scrollLeft = scrollLeft - walk;
            });
        })();

        // ════════════════════════════════════════════════════════════
        // MOUSE PARALLAX — hero spotlight follows cursor
        // ════════════════════════════════════════════════════════════
        (function() {
            const hero = document.getElementById('sp-hero');
            const spot = document.getElementById('sp-spotlight');
            if (!hero || !spot) return;

            hero.addEventListener('mousemove', e => {
                const rx = (e.clientX / window.innerWidth - 0.5) * 30;
                const ry = (e.clientY / window.innerHeight - 0.5) * 20;
                gsap.to(spot, {
                    x: rx,
                    y: ry,
                    duration: 1.8,
                    ease: 'power2.out',
                });
            });
        })();
    </script>
@endpush
