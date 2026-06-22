@extends('layouts.client')

@section('title', 'Menu — Sapiens House')
@section('description', 'Khám phá thực đơn Fusion Japanese tại Sapiens House — Small Plates, Main Dishes, Cocktails và hơn thế nữa.')

@section('content')

{{-- Header --}}
<section class="pt-36 pb-16 px-6 text-center" style="background:#0A0A08; position:relative; overflow:hidden;">
    <div style="position:absolute; inset:0; background:radial-gradient(ellipse 60% 60% at 50% 30%, rgba(184,146,90,0.05) 0%, transparent 70%); pointer-events:none;"></div>
    <p style="color:#B8925A; font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; margin-bottom:1rem;" class="fade-in-up">
        {{ __('pages.menu_page.label') }}
    </p>
    <h1 class="font-display fade-in-up fade-in-up-delay-1"
        style="font-size:clamp(3rem, 9vw, 6rem); color:#E5D9C8; line-height:0.95; margin-bottom:1.5rem;">
        {{ __('pages.menu_page.title') }}
    </h1>
    <p style="color:#8C7E6A; font-size:0.75rem; letter-spacing:0.1em; margin-bottom:0;" class="fade-in-up fade-in-up-delay-2">
        {{ __('pages.menu_page.vat_note') }}
    </p>
</section>

{{-- Tab Filter --}}
<div id="sp-menu-tabs" class="sticky top-[72px] z-40 px-6 py-4 flex justify-center gap-6"
     style="background:rgba(8,8,6,0.95); backdrop-filter:blur(20px); border-bottom:1px solid #1E1E1B;">
    <button class="menu-tab-btn sp-nav-link text-xs active" data-tab="food">{{ __('pages.menu_page.filter_food') }}</button>
    <button class="menu-tab-btn sp-nav-link text-xs" data-tab="drink">{{ __('pages.menu_page.filter_drink') }}</button>
</div>

{{-- Food Categories --}}
<div id="tab-food" class="menu-tab-content" style="background:#0A0A08;">
    @foreach($foodCategories as $category)
    @if($category->activeItems->count())
    <section class="py-16 px-6" style="border-bottom:1px solid #151513;">
        <div class="max-w-7xl mx-auto">
            <div style="display:flex; align-items:baseline; gap:1.5rem; margin-bottom:2.5rem;">
                <h2 class="font-display"
                    style="font-size:2.2rem; color:#E5D9C8; letter-spacing:0.04em;">
                    {{ $category->name }}
                </h2>
                <span style="font-family:'Cormorant Garamond',serif; font-style:italic; color:#3A3A35; font-size:0.75rem;">
                    {{ $category->activeItems->count() }} items
                </span>
            </div>

            <div class="menu-page-grid">
                @foreach($category->activeItems as $item)
                <article class="menu-page-card">
                    {{-- Image with Fancybox --}}
                    @if($item->image_path)
                    <a href="{{ $item->image_url }}"
                       data-fancybox="food-gallery"
                       data-caption="<strong>{{ $item->name_en }}</strong><br><em>{{ $item->name_vi }}</em>{{ $item->description_en ? '<br><small>'.$item->description_en.'</small>' : '' }}"
                       class="menu-page-card-img">
                        <img src="{{ $item->image_url }}"
                             alt="{{ $item->name_en }}"
                             loading="lazy">
                        <div class="menu-page-card-overlay"></div>
                        <span class="menu-page-card-zoom-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M8 11h6M11 8v6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </a>
                    @endif

                    {{-- Info --}}
                    <div class="menu-page-card-body">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:0.4rem;">
                            <div style="flex:1;">
                                <h3 style="color:#E5D9C8; font-size:0.9375rem; font-weight:500; line-height:1.3; margin-bottom:2px;">
                                    {{ $item->name_en }}
                                </h3>
                                <p style="color:#8C7E6A; font-size:0.75rem; font-family:'Cormorant Garamond',serif; font-style:italic;">
                                    {{ $item->name_vi }}
                                </p>
                            </div>
                            <p style="color:#B8925A; font-size:0.95rem; font-weight:600; white-space:nowrap; flex-shrink:0; letter-spacing:0.03em;">
                                {{ $item->formatted_price }}
                            </p>
                        </div>
                        @if($item->description_en)
                        <p style="color:#8C7E6A; font-size:0.8rem; line-height:1.65; margin-top:0.5rem;">
                            {{ $item->description_en }}
                        </p>
                        @endif
                        @if($item->is_featured)
                        <span style="display:inline-block; margin-top:0.6rem; font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:#B8925A; border:1px solid rgba(184,146,90,0.3); padding:2px 8px;">
                            Chef's Pick
                        </span>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach
</div>

{{-- Drink Categories --}}
<div id="tab-drink" class="menu-tab-content" style="background:#0A0A08; display:none;">
    @foreach($drinkCategories as $category)
    @if($category->activeItems->count())
    <section class="py-16 px-6" style="border-bottom:1px solid #151513;">
        <div class="max-w-7xl mx-auto">
            <h2 class="font-display mb-10"
                style="font-size:2.2rem; color:#E5D9C8; letter-spacing:0.04em;">
                {{ $category->name }}
            </h2>
            <div class="menu-page-grid">
                @foreach($category->activeItems as $item)
                <article class="menu-page-card">
                    @if($item->image_path)
                    <a href="{{ $item->image_url }}"
                       data-fancybox="drink-gallery"
                       data-caption="<strong>{{ $item->name_en }}</strong><br><em>{{ $item->name_vi }}</em>"
                       class="menu-page-card-img">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name_en }}" loading="lazy">
                        <div class="menu-page-card-overlay"></div>
                        <span class="menu-page-card-zoom-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </a>
                    @endif
                    <div class="menu-page-card-body">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                            <div>
                                <h3 style="color:#E5D9C8; font-size:0.9375rem; font-weight:500;">{{ $item->name_en }}</h3>
                                <p style="color:#8C7E6A; font-size:0.75rem; font-style:italic;">{{ $item->name_vi }}</p>
                            </div>
                            <p style="color:#B8925A; font-size:0.95rem; font-weight:600; white-space:nowrap; flex-shrink:0;">
                                {{ $item->formatted_price }}
                            </p>
                        </div>
                        @if($item->description_en)
                        <p style="color:#8C7E6A; font-size:0.8rem; line-height:1.65; margin-top:0.5rem;">{{ $item->description_en }}</p>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach

    @if($drinkCategories->every(fn($c) => $c->activeItems->count() === 0))
    <div class="py-32 text-center">
        <p class="font-display" style="font-size:2rem; color:#3A3A35;">{{ __('pages.menu_page.empty') }}</p>
        <p style="color:#3A3A35; font-size:0.8rem; margin-top:0.5rem;">{{ __('pages.events_page.empty') }}</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const tabs     = document.querySelectorAll('.menu-tab-btn');
    const contents = document.querySelectorAll('.menu-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            contents.forEach(c => { c.style.display = c.id === 'tab-' + target ? 'block' : 'none'; });
        });
    });
})();
</script>
@endpush

@endsection
