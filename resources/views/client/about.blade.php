@extends('layouts.client')

@section('title', 'About — Sapiens House')
@section('description', 'Câu chuyện về Sapiens House — cảm hứng từ cuốn sách Sapiens, khái niệm Modern Cave và tầm nhìn xây dựng cộng đồng.')

@section('content')

{{-- Hero --}}
<section class="relative flex items-end grain-overlay"
         style="min-height:70vh; background-color:#0F0F0D; padding-top:80px; overflow:hidden;">
    <div class="absolute inset-0" style="background:radial-gradient(ellipse at 60% 50%, rgba(184,146,90,0.06) 0%, transparent 70%);"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pb-20 w-full">
        <p style="color:#B8925A; font-size:0.7rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1.5rem;" class="fade-in-up">
            {{ __('pages.about_page.hero_label') }}
        </p>
        <h1 class="font-display fade-in-up fade-in-up-delay-1"
            style="font-size:clamp(3rem, 10vw, 7rem); color:#E5D9C8; line-height:0.95; max-width:700px;">
            {{ __('pages.about_page.hero_title') }}
        </h1>
    </div>
</section>

{{-- Chapter 1: The Book --}}
<section class="py-24 px-6" style="background-color:#1A1A18;">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <p style="color:#B8925A; font-size:0.65rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1rem;">
                {{ __('pages.about_page.book_label') }}
            </p>
            <h2 class="font-display" style="font-size:2.5rem; color:#E5D9C8; line-height:1.1; margin-bottom:1.5rem;">
                {{ __('pages.about_page.book_title') }}
            </h2>
            <div style="height:1px; width:50px; background:#B8925A; margin-bottom:1.5rem;"></div>
            <p style="color:#8C7E6A; font-size:0.9375rem; line-height:1.9; margin-bottom:1rem;">
                <em style="color:#C9B99A;">"Sapiens: A Brief History of Humankind"</em> của Yuval Noah Harari kể
                câu chuyện về hành trình phi thường của loài người — từ những bộ lạc săn bắt hái lượm đến nền
                văn minh toàn cầu.
            </p>
            <p style="color:#8C7E6A; font-size:0.9375rem; line-height:1.9;">
                Điều làm chúng tôi ấn tượng nhất không phải là công nghệ hay chính trị, mà là điều đơn giản
                nhất: con người luôn cần <span style="color:#C9B99A;">kết nối với nhau</span>.
            </p>
        </div>
        <div class="flex justify-center">
            <blockquote class="relative"
                        style="border-left:2px solid #B8925A; padding-left:2rem; max-width:420px;">
                <p class="font-display" style="font-size:1.75rem; color:#C9B99A; line-height:1.4; font-style:italic;">
                    "The secret of Sapiens' success is that we live in a dual reality — the objective world,
                    and the imagined world."
                </p>
                <cite style="color:#8C7E6A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; display:block; margin-top:1.5rem;">
                    — Yuval Noah Harari
                </cite>
            </blockquote>
        </div>
    </div>
</section>

{{-- Chapter 2: The Cave --}}
<section class="py-24 px-6" style="background-color:#0F0F0D;">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="order-2 lg:order-1">
            <div class="relative overflow-hidden flex items-center justify-center"
                 style="aspect-ratio:4/5; background:linear-gradient(135deg, #0A0A08 0%, #1A1A18 50%, #0F0F0D 100%);">
                <div class="absolute inset-0"
                     style="background:radial-gradient(ellipse 60% 60% at 50% 50%, rgba(184,146,90,0.08) 0%, transparent 70%);"></div>
                {{-- Cave entrance illustration --}}
                <svg viewBox="0 0 300 380" xmlns="http://www.w3.org/2000/svg"
                     style="width:70%; opacity:0.35; position:relative; z-index:1;">
                    <ellipse cx="150" cy="200" rx="120" ry="160" fill="none" stroke="#B8925A" stroke-width="0.5" opacity="0.6"/>
                    <ellipse cx="150" cy="200" rx="90" ry="130" fill="none" stroke="#B8925A" stroke-width="0.3" opacity="0.4"/>
                    <ellipse cx="150" cy="200" rx="60" ry="95" fill="none" stroke="#B8925A" stroke-width="0.2" opacity="0.25"/>
                    <line x1="150" y1="40" x2="150" y2="360" stroke="#B8925A" stroke-width="0.3" opacity="0.2"/>
                    <line x1="30" y1="200" x2="270" y2="200" stroke="#B8925A" stroke-width="0.3" opacity="0.2"/>
                    <circle cx="150" cy="200" r="4" fill="#B8925A" opacity="0.5"/>
                </svg>
                <p class="absolute bottom-8 left-0 right-0 text-center font-display"
                   style="font-size:0.7rem; color:#B8925A; letter-spacing:0.2em; text-transform:uppercase; opacity:0.6;">
                    {{ __('pages.about_page.cave_title') }}
                </p>
            </div>
        </div>
        <div class="order-1 lg:order-2">
            <p style="color:#B8925A; font-size:0.65rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1rem;">
                {{ __('pages.about_page.cave_label') }}
            </p>
            <h2 class="font-display" style="font-size:2.5rem; color:#E5D9C8; line-height:1.1; margin-bottom:1.5rem;">
                {{ __('pages.about_page.cave_title') }}
            </h2>
            <div style="height:1px; width:50px; background:#B8925A; margin-bottom:1.5rem;"></div>
            <p style="color:#8C7E6A; font-size:0.9375rem; line-height:1.9; margin-bottom:1rem;">
                Hàng nghìn năm trước, hang động là nơi con người tụ họp — chia sẻ lửa, kể chuyện,
                và xây dựng cộng đồng. Đó là nơi văn minh bắt đầu.
            </p>
            <p style="color:#8C7E6A; font-size:0.9375rem; line-height:1.9;">
                Sapiens House là <span style="color:#C9B99A;">hang động hiện đại</span> của bạn. Tối màu,
                ấm áp, bí ẩn — nhưng đầy sức sống và kết nối. Một không gian nơi cuộc sống thực sự
                xảy ra, giữa những tách cà phê và ly cocktail, giữa những bữa ăn và câu chuyện.
            </p>
        </div>
    </div>
