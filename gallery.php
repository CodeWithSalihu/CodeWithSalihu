<?php 
$page_title   = "Gallery - The Royals Hotel"; 
$current_page = "gallery"; 
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title text-center py-5">
                    <h1 class="display-2 text-gold mb-3" style="font-family: 'Playfair Display', serif;">
                        De Grand Gallery
                    </h1>
                    <p class="lead text-white opacity-9">Moments of Pure Opulence</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Luxury Gallery Section -->
<section class="gallery py-5">
    <div class="container-fluid px-lg-5">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-4 text-gold mb-4" style="font-family: 'Playfair Display', serif;">
                    Experience The De Grand
                </h2>
                <p class="lead text-light-gray">
                    Every corner tells a story of elegance, craftsmanship, and timeless luxury.
                </p>
            </div>
        </div>

        <!-- Royal Masonry Gallery -->
        <div class="row gallery-grid g-4">
            <!-- Image 1 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery1.jpg" 
                   data-lightbox="royals" 
                   data-title="Presidential Crown Suite"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery1.jpg" alt="Crown Suite Interior" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-crown fa-2x text-gold"></i>
                            <p class="mt-2">Presidential Suite</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 2 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery2.jpg" 
                   data-lightbox="royals" 
                   data-title="Infinity Pool at Sunset"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery2.jpg" alt="Infinity Pool" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-water fa-2x text-gold"></i>
                            <p class="mt-2">Infinity Pool</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 3 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery3.jpg" 
                   data-lightbox="royals" 
                   data-title="Royal Dining Experience"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery3.jpg" alt="Fine Dining" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-utensils fa-2x text-gold"></i>
                            <p class="mt-2">De Grand Dining</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 4 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery4.jpg" 
                   data-lightbox="royals" 
                   data-title="Grand Lobby"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery4.jpg" alt="Grand Lobby" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-building fa-2x text-gold"></i>
                            <p class="mt-2">De Grand Lobby</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 5 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery5.jpg" 
                   data-lightbox="royals" 
                   data-title="Royal Spa & Wellness"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery5.jpg" alt="Royal Spa" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-spa fa-2x text-gold"></i>
                            <p class="mt-2">De Grand Spa</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 6 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery6.jpg" 
                   data-lightbox="royals" 
                   data-title="Rooftop Bar"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery6.jpg" alt="Rooftop Lounge" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-cocktail fa-2x text-gold"></i>
                            <p class="mt-2">Rooftop Bar</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 7 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery7.jpg" 
                   data-lightbox="royals" 
                   data-title="Royal Ballroom"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery7.jpg" alt="Ballroom" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-chandelier fa-2x text-gold"></i>
                            <p class="mt-2">De Grand Ballroom</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Image 8 -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="images/gallery8.jpg" 
                   data-lightbox="royals" 
                   data-title="Executive Lounge"
                   class="gallery-item">
                    <div class="gallery_img position-relative overflow-hidden rounded shadow">
                        <img src="images/gallery8.jpg" alt="Executive Lounge" class="img-fluid">
                        <div class="gallery-overlay">
                            <i class="fas fa-chair fa-2x text-gold"></i>
                            <p class="mt-2">Executive Lounge</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Add more images here — just copy any block above -->
        </div>

        <!-- CTA -->
        <div class="text-center mt-5">
            <a href="booking.php" class="btn btn-gold btn-lg px-5 py-3 rounded-0 shadow-lg">
                Book Your Experience
            </a>
        </div>
    </div>
</section>

<!-- LIGHTBOX2 CSS & JS (MUST BE IN FOOTER OR HERE) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<!-- PREVENT DIRECT IMAGE OPENING (FORCE LIGHTBOX) -->
<script>
// This stops the browser from opening the raw image
document.querySelectorAll('.gallery-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault(); // Stop normal navigation
        const href = this.getAttribute('href');
        const title = this.getAttribute('data-title') || '';
        
        // Trigger Lightbox manually
        lightbox.option({
            'resizeDuration': 400,
            'fadeDuration': 300,
            'imageFadeDuration': 300
        });
        lightbox.start($(this)[0]);
    });
});
</script>

<?php include 'includes/footer.php'; ?>