<?php

session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: booking.php");
    exit();
}

// Use env prefix for booking reference
$booking_prefix = getenv('BOOKING_REF_PREFIX') ?: 'DEGRAND';
$booking_ref = $booking_prefix . date('dmy') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

// ...existing code...
$full_name        = trim($_POST['full_name']);
$email            = trim($_POST['email']);
$phone            = trim($_POST['phone']);
$country          = trim($_POST['country'] ?? 'NG');
$check_in         = trim($_POST['check_in']);
$check_out        = trim($_POST['check_out']);
$nights           = (int) ($_POST['nights'] ?? 1);
$room_type        = trim($_POST['room_type']);
$adults           = (int) ($_POST['adults'] ?? 1);
$children         = (int) ($_POST['children'] ?? 0);
$special_requests = trim($_POST['special_requests'] ?? '');
$subtotal         = (float) ($_POST['subtotal'] ?? 0);
$tax              = (float) ($_POST['tax'] ?? 0);
$total_amount     = (float) ($_POST['total_amount'] ?? 0);
$payment_method   = trim($_POST['payment_method'] ?? 'online');
$booked_by        = 'website';
$status           = 'confirmed';

if (!$full_name || !$email || !$phone || !$room_type || !$check_in || !$check_out) {
    die("Error: Required fields are missing.");
}

try {
    $sql = "INSERT INTO net_bookings 
        (booking_ref, full_name, email, phone, country, check_in, check_out, nights, room_type, adults, children, special_requests, subtotal, tax, total_amount, status, payment_method, booked_by)
        VALUES 
        (:booking_ref, :full_name, :email, :phone, :country, :check_in, :check_out, :nights, :room_type, :adults, :children, :special_requests, :subtotal, :tax, :total_amount, :status, :payment_method, :booked_by)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':booking_ref'       => $booking_ref,
        ':full_name'         => $full_name,
        ':email'             => $email,
        ':phone'             => $phone,
        ':country'           => $country,
        ':check_in'          => $check_in,
        ':check_out'         => $check_out,
        ':nights'            => $nights,
        ':room_type'         => $room_type,
        ':adults'            => $adults,
        ':children'          => $children,
        ':special_requests'  => $special_requests,
        ':subtotal'          => $subtotal,
        ':tax'               => $tax,
        ':total_amount'      => $total_amount,
        ':status'            => $status,
        ':payment_method'    => $payment_method,
        ':booked_by'         => $booked_by
    ]);

    $_SESSION['last_booking_ref'] = $booking_ref;

    if ($payment_method === 'online') {
        header("Location: paystack-init.php?ref=$booking_ref");
        exit();
    } elseif ($payment_method === 'transfer') {
        header("Location: complete-transfer.php?ref=$booking_ref");
        exit();
    } else {
        header("Location: booking-success.php?ref=$booking_ref");
        exit();
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>