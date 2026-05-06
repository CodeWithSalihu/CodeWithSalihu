<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
$success = $error = $last_booking_ref = '';
$check_in_default = $_POST['check_in'] ?? date('Y-m-d');
$check_out_default = $_POST['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
// REAL AVAILABILITY FUNCTION
function isRoomAvailable($pdo, $room_type, $check_in, $check_out) {
    $start = new DateTime($check_in);
    $end = new DateTime($check_out);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);
    $total_stmt = $pdo->prepare("SELECT total_rooms FROM room_inventory WHERE room_type = ?");
    $total_stmt->execute([$room_type]);
    $total_rooms = (int)$total_stmt->fetchColumn();
    $booked_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings
        WHERE room_type = ?
          AND status IN ('confirmed', 'checked_in')
          AND ? >= check_in
          AND ? < check_out
    ");
    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        $booked_stmt->execute([$room_type, $date_str, $date_str]);
        if ((int)$booked_stmt->fetchColumn() >= $total_rooms) {
            return false;
        }
    }
    return true;
}

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $full_name = trim($_POST['full_name']);
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $email = trim($_POST['email'] ?? '');
    $room_id = (int)$_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $nights = (int)$_POST['nights'];
    $total_amount = (int)$_POST['total_amount'];
    $payment_method = $_POST['payment_method'];
    $booking_ref = 'DEGRAND' . date('Ymd') . rand(100, 999);

    if (empty($full_name) || empty($phone) || empty($check_in) || empty($check_out) || empty($room_id) || $nights <= 0) {
        $error = "All required fields must be filled correctly!";
    } else {
        $stmt = $pdo->prepare("SELECT room_type, room_name, price_per_night FROM room_inventory WHERE id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room || !isRoomAvailable($pdo, $room['room_type'], $check_in, $check_out)) {
            $error = "Sorry, this suite is no longer available for the selected dates.";
        } else {
            $proof_path = '';
            if ($_FILES['payment_proof']['error'] === 0) {
                $ext = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                if (in_array($ext, $allowed) && $_FILES['payment_proof']['size'] < 5_000_000) {
                    if (!is_dir('../proofs')) mkdir('../proofs', 0755, true);
                    $proof_path = "proofs/" . $booking_ref . ".$ext";
                    move_uploaded_file($_FILES['payment_proof']['tmp_name'], "../$proof_path");
                } else {
                    $error = "Invalid proof file! Only JPG, PNG, PDF < 5MB";
                }
            }

            if (empty($error)) {
                try {
                    $pdo->beginTransaction();
                    $sql = "INSERT INTO net_bookings (
                        booking_ref, full_name, email, phone, country,
                        check_in, check_out, nights, room_type,
                        adults, children, total_amount, payment_method,
                        payment_proof, status, booked_by
                    ) VALUES (
                        ?, ?, ?, ?, 'NG',
                        ?, ?, ?, ?,
                        2, 0, ?, ?,
                        ?, 'confirmed', 'walkin'
                    )";
                    $pdo->prepare($sql)->execute([
                        $booking_ref, $full_name, $email, $phone,
                        $check_in, $check_out, $nights, $room['room_type'],
                        $total_amount, $payment_method, $proof_path
                    ]);
                    $pdo->commit();
                    $success = "Walk-in booking confirmed!";
                    $last_booking_ref = $booking_ref;

                    // Prepare receipt data
                    $receipt_data = [
                        'booking_ref' => $booking_ref,
                        'full_name' => $full_name,
                        'phone' => $phone,
                        'email' => $email,
                        'room_name' => $room['room_name'],
                        'price_per_night' => $room['price_per_night'],
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                        'nights' => $nights,
                        'total_amount' => $total_amount,
                        'payment_method' => strtoupper($payment_method),
                        'booking_date' => date('Y-m-d H:i')
                    ];
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Booking failed. Please try again.";
                }
            }
        }
    }
}

