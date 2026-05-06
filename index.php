<?php 
// index.php - DE GRAND HOTEL & ROOFTOP, CALABAR
$page_title   = "De Grand Hotel & Rooftop | Luxury Hotel in Calabar"; 
$current_page = "home"; 
include 'includes/header.php'; 
?>

<style>
    :root {
        --gold: #D4AF37;
        --deep-gold: #B8860B;
        --black: #000000;
        --dark: #1a1a1a;
    }
    .text-gold { color: var(--gold) !important; }
    .bg-gold { background: linear-gradient(135deg, var(--deep-gold), var(--gold)); }
    .btn-gold {
        background: linear-gradient(135deg, var(--deep-gold), var(--gold));
        color: white;
        border: none;
        transition: all 0.4s;
    }
    .btn-gold:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(212,175,55,0.5);
    }
    .glass-card {
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(212,175,55,0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.6);
    }
    .banner-overlay {
    }
    .text-shadow { text-shadow: 0 4px 20px rgba(0,0,0,0,0.9); }

    /* SHOW CAROUSEL CAPTIONS ON MOBILE TOO */
    @media (max-width: 767.98px) {
        .carousel-caption {
            position: absolute;
            bottom: 120px;
            left: 15px;
            right: 15px;
            text-align: center;
            padding: 0 10px;
        }
        .carousel-caption h1 {
            font-size: 2rem !important;
            line-height: 1.2;
        }
        .carousel-caption p {
            font-size: 1rem;
            margin-bottom: 10px;
        }
    }

    /* HIDE FULL BOOKING FORM ON MOBILE */
    @media (max-width: 991.98px) {
        .booking_ocline {
            display: none !important;
        }
    }

    /* PREMIUM MOBILE BOOK BUTTON - INSIDE BANNER */
     .mobile-book-btn {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, var(--deep-gold), var(--gold));
        color: white !important;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 900;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 2px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.6);
        z-index: 10;
        text-decoration: none;
        animation: pulseGlow 2s infinite;
        border: 3px solid rgba(255,255,255,0.3);
        min-width: 100px;
        text-align: center;
    }
    .mobile-book-btn:hover {
        color: white;
        transform: translateX(-50%) translateY(-8px) scale(1.05);
        box-shadow: 0 20px 50px rgba(212,175,55,0.6);
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 15px 40px rgba(0,0,0,0.6); }
        50% { box-shadow: 0 20px 60px rgba(212,175,55,0.7); }
    }

    /* CALL RECEPTION BUTTON */
    .call-reception {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--gold);
        color: #000;
        padding: 16px 22px;
        border-radius: 50px;
        font-weight: 800;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        z-index: 999;
        text-decoration: none;
    }
</style>

<!-- ====================== BANNER SECTION ====================== -->
<section class=" position-relative">

    <div id="degrandCarousel" class="carousel slide" data-ride="carousel" data-interval="7000">
        <ol class="carousel-indicators">
            <li data-target="#degrandCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#degrandCarousel" data-slide-to="1"></li>
            <li data-target="#degrandCarousel" data-slide-to="2"></li>
        </ol>

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="images/banner1.jpg" class="d-block w-100" alt="De Grand Hotel Luxury">
                <div class="carousel-caption pb-5">
                    <h1 class="display-4 display-md-2 font-weight-bold text-white text-shadow" style="font-family: 'Playfair Display', serif;">
                        DE GRAND <span class="text-gold">HOTEL & ROOFTOP</span>
                    </h1>
                    <p class="h3 h4-md text-gold text-shadow font-weight-bold">Nothing But LUXURY</p>
                    <p class="lead d-none d-md-block text-white">Modern • Rooftop • Calabar's Finest</p>
                </div>
            </div>

            <div class="carousel-item">
                <img src="images/banner2.jpg" class="d-block w-100" alt="Rooftop Night">
                <div class="carousel-caption">
                    <h1 class="display-4 display-md-3 font-weight-bold text-white text-shadow">
                        The Most Luxurious Rooftop in Calabar
                    </h1>
                    <p class="h5 text-gold font-weight-bold">Cocktails • Shisha • Music • Skyline</p>
                </div>
            </div>

            <div class="carousel-item">
                <img src="images/banner3.jpg" class="d-block w-100" alt="Presidential Suite">
                <div class="carousel-caption">
                    <h1 class="display-4 display-md-3 font-weight-bold text-white text-shadow">
                        Your Palace in Calabar
                    </h1>
                    <p class="h5 text-gold font-weight-bold">Where Royalty Meets Modern Luxury</p>
                </div>
            </div>

        </div>

        <!-- Carousel Controls -->
        <a class="carousel-control-prev" href="#degrandCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#degrandCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>

        <div class="banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
    </div>

    <!-- MOBILE: BOOK NOW BUTTON INSIDE BANNER (Eye-catching!) -->
    <a href="https://degrandhotel.com/rooms.php" class="mobile-book-btn d-block d-lg-none">
        Book A Room
    </a>

    <!-- DESKTOP: FULL BOOKING FORM (Hidden on mobile) -->
    <div class="booking_ocline d-none d-lg-block">
        <!-- Your full glass booking form here (same as before) -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-9">
                    <div class="book_room glass-card p-5 mt-n5">
                        <div class="text-center mb-4">
                            <img src="images/logo.jpg" alt="De Grand Logo" style="height: 90px; filter: drop-shadow(0 0 15px rgba(212,175,55,0.6));">
                            <h2 class="text-gold mt-3" style="font-family: 'Playfair Display', serif; font-size: 3rem; letter-spacing: 2px;">
                                Book Your Luxury Stay
                            </h2>
                            <p class="text-white">1B Felix Nsemo Drive, New Secretariat, Calabar</p>
                        </div>

                        <form class="book_now" action="booking.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-gold fw-bold small">Check In</label>
                                    <input type="date" class="form-control glass-input" name="check_in" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-gold fw-bold small">Check Out</label>
                                    <input type="date" class="form-control glass-input" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                                <div class="col-6">
                                    <label class="text-gold fw-bold small">Adults</label>
                                    <select class="form-control glass-input" name="adults">
                                        <?php for($i=1; $i<=8; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> <?= $i>1?'Adults':'Adult' ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="text-gold fw-bold small">Children</label>
                                    <select class="form-control glass-input" name="children">
                                        <option value="0">0 Children</option>
                                        <?php for($i=1; $i<=6; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> Child<?= $i>1?'ren':'' ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-lg btn-gold px-5 py-3 rounded-pill text-uppercase fw-bold">
                                        Check Availability
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-4 text-white small opacity-8">
                            <p class="mb-1"><i class="fas fa-phone-alt text-gold"></i> +234 913 531 9524</p>
                            <p><i class="fas fa-map-marker-alt text-gold"></i> 1B Felix Nsemo Drive, Calabar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CALL RECEPTION BUTTON (Always visible) -->
    <a href="tel:+2349135319524" class="call-reception">
        Call Reception
    </a>
</section>
<?php include 'includes/about.php'; ?>
<?php include 'includes/rooftop.php'; ?>

<?php include 'includes/footer.php'; ?>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>