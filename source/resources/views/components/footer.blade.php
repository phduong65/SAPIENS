<footer style="background-color:#0F0F0D; border-top:1px solid #2E2E2A;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">

            {{-- Brand --}}
            <div>
                <img src="{{ asset('images/sapiens/SAPIENS HOUSE_LOGO_HORIZONTAL.png') }}"
                     alt="Sapiens House"
                     class="h-14 w-auto mb-4"
                     loading="lazy">
                <p style="color:#8C7E6A; font-size:0.8125rem; line-height:1.7; letter-spacing:0.05em;">
                    {{ __('ui.footer.slogan') }}
                </p>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="font-display text-sm mb-5"
                    style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-i18n="footer.find_us">{{ __('ui.footer.find_us') }}</h4>
                <address class="not-italic" style="color:#8C7E6A; font-size:0.8125rem; line-height:2;">
                    <p>Tầng 4, 44 Nguyễn Huệ</p>
                    <p>Quận 1, TP.HCM</p>
                    <p class="mt-3">
                        <a href="https://maps.app.goo.gl/U4srxx72PFPQruoP7"
                           target="_blank" rel="noopener"
                           style="color:#B8925A;"
                           class="hover:underline">
                            ↗ Google Maps
                        </a>
                    </p>
                    <p class="mt-2">
                        <a href="mailto:hello@sapienshouse.vn" style="color:#B8925A;" class="hover:underline">
                            hello@sapienshouse.vn
                        </a>
                    </p>
                </address>
            </div>

            {{-- Hours --}}
            <div>
                <h4 class="font-display text-sm mb-5"
                    style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-i18n="footer.hours">{{ __('ui.footer.hours') }}</h4>
                <div style="color:#8C7E6A; font-size:0.8125rem; line-height:2.2;">
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.25rem;"
                       data-i18n="footer.working_space">{{ __('ui.footer.working_space') }}</p>
                    <p>{{ __('ui.footer.ws_hours') }}</p>
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-top:0.75rem; margin-bottom:0.25rem;"
                       data-i18n="footer.bistro_bar">{{ __('ui.footer.bistro_bar') }}</p>
                    <p>{{ __('ui.footer.bb_hours') }}</p>

                    {{-- Social --}}
                    <div class="flex gap-4 mt-6">
                        <a href="#" aria-label="Instagram"
                           style="color:#8C7E6A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase;"
                           class="hover:text-gold transition-colors">
                            Instagram
                        </a>
                        <a href="#" aria-label="Facebook"
                           style="color:#8C7E6A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase;"
                           class="hover:text-gold transition-colors">
                            Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider-gold mb-8" style="max-width:100%;"></div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p style="color:#3A3A35; font-size:0.7rem; letter-spacing:0.08em;">
                {{ __('ui.footer.copyright', ['year' => date('Y')]) }}
            </p>
            <a href="{{ route('login') }}" style="color:#3A3A35; font-size:0.7rem; letter-spacing:0.05em;"
               class="hover:underline">
                {{ __('ui.footer.admin') }}
            </a>
        </div>
    </div>
</footer>
