<?php 
$page_title   = "Contact Us"; 
$current_page = "contact"; 
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
                        Get In Touch
                    </h1>
                    <p class="lead text-white opacity-9">De Grand  Concierge Awaits</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Royal Contact Section -->
<section class="contact py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="contact-form royal-card p-5">
                    <h2 class="text-gold mb-4" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">
                        Send Us a Message
                    </h2>
                    <p class="text-light-gray mb-5">
                        Whether you have a question about our suites, dining, or special requests — our royal team is here 24/7.
                    </p>

                    <?php if(isset($_SESSION['contact_success'])): ?>
                        <div class="alert alert-success border-gold mb-4">
                            Thank you! Your message has been sent. Our concierge will respond within 1 hour.
                        </div>
                        <?php unset($_SESSION['contact_success']); ?>
                    <?php endif; ?>

                    <form action="contact-process.php" method="POST" class="main_form">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <input type="text" name="name" class="contactus royal-input" placeholder="Your Full Name" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="email" name="email" class="contactus royal-input" placeholder="Email Address" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="tel" name="phone" class="contactus royal-input" placeholder="Phone Number" required>
                            </div>
                            <div class="col-12 mb-4">
                                <select name="subject" class="contactus royal-input" required>
                                    <option value="">Select Subject</option>
                                    <option value="booking">Booking Inquiry</option>
                                    <option value="wedding">Wedding & Events</option>
                                    <option value="corporate">Corporate Booking</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="feedback">Feedback</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4">
                                <textarea name="message" class="textarea royal-input" rows="6" 
                                          placeholder="Your Message..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="send_btn btn-lg w-100">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info & Map -->
            <div class="col-lg-6">
                <!-- Contact Details -->
                <div class="contact-info royal-card p-5 mb-5">
                    <h2 class="text-gold mb-5" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">
                        Visit The De Grand
                    </h2>

                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt fa-3x text-gold mb-3"></i>
                                <h5 class="text-gold">Address</h5>
                                <p class="text-light-gray">
                                    1B Felix Nsemo Drive,<br>
                                    New Secretariat, <br>
                                    Calabar 540281,<br>
                                    Cross River State, Nigeria
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="info-item">
                                <i class="fas fa-phone-alt fa-3x text-gold mb-3"></i>
                                <h5 class="text-gold">Phone</h5>
                                <p class="text-light-gray">
                                    +234 091 3531 9524<br>
                                    +234 091 3531 9524<br> 9524
                                    <small>24/7 Royal Concierge</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="info-item">
                                <i class="fas fa-envelope fa-3x text-gold mb-3"></i>
                                <h5 class="text-gold">Email</h5>
                                <p class="text-light-gray">
                                     degrandbayhotel.rooftop@gmail.xom<br>
                                    info@degrandhotel.com
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="info-item">
                                <i class="fas fa-clock fa-3x text-gold mb-3"></i>
                                <h5 class="text-gold">Reception</h5>
                                <p class="text-light-gray">
                                    Open 24 Hours<br>
                                    Check-in: 2:00 PM<br>
                                    Check-out: 12:00 PM<br>
                                    <small class="text-gold">Current Time: <span id="liveTime"></span></small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="social-links mt-4">
                        <a href="https://www.instagram.com/degrandhotel_cally?igsh=N3RuYXIwZGFkNHFy" class="me-3"><i class="fab fa-instagram fa-2x text-gold"></i></a>
                        <a href="https://vm.tiktok.com/ZSHTDN7nYdwD5-bsahl/" class="me-3"><i class="fab fa-tiktok fa-2x text-gold"></i></a>
                        <a href="https://www.facebook.com/share/1BkWTDgykV/" class="me-3"><i class="fab fa-facebook fa-2x text-gold"></i></a>
                        <a href="https://wa.me/+2349135319524" class="me-3"><i class="fab fa-whatsapp fa-2x text-gold"></i></a>
                    </div>
                </div>

                <!-- Google Map - Real De Grand Hotel Calabar Location -->
                <div class="map_main">
                    <div class="map-responsive">
                        <iframe 
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.3967608956097!2d8.3460891!3d4.9750274!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1067ae3e7917d651%3A0xb3815014a4f14f7b!2sDe%20Grand%20Hotel%20%26%20Rooftop!5e0!3m2!1sen!2sng!4v1732700000000!5m2!1sen!2sng" 
    width="100%" 
    height="400" 
    style="border:0;" 
    allowfullscreen="" 
    loading="lazy" 
    referrerpolicy="no-referrer-when-downgrade">
</iframe>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Live Time Script (Africa/Lagos) -->
<script>
function updateTime() {
    const now = new Date().toLocaleTimeString('en-US', { 
        timeZone: 'Africa/Lagos', 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit',
        hour12: true 
    });
    document.getElementById('liveTime').textContent = now;
}
updateTime();
setInterval(updateTime, 1000);
</script>

<?php include 'includes/footer.php'; ?>