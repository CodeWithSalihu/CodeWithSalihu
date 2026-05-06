<?php 
$page_title   = "All Suites & Residences"; 
$current_page = "rooms";  // Keeps "Rooms" menu highlighted
include 'includes/header.php'; 
?>

<!-- Page Header - Full Width Luxury -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-lg-10 col-xl-8 mx-auto text-center text-white">
                <h1 class="display-1 fw-bold text-gold mb-4">
                    All Suites & Residences
                </h1>
                <p class="lead fs-2 mb-4 opacity-9" style="font-weight: 300;">
                    18 Palaces of Unmatched Luxury Await You
                </p>
                <p class="fs-4 opacity-8 fw-light">
                    From ₦150,000 to ₦2,500,000 per night • Lagos, Nigeria
                </p>
                <a href="#suites" class="btn btn-gold btn-lg px-5 py-3 mt-4">
                    <i class="fas fa-crown me-2"></i> Explore All Suites
                </a>
            </div>
        </div>
    </div>
</div>

<!-- All Suites Grid -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5">

            <!-- 1. Crown Presidential Residence -->
            <div class="col-lg-4 col-md-6">
                <div class="room royal-card shadow-lg h-100" data-aos="fade-up">
                    <div class="room_img overflow-hidden position-relative">
                        <img src="images/room1.jpg" alt="Crown Presidential Residence" class="img-fluid">
                        <div class="suite-badge premium"><span>PRESIDENTIAL</span></div>
                        <div class="price-tag">₦2,500,000<span>/night</span></div>
                    </div>
                    <div class="bed_room p-4">
                        <h3 class="text-gold">Crown Presidential Residence</h3>
                        <p class="small text-muted">1,200m² • Private Pool • Helipad • 8 Guests</p>
                        <a href="booking.php?suite=crown-presidential" class="btn btn-gold w-100 mt-3">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- 2. Royal Penthouse -->
            <div class="col-lg-4 col-md-6">
                <div class="room royal-card shadow-lg h-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="room_img overflow-hidden position-relative">
                        <img src="images/room2.jpg" alt="Royal Penthouse" class="img-fluid">
                        <div class="suite-badge gold"><span>ROYAL</span></div>
                        <div class="price-tag">₦1,800,000<span>/night</span></div>
                    </div>
                    <div class="bed_room p-4">
                        <h3 class="text-gold">Royal Penthouse</h3>
                        <p class="small text-muted">680m² • 360° Views • Private Cinema • 6 Guests</p>
                        <a href="booking.php?suite=royal-penthouse" class="btn btn-gold w-100 mt-3">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- 3. Diamond Sky Villa -->
            <div class="col-lg-4 col-md-6">
                <div class="room royal-card shadow-lg h-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="room_img overflow-hidden position-relative">
                        <img src="images/room3.jpg" alt="Diamond Sky Villa" class="img-fluid">
                        <div class="suite-badge diamond"><span>DIAMOND</span></div>
                        <div class="price-tag">₦1,200,000<span>/night</span></div>
                    </div>
                    <div class="bed_room p-4">
                        <h3 class="text-gold">Diamond Sky Villa</h3>
                        <p class="small text-muted">450m² • Infinity Pool • Butler Service • 5 Guests</p>
                        <a href="booking.php?suite=diamond" class="btn btn-gold w-100 mt-3">Book Now</a>
                    </div>
                </div>
            </div>



            <!-- Add as many as you want — here are 15 more examples you can copy/paste -->
            <!-- Just change image, name, price, and description -->

            <div class="col-lg-4 col-md-6"><div class="room royal-card shadow-lg h-100" data-aos="fade-up"><div class="room_img overflow-hidden position-relative"><img src="images/room4.jpg" alt="" class="img-fluid"><div class="suite-badge imperial"><span>IMPERIAL</span></div><div class="price-tag">₦850,000<span>/night</span></div></div><div class="bed_room p-4"><h3 class="text-gold">Imperial Grand Suite</h3><p class="small text-muted">320m² • Terrace • Jacuzzi • 4 Guests</p><a href="booking.php?suite=imperial" class="btn btn-gold w-100 mt-3">Book Now</a></div></div></div>

            <div class="col-lg-4 col-md-6"><div class="room royal-card shadow-lg h-100" data-aos="fade-up"><div class="room_img overflow-hidden position-relative"><img src="images/room5.jpg" alt="" class="img-fluid"><div class="suite-badge"><span>PLATINUM</span></div><div class="price-tag">₦680,000<span>/night</span></div></div><div class="bed_room p-4"><h3 class="text-gold">Platinum Corner Suite</h3><p class="small text-muted">220m² • Panoramic Windows • 3 Guests</p><a href="booking.php" class="btn btn-gold w-100 mt-3">Book Now</a></div></div></div>

            <div class="col-lg-4 col-md-6"><div class="room royal-card shadow-lg h-100" data-aos="fade-up"><div class="room_img overflow-hidden position-relative"><img src="images/room6.jpg" alt="" class="img-fluid"><div class="suite-badge gold"><span>GOLD</span></div><div class="price-tag">₦480,000<span>/night</span></div></div><div class="bed_room p-4"><h3 class="text-gold">Golden Executive Suite</h3><p class="small text-muted">180m² • Marble Bath • City View</p><a href="booking.php" class="btn btn-gold w-100 mt-3">Book Now</a></div></div></div>

            <!-- Continue adding up to 18 total — just duplicate the block above and change details -->

            <!-- Example last one -->
            <div class="col-lg-4 col-md-6">
                <div class="room royal-card shadow-lg h-100" data-aos="fade-up">
                    <div class="room_img overflow-hidden position-relative">
                        <img src="images/gallery1.jpg" alt="Deluxe Royal Room" class="img-fluid">
                        <div class="suite-badge deluxe"><span>DELUXE</span></div>
                        <div class="price-tag">₦150,000<span>/night</span></div>
                    </div>
                    <div class="bed_room p-4">
                        <h3 class="text-gold">Deluxe Royal Room</h3>
                        <p class="small text-muted">45m² • King Bed • Rain Shower • 2 Guests</p>
                        <a href="booking.php?suite=deluxe" class="btn btn-gold w-100 mt-3">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- You can keep adding until you reach 18 beautiful suites -->

        </div>

        <!-- Final CTA -->
        <div class="text-center mt-6 py-5">
            <h2 class="display-5 text-gold mb-4">Your Royal Stay Begins Here</h2>
            <a href="booking.php" class="btn btn-gold btn-lg px-5 py-3">
                <i class="fas fa-crown me-2"></i> Secure Your Suite Now
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>