// Fetch rooms
$rooms = $pdo->query("SELECT id, room_type, room_name, price_per_night, total_rooms FROM room_inventory ORDER BY price_per_night DESC")->fetchAll(PDO::FETCH_ASSOC);
$available_rooms = array_filter($rooms, fn($r) => isRoomAvailable($pdo, $r['room_type'], $check_in_default, $check_out_default));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
    <title>Walk-in Booking • De Grand Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --gold: #D4AF37; --dark: #0a0e17; --green: #22c55e; --red: #dc2626; }
        body { background: var(--dark); color: #e2e8f0; font-family: 'Inter', sans-serif; margin:0; }
        .header { text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #000, #111); border-bottom: 10px solid var(--gold); }
        .header img { height: 130px; filter: drop-shadow(0 0 60px gold); border-radius: 50%; border: 6px solid var(--gold); }
        .page-title { font-family: 'Cinzel', serif; font-size: 4rem; color: var(--gold); letter-spacing: 12px; text-shadow: 0 0 50px rgba(212,175,55,0.8); margin: 20px 0; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
           .back-btn { display: inline-block; background: #333; color: white; padding: 16px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 2rem; }
        .back-btn:hover { background: var(--gold); color: #000; }
        .form-card { background: #111; border: 5px solid var(--gold); border-radius: 30px; padding: 50px; box-shadow: 0 30px 100px rgba(212,175,55,0.25); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 30px; }
        label { color: var(--gold); font-weight: 900; font-size: 1.1rem; margin-bottom: 10px; display: block; }
        input, select { padding: 18px; background: #000; border: 2px solid #333; color: white; border-radius: 15px; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--gold); box-shadow: 0 0 0 4px rgba(212,175,55,0.3); outline: none; }
        .price-box { background: linear-gradient(135deg, var(--gold), #f4d03f); color: #000; padding: 20px; text-align: center; border-radius: 25px; font-size: 3rem; font-weight: 900; margin: 30px 0; }
        .upload-area { border: 4px dashed var(--gold);  text-align: center; border-radius: 25px; cursor: pointer; background: rgba(212,175,55,0.1); transition: 0.4s; font-size: 1rem; }
        .upload-area:hover { background: rgba(212,175,55,0.2); transform: scale(1.02); }
        .btn { padding: 22px 60px; border: none; border-radius: 50px; font-size: 1.4rem; font-weight: 900; cursor: pointer; transition: all 0.4s; margin: 10px; }
        .btn-success { background: var(--green); color: white; }
        .btn-print { background: var(--gold); color: #000; }
        .btn:hover { transform: translateY(-8px) scale(1.05); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .alert { padding: 40px; border-radius: 25px; text-align: center; font-size: 1.8rem; font-weight: bold; margin: 30px 0; border: 5px solid; }
        .alert-success { background: rgba(34,197,94,0.2); color: #86efac; border-color: var(--green); }
        .alert-error { background: rgba(220,38,38,0.2); color: #fca5a5; border-color: var(--red); }
        .nights-display { background: linear-gradient(135deg, #1a1a1a, #000); border: 3px solid var(--gold); color: var(--gold); font-size: 2.2rem; font-weight: 900; text-align: center; padding: 20px; border-radius: 25px; box-shadow: 0 0 30px rgba(212,175,55,0.3); }
        .nights-label { font-size: 1.1rem; color: #aaa; margin-bottom: 8px; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 1; }

        /* Receipt Styles */
        #receipt {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            color: black;
            padding: 50px;
            border: 8px double var(--gold);
            border-radius: 15px;
            font-family: 'Georgia', serif;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }
        #receipt .logo { text-align: center; margin-bottom: 30px; }
        #receipt .logo img { height: 100px; border-radius: 50%; border: 5px solid var(--gold); }
        #receipt h1 { font-family: 'Cinzel', serif; text-align: center; color: var(--gold); font-size: 2.8rem; margin: 20px 0; }
        #receipt .ref { font-size: 1.8rem; text-align: center; font-weight: bold; color: #000; margin: 20px 0; }
        #receipt table { width: 100%; margin: 30px 0; border-collapse: collapse; }
        #receipt td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
        #receipt td:first-child { font-weight: bold; width: 40%; color: #444; }
        #receipt .total { font-size: 1.8rem; font-weight: 900; color: var(--gold); background: #f9f9f9; }
        #receipt .footer { text-align: center; margin-top: 50px; font-style: italic; color: #555; }

        @media print {
            body * { visibility: hidden; }
            #receipt, #receipt * { visibility: visible; }
            #receipt { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; padding: 30px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../images/logo.jpg" alt="De Grand Hotel">
    <h1 class="page-title">WALK-IN BOOKING</h1>
    <p style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #94a3b8; margin:10px;">Instant Luxury Check-in</p>
</div>

<div class="container">
    <a href="index.php" class="back-btn">Back to Room Control</a>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success && !empty($receipt_data)): ?>
        <div class="alert alert-success"><?= $success ?> (Ref: <?= htmlspecialchars($last_booking_ref) ?>)</div>

        <!-- RECEIPT SECTION -->
        <div id="receipt">
            <div class="logo">
                <img src="../images/logo.jpg" alt="De Grand Hotel">
                <h1>DE GRAND HOTEL</h1>
                <p style="font-size:1.2rem; color:#555;">Luxury Redefined • Abuja, Nigeria</p>
            </div>

            <div class="ref">BOOKING RECEIPT - <?= htmlspecialchars($receipt_data['booking_ref']) ?></div>

            <table>
                <tr><td>Guest Name</td><td><?= htmlspecialchars($receipt_data['full_name']) ?></td></tr>
                <tr><td>Phone</td><td><?= htmlspecialchars($receipt_data['phone']) ?></td></tr>
                <?php if (!empty($receipt_data['email'])): ?>
                    <tr><td>Email</td><td><?= htmlspecialchars($receipt_data['email']) ?></td></tr>
                <?php endif; ?>
                <tr><td>Suite</td><td><?= htmlspecialchars($receipt_data['room_name']) ?></td></tr>
                <tr><td>Check-in</td><td><?= date('d M Y', strtotime($receipt_data['check_in'])) ?></td></tr>
                <tr><td>Check-out</td><td><?= date('d M Y', strtotime($receipt_data['check_out'])) ?></td></tr>
                <tr><td>Nights</td><td><?= $receipt_data['nights'] ?> night(s)</td></tr>
                <tr><td>Rate per Night</td><td>₦<?= number_format($receipt_data['price_per_night']) ?></td></tr>
                <tr><td>Payment Method</td><td><?= $receipt_data['payment_method'] ?></td></tr>
                <tr class="total"><td>TOTAL AMOUNT PAID</td><td>₦<?= number_format($receipt_data['total_amount']) ?></td></tr>
                <tr><td>Booking Date & Time</td><td><?= date('d M Y, h:i A', strtotime($receipt_data['booking_date'])) ?></td></tr>
                <tr><td>Status</td><td><strong>CONFIRMED (Walk-in)</strong></td></tr>
            </table>

            <div class="footer">
                Thank you for choosing De Grand Hotel.<br>
                We wish you a pleasant and luxurious stay.<br><br>
                For inquiries: +234 913 531 9524 | degrandbayhotel.rooftop@gmail.com

            </div> 
        </div>

        <div style="text-align:center; margin: 30px 0;">
            <button onclick="window.print()" class="btn btn-print no-print">
                <i class="fas fa-print"></i> PRINT RECEIPT
            </button>
            <a href="walkin-booking.php" class="btn btn-success no-print">
                <i class="fas fa-plus"></i> NEW WALK-IN BOOKING
            </a>
        </div>

    <?php else: ?>
        <!-- Original Booking Form -->
        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" id="bookingForm">
                <div class="form-grid">
                    <div><label>Full Name *</label><input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"></div>
                    <div><label>Phone Number *</label><input type="tel" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
                    <div><label>Email (optional)</label><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
                    <div>
                        <label>Check-in Date *</label>
                        <input type="date" name="check_in" required min="<?= date('Y-m-d') ?>" value="<?= $check_in_default ?>" onchange="calculate()">
                    </div>
                    <div>
                        <label>Check-out Date *</label>
                        <input type="date" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= $check_out_default ?>" onchange="calculate()">
                    </div>
                    <div>
                        <div class="nights-label">Total Nights</div>
                        <div class="nights-display">
                            <span id="nights_display">0</span> <small style="font-size:1rem;">night(s)</small>
                        </div>
                        <input type="hidden" name="nights" id="nights_hidden">
                    </div>
                    <div>
                        <label>Available Suites *</label>
                        <select name="room_id" id="room_select" required onchange="calculate()">
                            <option value="">Select dates first...</option>
                            <?php foreach ($available_rooms as $r):
                                $left_today = ($check_in_default === date('Y-m-d')) ?
                                    ($r['total_rooms'] - $pdo->query("SELECT COUNT(*) FROM net_bookings WHERE room_type='{$r['room_type']}' AND status IN ('confirmed','checked_in') AND '$check_in_default' >= check_in AND '$check_in_default' < check_out")->fetchColumn()) : $r['total_rooms'];
                                $badge = $left_today == 0 ? 'avail-none' : ($left_today <= 2 ? 'avail-low' : 'avail-good');
                                $text = $left_today == 0 ? 'FULL' : ($left_today == 1 ? 'LAST ONE!' : "$left_today LEFT");
                            ?>
                                <option value="<?= $r['id'] ?>" data-price="<?= $r['price_per_night'] ?>">
                                    <?= $r['room_name'] ?> — ₦<?= number_format($r['price_per_night']) ?>/night
                                    <span class="availability-badge <?= $badge ?>"><?= $text ?></span>
                                </option>
                            <?php endforeach; ?>
                            <?php if (empty($available_rooms)): ?>
                                <option disabled>No suites available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label>Payment Method *</label>
                        <select name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="pos">POS</option>
                        </select>
                    </div>
                </div>

                <div class="price-box" id="totalDisplay">₦0</div>
                <input type="hidden" name="total_amount" id="total_amount">

               <div style="margin:40px 0;"> 
    <label>Upload Payment Proof *</label>

    <div class="upload-area" onclick="document.getElementById('proof').click()">
        <i class="fas fa-cloud-upload-alt" style="font-size:3rem; color:var(--gold);"></i><br><br>
        <strong>Click to upload receipt</strong><br>
        <span style="font-size:0.9rem; color:#94a3b8;">JPG, PNG, PDF • Max 5MB</span>
        <input type="file" name="payment_proof" id="proof" required accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="previewProof(this)">
    </div>

    <!-- PREVIEW BOX -->
    <div id="proofPreview" style="margin-top:20px; display:none;">
        <label style="color:var(--gold); font-weight:bold;">Preview:</label>
        <div id="previewBox" 
             style="background:#000; padding:20px; border:2px solid var(--gold); border-radius:15px; text-align:center;">
        </div>
    </div>
</div>


                <button type="submit" name="submit_booking" class="btn btn-success">COMPLETE WALK-IN BOOKING</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
function calculate() {
    const cin = document.querySelector('[name="check_in"]').value;
    const cout = document.querySelector('[name="check_out"]').value;
    const select = document.getElementById('room_select');
    const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
    let nights = 0;
    let total = 0;
    if (cin && cout && cout > cin) {
        nights = Math.round((new Date(cout) - new Date(cin)) / 86400000);
        total = nights * price;
        document.getElementById('nights_display').textContent = nights;
        document.getElementById('nights_hidden').value = nights;
        document.getElementById('totalDisplay').textContent = '₦' + total.toLocaleString();
        document.getElementById('total_amount').value = total;
    } else {
        document.getElementById('nights_display').textContent = '0';
        document.getElementById('totalDisplay').textContent = '₦0';
    }
}

document.addEventListener('DOMContentLoaded', calculate);
document.querySelectorAll('[name="check_in"], [name="check_out"], #room_select').forEach(el => el.addEventListener('change', calculate));
</script>



<script>
function previewProof(input) {
    const file = input.files[0];
    const previewBox = document.getElementById('previewBox');
    const previewContainer = document.getElementById('proofPreview');

    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();

    previewContainer.style.display = 'block';
    previewBox.innerHTML = ""; // clear previous preview

    if (['jpg','jpeg','png'].includes(ext)) {
        // IMAGE preview
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.maxWidth = "100%";
        img.style.borderRadius = "10px";
        img.style.border = "3px solid var(--gold)";
        previewBox.appendChild(img);

    } else if (ext === 'pdf') {
        // PDF preview
        previewBox.innerHTML = `
            <iframe src="${URL.createObjectURL(file)}" 
                style="width:100%; height:500px; border:3px solid var(--gold); border-radius:10px;">
            </iframe>
        `;
    } else {
        previewBox.innerHTML = "<p style='color:red;'>Unsupported file type</p>";
    }
}
</script>

</body>
</html>