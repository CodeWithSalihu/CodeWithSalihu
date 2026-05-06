<!-- includes/footer.php - DE GRAND HOTEL & ROOFTOP (Mobile-First Luxury Footer) -->
<footer class="text-light" style="background:#0F172A; margin-top:80px;">
    <!-- Gold Top Accent -->
    <div style="height:6px; background:linear-gradient(90deg,#B8860B,#D4AF37,#B8860B);"></div>

    <div class="container py-5 py-md-6">
        <div class="row g-4 g-lg-5 text-center text-lg-start">

            <!-- Logo + Tagline + Contact (Always on top on mobile) -->
            <div class="col-12 col-lg-4 order-lg-1">
                <div class="text-center text-lg-start">
                    <img src="images/logo.jpg" alt="De Grand Logo" class="mb-4" style="height:80px; filter:drop-shadow(0 0 15px rgba(212,175,55,0.6));">
                   
                    <p class="opacity-8 small mb-4">Nothing but LUXURY<br>The finest hotel & rooftop in Calabar</p>

                    <div class="small opacity-8">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt text-gold me-2"></i>
                            1B Felix Nsemo Drive,<br>New Secretariat, Calabar
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-phone-alt text-gold me-2"></i>
                            <a href="tel:+2349135319524" class="text-white">+234 913 531 9524</a>
                        </div>
                        <div>
                            <i class="fab fa-whatsapp text-gold me-2 fa-lg"></i>
                            <a href="https://wa.me/2349135319524" target="_blank" class="text-white">Chat on WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-lg-2 order-lg-2">
                <h5 class="text-gold mb-4" style="font-family:'Cinzel',serif;">Explore</h5>
                <ul class="list-unstyled small lh-lg">
                    <li><a href="index.php"      class="text-white opacity-8 hover-gold">Home</a></li>
                    <li><a href="about.php"      class="text-white opacity-8 hover-gold">About</a></li>
                    <li><a href="rooms.php"      class="text-white opacity-8 hover-gold">Rooms & Suites</a></li>
                    <li><a href="rooftop.php"    class="text-white opacity-8 hover-gold">Rooftop</a></li>
                    <li><a href="gallery.php"    class="text-white opacity-8 hover-gold">Gallery</a></li>
                    <li><a href="contact.php"    class="text-white opacity-8 hover-gold">Contact</a></li>
                </ul>
            </div>

            <!-- Experiences -->
            <div class="col-6 col-lg-2 order-lg-3">
                <h5 class="text-gold mb-4" style="font-family:'Cinzel',serif;">Experiences</h5>
                <ul class="list-unstyled small lh-lg">
                    <li><a href="rooftop.php#vip-tables" class="text-white opacity-8 hover-gold">VIP Tables</a></li>
                    <li><a href="rooftop.php#events"     class="text-white opacity-8 hover-gold">Private Events</a></li>
                    <li><a href="booking.php"            class="text-white opacity-8 hover-gold">Book Room</a></li>
                    <li><a href="offers.php"             class="text-white opacity-8 hover-gold">Special Offers</a></li>
                </ul>
            </div>

            <!-- Social + Newsletter (Bottom on mobile) -->
            <div class="col-12 col-lg-4 order-lg-4 mt-5 mt-lg-0">
                <h5 class="text-gold mb-4 text-center text-lg-start" style="font-family:'Cinzel',serif;">Stay Connected</h5>

                <!-- Social Icons -->
                <div class="d-flex justify-content-center justify-content-lg-start gap-4 mb-4">
                    <a href="https://www.instagram.com/degrandhotel_cally" target="_blank" class="text-gold fs-3"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com/share/1BkWTDgykV/" target="_blank" class="text-gold fs-3"><i class="fab fa-facebook"></i></a>
                    <a href="https://wa.me/2349135319524" target="_blank" class="text-gold fs-3"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://vm.tiktok.com/ZSHTDN7nYdwD5-bsahl/" target="_blank" class="text-gold fs-3"><i class="fab fa-tiktok"></i></a>
                </div>

                <!-- Mini Newsletter (optional – you can remove if you want even cleaner) -->
                <form method="POST" class="d-flex mx-auto mx-lg-0" style="max-width:320px;">
                    <input type="email" name="sub_email" placeholder="Your Email" required
                           class="form-control form-control-sm rounded-pill me-2 px-4 py-3"
                           style="background: #fff); border:1px solid #D4AF37; color:white;">
                    <button type="submit" class="btn btn-sm rounded-pill px-4" style="background:#D4AF37;">
                        <i class="fas fa-crown"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="py-4 border-top border-secondary" style="background:rgba(212,175,55,0.05);">
        <div class="container text-center small">
            <p class="mb-0 opacity-7">
                © <?php echo date('Y'); ?> <span class="text-gold">De Grand Hotel & Rooftop</span>. All Rights Reserved.
            </p>
            <p class="text-gold mt-2" style="font-family:'Playfair Display',serif; font-style:italic; font-size:0.95rem;">
                Nothing but LUXURY
            </p>
        </div>
    </div>
</footer>

<!-- Footer CSS (clean & fast) -->
<style>
    .hover-gold { transition: all 0.3s ease; }
    .hover-gold:hover { color: #D4AF37 !important; transform: translateY(-2px); }
    .text-gold { color: #D4AF37 !important; }
    footer a { text-decoration: none; }
    @media (max-width: 767.98px) {
        footer h5, footer h2 { font-size: 1.4rem !important; }
        footer .fs-3 { font-size: 1.8rem !important; }
    }
</style>