</section>

{{-- Chapter 3: Community --}}
<section class="py-24 px-6" style="background-color:#1A1A18;">
    <div class="max-w-4xl mx-auto text-center">
        <p style="color:#B8925A; font-size:0.65rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1rem;">
            {{ __('pages.about_page.community_label') }}
        </p>
        <h2 class="font-display" style="font-size:clamp(2rem, 6vw, 3.5rem); color:#E5D9C8; line-height:1.1; margin-bottom:2rem;">
            {{ __('pages.about_page.community_title') }}
        </h2>
        <div class="divider-gold mb-8" style="width:80px; margin:0 auto 2rem;"></div>
        <p style="color:#8C7E6A; font-size:1rem; line-height:1.9; margin-bottom:1.5rem;">
            Sapiens House không đơn thuần là nơi ăn uống. Chúng tôi muốn tạo ra một <em style="color:#C9B99A;">community</em> —
            nơi những người sáng tạo, những nhà kinh doanh, những nghệ sĩ và những tâm hồn tự do
            có thể gặp gỡ và tạo nên những điều kỳ diệu cùng nhau.
        </p>
        <p style="color:#8C7E6A; font-size:1rem; line-height:1.9; margin-bottom:2.5rem;">
            Từ những workshop buổi sáng đến những đêm nhạc sống, từ những guest shift của bartender
            nổi tiếng đến những bữa ăn chia sẻ — mọi trải nghiệm tại Sapiens House đều được thiết kế
            để kết nối con người với con người.
        </p>
        <a href="{{ route('community') }}" class="btn-gold">{{ __('pages.events_page.title') }}</a>
    </div>
</section>

{{-- Chapter 4: Vision --}}
<section class="py-24 px-6 text-center grain-overlay relative" style="background-color:#0A0A08;">
    <div class="absolute inset-0" style="background:radial-gradient(ellipse at center, rgba(184,146,90,0.06) 0%, transparent 70%);"></div>
    <div class="relative z-10 max-w-3xl mx-auto">
        <p style="color:#B8925A; font-size:0.65rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1rem;">
            {{ __('pages.about_page.vision_label') }}
        </p>
        <h2 class="font-display" style="font-size:clamp(2rem, 6vw, 3.5rem); color:#E5D9C8; line-height:1.1; margin-bottom:2rem;">
            {{ __('pages.about_page.vision_title') }}
        </h2>
        <p style="color:#8C7E6A; font-size:1rem; line-height:1.9; margin-bottom:3rem;">
            Tầm nhìn của chúng tôi là trở thành <span style="color:#C9B99A;">destination</span> — không chỉ
            là điểm đến ăn uống mà là nơi nuôi dưỡng những ý tưởng, những mối quan hệ và
            những ký ức không thể quên. Sapiens House là nơi bạn muốn quay lại, không chỉ vì
            đồ ăn ngon mà vì <em style="color:#C9B99A;">cảm giác thuộc về</em>.
        </p>
        <a href="{{ route('reservation') }}" class="btn-gold">{{ __('pages.about_page.cta') }}</a>
    </div>
</section>

@endsection
