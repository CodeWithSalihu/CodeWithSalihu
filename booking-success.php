<?php 
session_start();
require_once 'includes/db_connect.php';

// ---------------------------
// Get booking reference
// ---------------------------
$ref = $_GET['ref'] ?? $_SESSION['last_booking_ref'] ?? null;
if (!$ref) {
    header('Location: booking.php');
    exit();
}
$_SESSION['last_booking_ref'] = $ref;

// ---------------------------
// Fetch booking from database
// ---------------------------
try {
    $stmt = $pdo->prepare("SELECT * FROM net_bookings WHERE booking_ref = ? LIMIT 1");
    $stmt->execute([$ref]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("<h1 class='text-center text-danger p-5'>Booking Not Found</h1><p>Please contact the hotel.</p>");
    }
} catch (Exception $e) {
    error_log("Booking success error: " . $e->getMessage());
    die("Error loading booking.");
}

// ---------------------------
// Defaults & formatting
// ---------------------------
$booking['adults']   = max(1, (int)($booking['adults'] ?? 1));
$booking['children'] = (int)($booking['children'] ?? 0);

$checkIn  = date('l, F j, Y', strtotime($booking['check_in']));
$checkOut = date('l, F j, Y', strtotime($booking['check_out']));
$bookedOn = date('j M Y \a\t g:ia', strtotime($booking['created_at']));

$roomName = ucwords(str_replace(['-', '_'], ' ', $booking['room_type'])) . ' Suite';

