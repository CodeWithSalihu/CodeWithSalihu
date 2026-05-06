 <?php
session_start();
$page_title = "Book Your Stay";
$current_page = "booking";
include 'includes/header.php';
require_once 'includes/db_connect.php';
// ✅ SECURITY: Initialize CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// REAL AVAILABILITY FUNCTION (NO FAKE occupied_rooms!)
function getRealAvailableToday($pdo, $room_type) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT total_rooms FROM room_inventory WHERE room_type = ?");
    $stmt->execute([$room_type]);
    $total = (int)$stmt->fetchColumn();

    $check = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings 
        WHERE room_type = ? 
          AND status = 'confirmed'
          AND ? >= check_in 
          AND ? < check_out
    ");
    $check->execute([$room_type, $today, $today]);
    $booked = (int)$check->fetchColumn();

    return max(0, $total - $booked);
}

// Fetch rooms
$stmt = $pdo->query("SELECT room_type, room_name, price_per_night, image FROM room_inventory ORDER BY price_per_night DESC");
$live_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build REAL availability
$availability = [];
foreach ($live_rooms as $r) {
    $real_avail = getRealAvailableToday($pdo, $r['room_type']);
    $availability[$r['room_type']] = [
        'available' => $real_avail,
        'name'      => $r['room_name'],
        'price'     => (int)$r['price_per_night'],
        'image'     => $r['image']
    ];
}
// ────────────────────── PRE-SELECTION FROM rooms.php ──────────────────────
$preselected = [
    'room_type' => '',
    'price' => 0,
    'name' => '',
    'image' => ''
];
if (!empty($_GET['room_type'])) {
    $type = strtolower(trim($_GET['room_type']));
    $price = (int)preg_replace('/[^0-9]/', '', $_GET['price'] ?? '0');
    if (isset($availability[$type]) && $availability[$type]['price'] == $price && $availability[$type]['available'] > 0) {
        $preselected = [
            'room_type' => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
            'price' => $price,
            'name' => htmlspecialchars($availability[$type]['name'], ENT_QUOTES, 'UTF-8'),
            'image' => htmlspecialchars($availability[$type]['image'], ENT_QUOTES, 'UTF-8')
        ];
    }
}
$selected_room = htmlspecialchars($preselected['room_type'], ENT_QUOTES, 'UTF-8');
// ✅ SECURITY: Get Paystack Public Key from environment
$paystack_public_key = getenv('PAYSTACK_PUBLIC_KEY');
if (!$paystack_public_key) {
    error_log("Paystack public key not configured");
    $paystack_public_key = ''; // Fallback - will show error to user
}
?>
<!-- Page Header -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title text-center py-5">
                    <h1 class="display-2 text-gold mb-3" style="font-family: 'Playfair Display', serif;">
                        Reserve Your Palace
                    </h1>
                    <p class="lead text-white opacity-9">Live Availability • Instant Booking • Zero Overbooking</p>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="booking-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="booking-card ">
                                       <div style="background:#1e293b; border:2px solid #D4AF37; padding:1px; border-radius:12px; text-align:center; margin-bottom:5px; font-weight:600;  font-size:20px;">

                        Live Availability updates every <span id="refreshCounter">20</span> seconds — never miss a room!
                    </div>
                    <!-- PRE-SELECTED BANNER -->
                    <?php if ($preselected['room_type']): ?>
                    <div class="alert alert-success border border-gold p-4 mb-4 rounded-4 text-center shadow-lg">
                        <h4 class="text-gold mb-2">Selected Suite:</h4>
                        <h3 class="fw-bold text-white mb-1"><?= $preselected['name'] ?></h3>
                        <p class="mb-0 fs-4 text-gold">₦<?= number_format($preselected['price']) ?>/night</p>
                        <small class="text-success">
                            Only <?= $availability[$preselected['room_type']]['available'] ?> left!
                        </small>
                    </div>
                    <?php endif; ?>
                   <form action="booking-process.php" method="POST" id="bookingForm">
                       <!-- ✅ SECURITY: CSRF Token -->
                       <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                      
    <div class="row">
        <!-- Dates -->
        <div class="col-lg-6 mb-4">
            <label class="text-gold fw-bold mb-3">Check-in Date</label>
            <input type="date" name="check_in" class="form-control royal-input" required
                   min="<?= date('Y-m-d') ?>" id="checkIn">
        </div>
        <div class="col-lg-6 mb-4">
            <label class="text-gold fw-bold mb-3">Check-out Date</label>
            <input type="date" name="check_out" class="form-control royal-input" required
                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" id="checkOut">
        </div>
        <!-- ROOM SELECTION -->
        <div class="col-lg-6 mb-4">
            <label class="text-gold fw-bold mb-3">Select Your Suite</label>
         
   <select name="room_type" class="form-control royal-input" required id="roomType">
    <option value="">Choose Available Suite</option>
   <?php foreach ($live_rooms as $room): 
    $type = $room['room_type'];
    $real_available = getRealAvailableToday($pdo, $type);
    $disabled = $real_available <= 0 ? 'disabled' : '';
    $label = $real_available <= 0 ? " [SOLD OUT]" : " • {$real_available} left";
