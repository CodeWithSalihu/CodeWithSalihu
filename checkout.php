<?php
session_start();
require_once '../includes/db_connect.php';

// Cart count for badge
$item_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $item_count += (int)($item['quantity'] ?? 1);
    }
}

// If cart check
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Generate Order ID
$order_ref = "DG" . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
$order_date = date('d F Y \a\t g:ia');

// Calculate total
$subtotal = 0;
$order_items_html = "";
$order_items_text = "";

foreach ($_SESSION['cart'] as $id => $item) {
    $name     = htmlspecialchars($item['name'] ?? 'Unknown');
    $price    = (float)($item['price'] ?? 0);
    $quantity = (int)($item['quantity'] ?? 1);
    $line_total = $price * $quantity;
    $subtotal += $line_total;

    $order_items_html .= "<div class=\"item\">
        <span class=\"item-name\">{$quantity} × {$name}</span>
        <span class=\"item-price\">₦" . number_format($line_total) . "</span>
    </div>";

    $order_items_text .= "• {$quantity} × {$name} — ₦" . number_format($line_total) . "\n";
}

$total = $subtotal;
$whatsapp_number = "2349135319524";

// Handle form submission (SAVE TO DATABASE + SEND WHATSAPP)
$success_message = $error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name  = trim($_POST['name'] ?? '');
    $customer_phone = trim($_POST['phone'] ?? '');
    $delivery_info  = trim($_POST['address'] ?? '');
    $notes          = trim($_POST['notes'] ?? '');

    if (empty($customer_name) || empty($customer_phone) || empty($delivery_info)) {
        $error_message = "Please fill in all required fields!";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert into orders table
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (customer_name, customer_phone, table_number, notes, total_amount, status, payment_method, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', 'cash', NOW())
            ");
            $stmt->execute([$customer_name, $customer_phone, $delivery_info, $notes, $total]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $item_stmt = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, product_id, product_name, quantity, price)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $product_id => $item) {
                $item_stmt->execute([
                    $order_id,
                    $product_id,
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            $pdo->commit();

            // Clear cart after success
            unset($_SESSION['cart']);
            $item_count = 0;

            $success_message = "Order placed successfully! Sending to WhatsApp...";

            // Prepare WhatsApp message
            $message = "*NEW ORDER - DE GRAND RESTAURANT CALABAR*\n\n" .
                       "*Order ID:* $order_ref*\n" .
                       "*Date:* $order_date\n\n" .
                       "*Customer Details*\n" .
                       "• Name: $customer_name\n" .
                       "• Phone: $customer_phone\n" .
                       "• Delivery: $delivery_info\n" .
                       (!empty($notes) ? "• Notes: $notes\n" : "") .
                       "\n*Order Items*\n$order_items_text\n" .
                       "*TOTAL: ₦" . number_format($total) . "*\n\n" .
                       "Thank you! Your order will be delivered immediately!";

            $wa_url = "https://wa.me/$whatsapp_number?text=" . urlencode($message);

            // Auto redirect to WhatsApp after 2 seconds
            echo "<script>
                setTimeout(() => { window.location.href = '$wa_url'; }, 2000);
            </script>";

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Order failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout • De Grand Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root { --green:#00A651; --darkgreen:#008040; --shadow:0 20px 50px rgba(0,166,81,0.18); }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#f5f7fa,#e4f5e9); color:#222; min-height:100vh; padding-bottom:120px; }
        .header { background:linear-gradient(135deg,var(--green),var(--darkgreen)); color:white; text-align:center; padding:50px 20px 70px; border-radius:0 0 40px 40px; }
        .logo-text { font-size:38px; font-weight:900; }
        .container { max-width:500px; margin:0 auto; padding:0 20px; margin-top:-50px; }
        .order-id-banner { background:var(--green); color:white; padding:18px; border-radius:20px; text-align:center; font-weight:800; font-size:16px; margin-bottom:25px; box-shadow:0 10px 30px rgba(0,166,81,0.3); }
        .card { background:white; border-radius:24px; padding:28px; margin:20px 0; box-shadow:var(--shadow); }
        .card h2, .card h3 { color:var(--green); margin-bottom:20px; font-weight:700; }
        .item { display:flex; justify-content:space-between; margin:16px 0; font-size:17px; }
        .item-name { color:#555; }
        .item-price { font-weight:700; color:var(--green); }
        .total-row { font-size:28px; font-weight:900; color:var(--green); border-top:3px dashed #ddd; padding-top:25px; margin-top:25px; text-align:right; }
        input, textarea {
            width:100%; padding:18px; margin:12px 0; border:2px solid #eee; border-radius:16px; font-size:16px;
            transition:0.3s;
        }
        input:focus, textarea:focus { outline:none; border-color:var(--green); box-shadow:0 0 0 4px rgba(0,166,81,0.1); }
        .btn-whatsapp {
            display:block; width:100%; padding:22px; background:#25D366; color:white; text-align:center;
            border-radius:50px; font-size:24px; font-weight:900; text-decoration:none; margin:30px 0;
            box-shadow:0 15px 40px rgba(37,211,102,0.4); transition:0.4s;
        }
        .btn-whatsapp:hover { background:#128C7E; transform:translateY(-5px); }
        .alert-success { background:#d4edda; color:#155724; padding:20px; border-radius:16px; text-align:center; font-weight:700; border:2px solid #c3e6cb; }
        .alert-danger { background:#f8d7da; color:#721c24; padding:20px; border-radius:16px; text-align:center; font-weight:700; }
        .note { text-align:center; color:#666; font-size:14px; margin-top:20px; line-height:1.6; }

        /* Premium Bottom Nav */
        .bottom-nav { position:fixed; bottom:0; left:0; right:0; background:white; display:flex; justify-content:space-around; padding:12px 0 28px; box-shadow:0 -10px 40px rgba(0,0,0,0.12); border-radius:34px 34px 0 0; z-index:1000; }
        .nav-item { color:#888; text-decoration:none; text-align:center; font-size:11px; font-weight:600; padding:8px 12px; border-radius:20px; transition:0.3s; }
        .nav-item i { font-size:26px; display:block; margin-bottom:6px; }
        .nav-item.active { color:var(--green); background:#e8f7f0; box-shadow:0 8px 25px rgba(0,166,81,0.25); }
        .nav-item.active i { transform:scale(1.2); }
        .cart-badge { position:absolute; top:-2px; right:6px; background:#FF3B30; color:white; font-size:11px; font-weight:900; min-width:22px; height:22px; border-radius:50%; display:grid; place-items:center; border:3px solid white; animation:pulseBadge 2s infinite; }
        @keyframes pulseBadge { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo-text">De Grand</div>
        <div style="font-size:18px; opacity:0.9; margin-top:8px;">Restaurant • Calabar</div>
    </header>

    <div class="container">

        <div class="order-id-banner">
            Order ID: <?= $order_ref ?> • <?= $order_date ?>
        </div>

        <?php if ($success_message): ?>
            <div class="alert-success">
                <?= $success_message ?><br><br>
                Redirecting to WhatsApp in 2 seconds...
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Order Summary</h2>
            <?= $order_items_html ?>
            <div class="total-row">TOTAL: ₦<?= number_format($total) ?></div>
        </div>

        <form method="POST">
            <div class="card">
                <h3>Delivery Information</h3>
                <input type="text" name="name" placeholder="Full Name (e.g. Victor Effiong)" value="<?= $_POST['name'] ?? '' ?>" required>
                <input type="tel" name="phone" placeholder="Phone Number (e.g. 08066610571)" value="<?= $_POST['phone'] ?? '' ?>" required>
                <input type="text" name="address" placeholder="Room/Table No. or Full Address" value="<?= $_POST['address'] ?? '' ?>" required>
                <textarea name="notes" rows="3" placeholder="Extra note (optional) e.g. No onions, extra spicy"></textarea>
            </div>

            <button type="submit" class="btn-whatsapp">
                Place Order & Send via WhatsApp
            </button>
        </form>

        <p class="note">
            Your order will be saved and sent to our kitchen instantly!<br>
            We go deliver am hot-hot!
        </p>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="../restaurant.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="../restaurant.php#menu" class="nav-item"><i class="fas fa-utensils"></i><span>Menu</span></a>
        <a href="cart.php" class="nav-item">
            <i class="fas fa-shopping-cart"></i><span>Cart</span>
            <?php if($item_count > 0): ?>
                <span class="cart-badge"><?= $item_count ?></span>
            <?php endif; ?>
        </a>
        <a href="orders.php" class="nav-item"><i class="fas fa-receipt"></i><span>Orders</span></a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
    </nav>

</body>
</html>