// ---------------------------
// WhatsApp message
// ---------------------------
$whatsapp_number = "2349135319524";
$whatsapp_message = rawurlencode(
    "DE GRAND HOTEL & ROOFTOP CALABAR\n\nBOOKING CONFIRMED!\n\nRef: $ref\nGuest: {$booking['full_name']}\nCheck-in: $checkIn\nCheck-out: $checkOut\nSuite: $roomName\nGuests: {$booking['adults']}" .
    ($booking['children'] > 0 ? " + {$booking['children']}" : "") . "\nNights: {$booking['nights']}\nTotal Paid: ₦" . number_format($booking['total_amount']) . "\n\nThank you for choosing luxury!\nDe Grand Hotel • Calabar"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmed #<?= htmlspecialchars($ref) ?> | De Grand Hotel Calabar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Cinzel:wght@700;900&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
:root {
    --gold: #D4AF37;
    --dark: #0F172A;
}

body {
    background: linear-gradient(135deg, #0a0e17, #1e293b);
    color: #e2e8f0;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.receipt-wrapper {
    max-width: 1000px;
    margin: 20px auto;
    padding: 40px;
    background: #fff;
    color: #000;
    border: 6px double var(--gold);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(212,175,55,0.3);
    position: relative;
}

.receipt-header {
    text-align: center;
    border-bottom: 3px solid var(--gold);
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.logo-img {
    max-height: 120px;
    width: auto;
    filter: drop-shadow(0 4px 15px gold);
}

.hotel-name {
    font-family: 'Cinzel', serif;
    font-size: 3rem;
    color: var(--gold);
    text-shadow: 0 0 15px rgba(212,175,55,0.5);
    margin: 10px 0;
}

.ref-code {
    font-family: 'Cinzel', serif;
    font-size: 2rem;
    letter-spacing: 10px;
    color: var(--gold);
    font-weight: 900;
    margin: 15px 0;
}

.confirmed-badge {
    background: linear-gradient(45deg, #D4AF37, #FFD700);
    color: #000;
    padding: 15px 60px;
    border-radius: 50px;
    font-weight: 900;
    font-size: 1.8rem;
    display: inline-block;
    margin: 15px 0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.info-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    border-left: 6px solid var(--gold);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.info-card h3 {
    color: var(--gold);
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    margin-bottom: 10px;
}

.total-box {
    background: linear-gradient(135deg, #D4AF37, #FFD700);
    color: #000;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    margin: 40px 0;
    box-shadow: 0 15px 40px rgba(212,175,55,0.5);
}

.total-amount {
    font-size: 3.5rem;
    font-weight: 900;
    letter-spacing: 4px;
    margin: 10px 0;
}

.print-btn {
    background: var(--gold);
    color: #000;
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 900;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    transition: all 0.3s;
    margin: 5px;
}

.print-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 30px rgba(212,175,55,0.5);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .receipt-wrapper {
        padding: 25px;
        margin: 15px;
    }

    .hotel-name {
        font-size: 1rem;
    }

    .ref-code {
        font-size: 1rem;
        letter-spacing: 6px;
    }

    .confirmed-badge {
        font-size: 1rem;
        padding: 12px 40px;
    }

    .info-card {
        padding: 15px;
    }

    .total-box {
        padding: 30px;
    }

    .total-amount {
        font-size: 1.5rem;
    }

    .print-btn {
        padding: 12px 5px;
        font-size: 1rem;
    }
}

@media print {
    body {
        background: white;
        margin: 0;
        padding: 20px;
    }
    .no-print { display: none !important; }
    .receipt-wrapper {
        border: 6px double #D4AF37 !important;
        box-shadow: none;
        margin: 0;
        padding: 30px;
    }
    @page { margin: 0.5cm; }
}

</style>
</head>
<body>

<canvas id="confetti-canvas" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;"></canvas>

<script>
// Auto-open WhatsApp for guest (optional — super cool)
setTimeout(() => {
    window.open('<?= $guest_wa_url ?>', '_blank');
}, 5000);
</script>

<div class="container-fluid py-5">
    <div class="receipt-wrapper" id="receiptContent">
        <!-- HEADER -->
        <div class="receipt-header">
            <img src="images/logo.jpg" alt="De Grand Hotel" class="logo-img"> 
            <h1 class="hotel-name mt-3">DE GRAND HOTEL</h1>
            <p class="fs-3">Calabar • Luxury & Rooftop Experience</p>
            <div class="confirmed-badge">BOOKING CONFIRMED</div>
        </div>

        <!-- REFERENCE -->
        <div class="text-center my-5">
            <p class="text-uppercase" style="letter-spacing: 8px; color: #666; font-weight: 700;">Your Booking Reference</p>
            <div class="ref-code"><?= htmlspecialchars($ref) ?></div>
            <p class="text-muted">Present this code at check-in • Confirmation sent to <?= htmlspecialchars($booking['email']) ?></p>
        </div>

        <!-- GUEST INFO GRID -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Guest Name</h3>
                <p class="fs-2 fw-bold text-dark"><?= htmlspecialchars($booking['full_name']) ?></p>
            </div>
            <div class="info-card">
                <h3>Contact Phone</h3>
                <p class="fs-2 fw-bold text-dark"><?= htmlspecialchars($booking['phone']) ?></p>
            </div>
            <div class="info-card">
                <h3>Check-In</h3>
                <p class="fs-1 fw-bold" style="color:var(--gold);"><?= $checkIn ?></p>
                <small>From 2:00 PM</small>
            </div>
            <div class="info-card">
                <h3>Check-Out</h3>
                <p class="fs-1 fw-bold" style="color:var(--gold);"><?= $checkOut ?></p>
                <small>Until 12:00 PM</small>
            </div>
        </div>

        <!-- ROOM DETAILS -->
        <div class="info-card text-center py-5" style="grid-column: 1 / -1;">
            <h2 class="display-4" style="color:var(--gold); font-family:'Playfair Display',serif;"><?= $roomName ?></h2>
            <p class="fs-2 fw-bold">
                <?= $booking['adults'] ?> Adult<?= $booking['adults'] > 1 ? 's' : '' ?>
                <?= $booking['children'] > 0 ? " + {$booking['children']} Child" . ($booking['children'] > 1 ? 'ren' : '') : '' ?>
                • <?= $booking['nights'] ?> Night<?= $booking['nights'] > 1 ? 's' : '' ?>
            </p>
            <?php if(!empty($booking['special_requests'])): ?>
                <p class="mt-4 fst-italic border-top pt-4">"<?= nl2br(htmlspecialchars($booking['special_requests'])) ?>"</p>
            <?php endif; ?>
        </div>

        <!-- TOTAL AMOUNT -->
        <div class="total-box">
            <p style="font-size:2.5rem; margin:0; font-weight:800;">TOTAL AMOUNT PAID</p>
            <div class="total-amount">₦<?= number_format($booking['total_amount']) ?></div>
            <p class="mt-3 fs-3">Paid on <?= $bookedOn ?></p>
        </div>

        <!-- FOOTER -->
        <div class="text-center mt-5">
            <p class="display-5" style="font-family:'Playfair Display',serif; color:var(--gold);">Welcome to Luxury</p>
            <p class="fs-3">We can't wait to host you at De Grand Hotel & Rooftop, Calabar</p>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="text-center my-5 no-print">
        <button onclick="window.print()" class="print-btn mx-3">Print Receipt</button>
        <!-- <button onclick="downloadPDF()" class="print-btn mx-3" style="background:#dc3545;">Download PDF</button> -->
        <a href="https://wa.me/<?= $whatsapp_number ?>?text=<?= $whatsapp_message ?>" target="_blank" class="print-btn mx-3" style="background:#25D366; text-decoration:none;">Share on WhatsApp</a>
        <a href="index.php" class="print-btn mx-3" style="background:transparent; color:var(--gold); border:5px solid var(--gold);">Back to Home</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    confetti({ particleCount: 300, spread: 80, origin: { y: 0.6 }, colors: ['#D4AF37', '#FFD700', '#FFFFFF', '#000000', '#FF6B6B'] });
});

function downloadPDF() {
    const element = document.getElementById('receiptContent');
    const opt = { margin: [0.3,0.3,0.3,0.3], filename: 'DeGrand-Booking-Receipt-<?= $ref ?>.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 3, useCORS: true }, jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' } };
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>