?>
    <option value="<?= htmlspecialchars($type) ?>"
        data-price="<?= (int)$room['price_per_night'] ?>"
        data-available="<?= $real_available ?>"
        <?= $preselected['room_type'] === $type ? 'selected' : '' ?>
        <?= $disabled ?>>
        <?= htmlspecialchars($room['room_name']) ?> — ₦<?= number_format((int)$room['price_per_night']) ?>/night<?= $label ?>
    </option>
<?php endforeach; ?>
</select>
          
            <div class="mt-2">
                <small class="text-danger" id="availabilityWarning" style="display:none;">
                    This suite is no longer available!
                </small>
            </div>
        </div>
        <!-- Guests -->
        <div class="col-lg-3 col-md-6 mb-4">
            <label class="text-gold fw-bold mb-3">Adults</label>
            <select name="adults" class="form-control royal-input" required>
                <?php for($i=1; $i<=8; $i++): ?>
                    <option value="<?= $i ?>" <?= $i==1?'selected':'' ?>><?= $i ?> Adult<?= $i>1?'s':'' ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <label class="text-gold fw-bold mb-3">Children</label>
            <select name="children" class="form-control royal-input">
                <option value="0">0 Children</option>
                <?php for($i=1; $i<=6; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> Child<?= $i>1?'ren':'' ?></option>
                <?php endfor; ?>
            </select>
        </div>
      
        <div class="col-lg-6 mb-4"><input type="text" name="full_name" placeholder="Full Name" class="form-control royal-input" required maxlength="100"></div>
        <div class="col-lg-6 mb-4"><input type="email" name="email" placeholder="Email Address" class="form-control royal-input" required maxlength="255"></div>
        <div class="col-lg-6 mb-4"><input type="tel" name="phone" placeholder="Phone Number" class="form-control royal-input" required maxlength="20"></div>
        <div class="col-lg-6 mb-4">
            <select name="country" class="form-control royal-input" required>
                <option value="">Select Country</option>
                <option value="NG" selected>Nigeria</option>
                <option value="GH">Ghana</option>
                <option value="US">United States</option>
                <option value="UK">United Kingdom</option>
                <option value="UAE">Dubai (UAE)</option>
            </select>
        </div>
        <div class="col-12 mb-4">
            <textarea name="special_requests" rows="4" class="form-control royal-input"
                      placeholder="Special Requests (Champagne, airport transfer, etc.)" maxlength="500"></textarea>
        </div>
        <!-- Hidden Fields for Pricing -->
        <input type="hidden" name="nights" id="nightsInput" value="0">
        <input type="hidden" name="total_amount" id="totalInput" value="0">
       <!-- Price Summary and Submit -->
<div class="col-12">
    <div class="price-summary p-4 mb-4">
        <h4 class="text-gold mb-4">Booking Summary</h4>
        <div class="d-flex justify-content-between mb-2"><span>Room Rate:</span> <strong id="roomRate">₦0</strong></div>
        <div class="d-flex justify-content-between mb-2"><span>Nights:</span> <strong id="nightsCount">0</strong></div>
        <hr class="bg-gold">
        <div class="d-flex justify-content-between">
            <h5 class="text-gold">Total Amount:</h5>
            <h4 class="text-gold fw-bold" id="totalAmount">₦0</h4>
        </div>
        <div class="policy-box" style="background:#1e293b; border-left:4px solid #D4AF37; border-radius:10px;">
    <h4 class="text-gold mb-3" style="font-family: 'Playfair Display', serif;">DE GRAND HOTEL & ROOFTOP — REFINEMENT & POLICIES</h4>
    <ul class="text-light-gray" style="line-height: 1.9; font-size: 16px; padding-left: 18px;">
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> All rates are exclusive of buffet breakfast for one guest. Additional guest attracts an extra charge of ₦10,000 for breakfast.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Check-in time is 2pm. Payment validates reservation.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Check-out time is 12 noon. 50% of room rate is charged for check-outs between 12pm and 4pm. After 5pm attracts a full day's payment.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Smoking is strictly prohibited in all rooms, indoor areas and the Rooftop. A charge of ₦100,000 applies if found smoking.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Pets are not permitted within the hotel premises.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Bringing large quantities of food or drinks from outside is not allowed.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Loud music and speakers in rooms should be avoided. No disruptive gatherings or conflicts allowed.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Hotel properties must remain in original condition. Stickers, decorations or markings are not allowed. Damages or excessive stains will be charged.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Unregistered guests (visitors) are not permitted in rooms beyond designated hours.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Guests must dress decently in shared areas. Swimwear is allowed only at the pool area.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Lodging underage guests is strictly prohibited.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Cancellation less than 7 days before arrival attracts a full night charge.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> No-show attracts a full charge of the reservation.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> Rooms not occupied by 10pm will be marked as no-show unless prior arrangement exists.</li>
    <li><span style="font-size: 22px; font-weight: bold; margin-right: 6px;">✔</span> A valid means of identification is required at check-in.</li>
</ul>
</div>
    </div>
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="terms" required>
                <label class="form-check-label text-light-gray" for="terms">
                    I agree to the <a href="#" class="text-gold">Terms & Conditions</a> and
                    <a href="#" class="text-gold">Cancellation Policy</a>
                </label>
            </div>
            <button type="button" id="paystackButton" class="btn btn-gold btn-lg w-100 py-3 rounded-0 shadow-lg" disabled>
                SELECT A SUITE TO CONTINUE
            </button>
        </div>
    </div>
</form>
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
<script src="https://js.paystack.co/v1/inline.js"></script>
<!-- ✅ SECURITY: PAYSTACK KEY SETUP (BEFORE OTHER SCRIPTS) -->
<script>
const PAYSTACK_PUBLIC_KEY = '<?= htmlspecialchars($paystack_public_key, ENT_QUOTES, 'UTF-8') ?>';
const availability = <?= json_encode($availability, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
// ✅ SECURITY: Rate limiting
let lastPaymentAttempt = 0;
const PAYMENT_COOLDOWN = 3000; // 3 seconds
</script>
<!-- ✅ MAIN BOOKING SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Check if Paystack key is configured
    if (!PAYSTACK_PUBLIC_KEY) {
        document.getElementById('paystackButton').innerHTML = 'Payment System Unavailable';
        console.error('Paystack public key is not configured');
        return;
    }
    const roomSelect = document.getElementById('roomType');
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');
    const payButton = document.getElementById('paystackButton');
    const roomRateEl = document.getElementById('roomRate');
    const nightsCountEl= document.getElementById('nightsCount');
    const totalAmountEl= document.getElementById('totalAmount');
    const nightsInput = document.getElementById('nightsInput');
    const totalInput = document.getElementById('totalInput');
    const availabilityWarning = document.getElementById('availabilityWarning');
    // Preselect room if came from rooms.php
    <?php if ($preselected['room_type']): ?>
        roomSelect.value = "<?= $preselected['room_type'] ?>";
    <?php endif; ?>
    function updateBookingSummary() {
        const selected = roomSelect.value;
        const cin = checkIn.value;
        const cout = checkOut.value;
        // Reset
        roomRateEl.textContent = '₦0';
        nightsCountEl.textContent = '0';
        totalAmountEl.textContent = '₦0';
        payButton.disabled = true;
        payButton.textContent = "SELECT A SUITE FIRST";
        availabilityWarning.style.display = 'none';
        if (!selected || !cin || !cout) return;
        const room = availability[selected];
        if (!room || room.available <= 0) {
            availabilityWarning.style.display = 'block';
            payButton.textContent = "SOLD OUT";
            return;
        }
        const nights = Math.max(1, Math.ceil((new Date(cout) - new Date(cin)) / 86400000));
        const price = room.price;
        const total = nights * price;
        // Update UI
        roomRateEl.textContent = '₦' + price.toLocaleString();
        nightsCountEl.textContent = nights + ' night' + (nights > 1 ? 's' : '');
        totalAmountEl.textContent = '₦' + total.toLocaleString();
        // Update hidden fields
        nightsInput.value = nights;
        totalInput.value = total;
        // Enable button
        payButton.disabled = false;
        payButton.textContent = room.available <= 2
            ? `BOOK NOW — ONLY ${room.available} LEFT!`
            : "PROCEED TO SECURE PAYMENT";
    }
    // Attach events
    [roomSelect, checkIn, checkOut].forEach(el =>
        el.addEventListener('change', updateBookingSummary)
    );
    // Initial update
    updateBookingSummary();
    // ✅ PAYSTACK PAYMENT HANDLER
    payButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (this.disabled) return;
        // ✅ Rate limiting
        const now = Date.now();
        if (now - lastPaymentAttempt < PAYMENT_COOLDOWN) {
            alert('Please wait before trying again');
            return;
        }
        lastPaymentAttempt = now;
        const form = document.getElementById('bookingForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const roomType = roomSelect.value;
        const room = availability[roomType];
        if (!room || room.available <= 0) {
            alert("This suite is no longer available!");
            location.reload();
            return;
        }
        // ✅ SECURITY: Input validation
        const email = form.email.value.trim();
        const name = form.full_name.value.trim();
        const phone = form.phone.value.trim();
       
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            alert('Please enter a valid email address');
            return;
        }
       
        if (name.length < 2 || name.length > 100) {
            alert('Full name must be between 2 and 100 characters');
            return;
        }
        const cinVal = checkIn.value;
        const coutVal= checkOut.value;
        const nightsVal = Math.max(1, Math.ceil((new Date(coutVal) - new Date(cinVal)) / 86400000));
        const priceVal = room.price;
      
// Original room total
const totalVal = Math.round(nightsVal * priceVal);

// Paystack fee 3.5%
const paystackFeeRate = 0.035;
const totalWithFee = Math.ceil(totalVal / (1 - paystackFeeRate)); // Amount customer pays
const feeAmount = totalWithFee - totalVal; // Optional: can show to user

// Update hidden field so backend knows final amount
totalInput.value = totalWithFee;

// Update booking summary UI (optional)
totalAmountEl.textContent = '₦' + totalWithFee.toLocaleString() + ' (incl. Paystack fee)';

const handler = PaystackPop.setup({
    key: PAYSTACK_PUBLIC_KEY,
    email,
    amount: totalWithFee * 100, // Amount in kobo
    currency: 'NGN',
    ref: 'DEGRAND' + Date.now(),
    metadata: {
        name,
        phone,
        check_in: cinVal,
        check_out: coutVal,
        room_type: roomType,
        nights: nightsVal,
        total: totalVal,  // Original total
        fee: feeAmount    // Paystack fee
    },
    callback: function(res) {
        const adults = form.adults.value;
        const children = form.children.value;
        const special_requests = form.special_requests.value.trim() || null;
        const country = form.country.value;
        fetch('paystack-verify.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                reference: res.reference,
                amount: totalWithFee,  // send the final amount including fee
                base_total: totalVal,  // original room total
                fee: feeAmount,        // Paystack fee
                name,
                email,
                phone,
                checkin: cinVal,
                checkout: coutVal,
                room: roomType,
                nights: nightsVal,
                adults,
                children,
                special_requests,
                country
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.reference) {
                location.href = 'booking-success.php?ref=' + encodeURIComponent(data.reference);
            } else {
                alert(data.message || 'Booking failed. The room may have been taken.');
                setTimeout(() => location.reload(), 2500);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error. Please try again.');
        });
    },
    onClose: function() {
        alert('Payment window closed. You can try again.');
    }
});


        handler.openIframe();
    });
    // ✅ LIVE AVAILABILITY AUTO-REFRESH
    setInterval(() => {
        fetch('live-availability.php')
        .then(res => res.json())
        .then(data => {
            if (!data || typeof data !== 'object') return;
           
            Object.keys(data).forEach(type => {
                if(availability[type]) {
                    availability[type].available = parseInt(data[type].available) || 0;
                }
            });
           
            const selected = roomSelect.value;
            if (selected && availability[selected] && availability[selected].available <= 0) {
                alert("This room just got sold out!");
                location.reload();
            }
        })
        .catch(err => console.error('Failed to refresh availability:', err));
    }, 20000);
});
</script>