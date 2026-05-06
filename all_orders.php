<?php
session_start();
require_once '../includes/db_connect.php';

// Security
if (empty($_SESSION['restaurant_admin_id'])) {
    header("Location: login.php");
    exit();
}

// Filters
$status_filter = $_GET['status'] ?? 'all';
$date_filter   = $_GET['date'] ?? 'all'; // today, week, month, all
$search        = trim($_GET['search'] ?? '');

// Build WHERE clause
$where = [];
$params = [];

if ($status_filter !== 'all') {
    $where[] = "o.status = ?";
    $params[] = $status_filter;
}

switch ($date_filter) {
    case 'today':
        $where[] = "DATE(o.created_at) = CURDATE()";
        break;
    case 'week':
        $where[] = "o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $where[] = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        break;
}

if ($search !== '') {
    $where[] = "(o.customer_name LIKE ? OR o.customer_phone LIKE ? OR o.id LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Main Query
$sql = "
    SELECT o.*, 
           COUNT(oi.id) as items_count,
           SUM(oi.quantity * oi.price) as calculated_total
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    $where_clause
    GROUP BY o.id
    ORDER BY o.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Stats
$stats = [
    'total'      => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending'    => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
    'preparing'  => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='preparing'")->fetchColumn(),
    'ready'      => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='ready'")->fetchColumn(),
    'delivered'  => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn(),
    'today_sales'=> $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status IN('ready','delivered') AND DATE(created_at)=CURDATE()")->fetchColumn(),
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>All Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --green:#00A651; --darkgreen:#008040; --orange:#FF9500; --blue:#007AFF; --red:#FF3B30; --purple:#8E44AD; --shadow:0 20px 50px rgba(0,166,81,0.18); }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#f5f7fa; color:#222; }

        .header { background:linear-gradient(135deg,var(--green),var(--darkgreen)); color:white; padding:30px; text-align:center; box-shadow:0 10px 30px rgba(0,166,81,0.3); }
        .header h1 { font-size:2.8rem; font-weight:900; }
        .header p { font-size:1.1rem; opacity:0.9; margin-top:8px; }

        .container { max-width:1300px; margin:40px auto; padding:0 20px; }

        .stats-grid {
            display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:20px; margin-bottom:40px;
        }
        .stat-card {
            background:white; border-radius:20px; padding:24px; text-align:center; box-shadow:var(--shadow);
            border-left:6px solid var(--green);
        }
        .stat-card h3 { font-size:1rem; color:#888; margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; }
        .stat-card .num { font-size:2.2rem; font-weight:900; color:var(--green); }
        .stat-card.pending { border-left-color:var(--orange); }
        .stat-card.preparing { border-left-color:var(--blue); }
        .stat-card.ready { border-left-color:var(--purple); }
        .stat-card.delivered { border-left-color:#27AE60; }
        .stat-card.sales { border-left-color:#8E44AD; }

        .filters {
            background:white; border-radius:20px; padding:25px; margin-bottom:30px; box-shadow:var(--shadow);
            display:flex; flex-wrap:wrap; gap:15px; align-items:center;
        }
        .filters select, .filters input {
            padding:14px 20px; border-radius:50px; border:2px solid #eee; font-size:1rem;
            transition:0.3s;
        }
        .filters select:focus, .filters input:focus { outline:none; border-color:var(--green); }

        .table-card { background:white; border-radius:24px; overflow:hidden; box-shadow:var(--shadow); }
        table { width:100%; border-collapse:collapse; }
        th { background:#f8f9fa; padding:20px; text-align:left; font-weight:600; color:#333; }
        td { padding:18px 20px; border-bottom:1px solid #eee; vertical-align:middle; }
        tr:hover { background:#f8fff8; }

        .order-id { font-weight:900; color:var(--green); font-size:1.3rem; }
        .status {
            padding:8px 20px; border-radius:50px; color:white; font-weight:800; font-size:0.9rem;
        }
        .status-pending    { background:var(--orange); }
        .status-preparing  { background:var(--blue); animation:pulse 2s infinite; }
        .status-ready      { background:var(--purple); }
        .status-delivered  { background:#27AE60; }
        .status-cancelled  { background:var(--red); }

        .btn { padding:10px 20px; border-radius:50px; font-weight:700; text-decoration:none; transition:0.3s; }
        .btn-sm { padding:8px 16px; font-size:0.9rem; }
        .btn-success { background:var(--green); color:white; }
        .btn-success:hover { background:var(--darkgreen); transform:translateY(-3px); }

        .no-orders {
            text-align:center; padding:80px 20px; color:#888;
        }
        .no-orders i { font-size:4rem; color:#ddd; margin-bottom:20px; }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }
    </style>
</head>
<body>

    <div class="header">
        <h1>ALL ORDERS</h1>
        <p>De Grand Restaurant • Calabar</p>
    </div>

    <div class="container">

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Orders</h3><div class="num"><?= number_format($stats['total']) ?></div></div>
            <div class="stat-card pending"><h3>Pending</h3><div class="num"><?= $stats['pending'] ?></div></div>
            <div class="stat-card preparing"><h3>Preparing</h3><div class="num"><?= $stats['preparing'] ?></div></div>
            <div class="stat-card ready"><h3>Ready</h3><div class="num"><?= $stats['ready'] ?></div></div>
            <div class="stat-card delivered"><h3>Delivered</h3><div class="num"><?= $stats['delivered'] ?></div></div>
            <div class="stat-card sales"><h3>Today's Sales</h3><div class="num">₦<?= number_format($stats['today_sales']) ?></div></div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" style="display:flex; gap:15px; flex:1; flex-wrap:wrap;">
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?= $status_filter==='all'?'selected':'' ?>>All Status</option>
                    <option value="pending" <?= $status_filter==='pending'?'selected':'' ?>>Pending</option>
                    <option value="preparing" <?= $status_filter==='preparing'?'selected':'' ?>>Preparing</option>
                    <option value="ready" <?= $status_filter==='ready'?'selected':'' ?>>Ready</option>
                    <option value="delivered" <?= $status_filter==='delivered'?'selected':'' ?>>Delivered</option>
                    <option value="cancelled" <?= $status_filter==='cancelled'?'selected':'' ?>>Cancelled</option>
                </select>

                <select name="date" onchange="this.form.submit()">
                    <option value="all" <?= $date_filter==='all'?'selected':'' ?>>All Time</option>
                    <option value="today" <?= $date_filter==='today'?'selected':'' ?>>Today</option>
                    <option value="week" <?= $date_filter==='week'?'selected':'' ?>>Last 7 Days</option>
                    <option value="month" <?= $date_filter==='month'?'selected':'' ?>>This Month</option>
                </select>

                <input type="text" name="search" placeholder="Search customer, phone or ID..." value="<?= htmlspecialchars($search) ?>" style="min-width:250px;">
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="all_orders.php" class="btn btn-sm" style="background:#ccc; color:#333;">Clear</a>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="table-card">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-receipt"></i>
                    <h3>No orders found</h3>
                    <p>Try adjusting your filters or wait for new orders!</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): 
                            $time_ago = strtotime($o['created_at']);
                            $ago = $time_ago > strtotime('-1 hour') ? round((time() - $time_ago)/60) . " min ago" : "Over an hour ago";
                        ?>
                        <tr>
                            <td class="order-id">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><strong><?= htmlspecialchars($o['customer_name']) ?></strong></td>
                            <td><a href="tel:<?= htmlspecialchars($o['customer_phone']) ?>" style="color:var(--green);"><?= htmlspecialchars($o['customer_phone']) ?></a></td>
                            <td><?= htmlspecialchars($o['table_number'] ?: 'Delivery') ?></td>
                            <td><?= $o['items_count'] ?> item<?= $o['items_count']>1?'s':'' ?></td>
                            <td><strong style="color:var(--green);">₦<?= number_format($o['total_amount']) ?></strong></td>
                            <td><span class="status status-<?= $o['status'] ?>"><?= strtoupper($o['status']) ?></span></td>
                            <td><?= $ago ?></td>
                            <td>
                                <a href="order_details.php?id=<?= $o['id'] ?>" class="btn btn-success btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="text-align:center; margin:50px 0;">
            <a href="dashboard.php" class="btn btn-success" style="padding:18px 60px; font-size:1.4rem;">
                Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>