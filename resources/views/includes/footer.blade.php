<<!--========================
  Footer
============================-->
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 footer-info">
                    <h3>CAD (Ceramic Art Dinoyo)</h3>
                    <p>Kampung Keramik Dinoyo adalah area di Kota Malang, Jawa Timur, yang terkenal dengan kerajinan keramik dan tembikar. Tempat ini menjadi pusat produksi keramik tradisional yang telah berkembang sejak lama.</p>
                </div>

                <div class="col-lg-3 col-md-6 footer-links">
                    <h4>{{ __('messages.useful_links') }}</h4>
                    <ul>
                        <li><i class="ion-chevron-right"></i> <a href="{{ route('frontend.home') }}">Home</a></li>
                        <li><i class="ion-chevron-right"></i> <a href="{{ route('frontend.about') }}">About us</a></li>
                        <li><i class="ion-chevron-right"></i> <a href="{{ route('frontend.catalog') }}">Catalog</a></li>
                        <li><i class="ion-chevron-right"></i> <a href="{{ route('frontend.booking') }}">Booking</a></li>
                        <li><i class="ion-chevron-right"></i> <a href="{{ route('frontend.chatbot') }}">Chatbot</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-5 footer-contact">
                    <h4>{{ __('messages.contact_info') }}</h4>
                    <p>
                        {{ __('messages.address') }}<br>
                        <strong>{{ __('messages.phone') }}:</strong> +62 341 123456<br>
                        <strong>{{ __('messages.email') }}:</strong> info@ceramicartdinoyo.com<br>
                    </p>

                    <div class="social-links">
                        <a href="https://www.instagram.com/dinoyoceramic" class="instagram" target="_blank">
                            <i class="ion-social-instagram"></i>@dinoyoceramic
                        </a>
                    </div>
                </div>

                <!-- Adjusted Google Maps iframe to be more proportional -->
                <div class="col-lg-3 col-md-6 footer-map">
                    <h4 class="text-center mb-4">Lokasi Kami</h4>
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.2742486068647!2d112.61175506958969!3d-7.941624325211181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629086c64985b%3A0x5027a76e356bda0!2sKampung%20Wisata%20Keramik%20Dinoyo!5e0!3m2!1sen!2sid!4v1636123456789!5m2!1sen!2sid"
                            style="border:0; width: 100%; height: 300px; border-radius: 8px;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            &copy; Copyright <strong>Ceramic Art Dinoyo</strong>. All Rights Reserved
        </div>
    </div>
</footer>