<?php 
$page_title   = "Our Suites"; 
$current_page = "rooms"; 
require_once 'includes/db_connect.php';
// ===== FORCE FULL HOTEL MODE =====
$force_full = $pdo->query("SELECT force_full FROM hotel_system_flags WHERE id=1")->fetchColumn();

// TODAY'S DATE
$today = date('Y-m-d');

// Fetch ALL rooms from your existing table
$stmt = $pdo->query("SELECT * FROM room_inventory ORDER BY price_per_night DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get available rooms for a date range (default: tonight)
function getAvailableCount($pdo, $room_type, $checkin = null, $checkout = null) {
    if (!$checkin) $checkin = date('Y-m-d');
    if (!$checkout) $checkout = date('Y-m-d', strtotime('+1 day'));

    $checkinDate  = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
    $period       = new DatePeriod($checkinDate, new DateInterval('P1D'), $checkoutDate);

    // Get total rooms
    $stmt = $pdo->prepare("SELECT total_rooms FROM room_inventory WHERE room_type = ?");
    $stmt->execute([$room_type]);
    $total = (int)$stmt->fetchColumn();

    // Count overlapping confirmed bookings
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings 
        WHERE room_type = ? 
          AND status = 'confirmed'
          AND ? >= check_in 
          AND ? < check_out
    ");

    $max_booked = 0;
    foreach ($period as $date) {
        $dateStr = $date->format('Y-m-d');
        $check->execute([$room_type, $dateStr, $dateStr]);
        $booked = (int)$check->fetchColumn();
        if ($booked > $max_booked) $max_booked = $booked;
    }

    return max(0, $total - $max_booked);
}
?>

<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    <div class="container text-center py-5 text-white position-relative" style="z-index:2;">
        
        <h1 class="text-gold" style="font-family: 'Playfair Display', serif; font-size: 4.2rem; letter-spacing: 2px; margin:0;">
            De Grand Hotel & Rooftop
        </h1>
        
        <div style="margin:20px 0; font-size:1.4rem;">
            <span style="background:#1a5f1a; color:#fff; padding:6px 16px; border-radius:50px; font-weight:700;">
                4.0 star property
            </span>
            <span style="margin-left:15px; color:#e2e8f0;">
                Calabar hotel with 2 restaurants and indoor pool
            </span>
        </div>

        <?php 
        $available_tonight = 0;
        foreach ($rooms as $r) {
            $avail = getAvailableCount($pdo, $r['room_type']);
            if($force_full == 1){
    $avail = 0;
}

            if ($avail > 0) $available_tonight++;
        }
        ?>

        <p class="lead" style="font-size:2rem; margin-top:25px; font-weight:300;">
            <?php if ($available_tonight == 0): ?>
            <?php else: ?>
               
            <?php endif; ?>
        </p>

        <div style="width:220px; height:6px; background:#D4AF37; margin:35px auto; border-radius:10px;"></div>
      
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="inventory-wrapper" style="padding:60px 20px; background:#0F172A; min-height:100vh;">
    <div class="rooms-grid" style="max-width:1400px; margin:0 auto;">
        <?php foreach ($rooms as $r): 
            $available = getAvailableCount($pdo, $r['room_type']); // Real availability tonight
            if($force_full == 1){
    $available = 0;
}

            $status    = $available == 0 ? 'full' : ($available <= 2 ? 'critical' : 'good');
        ?>
        <div class="room-card <?= $status ?>" style="position:relative;">
            <div class="room-image">
                <?php 
                $room_images = !empty($r['image']) ? array_map('trim', explode(',', $r['image'])) : ['default-suite.jpg'];
                $first_image = $room_images[0];
                ?>
                <img src="images/<?= htmlspecialchars($first_image) ?>" alt="<?= htmlspecialchars($r['room_name']) ?>">
                <div class="price-overlay">
                    ₦<?= number_format($r['price_per_night']) ?><small>/night</small>
                </div>
            </div>

            <div class="room-header">
                <h3><?= htmlspecialchars($r['room_name']) ?></h3>
            </div>

            <div class="room-info">
                <div class="avail">
                    <span class="number <?= $available <= 2 ? 'critical' : '' ?>"><?= $available ?></span>
                    <span class="label">Available Tonight</span>
                </div>

                <div class="status-badge <?= $status ?>">
                    <?php if ($available == 0): ?>
                        FULLY BOOKED
                    <?php elseif ($available == 1): ?>
                        LAST ROOM!
                    <?php else: ?>
                        <?= $available ?> LEFT
                    <?php endif; ?>
                </div>
            </div>

            <div style="padding:20px; background:rgba(0,0,0,0.2);">
                <div style="color:#94a3b8; font-size:15px; margin-bottom:20px;">
                    Max Guests: <?= $r['max_guests'] ?> • <?= $r['size_sqm'] ?>m² • <?= ucwords(str_replace('_', ' ', $r['bed_type'])) ?>
                </div>
                <div style="margin-top:15px; color:#facc15; font-size:14px;">
                    4.8 (120 Reviews)
                </div>

                <?php if ($available > 0): ?>
                    <a href="room-details.php?type=<?= urlencode($r['room_type']) ?>" 
                       style="display:block; text-align:center; margin:15px 0; color:#D4AF37; font-weight:700; text-decoration:underline; font-size:15px;">
                       View room details
                    </a>

                    <a href="booking.php?room_type=<?= urlencode(strtolower($r['room_type'])) ?>
                        &price=<?= (int)$r['price_per_night'] ?>
                        &room_name=<?= urlencode($r['room_name']) ?>
                        &image=<?= urlencode($r['image'] ?? '') ?>#bookingForm"
                       class="btn-update">
                        <?= $available <= 2 ? 'BOOK NOW BEFORE E FINISH!' : 'Book This Suite' ?>
                    </a>
                <?php else: ?>
                    <button disabled class="btn-update" style="background:#666; cursor:not-allowed;">
                        Fully Booked Tonight
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($available_tonight == 0): ?>
        <div style="text-align:center; padding:80px 20px;">
            <h2 style="color:#D4AF37; font-family:'Playfair Display',serif; font-size:42px;">
                We're Fully Booked Tonight!
            </h2>
            <p style="color:#94a3b8; font-size:18px; margin:20px 0;">
                All suites are occupied tonight. Check other dates or call us!
            </p>
            <a href="tel:+2348066610571" style="background:#D4AF37; color:#000; padding:16px 40px; border-radius:50px; font-weight:700; font-size:18px; text-decoration:none; display:inline-block; margin-top:20px;">
                Call Front Desk Now
            </a>
        </div>
    <?php endif; ?>
</div>

<a href="tel:+2349135319524" style="position:fixed; bottom:20px; right:20px; background:#D4AF37; color:#000; padding:16px 22px; border-radius:50px; font-weight:800; box-shadow:0 8px 25px rgba(0,0,0,0.4); text-decoration:none; z-index:999;">Call Reception</a>

<?php include 'includes/footer.php'; ?>

<!-- SAME BEAUTIFUL STYLES AS BEFORE -->
<style>
    :root {
        --blue: #0A2647;
        --white: #FFFFFF;
        --gold: #D4AF37;
        --gold-light: #F4D03F;
        --silver: #CBD5E1;
        --card-bg: rgba(10, 24, 41, 0.95);
        --text: #e2e8f0;
        --muted: #94a3b8;
    }

    .inventory-wrapper {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(to bottom, var(--blue), #020617);
        padding: 60px 15px 100px;
        min-height: 100vh;
    }

    /* === DESKTOP: 3-4 CARDS PER ROW === */
    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }

/* === MOBILE-ONLY HERO SECTION – PURE LUXURY === */
@media (max-width: 768px) {
    .back_re {
        background: linear-gradient(135deg, #0A2647, #020617) !important;
        min-height: auto !important;
        padding: 120px 20px 60px !important;
    }
    .back_re .overlay {
        background: rgba(0,0,0,0.4) !important;
    }
    .back_re img {
        width: 140px !important;
        margin-bottom: 20px !important;
    }
    .back_re h1 {
        font-size: 2rem !important;
        letter-spacing: 1.8px !important;
        padding-top: 90px;
    }
    .back_re .lead {
        font-size: 1rem !important;
        line-height: 1.4 !important;
    }
    .back_re .lead span {
        font-size: 1.6rem !important;
    }
    .back_re > div > div > div:nth-child(3) span {
        display: inline-block;
        padding: 8px 18px !important;
        border-radius: 50px !important;
        background: #166534 !important;
        font-size: 0.95rem !important;
    }
    .back_re > div > div > div:nth-child(3) span + span {
        display: block;
        margin: 12px 0 0 !important;
        font-size: 1rem !important;
    }
    .back_re .container > div[style*="width:220px"] {
        width: 180px !important;
        height: 5px !important;
        margin: 28px auto !important;
    }
    .back_re p:last-of-type {
        font-size: 1rem !important;
        max-width: 90% !important;
        line-height: 1.6 !important;
    }
}


    /* === MOBILE: 1 CARD PER ROW + SMALLER SIZE === */
    @media (max-width: 768px) {
        .rooms-grid {
            grid-template-columns: 1fr;
            gap: 24px;
            padding: 0 10px;
        }

        .room-card {
            border-radius: 20px !important;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,0.6);
        }

        .room-image {
            height: 220px !important;
        }

        .price-overlay {
            bottom: 12px !important;
            left: 12px !important;
            font-size: 16px !important;
            padding: 8px 16px !important;
        }

        .room-header {
            padding: 18px !important;
            font-size: 19px !important;
        }

        .room-info {
            padding: 10px !important;
            flex-direction: row;
            gap: 6px;
            text-align: center;
        }

        .avail .number {
            font-size: 42px !important;
        }

        .status-badge {
            padding: 10px 20px !important;
            font-size: 13px !important;
        }

        .btn-update {
            padding: 10px !important;
            font-size: 14px !important;
            font-weight: 900 !important;
            border-radius: 16px !important;
        }
    }

    /* === REST OF YOUR BEAUTIFUL STYLES (UNCHANGED) === */
    .room-card {
        background: var(--card);
        backdrop-filter: blur(12px);
        border: 2px solid var(--gold);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(10, 38, 71, 0.8);
        transition: all 0.4s ease;
    }
    .room-card:hover { transform: translateY(-12px); box-shadow: 0 25px 60px rgba(212,175,55,0.35); border-color: var(--gold-light); }
    .room-card.full { border-color: #ef4444; }
    .room-card.critical { border-color: #f97316; }

    .room-image { position: relative; height: 260px; overflow: hidden; }
    .room-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .room-card:hover img { transform: scale(1.08); }

    .price-overlay {
        position: absolute;
        bottom: 15px; left: 15px;
        background: var(--gold); color: #000;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 900;
        font-size: 18px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }

    .room-header {
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        color: #000;
        padding: 24px;
        font-weight: 800;
        font-size: 22px;
        text-align: center;
    }

    .room-info {
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,0.03);
    }

    .avail .number {
        font-size: 52px;
        font-weight: 900;
        color: var(--gold);
        line-height: 1;
    }
    .avail .number.critical { color: #ef4444; animation: pulse 1.5s infinite; }
    .avail .label { display: block; font-size: 15px; color: var(--silver); margin-top: 6px; font-weight: 500; }

    .status-badge {
        background: #dc2626; color: white; padding: 12px 24px; border-radius: 50px;
        font-weight: 900; font-size: 14px; animation: pulse 2s infinite;
    }
    .status-badge.good { background: #16a34a; animation: none; }
    .status-badge.critical { background: #ea580c; }

    .btn-update {
        background: var(--gold); color: #000; border: none; padding: 18px;
        font-size: 17px; font-weight: 800; border-radius: 12px; cursor: pointer;
        text-decoration: none; text-align: center; display: block; width: 100%;
        box-shadow: 0 8px 25px rgba(212,175,55,0.4); transition: all 0.3s;
    }
    .btn-update:hover { background: var(--gold-light); transform: translateY(-4px); box-shadow: 0 15px 35px rgba(212,175,55,0.5); }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }
</style>