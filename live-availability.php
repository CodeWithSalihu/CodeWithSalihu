<?php
require_once 'includes/db_connect.php';
header('Content-Type: application/json');

$today = date('Y-m-d');
$result = [];

$stmt = $pdo->query("SELECT room_type, total_rooms FROM room_inventory");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $type = $row['room_type'];
    $total = (int)$row['total_rooms'];

    $check = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings 
        WHERE room_type = ? 
          AND status IN ('confirmed', 'checked_in')
          AND ? >= check_in 
          AND ? < check_out
    ");
    $check->execute([$type, $today, $today]);
    $booked = (int)$check->fetchColumn();

    $result[$type] = [
        'available' => max(0, $total - $booked)
    ];
}

echo json_encode($result);
?>