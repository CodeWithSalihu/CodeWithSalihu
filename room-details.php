<?php
require_once 'includes/db_connect.php';
$type = $_GET['type'] ?? '';
if (!$type) die("<h1 class='text-center text-gold py-5'>Room not found</h1>");

function getRealAvailableToday($pdo, $room_type) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT total_rooms FROM room_inventory WHERE room_type = ?");
    $stmt->execute([$room_type]);
    $total = (int)$stmt->fetchColumn();

    $check = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings
        WHERE room_type = ? AND status = 'confirmed'
          AND ? >= check_in AND ? < check_out
    ");
    $check->execute([$room_type, $today, $today]);
    $booked = (int)$check->fetchColumn();
    return max(0, $total - $booked);
}

$stmt = $pdo->prepare("SELECT * FROM room_inventory WHERE room_type = ? LIMIT 1");
$stmt->execute([$type]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$room) die("<h1 class='text-center text-gold py-5'>Room not found</h1>");

$available_today = getRealAvailableToday($pdo, $room['room_type']);
$images = !empty($room['image']) ? array_filter(array_map('trim', explode(',', $room['image']))) : ['default-suite.jpg'];
if (empty($images)) $images = ['default-suite.jpg'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">

    <title><?= htmlspecialchars($room['room_name']) ?> • De Grand Hotel</title>

    <!-- Font Awesome + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --gold: #D4AF37;
            --deep: #B8860B;
            --dark: #0a0a0a;
        }
        body { margin:0; font-family:'Inter',sans-serif; background:#000; color:#fff; overflow-x:hidden; }
        .text-gold { color:var(--gold) !important; }
        .bg-dark { background:#000 !important; }

        /* === HERO GALLERY – FULL SCREEN MOBILE === */
        .hero-gallery {
            height:100vh;
            position:relative;
            overflow:hidden;
        }
        .hero-slide {
            position:absolute;
            inset:0;
            background:#000;
            opacity:0;
            transition:opacity 1.2s ease;
        }
        .hero-slide.active { opacity:1; }
        .hero-slide img {
            width:100%; height:auto; object-fit:cover; object-position:center;
        }

        /* GOLD OVERLAY + CONTENT */
        .hero-content {
            position:absolute;
            bottom:0; left:0; right:0;
            padding:120px 20px 80px;
            text-align:center;
        }
        .room-title {
            font-family:'Playfair Display',serif;
            font-size:3rem;
            font-weight:900;
            margin:0;
            text-shadow:0 4px 20px rgba(0,0,0,0.8);
        }
        .room-price {
            font-size:2.8rem;
            font-weight:900;
            margin:15px 0 10px;
        }
        .availability {
            font-size:1.4rem;
            font-weight:700;
            padding:10px 20px;
            border-radius:50px;
            display:inline-block;
            margin:15px 0;
            background:rgba(0,0,0,0.5);
        }
        .avail-low { background:#dc2626 !important; animation:pulse 1.8s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }

        /* BOOK BUTTON – BIG, BOLD, UNMISSABLE */
        .btn-book-mobile {
          
    background: linear-gradient(135deg, var(--deep), var(--gold));
    color: #000;
    font-size: 1.rem;
    font-weight: 900;
    padding: 20px 20px;
    border-radius: 50px;
    border: none;
    box-shadow: 0 15px 40px rgba(212, 175, 55, 0.5);
    display: block;
    max-width: 400px;
    margin: 30px auto 0;
    transform: translateY(20px);
    opacity: 0;
    animation: slideUp 1s forwards 0.6s;
    outline-style: none;
}
        
        .btn-book-mobile:hover {
            transform:translateY(-8px) scale(1.05);
            box-shadow:0 25px 60px rgba(212,175,55,0.7);
            color:#000;
        }
        @keyframes slideUp {
            to { transform:translateY(0); opacity:1; }
        }

        /* THUMBNAILS – HIDDEN ON MOBILE, VISIBLE ON TABLET+ */
        .thumbnails { display:none; }
        @media (min-width:768px) {
            .thumbnails { display:flex; gap:10px; padding:20px; background:#111; overflow-x:auto; }
            .thumb { height:90px; width:120px; object-fit:cover; border:4px solid transparent; border-radius:12px; cursor:pointer; transition:all 0.3s; flex-shrink:0; }
            .thumb.active, .thumb:hover { border-color:var(--gold); transform:scale(1.08); }
            
            



            
            
            
        }

        /* MAIN CONTENT – CLEAN & SPACED */
        .content-section {
            padding:60px 20px;
            background:#fff;
            color:#000;
        }
        .specs-grid {
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:20px;
            margin:40px 0;
        }
        .spec-item {
            text-align:center;
            padding:20px;
            background:#f8f9fa;
            border-radius:16px;
        }
        .spec-item i {
            font-size:2.5rem;
            color:var(--gold);
            margin-bottom:15px;
        }

        /* FIXED BOOK BUTTON (BOTTOM) */
        .fixed-book {
            position:fixed;
            bottom:20px;
            left:50%;
            transform:translateX(-50%);
            background:linear-gradient(135deg,var(--deep),var(--gold));
            color:#000;
            width:90%;
            max-width:420px;
            padding:18px;
            border-radius:50px;
            font-size:1.5rem;
            font-weight:900;
            z-index:999;
            box-shadow:0 10px 40px rgba(0,0,0,0.6);
            text-align:center;
            animation:float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform:translateX(-50%) translateY(0); }
            50% { transform:translateX(-50%) translateY(-10px); }
        }

        /* HIDE DESKTOP SIDEBAR ON MOBILE */
        .desktop-sidebar { display:none; }
        @media (min-width:992px) {
            .desktop-sidebar { display:block; }
            .fixed-book { display:none; }

       
       
       @media (max-width: 768px) {
   
 .hero-slide img {
            height:70%;
        }
    
}
    </style>
</head>
<body>

<!-- FULL SCREEN HERO GALLERY -->
<div class="hero-gallery">
    <?php foreach ($images as $i => $img): ?>
        <div class="hero-slide <?= $i===0?'active':'' ?>" id="hero<?= $i ?>">
            <img src="images/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>">
        </div>
    <?php endforeach; ?>

    <div class="hero-content">
        <h1 class="room-title text-gold"><?= htmlspecialchars($room['room_name']) ?></h1>
        <div class="room-price text-gold">₦<?= number_format($room['price_per_night']) ?><small style="font-size:1.2rem"> / night</small></div>
        <div class="availability <?= $available_today <= 2 ? 'avail-low' : '' ?>">
            Only <?= $available_today ?> room<?= $available_today==1?'':'s' ?> left today
        </div>

        <?php if ($available_today > 0): ?>
            <a href="booking.php?room_type=<?= urlencode($room['room_type']) ?>&price=<?= (int)$room['price_per_night'] ?>"
               class="btn-book-mobile">
                <?= $available_today <= 2 ? 'ALMOST GONE — BOOK NOW!' : 'Reserve This Suite' ?>
            </a>
        <?php else: ?>
            <div class="btn-book-mobile" style="background:#666;cursor:not-allowed;">
                Fully Booked Today
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- THUMBNAILS (Tablet & Up) -->
<div class="thumbnails">
    <?php foreach ($images as $i => $img): ?>
        <img src="images/<?= htmlspecialchars($img) ?>" class="thumb <?= $i===0?'active':'' ?>"
             onclick="showHeroSlide(<?= $i ?>)">
    <?php endforeach; ?>
</div>

<!-- MAIN CONTENT -->
<div class="content-section">
    <div class="container">
        <h2 class="display-5 fw-bold text-center mb-5">Luxury Redefined</h2>
        <p class="lead text-center mx-auto" style="max-width:700px; line-height:1.9; color:#444;">
            <?= nl2br(htmlspecialchars($room['description'] ?? 'Indulge in unparalleled elegance with panoramic views, premium king bedding, Italian marble bathroom, smart automation, and exclusive access to our rooftop infinity pool and lounge.')) ?>
        </p>

        <h3 class="mt-5 mb-4 text-center fw-bold">Room Features</h3>
        <div class="specs-grid">
            <div class="spec-item">
                <i class="fas fa-users"></i>
                <div>Sleeps <?= $room['max_guests'] ?></div>
            </div>
            <div class="spec-item">
                <i class="fas fa-bed"></i>
                <div><?= ucwords(str_replace('_',' ',$room['bed_type'])) ?></div>
            </div>
            <div class="spec-item">
                <i class="fas fa-expand-arrows-alt"></i>
                <div><?= $room['size_sqm'] ?> m²</div>
            </div>
            <div class="spec-item">
                <i class="fas fa-eye"></i>
                <div>City View</div>
            </div>
            <div class="spec-item">
                <i class="fas fa-wifi"></i>
                <div>Ultra-fast WiFi</div>
            </div>
            <div class="spec-item">
                <i class="fas fa-bath"></i>
                <div>Marble Bath</div>
            </div>
        </div>
    </div>
</div>



<script>
// Mobile hero gallery auto-rotate
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const thumbs = document.querySelectorAll('.thumb');

function showHeroSlide(n) {
    slides.forEach(s => s.classList.remove('active'));
    thumbs.forEach(t => t.classList.remove('active'));
    slides[n].classList.add('active');
    if(thumbs[n]) thumbs[n].classList.add('active');
}

setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showHeroSlide(currentSlide);
}, 6000);

// Thumb click
thumbs.forEach((thumb, i) => {
    thumb.addEventListener('click', () => {
        currentSlide = i;
        showHeroSlide(i);
    });
});
</script>

</body>
</html>