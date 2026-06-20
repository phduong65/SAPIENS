<!DOCTYPE html>
<html lang="vi" class="loading">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sapiens House – Eatery & Drinks')</title>
    <meta name="description" content="@yield('description', 'A Modern Cave for Modern Humans. Fusion Japanese Eatery & Bistro Bar tại Tầng 4, 44 Nguyễn Huệ, Quận 1, TP.HCM.')">
    <meta property="og:title" content="@yield('og_title', 'Sapiens House – Eatery & Drinks')">
    <meta property="og:description" content="@yield('og_description', 'A Modern Cave for Modern Humans.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    {{-- Fancybox 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css"/>

    {{-- ── Anti-flash: apply stored theme & lang before CSS renders ── --}}
    <script>
    (function(){
        var t = localStorage.getItem('sp-theme') || 'dark';
        var l = localStorage.getItem('sp-lang')  || 'en';
        document.documentElement.setAttribute('data-theme', t);
        document.documentElement.setAttribute('data-lang', l);
    })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    <style>
        html.loading body { overflow: hidden; }
    </style>
</head>
<body>

    {{-- ═══ LUXURY LOADING SCREEN ═══ --}}
    <div id="sp-loader" aria-hidden="true">
        <div id="sp-loader-inner">
            <img id="sp-loader-logo"
                 src="{{ asset('images/sapiens/SAPIENS HOUSE_LOGO_W TEXTURE.png') }}"
                 alt="Sapiens House"
                 style="width:200px; height:auto; opacity:0; transform:translateY(20px); margin-bottom:1.5rem;">
            <div id="sp-loader-tagline">A Modern Cave for Modern Humans</div>
            <div id="sp-loader-bar"><div id="sp-loader-fill"></div></div>
        </div>
    </div>

    @include('components.navbar')

    <main id="sp-main">
        @yield('content')
    </main>

    @include('components.footer')

    {{-- GSAP CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    {{-- Fancybox 5 --}}
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind('[data-fancybox]', {
                Toolbar: {
                    display: { left: ['infobar'], middle: [], right: ['close'] }
                },
                Images: { zoom: false },
                animated: true,
                dragToClose: true,
                on: {
                    ready: (fancybox) => {
                        // Dark themed fancybox already via CSS override below
                    }
                }
            });
        });
    </script>

    <script>
    // ── Register GSAP plugins ──────────────────────────────────
    gsap.registerPlugin(ScrollTrigger);

    // ── Loading Screen ─────────────────────────────────────────
    (function() {
        const loader  = document.getElementById('sp-loader');
        const fill    = document.getElementById('sp-loader-fill');
        const logo    = document.getElementById('sp-loader-logo');
        const tagline = document.getElementById('sp-loader-tagline');

        const tl = gsap.timeline({
            onComplete: () => {
                gsap.to(loader, {
                    opacity: 0, duration: 0.9, ease: 'power2.inOut',
                    onComplete: () => {
                        loader.style.display = 'none';
                        document.documentElement.classList.remove('loading');
                        initPageAnimations();
                    }
                });
            }
        });

        tl.set([logo, tagline], { opacity: 0 })
          .to(fill,    { scaleX: 1, duration: 1.8, ease: 'power3.inOut' }, 0)
          .to(logo,    { opacity: 1, y: 0, duration: 1.0, ease: 'power3.out' }, 0.4)
          .to(tagline, { opacity: 1, y: 0, duration: 0.7, ease: 'power2.out' }, 1.1)
          .to({}, { duration: 0.5 }); // hold
    })();

    // ── Glassmorphism Navbar on scroll ─────────────────────────
    (function() {
        const nav = document.getElementById('sp-nav');
        if (!nav) return;
        ScrollTrigger.create({
            start: 80,
            onEnter: () => nav.classList.add('scrolled'),
            onLeaveBack: () => nav.classList.remove('scrolled'),
        });
    })();

    // ── Global scroll-reveal helper ────────────────────────────
    function revealOnScroll(selector, opts) {
        document.querySelectorAll(selector).forEach(el => {
            gsap.fromTo(el,
                { opacity: 0, y: opts.y ?? 40, scale: opts.scale ?? 1 },
                {
                    opacity: 1, y: 0, scale: 1,
                    duration: opts.duration ?? 0.9,
                    ease: opts.ease ?? 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: opts.start ?? 'top 88%',
                        toggleActions: 'play none none none',
                    }
                }
            );
        });
    }

    // ── All page animations (called after loader exits) ────────
    function initPageAnimations() {
        // Generic reveal classes
        revealOnScroll('[data-reveal]', {});
        revealOnScroll('[data-reveal-slow]', { duration: 1.3, y: 30 });

        // Staggered children
        document.querySelectorAll('[data-stagger]').forEach(parent => {
            const children = parent.children;
            gsap.fromTo(Array.from(children),
                { opacity: 0, y: 50 },
                {
                    opacity: 1, y: 0,
                    duration: 0.8,
                    stagger: 0.12,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: parent,
                        start: 'top 85%',
                        toggleActions: 'play none none none',
                    }
                }
            );
        });

        // Line-by-line text reveals
        document.querySelectorAll('[data-lines]').forEach(el => {
            const lines = el.querySelectorAll('[data-line]');
            gsap.fromTo(lines,
                { opacity: 0, y: 24, clipPath: 'inset(0 0 100% 0)' },
                {
                    opacity: 1, y: 0, clipPath: 'inset(0 0 0% 0)',
                    duration: 0.85,
                    stagger: 0.14,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 82%',
                    }
                }
            );
        });

        // Parallax sections
        document.querySelectorAll('[data-parallax]').forEach(el => {
            const speed = parseFloat(el.dataset.parallax) || 0.15;
            gsap.to(el, {
                yPercent: -100 * speed,
                ease: 'none',
                scrollTrigger: { trigger: el.parentElement, scrub: true }
            });
        });

        // Horizontal dividers (scale in from center)
        document.querySelectorAll('.sp-divider').forEach(el => {
            gsap.fromTo(el, { scaleX: 0 }, {
                scaleX: 1, duration: 1.2, ease: 'power3.inOut',
                scrollTrigger: { trigger: el, start: 'top 90%' }
            });
        });
    }
    </script>

    @stack('scripts')
</body>
</html>
