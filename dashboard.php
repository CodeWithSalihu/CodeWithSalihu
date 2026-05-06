<?php
session_start();
require_once '../includes/db_connect.php';

// Simple admin check (you fit upgrade later with login)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Get stats
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$preparing_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'preparing'")->fetchColumn();
$ready_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'ready'")->fetchColumn();
$delivered_today = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered' AND DATE(created_at) = CURDATE()")->fetchColumn();

$today_sales = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN ('ready','delivered') AND DATE(created_at) = CURDATE()")->fetchColumn();
$month_sales = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN ('ready','delivered') AND MONTH(created_at) = MONTH(CURDATE())")->fetchColumn();

// Recent orders
$stmt = $pdo->query("
    SELECT o.id, o.customer_name, o.customer_phone, o.table_number, o.total_amount, o.status, o.created_at,
           COUNT(oi.id) as items_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>Admin Dashboard • De Grand Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --green: #00A651; --darkgreen: #008040; --red: #FF3B30; --orange: #FF9500;
            --blue: #007AFF; --gray: #f8f9fa; --shadow: 0 20px 50px rgba(0,166,81,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            background:#f5f7fa;
            color:#1a1a1a;
        }
        .header {
            background:linear-gradient(135deg,var(--green),var(--darkgreen));
            color:white; padding:25px 20px; text-align:center;
            box-shadow:0 10px 30px rgba(0,166,81,0.3);
        }
        .header h1 { font-size:32px; font-weight:900; }
        .header p { opacity:0.9; font-size:16px; margin-top:8px; }

        .container { max-width:1500px; margin:30px auto; padding:0 20px; }

        .stats-grid {
            display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-bottom:40px;
        }
        .stat-card {
            background:white; border-radius:20px; padding:24px; text-align:center;
            box-shadow:var(--shadow); transition:0.4s; border-left:6px solid var(--green);
        }
        .stat-card:hover { transform:translateY(-10px); }
        .stat-card h3 { font-size:14px; color:#888; margin-bottom:12px; text-transform:uppercase; letter-spacing:1px; }
        .stat-card .number { font-size:36px; font-weight:900; color:var(--green); }
        .stat-card.pending { border-left-color:var(--orange); }
        .stat-card.preparing { border-left-color:var(--blue); }
        .stat-card.ready { border-left-color:#FFCC00; }
        .stat-card.sales { border-left-color:#8E44AD; }

        .section {
            background:white; border-radius:20px; padding:30px; margin-bottom:30px;
            box-shadow:var(--shadow);
        }
        .section h2 {
            font-size:24px; color:var(--green); margin-bottom:20px; font-weight:700;
            border-bottom:3px solid #e8f7f0; padding-bottom:10px;
        }

        table {
            width:100%; border-collapse:collapse; margin-top:10px;
        }
        th, td {
            padding:16px; text-align:left; border-bottom:1px solid #eee;
        }
        th { background:#f8f9fa; color:#333; font-weight:600; }
        tr:hover { background:#f8fff8; }

        .status {
            padding:8px 16px; border-radius:50px; font-size:13px; font-weight:800; color:white;
        }
        .status.pending { background:var(--orange); }
        .status.preparing { background:var(--blue); animation:pulse 2s infinite; }
        .status.ready { background:#8E44AD; }
        .status.delivered { background:#27AE60; }
        .status.cancelled { background:var(--red); }

        .btn {
            padding:10px 20px; background:var(--green); color:white; border:none;
            border-radius:50px; cursor:pointer; font-weight:700; text-decoration:none;
            display:inline-block; margin:5px; transition:0.3s;
        }
        .btn:hover { background:var(--darkgreen); transform:translateY(-3px); }
        .btn-small { padding:6px 12px; font-size:12px; }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }

        .logout {
            position:fixed; top:20px; right:20px; background:var(--red); padding:12px 20px;
            border-radius:50px; color:white; text-decoration:none; font-weight:700;
            box-shadow:0 10px 30px rgba(255,59,48,0.3); z-index:1000;
        }
        .logout:hover { background:#c41e1e; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns:1fr 1fr; }
            table, th, td { font-size:14px; }
        }
    </style>
</head>
<body>

    <a href="logout.php" class="logout">
        Logout
    </a>

    <div class="header">
        <h1>DE GRAND RESTAURANT</h1>
        <p>Admin Dashboard • Calabar</p>
    </div>

    <div class="container">

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?= number_format($total_orders) ?></div>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <div class="number"><?= $pending_orders ?></div>
            </div>
            <div class="stat-card preparing">
                <h3>Preparing</h3>
                <div class="number"><?= $preparing_orders ?></div>
            </div>
            <div class="stat-card ready">
                <h3>Ready</h3>
                <div class="number"><?= $ready_orders ?></div>
            </div>
            <div class="stat-card sales">
                <h3>Today's Sales</h3>
                <div class="number">₦<?= number_format($today_sales) ?></div>
            </div>
            <div class="stat-card sales">
                <h3>Month Sales</h3>
                <div class="number">₦<?= number_format($month_sales) ?></div>
            </div>
        </div>

        <!-- RECENT ORDERS -->
        <div class="section">
            <h2>Recent Orders</h2>
            <?php if (empty($recent_orders)): ?>
                <p style="text-align:center; color:#888; padding:40px;">No orders yet. Time to make money!</p>
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
                        <?php foreach ($recent_orders as $order): 
                            $time_ago = time() - strtotime($order['created_at']);
                            $ago = $time_ago < 3600 ? round($time_ago/60) . " min ago" : "Over an hour ago";
                        ?>
                            <tr>
                                <td><strong>#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($order['table_number'] ?: 'Delivery') ?></td>
                                <td><?= $order['items_count'] ?> item<?= $order['items_count'] > 1 ? 's' : '' ?></td>
                                <td><strong>₦<?= number_format($order['total_amount']) ?></strong></td>
                                <td><span class="status <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                <td><?= $ago ?></td>
                                <td>
                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="text-align:center; margin:40px 0;">
            <a href="all_orders.php" class="btn">View All Orders</a>
            <a href="menu.php" class="btn" style="background:#8E44AD;">Manage Menu</a>
        </div>
    </div>

</body>
</html>