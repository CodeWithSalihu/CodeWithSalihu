<?php 
session_start();
require_once 'includes/db_connect.php';

// SECURITY: Parse JSON input safely
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Sanitize inputs
$reference        = preg_replace('/[^a-zA-Z0-9\-_]/', '', $input['reference'] ?? '');
$full_name        = trim($input['name'] ?? '');
$email            = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone            = preg_replace('/\D/', '', $input['phone'] ?? '');
$checkin          = $input['checkin'] ?? '';
$checkout         = $input['checkout'] ?? '';
$nights           = (int)($input['nights'] ?? 1);
$room_type        = preg_replace('/[^a-z0-9_\-]/i', '', $input['room'] ?? '');
$adults           = max(1, (int)($input['adults'] ?? 2));
$children         = (int)($input['children'] ?? 0);
$special_requests = trim($input['special_requests'] ?? '') ?: null;

$secret_key = getenv('PAYSTACK_SECRET_KEY') ?: 'sk_test_d78d3460bc608a064acbcb402b1fac0a673283b9';
if (!$reference || !$checkin || !$checkout || !$room_type) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

// 1. VERIFY PAYMENT WITH PAYSTACK
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $secret_key"],
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);

if (!($result['status'] ?? false) || $result['data']['status'] !== 'success') {
    echo json_encode(['success' => false, 'message' => 'Payment failed or not verified']);
    exit();
}

$paid_amount = $result['data']['amount'] / 100;

// 2. BOOKING LOGIC — SMART, DATE-BASED, NO PERMANENT REDUCTION
try {
    $pdo->beginTransaction();

    // Validate dates
    $checkinDate  = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
    if ($checkinDate >= $checkoutDate) {
        throw new Exception("Invalid check-in/check-out dates");
    }

    // Get total rooms from your existing room_inventory table
    $stmt = $pdo->prepare("SELECT total_rooms, price_per_night FROM room_inventory WHERE room_type = ?");
    $stmt->execute([$room_type]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room || $room['total_rooms'] <= 0) {
        throw new Exception("Room type not available");
    }

    $total_rooms     = (int)$room['total_rooms'];
    $price_per_night = (float)$room['price_per_night'];
    $expected_amount = $nights * $price_per_night;

    // Optional: Verify amount (uncomment if you want)
    // if (abs($paid_amount - $expected_amount) > 100) { // allow ₦100 tolerance
    //     throw new Exception("Payment amount mismatch");
    // }

    $period = new DatePeriod($checkinDate, new DateInterval('P1D'), $checkoutDate);

$checkStmt = $pdo->prepare("
    SELECT COUNT(*) FROM net_bookings 
    WHERE room_type = ? 
      AND status = 'confirmed'
      AND check_in <= ? 
      AND check_out > ?
");

foreach ($period as $date) {
    $dateStr = $date->format('Y-m-d');
    $checkStmt->execute([$room_type, $dateStr, $dateStr]);
    $booked_on_date = (int)$checkStmt->fetchColumn();

    if ($booked_on_date >= $total_rooms) {
        // Stop immediately if fully booked
throw new Exception("Sorry! The room you selected is fully booked on " . $date->format('D, j M Y'));
    }
}


    // All dates available → Save booking
    $booking_ref = $reference;

    $sql = "INSERT INTO net_bookings (
                booking_ref, full_name, email, phone, country,
                check_in, check_out, nights, room_type,
                adults, children, special_requests, total_amount,
                payment_method, transaction_id, status, booked_by
            ) VALUES (
                ?, ?, ?, ?, 'NG', ?, ?, ?, ?, ?, ?, ?, ?, 'online', ?, 'confirmed', 'website'
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $booking_ref, $full_name, $email, $phone,
        $checkin, $checkout, $nights, $room_type,
        $adults, $children, $special_requests, $paid_amount, $booking_ref
    ]);

    $pdo->commit();

    // SEND SUCCESS RESPONSE IMMEDIATELY (so receipt prints fast)
    $_SESSION['last_booking_ref'] = $booking_ref;
    echo json_encode(['success' => true, 'reference' => $booking_ref]);
    flush();

    // BACKGROUND NOTIFICATIONS (non-blocking)
    ignore_user_abort(true);
    set_time_limit(0);

    $base_url = "https://" . $_SERVER['HTTP_HOST'] . "/de-grand-hotel";

    // GUEST WHATSAPP
    $guest_msg = "*DE GRAND HOTEL & ROOFTOP CALABAR*\n\nBOOKING CONFIRMED!\n\nRef: *$booking_ref*\nGuest: *$full_name*\nSuite: " . ucwords(str_replace('-', ' ', $room_type)) . "\nCheck-in: " . $checkinDate->format('D, j M Y') . "\nCheck-out: " . $checkoutDate->format('D, j M Y') . "\nGuests: $adults Adult" . ($adults>1?'s':'') . ($children>0?" + $children Child".($children>1?'ren':''):'') . "\nTotal: ₦" . number_format($paid_amount) . "\n\nView Receipt: $base_url/booking-success.php?ref=$booking_ref";
    $guest_url = "https://api.whatsapp.com/send?phone=234" . $phone . "&text=" . urlencode($guest_msg);

    // ADMIN WHATSAPP
    $admin_phone = "2349135319524";
    $admin_msg = "NEW BOOKING!\nRef: $booking_ref\n$full_name\n$phone\n" . ucwords(str_replace('-', ' ', $room_type)) . "\n" . $checkinDate->format('j M') . " → " . $checkoutDate->format('j M Y') . "\n₦" . number_format($paid_amount);
    $admin_url = "https://api.whatsapp.com/send?phone=$admin_phone&text=" . urlencode($admin_msg);

    // GUEST EMAIL
    $subject = "Booking Confirmed! Ref: $booking_ref";
    $headers = "From: De Grand Hotel <bookings@degrandhotel.com>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $email_body = "DE GRAND HOTEL & ROOFTOP CALABAR\n" . str_repeat("=", 50) . "\n\nBOOKING CONFIRMED!\n\nReference: $booking_ref\nGuest: $full_name\nCheck-in: " . $checkinDate->format('l, F j, Y') . "\nCheck-out: " . $checkoutDate->format('l, F j, Y') . "\nTotal: ₦" . number_format($paid_amount) . "\n\nView Receipt: $base_url/booking-success.php?ref=$booking_ref\n\nThank you!";

    // Fire and forget
    @file_get_contents($guest_url . "&_=" . time());
    @file_get_contents($admin_url . "&_=" . time());
    @mail($email, $subject, $email_body, $headers);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();

    // Return JSON to the client so they see the error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit(); // stop further execution
}
?>