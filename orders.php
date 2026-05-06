<?php
// Start session only for cart count (no login needed!)
session_start();
require_once '../includes/db_connect.php';

// Fetch ALL orders (public view — anyone fit see)
$stmt = $pdo->prepare("
    SELECT o.*, 
           GROUP_CONCAT(p.name SEPARATOR ', ') AS items,
           COUNT(oi.product_id) AS total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cart badge count (still works even without login)
$item_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $item_count += (int)($item['quantity'] ?? 1);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>My Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --green: #00A651; --darkgreen: #008040; --green-light: #e8f7f0;
            --red: #FF3B30; --orange: #FF9500; --blue: #007AFF;
            --gray: #f8f9fa; --text: #1a1a1a; --shadow: 0 20px 50px rgba(0,166,81,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family:'Poppins',sans-serif; 
            background:linear-gradient(135deg,#f5f7fa,#e4f5e9); 
            color:var(--text); 
            padding-bottom:140px; 
            min-height:100vh; 
        }

        .header {
            background:linear-gradient(135deg,var(--green),var(--darkgreen));
            color:white; padding:30px 20px 50px; text-align:center;
            border-radius:0 0 32px 32px; position:relative; overflow:hidden;
        }
        .header::before { 
            content:''; position:absolute; inset:0; 
            background:url('../images/pattern.png') repeat; opacity:0.08; 
        }
        .title { font-size:34px; font-weight:900; letter-spacing:-1px; }

        .container { max-width:500px; margin:0 auto; padding:0 20px; margin-top:-30px; }

        .order-card {
            background:white; border-radius:24px; padding:20px; margin:20px 0;
            box-shadow:var(--shadow); position:relative; overflow:hidden;
            transition:0.4s; border-left:6px solid var(--green);
        }
        .order-card:hover { 
            transform:translateY(-8px); 
            box-shadow:0 30px 60px rgba(0,166,81,0.3); 
        }

        .status {
            position:absolute; top:16px; right:16px; padding:8px 16px;
            border-radius:50px; font-size:13px; font-weight:800; color:white;
        }
        .status.pending    { background:#FF9500; }
        .status.preparing  { background:#007AFF; animation:pulse 2s infinite; }
        .status.ready      { background:#00C853; }
        .status.delivered  { background:#888; }
        .status.cancelled  { background:#FF3B30; }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }

        .order-id { font-size:18px; font-weight:900; color:var(--green); margin-bottom:8px; }
        .items { font-size:15px; color:#555; margin:10px 0; line-height:1.5; }
        .total { font-size:22px; font-weight:900; color:var(--green); margin:12px 0; }

        .time { 
            font-size:14px; color:#888; margin-top:8px; 
            display:flex; align-items:center; gap:8px; 
        }
        .countdown { 
            background:var(--orange); color:white; padding:4px 12px; 
            border-radius:20px; font-weight:700; font-size:13px; 
        }

        /* REORDER BUTTON — ALWAYS VISIBLE & SEXY */
        .action-buttons {
            margin-top: 16px;
            display: flex;
            gap: 10px;
        }
        .reorder-btn, .track-btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: 0.4s;
        }
        .reorder-btn {
            background: var(--green);
            color: white;
        }
        .reorder-btn:hover {
            background: var(--darkgreen);
            transform: translateY(-3px);
        }
        .track-btn {
            background: #f0f0f0;
            color: #333;
        }
        .track-btn:hover {
            background: #e0e0e0;
            transform: translateY(-3px);
        }

        .empty { text-align:center; padding:100px 20px; color:#888; }
        .empty i { font-size:90px; color:#ddd; margin-bottom:20px; }

        .bottom-nav {
            position:fixed; bottom:0; left:0; right:0; background:white;
            display:flex; justify-content:space-around; padding:16px 0 36px;
            box-shadow:0 -15px 40px rgba(0,0,0,0.12); border-radius:36px 36px 0 0; z-index:1000;
        }
        .nav-item { color:#888; text-decoration:none; text-align:center; font-size:11px; font-weight:600; position:relative; }
        .nav-item.active, .nav-item:hover { color:var(--green); }
        .nav-item i { font-size:28px; display:block; margin-bottom:6px; }
        .cart-badge {
            position:absolute; top:-8px; right:-8px; background:var(--red); color:white;
            font-size:12px; font-weight:800; min-width:24px; height:24px; border-radius:50%;
            display:grid; place-items:center;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="title">My Orders</div>
    </header>

    <div class="container">

        <?php if (empty($orders)): ?>
            <div class="empty">
                <i class="fas fa-receipt"></i>
                <h2>No orders yet</h2>
                <p>Time to treat yourself to something delicious!</p>
                <a href="../restaurant.php" style="display:inline-block; margin-top:20px; padding:16px 50px; background:var(--green); color:white; border-radius:50px; text-decoration:none; font-weight:700;">
                    Order Now
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $status = strtolower($order['status'] ?? 'pending');
                $created = new DateTime($order['created_at']);
                $now = new DateTime();
                $mins_ago = $now->diff($created)->i;
                $remaining = max(0, 25 - $mins_ago);
            ?>
                <div class="order-card">
                    <div class="status <?= $status ?>">
                        <?= ucfirst($status) ?>
                        <?php if ($status === 'preparing' && $remaining > 0): ?>
                            <div class="countdown">Ready in <?= $remaining ?> min</div>
                        <?php endif; ?>
                    </div>

                    <div class="order-id">Order #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></div>

                    <div class="items">
                        <?= htmlspecialchars(strlen($order['items'] ?? '') > 80 ? substr($order['items'], 0, 80).'...' : $order['items']) ?>
                        <br><small>(<?= $order['total_items'] ?? 0 ?> item<?= ($order['total_items'] ?? 0) > 1 ? 's' : '' ?>)</small>
                    </div>

                    <div class="total">₦<?= number_format($order['total_amount']) ?></div>

                    <div class="time">
                        <i class="fas fa-clock"></i>
                        <?= $created->format('d M, h:i A') ?>
                        • <?= $mins_ago < 60 ? "$mins_ago mins ago" : "Over an hour ago" ?>
                    </div>

                    <!-- BUTTONS ALWAYS SHOW — EVEN FOR DELIVERED! -->
                    <div class="action-buttons">
                        <button class="reorder-btn" onclick="alert('Reorder feature coming soon!')">
                            <i class="fas fa-redo"></i> Reorder
                        </button>
                        <button class="track-btn" onclick="alert('Tracking coming soon!')">
                            <i class="fas fa-map-marker-alt"></i> Track
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

   <!-- BOTTOM NAV - FINAL VERSION (PHP-FREE URLS) -->
<nav class="bottom-nav">
    <a href="/restaurant" class="nav-item">
        <i class="fas fa-home"></i>Home
    </a>

    <a href="/restaurant/cart" class="nav-item <?php echo $current_page === 'cart' ? 'active' : ''; ?>">
        <i class="fas fa-shopping-cart"></i>Cart
        <?php if($item_count > 0): ?>
            <span class="cart-badge"><?= $item_count ?></span>
        <?php endif; ?>
    </a>

    <a href="/restaurant/orders" class="nav-item <?php echo $current_page === 'orders' ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i>Orders
    </a>

    <a href="/restaurant/profile" class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>Profile
    </a>
</nav>

    <!-- Live countdown update -->
    <script>
        setInterval(() => {
            document.querySelectorAll('.countdown').forEach(el => {
                let mins = parseInt(el.textContent.match(/\d+/)?.[0] || 0);
                if (mins > 0) {
                    mins--;
                    el.textContent = `Ready in ${mins} min`;
                    if (mins === 0) {
                        el.closest('.order-card').querySelector('.status').innerHTML = 'Ready';
                    }
                }
            });
        }, 60000);
    </script>
</body>
</html>