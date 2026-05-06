<?php 
$page_title   = "Rooftop Lounge & Skybar - De Grand Hotel Calabar"; 
$current_page = "rooftop"; 
require_once 'includes/db_connect.php';
?>

<?php include 'includes/header.php'; ?>

<!-- FULLSCREEN HERO VIDEO + PARALLAX -->
<div class="rooftop-hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="videos/rooftop-night.mp4" type="video/mp4">
        <source src="videos/rooftop-night.webm" type="video/webm">
        Your browser no support video o!
    </video>
    
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <h1 class="hero-title">
            <span class="gold-text">THE ROOFTOP</span><br>
            <span class="thin-text">Calabar's Highest Skybar</span>
        </h1>
        <p class="hero-subtitle">360° Views • Live DJ • Premium Cocktails • VIP Tables</p>
        
        <div class="hero-cta">
            <a href="#book-table" class="btn-giant gold-pulse">
                <span>BOOK VIP TABLE NOW</span>
            </a>
        </div>
        
        <div class="scroll-hint">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>





<!-- GALLERY – ROOFTOP EDITION (100% SAME STYLE AS YOUR MAIN GALLERY PAGE) -->
<section class="gallery">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title" style="font-family: 'Playfair Display', serif; font-size: 3rem; color: #D4AF37; margin:0;">
                The Rooftop Vibe
            </h2>
            <p style="color:#94a3b8; font-size:1.4rem; margin-top:15px;">
                Where Calabar comes alive after dark
            </p>
            <div style="width:180px; height:5px; background:#D4AF37; margin:30px auto; border-radius:10px;"></div>
        </div>

        <div class="gallery-grid row g-3">
            <!-- Image 1 -->
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/rootd.jpg" alt="Rooftop Night View">
                    <div class="gallery-overlay">
                        <i class="fas fa-glass-cheers fa-3x"></i>
                        <p> Showers</p>
                    </div>
                </div>
            </div>

            <!-- Image 2 -->
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/rooftops.jpg" alt=" Performance">
                    <div class="gallery-overlay">
                        <i class="fas fa-music fa-3x"></i>
                        <p>DJ Xclusive Live</p>
                    </div>
                </div>
            </div>

            <!-- Image 3 -->
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/roofs.jpg" alt="Calabar Skyline">
                    <div class="gallery-overlay">
                        <i class="fas fa-city fa-3x"></i>
                        <p>360° Calabar Skyline</p>
                    </div>
                </div>
            </div>

            <!-- Image 4 -->
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/roofty.jpg" alt="VIP Celebration">
                    <div class="gallery-overlay">
                        <i class="fas fa-crown fa-3x"></i>
                        <p>VIP Only</p>
                    </div>
                </div>
            </div>

            
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/rooty.jpg" alt="Sunset Cocktails">
                    <div class="gallery-overlay">
                        <i class="fas fa-cocktail fa-3x"></i>
                        <p> Sessions</p>
                    </div>
                </div>
            </div>

            <!-- Image 6 -->
            <div class="col-6 col-md-4">
                <div class="gallery_img">
                    <img src="images/rootr.jpg" alt="Champagne Showers">
                    <div class="gallery-overlay">
                        <i class="fas fa-wine-bottle fa-3x"></i>
                        <p> Parade</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<a href="tel:+234 913 531 9524" style="
    position:fixed; bottom:20px; right:20px;
    background:#D4AF37; color:#000;
    padding:16px 22px; border-radius:50px;
    font-weight:800; box-shadow:0 8px 25px rgba(0,0,0,0.4);
    text-decoration:none; z-index:999;
">📞 Call Reception</a>


<?php include 'includes/footer.php'; ?>

