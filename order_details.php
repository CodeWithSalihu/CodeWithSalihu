<?php
session_start();
require_once '../includes/db_connect.php';

// Security - Only logged-in admin
if (empty($_SESSION['restaurant_admin_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'] ?? 0;
$order_id = (int)$order_id;

if ($order_id <= 0) {
    die("Invalid Order ID");
}

/* ==============================
   DELETE ORDER (ADMIN)
================================= */
if (isset($_POST['delete_order'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    if ($order_id > 0) {
        $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
        $_SESSION['msg'] = "Order deleted successfully!";
       header("Location: https://www.degrandhotel.com/resta/dashboard.php");
        exit();
    }
}


/* ==============================
   FETCH ORDER DETAILS
================================= */
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?
    GROUP BY o.id
    LIMIT 1
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found!");
}

/* ==============================
   FETCH ORDER ITEMS
================================= */
$items_stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll();

/* ==============================
   UPDATE STATUS
================================= */
if ($_POST['update_status'] ?? false) {
    $new_status = $_POST['new_status'];
    $valid_statuses = ['pending', 'preparing', 'ready', 'delivered', 'cancelled'];

    if (in_array($new_status, $valid_statuses)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")
            ->execute([$new_status, $order_id]);

        $_SESSION['msg'] = "Order status updated to <strong>" . ucfirst($new_status) . "</strong>!";
        header("Location: order_details.php?id=$order_id");
        exit();
    }
}

// Format date
$order_date = date('d F Y \a\t g:ia', strtotime($order['created_at']));
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>Order #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?> • De Grand Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --green:#00A651; --darkgreen:#008040; --red:#FF3B30; --orange:#FF9500; --blue:#007AFF; --shadow:0 20px 50px rgba(0,166,81,0.18); }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#f5f7fa; color:#222; }
        
        .header { background:linear-gradient(135deg,var(--green),var(--darkgreen)); color:white; padding:30px; text-align:center; box-shadow:0 10px 30px rgba(0,166,81,0.3); }
        .header h1 { font-size:2.8rem; font-weight:900; }
        .header p { font-size:1.1rem; opacity:0.9; margin-top:8px; }

        .container { max-width:1500px; margin:40px auto; padding:0 20px; }

        .order-header {
            background:white; border-radius:24px; padding:30px; margin-bottom:30px; box-shadow:var(--shadow);
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;
        }
        .order-id { font-size:2.5rem; font-weight:900; color:var(--green); }
        .status-badge {
            padding:12px 28px; border-radius:50px; font-weight:900; font-size:1.1rem; color:white; text-transform:uppercase;
        }
        .status-pending    { background:var(--orange); }
        .status-preparing  { background:var(--blue); animation:pulse 2s infinite; }
        .status-ready      { background:#8E44AD; }
        .status-delivered  { background:#27AE60; }
        .status-cancelled  { background:var(--red); }

        .card { background:white; border-radius:24px; padding:30px; margin-bottom:30px; box-shadow:var(--shadow); }
        .card h3 { color:var(--green); font-weight:700; margin-bottom:20px; font-size:1.6rem; border-bottom:3px solid #e8f7f0; padding-bottom:10px; }

        table { width:100%; border-collapse:collapse; }
        th, td { padding:16px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f8f9fa; color:#333; font-weight:600; }
        tr:hover { background:#f8fff8; }

        .total-row { font-size:1.8rem; font-weight:900; color:var(--green); text-align:right; padding:20px 0; }

        .btn-group { display:flex; gap:15px; flex-wrap:wrap; margin-top:20px; }
        .btn {
            padding:14px 28px; border:none; border-radius:50px; font-weight:700; cursor:pointer;
            transition:all 0.4s; text-decoration:none; display:inline-block;
        }
        .btn-success { background:var(--green); color:white; }
        .btn-warning { background:#FF9500; color:white; }
        .btn-danger  { background:var(--red); color:white; }
        .btn:hover { transform:translateY(-5px); box-shadow:0 15px 30px rgba(0,0,0,0.2); }

        .success-msg {
            background:#d4edda; color:#155724; padding:20px; border-radius:16px; text-align:center;
            font-weight:600; margin:20px 0; border:2px solid #c3e6cb;
        }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }
    </style>
</head>
<body>

    <div class="header">
        <h1>ORDER DETAILS</h1>
        <p>De Grand Restaurant • Calabar</p>
    </div>

    <div class="container">

        <!-- Success Message -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="success-msg">
                <?= $_SESSION['msg'] ?> 
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- Order Header -->
        <div class="order-header">
            <div>
                <div class="order-id">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></div>
                <div style="margin-top:8px; color:#666;">
                    Placed on <strong><?= $order_date ?></strong>
                </div>
            </div>
            <div class="status-badge status-<?= $order['status'] ?>">
                <?= strtoupper($order['status']) ?>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card">
            <h3>Customer Information</h3>
            <table>
                <tr><td width="180"><strong>Name:</strong></td><td><?= htmlspecialchars($order['customer_name']) ?></td></tr>
                <tr><td><strong>Phone:</strong></td><td><a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>" style="color:var(--green); font-weight:700;"><?= htmlspecialchars($order['customer_phone']) ?></a></td></tr>
                <tr><td><strong>Delivery:</strong></td><td><?= htmlspecialchars($order['table_number'] ?: 'Not specified') ?></td></tr>
                <?php if (!empty($order['notes'])): ?>
                <tr><td><strong>Notes:</strong></td><td><em><?= htmlspecialchars($order['notes']) ?></em></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Order Items -->
        <div class="card">
            <h3>Order Items (<?= $order['total_items'] ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="120">Qty</th>
                        <th width="150">Price</th>
                        <th width="150">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['product_name'] ?? $item['product_name']) ?></strong></td>
                        <td><strong><?= $item['quantity'] ?>×</strong></td>
                        <td>₦<?= number_format($item['price']) ?></td>
                        <td><strong>₦<?= number_format($item['price'] * $item['quantity']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-row">
                TOTAL: ₦<?= number_format($order['total_amount']) ?>
            </div>
        </div>

        <!-- Update Status -->
        <div class="card">
            <h3>Update Order Status</h3>
            <form method="POST">
                <input type="hidden" name="update_status" value="1">
                <div class="btn-group">
                    <button type="submit" name="new_status" value="pending"    class="btn <?= $order['status']=='pending'?'btn-success':'' ?>">Pending</button>
                    <button type="submit" name="new_status" value="preparing"  class="btn <?= $order['status']=='preparing'?'btn-success':'' ?>">Preparing</button>
                    <button type="submit" name="new_status" value="ready"      class="btn <?= $order['status']=='ready'?'btn-success':'' ?>">Ready</button>
                    <button type="submit" name="new_status" value="delivered"  class="btn <?= $order['status']=='delivered'?'btn-success':'' ?>">Delivered</button>
                    <button type="submit" name="new_status" value="cancelled"  class="btn btn-danger" onclick="return confirm('Cancel this order?')">Cancel Order</button>
                </div>
            </form>
        </div>


        <!-- Delete Order -->
<div class="card">
    <h3>Danger Zone</h3>
    <form method="POST" onsubmit="return confirm('Are you sure you want to DELETE this order permanently? This cannot be undone.');">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <button type="submit" name="delete_order" class="btn btn-danger">
        <i class="fas fa-trash"></i> Delete Order
    </button>
</form>

</div>
        <!-- Back Button -->
        <div style="text-align:center; margin:40px 0;">
            <a href="dashboard.php" class="btn btn-success" style="padding:18px 50px; font-size:1.3rem;">
                Back to Dashboard
            </a>
        </div>
    </div>

</body>
</html>