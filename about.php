<?php 
$page_title   = "About Us - De Grand Hotel & Rooftop Calabar"; 
$current_page = "about"; 
include 'includes/header.php'; 
?>

<!-- ===================== HERO SECTION – REDUCED HEIGHT & FULLY RESPONSIVE ===================== -->
<div class="back_re">
    <div class="overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 text-center">
                <h1 class="hero-title text-gold mb-3">
                    About De Grand
                </h1>
                <p class="hero-subtitle text-white opacity-9 lead">
                    Nothing but LUXURY in the Heart of Calabar
                </p>
                <div class="golden-line mx-auto mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== ABOUT CONTENT SECTION ===================== -->
<section class="about-section py-5" style="background:#0F172A;">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">

            <!-- Text Content -->
            <div class="col-lg-6 col-md-12 order-lg-1 order-2">
                <div class="pe-lg-5">
                    <h2 class="display-5 text-gold mb-4 fw-bold" style="font-family:'Cinzel', serif;">
                        Welcome to Luxury Redefined
                    </h2>
                    <p class="lead text-light opacity-9 mb-4">
                        Located in the heart of Calabar, <strong>De Grand Hotel & Rooftop</strong> offers contemporary 4-star accommodations with modern comfort and authentic Cross River hospitality.
                    </p>
                    <p class="text-light opacity-8 mb-4" style="line-height:1.8; font-size:1.1rem;">
                         <strong>Located in the most secured neighborhood in calabar</strong>, Enjoy <strong>free WiFi</strong>, a delicious and yummy meals, on-site restaurant, and the most talked-about <strong>rooftop bar in Calabar</strong>, featuring breathtaking panoramic views of the city skyline.
                    </p>
                    <p class="text-light opacity-8 mb-5" style="line-height:1.8;">
                        Only <strong> less than 10  minutes drive from from airport, about 1.6 km from U. J. Esuene Stadium</strong> and minutes away from major attractions, we are the perfect base for leisure travelers, business guests, and event attendees seeking comfort, style, and warm Nigerian hospitality.
                    </p>

                    <div class="row text-center text-md-start g-4 mb-5">
                        <div class="col-6 col-md-4">
                            <h3 class="text-gold display-6 fw-bold mb-1">4 Star</h3>
                            <p class="text-light small opacity-7">Modern Comfort</p>
                        </div>
                        <div class="col-6 col-md-4">
                            <h3 class="text-gold display-6 fw-bold mb-1">Free</h3>
                            <p class="text-light small opacity-7">WiFi & Parking</p>
                        </div>
                        <div class="col-12 col-md-4">
                            <h3 class="text-gold display-6 fw-bold mb-1">Iconic</h3>
                            <p class="text-light small opacity-7">Rooftop Bar</p>
                        </div>
                    </div>

                    <a href="booking.php" class="btn btn-gold px-5 py-3 text-dark fw-bold rounded-pill shadow-lg">
                        Book Your Stay Now
                    </a>
                </div>
            </div>

            <!-- Image Side -->
            <div class="col-lg-6 col-md-12 order-lg-2 order-1">
                <div class="position-relative about-img-wrapper rounded-4 overflow-hidden shadow-2xl">
                    <img src="images/about-degrand.jpg" alt="De Grand Hotel & Rooftop Calabar" class="img-fluid w-100 h-100 object-cover">
                    
                    <!-- Gold Crown Badge -->
                    <div class="crown-badge">
                        <i class="fas fa-crown fa-3x text-gold mb-2"></i>
                        <span class="d-block fw-bold">Calabar's Finest</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>