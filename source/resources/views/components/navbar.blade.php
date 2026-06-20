<nav id="sp-nav" role="navigation" aria-label="Main navigation">
    <div class="sp-nav-inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="sp-nav-logo" aria-label="Sapiens House Home">
            <img src="{{ asset('images/sapiens/SAPIENS HOUSE_LOGO_HORIZONTAL (NO TAGLINE).png') }}"
                 alt="Sapiens House"
                 class="sp-nav-logo-img"
                 loading="eager">
        </a>

        {{-- Desktop links --}}
        <div class="sp-nav-links" role="list">
            <a href="{{ route('home') }}"      class="sp-nav-link {{ request()->routeIs('home')      ? 'active' : '' }}" role="listitem" data-en="Home"      data-vi="Trang chủ">Home</a>
            <a href="{{ route('about') }}"     class="sp-nav-link {{ request()->routeIs('about')     ? 'active' : '' }}" role="listitem" data-en="Story"     data-vi="Câu chuyện">Story</a>
            <a href="{{ route('menu') }}"      class="sp-nav-link {{ request()->routeIs('menu')      ? 'active' : '' }}" role="listitem" data-en="Menu"      data-vi="Thực đơn">Menu</a>
            <a href="{{ route('community') }}" class="sp-nav-link {{ request()->routeIs('community') ? 'active' : '' }}" role="listitem" data-en="Community" data-vi="Cộng đồng">Community</a>
        </div>

        {{-- Preference toggles --}}
        <div id="sp-lang-toggle" role="group" aria-label="Language">
            <button class="sp-lang-btn" data-lang="en" aria-pressed="true">EN</button>
            <span id="sp-lang-sep" aria-hidden="true">·</span>
            <button class="sp-lang-btn" data-lang="vi" aria-pressed="false">VI</button>
        </div>

        <button id="sp-theme-toggle" aria-label="Toggle theme">
            {{-- Sun: shown when dark mode is active --}}
            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            {{-- Moon: shown when light mode is active --}}
            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>

        {{-- CTA --}}
        <a href="{{ route('reservation') }}" class="sp-nav-cta" data-en="Reserve" data-vi="Đặt bàn">Reserve</a>

        {{-- Mobile burger --}}
        <button id="sp-burger" aria-label="Open menu" aria-expanded="false" aria-controls="sp-mobile-menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="sp-mobile-menu" aria-hidden="true">
        <img src="{{ asset('images/sapiens/SAPIENS HOUSE_LOGO_W TEXTURE.png') }}"
             alt="Sapiens House"
             style="width:120px; height:auto; margin-bottom:2rem; opacity:0.8;">
        <a href="{{ route('home') }}"        class="sp-mobile-link" data-en="Home"          data-vi="Trang chủ">Home</a>
        <a href="{{ route('about') }}"       class="sp-mobile-link" data-en="Our Story"    data-vi="Câu chuyện">Our Story</a>
        <a href="{{ route('menu') }}"        class="sp-mobile-link" data-en="Menu"          data-vi="Thực đơn">Menu</a>
        <a href="{{ route('community') }}"   class="sp-mobile-link" data-en="Community"    data-vi="Cộng đồng">Community</a>
        <a href="{{ route('reservation') }}" class="sp-mobile-link sp-mobile-cta" data-en="Reserve a Table" data-vi="Đặt một bàn">Reserve a Table</a>

        {{-- Mobile preference toggles --}}
        <div id="sp-mobile-toggles">
            <button class="sp-lang-btn" data-lang="en" aria-pressed="true"
                    style="font-size:1rem; color:#8C7E6A;">EN</button>
            <span style="color:#8C7E6A; font-size:1rem;">·</span>
            <button class="sp-lang-btn" data-lang="vi" aria-pressed="false"
                    style="font-size:1rem; color:#8C7E6A;">VI</button>
            <span style="color:#2E2E2A; font-size:1rem; margin: 0 0.25rem;">|</span>
            <button id="sp-theme-toggle-mobile" aria-label="Toggle theme"
                    style="background:none; border:none; cursor:pointer; color:#8C7E6A;">
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

<script>
(function () {
    const burger = document.getElementById('sp-burger');
    const menu   = document.getElementById('sp-mobile-menu');
    if (!burger || !menu) return;

    burger.addEventListener('click', () => {
        const open = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', String(!open));
        menu.setAttribute('aria-hidden', String(open));
        burger.classList.toggle('open', !open);
        menu.classList.toggle('open', !open);
    });
})();
</script>
