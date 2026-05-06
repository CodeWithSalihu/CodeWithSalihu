<?php
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// FORCE SAME COLLATION ON JOIN — THIS FIXES THE ERROR 100%
$pdo->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");

// ==================== EXPORT TO EXCEL ====================
if (isset($_POST['export_excel'])) {
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? 'all';
    $date_from = $_GET['from'] ?? '';
    $date_to = $_GET['to'] ?? '';

    $sql = "SELECT 
                b.booking_ref,
                b.full_name,
                b.email,
                b.phone,
                b.check_in,
                b.check_out,
                b.nights,
                COALESCE(ri.room_name, UPPER(REPLACE(b.room_type, '-', ' '))) AS room_display,
                b.total_amount,
                b.status,
                DATE(b.created_at) as booking_date,
                TIME(b.created_at) as booking_time
            FROM net_bookings b 
            LEFT JOIN room_inventory ri ON b.room_type = ri.room_type COLLATE utf8mb4_unicode_ci
            WHERE 1=1";

    $params = [];
    if ($search !== '') {
        $sql .= " AND (b.booking_ref LIKE ? OR b.full_name LIKE ? OR b.email LIKE ? OR b.phone LIKE ?)";
        $like = "%$search%";
        $params = array_merge($params, [$like, $like, $like, $like]);
    }
    if ($status_filter !== 'all') {
        $sql .= " AND b.status = ?";
        $params[] = $status_filter;
    }
    if ($date_from !== '') {
        $sql .= " AND DATE(b.created_at) >= ?";
        $params[] = $date_from;
    }
    if ($date_to !== '') {
        $sql .= " AND DATE(b.created_at) <= ?";
        $params[] = $date_to;
    }
    $sql .= " ORDER BY b.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filename = "DeGrand_Bookings_" . date('d_M_Y') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body style='font-family:Arial;'>
          <div style='text-align:center; padding:30px; background:#000; color:#D4AF37;'>
            <h1>DE GRAND HOTEL & ROOFTOP</h1>
            <h2>Calabar • Bookings Report</h2>
            <p>Generated: " . date('d F Y \a\t g:i A') . "</p>
          </div>
          <table border='1' style='width:100%; border-collapse:collapse; margin:20px 0;'>
            <tr style='background:#D4AF37; color:#000; font-weight:bold; text-align:center;'>
                <th>No</th><th>Date</th><th>Ref</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Nights</th><th>Amount</th><th>Status</th>
            </tr>";

    $no = 1; $total = 0;
    foreach ($data as $row) {
        $total += $row['total_amount'];
        echo "<tr style='text-align:center;'>
                <td>$no</td>
                <td>" . date('d/m/Y', strtotime($row['booking_date'])) . "</td>
                <td>{$row['booking_ref']}</td>
                <td>{$row['full_name']}<br><small>{$row['email']}</small></td>
                <td>{$row['room_display']}</td>
                <td>" . date('d M Y', strtotime($row['check_in'])) . "</td>
                <td>" . date('d M Y', strtotime($row['check_out'])) . "</td>
                <td>{$row['nights']}</td>
                <td>₦" . number_format($row['total_amount']) . "</td>
                <td>" . strtoupper($row['status']) . "</td>
              </tr>";
        $no++;
    }
    echo "<tr style='background:#D4AF37; color:#000; font-weight:bold; font-size:18px;'>
            <td colspan='8' style='text-align:right;'>TOTAL REVENUE:</td>
            <td colspan='2'>₦" . number_format($total) . "</td>
          </tr></table></body></html>";
    exit();
}

// ==================== MAIN LIST ====================
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';
$date_from = $_GET['from'] ?? '';
$date_to = $_GET['to'] ?? '';

$sql = "SELECT b.*, 
               COALESCE(ri.room_name, UPPER(REPLACE(b.room_type, '-', ' '))) AS room_display,
               DATE(b.created_at) as booking_date 
        FROM net_bookings b 
        LEFT JOIN room_inventory ri ON b.room_type = ri.room_type COLLATE utf8mb4_unicode_ci
        WHERE 1=1";

