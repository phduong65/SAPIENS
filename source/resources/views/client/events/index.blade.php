@extends('layouts.client')

@section('title', 'Events & Community — Sapiens House')
@section('description', 'Khám phá các sự kiện, workshop và đêm nhạc tại Sapiens House.')

@section('content')

<section class="pt-36 pb-16 px-6 text-center" style="background-color:#0F0F0D;">
    <p style="color:#B8925A; font-size:0.7rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1rem;" class="fade-in-up">
        Community & Events
    </p>
    <h1 class="font-display fade-in-up fade-in-up-delay-1"
        style="font-size:clamp(2.5rem, 8vw, 5rem); color:#E5D9C8; line-height:1.05;">
        What's On
    </h1>
</section>

<section class="py-16 px-6" style="background-color:#1A1A18;">
    <div class="max-w-6xl mx-auto">

        @if($events->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px" style="background-color:#2E2E2A;">
            @foreach($events as $event)
            <article class="menu-card flex flex-col" style="background-color:#1A1A18;">
                {{-- Image --}}
                <div class="overflow-hidden" style="aspect-ratio:16/9; background-color:#242420;">
                    <img src="{{ $event->image_url }}"
                         alt="{{ $event->title }}"
                         loading="lazy"
                         class="w-full h-full object-cover opacity-70 transition-all duration-500 hover:opacity-90 hover:scale-105"
                         onerror="this.parentElement.style.background='#242420'; this.style.display='none'">
                </div>
                <div class="p-7 flex flex-col flex-1">
                    {{-- Type badge --}}
                    <span class="inline-block mb-4 px-3 py-1 text-xs tracking-widest uppercase"
                          style="background:rgba(184,146,90,0.12); color:#B8925A; border:1px solid rgba(184,146,90,0.3); width:fit-content;">
                        {{ $event->type_label }}
                    </span>

                    <h2 style="color:#E5D9C8; font-size:1.05rem; font-weight:500; line-height:1.35; margin-bottom:0.75rem;">
                        {{ $event->title }}
                    </h2>
                    <p style="color:#8C7E6A; font-size:0.8125rem; line-height:1.65; flex:1; margin-bottom:1.25rem;">
                        {{ Str::limit($event->description, 120) }}
                    </p>
                    <div class="flex items-center justify-between" style="border-top:1px solid #2E2E2A; padding-top:1rem;">
                        <p style="color:#B8925A; font-size:0.75rem; letter-spacing:0.05em;">
                            {{ $event->event_date->format('d M Y') }}
                        </p>
                        <p style="color:#8C7E6A; font-size:0.75rem;">
                            {{ $event->event_time }}
                        </p>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @else
        <div class="text-center py-32">
            <p class="font-display" style="font-size:1.75rem; color:#3A3A35; margin-bottom:1rem;">
                Stay tuned — something's brewing.
            </p>
            <p style="color:#2E2E2A; font-size:0.875rem;">Những sự kiện thú vị đang được chuẩn bị.</p>
        </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="py-20 px-6 text-center" style="background-color:#0F0F0D; border-top:1px solid #2E2E2A;">
    <p style="color:#8C7E6A; font-size:0.875rem; line-height:1.7; max-width:480px; margin:0 auto 2rem;">
        Muốn host sự kiện riêng tại Sapiens House? Liên hệ với chúng tôi.
    </p>
    <a href="{{ route('reservation') }}" class="btn-gold">Get in Touch</a>
</section>

@endsection