<!-- SENIOR DEVELOPER-GRADE CSS + JS -->
<style>
    :root {
        --gold: #D4AF37;
        --gold-light: #F4D03F;
        --dark: #0F172A;
    }

    
    .rooftop-hero {
       position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('images/food2.jpg') center/cover no-repeat; /* CHANGE THIS IMAGE */
    transform: scale(1.1);
    transition: transform 10s ease;
            height: 70vh;

    }

  


    .hero-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: ;
        background: linear-gradient(to bottom, transparent 0%, var(--dark) 100%);
        z-index: 2;
    }

    .hero-content {
        position: absolute;
        top: 60%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 3;
        width: 90%;
    }

    /* .hero-logo {
        width: 220px;
        margin-bottom: 20px;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.8));
    } */

    .hero-title {
        font-family: 'Cinzel', serif;
        font-size: 3vw;
        line-height: 1.1;
        margin: 0;
    }

    .gold-text { color: var(--gold); font-weight: 900; }
    .thin-text { font-weight: 300; font-size: 0.6em; letter-spacing: 8px; }

    .hero-subtitle {
        font-size: 1.8rem;
    }

    .btn-giant {
        display: inline-block;
        padding: 20px 30px;
        font-size: 1rem;
        font-weight: 600;
        background: var(--gold);
        color: #000;
        border-radius: 60px;
        text-decoration: none;
        box-shadow: 0 20px 40px rgba(212,175,55,0.4);
        animation: goldPulse 2s infinite;
    }

    @keyframes goldPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .status-bar {
        background: rgba(15,23,42,0.95);
        padding: 15px 0;
        border-bottom: 1px solid var(--gold);
        backdrop-filter: blur(10px);
    }

    .status-item {
        display: inline-block;
        margin: 0 25px;
        color: #e2e8f0;
        font-weight: 600;
    }

    .live-pulse {
        color: #ef4444;
        animation: pulse 1.5s infinite;
    }


    /* ===== ROOFTOP GALLERY – 100% SAME AS YOUR MAIN GALLERY PAGE ===== */
.gallery {
    padding: 140px 0 120px;
 
        min-height: auto;
    margin-top: 500px;
}

.gallery-grid {
    margin: 0 -12px;
}

.gallery_img {
    position: relative;
    overflow: hidden;
    border-radius: 0;
    box-shadow: 0 15px 40px rgba(0,0,0,0.5);
    transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
    cursor: pointer;
    height: 26vh;
}

.gallery_img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.9s ease;
    filter: brightness(0.85);
}

.gallery_img:hover img {
    transform: scale(1.18);
    filter: brightness(0.6);
}

.gallery-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, rgba(15,23,42,0.92), rgba(212,175,55,0.25));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.6s ease;
    color: white;
    text-align: center;
    padding: 20px;
    backdrop-filter: blur(2px);
}

.gallery_img:hover .gallery-overlay {
    opacity: 1;
}

.gallery-overlay i {
    color: #D4AF37;
    font-size: 3.5rem;
    margin-bottom: 20px;
    text-shadow: 0 8px 30px rgba(0,0,0,0.9);
    animation: float 3s ease-in-out infinite;
}

.gallery-overlay p {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: 2px;
    margin: 0;
    text-shadow: 0 4px 15px rgba(0,0,0,0.9);
    color: #F4D03F;
}

/* Floating icon animation */
@keyframes float {
    0%, 100% { transform: translateY(0);
    50% { transform: translateY(-15px); }
}

/* Lightbox – Same as your main gallery */
.lightbox .lb-image {
    border: 10px solid #0F172A;
    box-shadow: 0 30px 80px rgba(0,0,0,0.8);
}

.lb-data .lb-caption {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    color: #D4AF37 !important;
    font-weight: 700;
}

.lb-data .lb-number {
    color: #64748b;
    font-size: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .gallery_img { height: 220px; }
    .gallery-overlay p { font-size: 1.2rem; }
    .gallery-overlay i { font-size: 2.8rem; }

    
}
    /* Add all other styles (table cards, gallery, etc.) - I fit send full CSS if you want */
</style>

<script>
    // Real-time VIP countdown
    setInterval(() => {
        const left = parseInt(document.getElementById('vip-left').textContent);
        if (left > 0 && Math.random() < 0.1) {
            document.getElementById('vip-left').textContent = left - 1;
            document.querySelector('.btn-giant').innerHTML = '<span>ALMOST GONE!</span>';
        }
    }, 8000);

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>