$params = [];
if ($search !== '') {
    $sql .= " AND (b.booking_ref LIKE ? OR b.full_name LIKE ? OR b.email LIKE ? OR b.phone LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($status_filter !== 'all') {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
}
if ($date_from !== '') {
    $sql .= " AND DATE(b.created_at) >= ?";
    $params[] = $date_from;
}
if ($date_to !== '') {
    $sql .= " AND DATE(b.created_at) <= ?";
    $params[] = $date_to;
}
$sql .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_bookings = $pdo->query("SELECT COUNT(*) FROM net_bookings WHERE status='confirmed'")->fetchColumn();
$total_revenue  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM net_bookings WHERE status='confirmed'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings • De Grand Hotel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --gold: #D4AF37; }
        body { background: #000; color: #e2e8f0; font-family: 'Inter', sans-serif; margin:0; }
        .container { max-width: 2400px; margin: 0 auto; padding: 30px; }
        .header { text-align: center; padding: 50px; background: linear-gradient(135deg, #111, #000); border-bottom: 6px solid var(--gold); border-radius: 20px; margin-bottom: 40px; }
        .header img { height: 100px; filter: drop-shadow(0 0 30px gold); }
        .page-title { font-family: 'Cinzel', serif; font-size: 52px; color: var(--gold); margin: 20px 0; letter-spacing: 5px; }

        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 40px 0; }
        .stat-card { background: linear-gradient(135deg, var(--gold), #b8860b); color: #000; padding: 50px; border-radius: 25px; text-align: center; font-weight: 900; font-size: 28px; box-shadow: 0 20px 50px rgba(212,175,55,0.4); }

        table { width: 100%; border-collapse: separate; border-spacing: 0 18px; margin-top: 20px; }
        th { background: linear-gradient(135deg, var(--gold), #b8860b); color: #000; padding: 28px; text-align: center; font-weight: 900; letter-spacing: 2px; }
        td { background: #111; padding: 25px; text-align: center; border-bottom: 1px solid #333; }
        tr:hover td { background: rgba(212,175,55,0.2); }
        .btn-view { background: var(--gold); color: #000; padding: 14px 30px; border-radius: 50px; font-weight: bold; text-decoration: none; font-size: 16px; }
        .btn-view:hover { background: #fff; transform: translateY(-5px); }
        .status-confirmed { background: #166534; color: #86efac; padding: 12px 25px; border-radius: 50px; font-weight: bold; }
        .export-btn { background: #16a34a; color: white; padding: 20px 60px; font-size: 22px; font-weight: 900; border-radius: 50px; float: right; margin: 30px 0; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="../images/logo.jpg" alt="De Grand Hotel">
        <h1 class="page-title">BOOKINGS DASHBOARD</h1>
        <p>De Grand Hotel & Rooftop • Calabar</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= number_format($total_bookings) ?></h3>
            <p>TOTAL CONFIRMED BOOKINGS</p>
        </div>
       
    </div>

    <form method="GET" class="mb-4">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; background:#111; padding:30px; border-radius:20px; border:3px solid var(--gold);">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search guest/ref/email/phone" style="padding:16px; border-radius:12px; border:2px solid #333; background:#000; color:white;">
            <select name="status" style="padding:16px; border-radius:12px; border:2px solid #333; background:#000; color:white;">
                <option value="all">All Status</option>
                <option value="confirmed" <?= $status_filter=='confirmed'?'selected':'' ?>>Confirmed</option>
                <option value="pending" <?= $status_filter=='pending'?'selected':'' ?>>Pending</option>
                <option value="cancelled" <?= $status_filter=='cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
            <input type="date" name="from" value="<?= $date_from ?>" style="padding:16px; border-radius:12px; border:2px solid #333; background:#000; color:white;">
            <input type="date" name="to" value="<?= $date_to ?>" style="padding:16px; border-radius:12px; border:2px solid #333; background:#000; color:white;">
            <div>
                <button type="submit" style="background:var(--gold); color:#000; padding:16px 40px; border:none; border-radius:12px; font-weight:900; font-size:18px;">Apply Filter</button>
                <a href="?" style="background:#475569; color:white; padding:16px 30px; border-radius:12px; margin-left:10px; text-decoration:none;">Clear</a>
            </div>
        </div>
    </form>

    <form method="POST" style="display:inline;">
        <button type="submit" name="export_excel" class="export-btn">
            Export to Excel
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Ref</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
            <tr><td colspan="9" style="padding:120px; font-size:24px; color:#64748b;">
                <i class="fas fa-inbox fa-5x mb-4"></i><br>No bookings found
            </td></tr>
            <?php else: foreach($bookings as $b): ?>
            <tr>
                <td><?= date('d M Y', strtotime($b['created_at'])) ?><br><small><?= date('h:i A', strtotime($b['created_at'])) ?></small></td>
                <td><strong style="color:var(--gold); font-size:20px;"><?= $b['booking_ref'] ?></strong></td>
                <td><strong><?= htmlspecialchars($b['full_name']) ?></strong><br><small><?= htmlspecialchars($b['email']) ?></small></td>
                <td><strong><?= $b['room_display'] ?></strong></td>
                <td><?= date('d M Y', strtotime($b['check_in'])) ?></td>
                <td><?= date('d M Y', strtotime($b['check_out'])) ?></td>
                <td><strong style="font-size:22px;">₦<?= number_format($b['total_amount']) ?></strong></td>
                <td><span class="status-confirmed"><?= strtoupper($b['status']) ?></span></td>
                <td>
                    <a href="../booking-success.php?ref=<?= $b['booking_ref'] ?>" target="_blank" class="btn-view">
                        View Receipt
                    </a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>