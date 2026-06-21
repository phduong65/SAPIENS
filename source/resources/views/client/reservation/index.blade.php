@extends('layouts.client')

@section('title', 'Đặt Bàn — Sapiens House')
@section('description', 'Đặt bàn tại Sapiens House. Working Space 11:00–17:00 · Bistro Bar 18:00–01:00.')

@section('content')

<section class="min-h-screen pt-24 pb-16 px-6" style="background-color:#0F0F0D;">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-0 min-h-screen" style="align-items:start; padding-top:3rem;">

        {{-- Left: Brand info --}}
        <div class="lg:pr-16 lg:sticky lg:top-28 py-8">
            <p style="color:#B8925A; font-size:0.7rem; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:1.5rem;" class="fade-in-up">
                {{ __('pages.reservation_page.label') }}
            </p>
            <h1 class="font-display fade-in-up fade-in-up-delay-1"
                style="font-size:clamp(2.5rem, 6vw, 4rem); color:#E5D9C8; line-height:1.05; margin-bottom:2rem;">
                {{ __('pages.reservation_page.title') }}
            </h1>
            <div class="divider-gold mb-8" style="width:60px; margin-left:0; background:linear-gradient(to right, #B8925A, transparent);"></div>

            <p style="color:#8C7E6A; font-size:0.9rem; line-height:1.9; margin-bottom:2rem;">
                {{ __('pages.reservation_page.sub') }}
            </p>

            <div style="border:1px solid #2E2E2A; padding:1.5rem; margin-bottom:1.5rem;">
                <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ __('pages.reservation_page.bistro_label') }}
                </p>
                <p style="color:#8C7E6A; font-size:0.875rem; margin-bottom:0.25rem;">{{ __('pages.reservation_page.bistro_hours') }}</p>
                <p style="color:#8C7E6A; font-size:0.875rem;">{{ __('pages.reservation_page.bistro_addr') }}</p>
            </div>

            <a href="https://maps.app.goo.gl/U4srxx72PFPQruoP7" target="_blank" rel="noopener"
               style="color:#B8925A; font-size:0.8rem; letter-spacing:0.08em;"
               class="hover:underline">
                {{ __('pages.reservation_page.maps_link') }}
            </a>
        </div>

        {{-- Right: Form --}}
        <div class="py-8 fade-in-up fade-in-up-delay-1">

            {{-- Success state --}}
            <div id="reservation-success"
                 style="display:none; border:1px solid rgba(52,211,153,0.3); background:rgba(52,211,153,0.05); padding:2.5rem; text-align:center;">
                <div style="width:48px; height:48px; border-radius:50%; background:rgba(52,211,153,0.15); display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3 class="font-display" style="color:#E5D9C8; font-size:1.75rem; margin-bottom:0.75rem;">{{ __('pages.reservation_page.success_title') }}</h3>
                <p style="color:#8C7E6A; font-size:0.875rem; line-height:1.7; margin-bottom:0.5rem;">
                    {{ __('pages.reservation_page.success_body') }}
                </p>
                <p id="reservation-code"
                   style="color:#B8925A; font-size:1rem; font-weight:600; letter-spacing:0.1em; margin-bottom:1rem;"></p>
                <p style="color:#8C7E6A; font-size:0.8rem;">
                    {{ __('pages.reservation_page.success_email') }}
                </p>
            </div>

            {{-- Form --}}
            <form id="reservation-form" novalidate>
                @csrf

                {{-- Guest Info --}}
                <div class="mb-8">
                    <h3 style="color:#C9B99A; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2E2E2A;">
                        {{ __('pages.reservation_page.section_guest_info') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="form-label">{{ __('pages.reservation_page.field_name') }} <span style="color:#B8925A;">*</span></label>
                            <input type="text" name="full_name" class="form-input" placeholder="Nguyễn Văn A" required>
                            <p class="field-error" data-field="full_name"></p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_phone') }} <span style="color:#B8925A;">*</span></label>
                            <input type="tel" name="phone" class="form-input" placeholder="0912 345 678" required>
                            <p class="field-error" data-field="phone"></p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_email') }} <span style="color:#B8925A;">*</span></label>
                            <input type="email" name="email" class="form-input" placeholder="you@email.com" required>
                            <p class="field-error" data-field="email"></p>
                        </div>
                    </div>
                </div>

                {{-- Booking Info --}}
                <div class="mb-8">
                    <h3 style="color:#C9B99A; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2E2E2A;">
                        {{ __('pages.reservation_page.section_booking_info') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_date') }} <span style="color:#B8925A;">*</span></label>
                            <input type="date" name="reservation_date" class="form-input"
                                   min="{{ date('Y-m-d') }}" required>
                            <p class="field-error" data-field="reservation_date"></p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_time') }} <span style="color:#B8925A;">*</span></label>
                            <select name="reservation_time" class="form-input" required>
                                <option value="" disabled selected style="background:#242420;">{{ __('pages.reservation_page.time_select') }}</option>
                                @foreach(['18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30','00:00','00:30'] as $t)
                                <option value="{{ $t }}" style="background:#242420;">{{ $t }}</option>
                                @endforeach
                            </select>
                            <p class="field-error" data-field="reservation_time"></p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_guests') }} <span style="color:#B8925A;">*</span></label>
                            <input type="number" name="guest_count" class="form-input"
                                   placeholder="2" min="1" max="50" required>
                            <p class="field-error" data-field="guest_count"></p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_area') }}</label>
                            <select name="seating_area" class="form-input">
                                <option value="" style="background:#242420;">{{ __('pages.reservation_page.area_no_preference') }}</option>
                                <option value="indoor" style="background:#242420;">{{ __('pages.reservation_page.area_indoor') }}</option>
                                <option value="outdoor" style="background:#242420;">{{ __('pages.reservation_page.area_outdoor') }}</option>
                                <option value="bar" style="background:#242420;">{{ __('pages.reservation_page.area_bar') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-8">
                    <h3 style="color:#C9B99A; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2E2E2A;">
                        {{ __('pages.reservation_page.field_note') }}
                    </h3>

                    {{-- Birthday toggle --}}
                    <div class="flex items-center gap-3 mb-4" style="padding:1rem; border:1px solid #2E2E2A;">
                        <input type="checkbox" name="is_birthday" id="is_birthday"
                               value="1"
                               style="width:18px; height:18px; accent-color:#B8925A;">
                        <label for="is_birthday" style="color:#C9B99A; font-size:0.875rem; cursor:pointer;">
                            🎂 {{ __('pages.reservation_page.field_birthday') }}
                        </label>
                    </div>

                    <div class="grid gap-4">
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_allergy') }}</label>
                            <input type="text" name="food_allergy" class="form-input"
                                   placeholder="Gluten, hải sản, đậu phộng...">
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_special') }}</label>
                            <textarea name="special_request" class="form-input" rows="2"
                                      placeholder="Trang trí bàn, yêu cầu đặc biệt..."></textarea>
                        </div>
                        <div>
                            <label class="form-label">{{ __('pages.reservation_page.field_note') }}</label>
                            <textarea name="note" class="form-input" rows="2"
                                      placeholder="Bất kỳ điều gì bạn muốn cho chúng tôi biết..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- General error --}}
                <div id="form-general-error"
                     style="display:none; color:#ef4444; font-size:0.8rem; margin-bottom:1rem; padding:0.75rem; border:1px solid rgba(239,68,68,0.3); background:rgba(239,68,68,0.05);">
                </div>

                <button type="submit" id="submit-btn" class="btn-gold w-full text-center" style="width:100%; padding:1rem 2rem;">
                    <span id="submit-text">{{ __('pages.reservation_page.submit') }}</span>
                    <span id="submit-loading" style="display:none;">{{ __('pages.reservation_page.submitting') }}</span>
                </button>

                <p style="color:#3A3A35; font-size:0.7rem; text-align:center; margin-top:1rem; line-height:1.5;">
                    Bằng cách gửi form, bạn đồng ý để Sapiens House liên hệ xác nhận đặt bàn.
                </p>
            </form>
        </div>
    </div>
</section>

@push('head')
<style>
.form-label {
    display: block;
    color: #8C7E6A;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}
.field-error {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.35rem;
    min-height: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const form = document.getElementById('reservation-form');
    const successDiv = document.getElementById('reservation-success');
    const codeEl = document.getElementById('reservation-code');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const generalError = document.getElementById('form-general-error');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(function (el) { el.textContent = ''; });
        generalError.style.display = 'none';
        generalError.textContent = '';
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(function ([field, messages]) {
            const el = document.querySelector('[data-field="' + field + '"]');
            if (el) el.textContent = Array.isArray(messages) ? messages[0] : messages;
        });
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        submitBtn.disabled = true;
        submitText.style.display = 'none';
        submitLoading.style.display = 'inline';

        const formData = new FormData(form);
        // Ensure boolean for checkbox
        if (!form.querySelector('[name=is_birthday]').checked) {
            formData.set('is_birthday', '0');
        }

        try {
            const res = await fetch('/reservation', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (res.status === 419) {
                generalError.textContent = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
                generalError.style.display = 'block';
                return;
            }

            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                generalError.textContent = 'Lỗi máy chủ. Vui lòng thử lại sau.';
                generalError.style.display = 'block';
                return;
            }

            const data = await res.json();

            if (res.ok && data.success) {
                form.style.display = 'none';
                codeEl.textContent = '{{ __("pages.reservation_page.code_label") }}: ' + data.code;
                successDiv.style.display = 'block';
                window.scrollTo({ top: successDiv.offsetTop - 100, behavior: 'smooth' });
            } else if (res.status === 422 && data.errors) {
                showErrors(data.errors);
            } else {
                generalError.textContent = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                generalError.style.display = 'block';
            }
        } catch (err) {
            generalError.textContent = 'Không thể kết nối. Vui lòng kiểm tra đường truyền và thử lại.';
            generalError.style.display = 'block';
        } finally {
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            submitLoading.style.display = 'none';
        }
    });
})();
</script>
@endpush

@endsection
