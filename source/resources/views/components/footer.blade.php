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
                    A Modern Cave for Modern Humans
                </p>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="font-display text-sm mb-5" style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-en="Find Us" data-vi="Tìm Chúng Tôi">
                    Find Us
                </h4>
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
                <h4 class="font-display text-sm mb-5" style="color:#C9B99A; letter-spacing:0.15em; text-transform:uppercase;"
                    data-en="Hours" data-vi="Giờ Mở Cửa">
                    Hours
                </h4>
                <div style="color:#8C7E6A; font-size:0.8125rem; line-height:2.2;">
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.25rem;"
                       data-en="Working Space" data-vi="Không Gian Làm Việc">
                        Working Space
                    </p>
                    <p>Mon – Sun &nbsp; 11:00 – 17:00</p>
                    <p style="color:#C9B99A; font-size:0.75rem; letter-spacing:0.1em; text-transform:uppercase; margin-top:0.75rem; margin-bottom:0.25rem;"
                       data-en="Bistro Bar" data-vi="Bistro Bar">
                        Bistro Bar
                    </p>
                    <p>Mon – Sun &nbsp; 18:00 – 01:00</p>

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
                &copy; {{ date('Y') }} Sapiens House. All rights reserved.
            </p>
            <a href="{{ route('login') }}" style="color:#3A3A35; font-size:0.7rem; letter-spacing:0.05em;"
               class="hover:underline">
                Admin
            </a>
        </div>
    </div>
</footer>
