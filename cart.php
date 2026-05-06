<?php
session_start();

// FORCE SESSION SETTINGS FOR SUBFOLDER (this fixed everything)
ini_set('session.cookie_path', '/restaurant/');
ini_set('session.cookie_domain', '.degrandhotel.com');
ini_set('session.gc_maxlifetime', 3600);

require_once '../includes/db_connect.php';

// === HANDLE REMOVE ITEM ===
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    $_SESSION['msg'] = "Item removed from cart!";
    header("Location: cart.php");
    exit();
}

// === HANDLE UPDATE QUANTITY ===
if (isset($_POST['update_qty'])) {
    $id = (int)$_POST['id'];
    $qty = max(1, (int)$_POST['qty']);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// === CALCULATE TOTALS SAFELY ===
$cart_items = $_SESSION['cart'] ?? [];
$subtotal = 0;
$item_count = 0;
foreach ($cart_items as $item) {
    $price = (float)($item['price'] ?? 0);
    $qty = (int)($item['quantity'] ?? 1);
    $subtotal += $price * $qty;
    $item_count += $qty;
}
$total = $subtotal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>My Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --green: #00A651; --green-light: #e8f7f0; --red: #FF3B30; --gray: #f8f9fa;
            --text: #1a1a1a; --shadow: 0 20px 50px rgba(0,166,81,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#f5f7fa,#e4f5e9); color:var(--text); padding-bottom:140px; min-height:100vh; }

        .header { background:linear-gradient(135deg,var(--green),#008040); color:white; padding:30px 20px 50px; text-align:center; border-radius:0 0 32px 32px; position:relative; overflow:hidden; }
        .header::before { content:''; position:absolute; inset:0; background:url('../images/pattern.png') repeat; opacity:0.08; }
        .title { font-size:34px; font-weight:900; letter-spacing:-1px; }
        .badge { background:var(--red); color:white; width:38px; height:38px; border-radius:50%; display:inline-grid; place-items:center; font-weight:900; font-size:18px; margin-left:10px; animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

        .container { max-width:500px; margin:0 auto; padding:0 20px; margin-top:-30px; }

        .cart-item { background:white; border-radius:24px; padding:20px; margin:20px 0; box-shadow:var(--shadow); display:flex; gap:16px; position:relative; transition:0.4s; }
        .cart-item:hover { transform:translateY(-8px); box-shadow:0 30px 60px rgba(0,166,81,0.3); }

        .item-img { width:100px; height:100px; border-radius:18px; object-fit:cover; border:4px solid var(--green-light); }

        .item-details { flex:1; }
        .item-name { font-size:18px; font-weight:800; color:#111; margin-bottom:6px; }
        .item-price { font-size:20px; font-weight:900; color:var(--green); }

        .quantity-control { display:flex; align-items:center; gap:16px; margin-top:12px; }
        .qty-btn { width:44px; height:44px; background:var(--green); color:white; border:none; border-radius:50%; font-size:24px; font-weight:bold; cursor:pointer; }
        .qty-btn:hover { background:#008040; transform:scale(1.1); }
        .qty-number { font-size:22px; font-weight:900; min-width:50px; text-align:center; }

        .remove-btn { position:absolute; top:12px; right:12px; background:var(--red); color:white; width:40px; height:40px; border-radius:50%; display:grid; place-items:center; text-decoration:none; font-size:18px; }
        .remove-btn:hover { transform:scale(1.15); }

        .summary { background:white; border-radius:28px; padding:25px; margin:30px 0; box-shadow:var(--shadow); }
        .row { display:flex; justify-content:space-between; margin:14px 0; font-size:18px; }
        .total-row { font-size:24px; font-weight:900; color:var(--green); border-top:3px dashed #ddd; padding-top:20px; margin-top:20px; }

        .checkout-btn { width:100%; padding:22px; background:var(--green); color:white; border:none; border-radius:50px; font-size:22px; font-weight:900; cursor:pointer; box-shadow:0 15px 40px rgba(0,166,81,0.4); transition:0.4s; }
        .checkout-btn:hover { background:#008040; transform:translateY(-6px); box-shadow:0 25px 60px rgba(0,166,81,0.5); }

        .empty-cart { text-align:center; padding:100px 20px; color:#888; }
        .empty-cart i { font-size:90px; color:#ddd; margin-bottom:20px; }

        .bottom-nav { position:fixed; bottom:0; left:0; right:0; background:white; display:flex; justify-content:space-around; padding:16px 0 36px; box-shadow:0 -15px 40px rgba(0,0,0,0.12); border-radius:36px 36px 0 0; z-index:1000; }
        .nav-item { color:#888; text-decoration:none; text-align:center; font-size:11px; font-weight:600; position:relative; }
        .nav-item.active, .nav-item:hover { color:var(--green); }
        .nav-item i { font-size:28px; display:block; margin-bottom:6px; }
        .cart-badge { position:absolute; top:-8px; right:-8px; background:var(--red); color:white; font-size:12px; font-weight:800; min-width:24px; height:24px; border-radius:50%; display:grid; place-items:center; }
    </style>
</head>
<body>

    <header class="header">
        <div class="title">
            My Cart
            <?php if($item_count > 0): ?>
                <span class="badge"><?= $item_count ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">

        <?php if(isset($_SESSION['msg'])): ?>
            <div style="background:#d4edda; color:#155724; padding:16px; border-radius:20px; text-align:center; margin:20px 0; font-weight:700; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
                <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Add some delicious food from the menu!</p>
                <a href="../restaurant.php" class="checkout-btn" style="display:inline-block; width:auto; padding:18px 50px; margin-top:20px;">
                    Browse Menu
                </a>
            </div>
        <?php else: ?>
            <?php foreach($cart_items as $id => $item): 
                $name = $item['name'] ?? 'Unknown Item';
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['quantity'] ?? 1);
                $image = $item['image'] ?? '';
                $img_path = $image ? "../assets/images/products/$image" : "../assets/images/products/placeholder.jpg";
            ?>
                <div class="cart-item">
                    <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($name) ?>" class="item-img" onerror="this.src='../assets/images/products/placeholder.jpg'">

                    <div class="item-details">
                        <div class="item-name"><?= htmlspecialchars($name) ?></div>
                        <div class="item-price">₦<?= number_format($price) ?> each</div>

                        <div class="quantity-control">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="qty" value="<?= $qty - 1 ?>">
                                <button type="submit" name="update_qty" class="qty-btn" <?= $qty <= 1 ? 'disabled' : '' ?>>−</button>
                            </form>

                            <div class="qty-number"><?= $qty ?></div>

                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="qty" value="<?= $qty + 1 ?>">
                                <button type="submit" name="update_qty" class="qty-btn">+</button>
                            </form>
                        </div>
                    </div>

                    <a href="cart.php?remove=<?= $id ?>" class="remove-btn" onclick="return confirm('Remove this item?')">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            <?php endforeach; ?>

            <div class="summary">
                <div class="row"><span>Subtotal</span> <span>₦<?= number_format($subtotal) ?></span></div>
                <div class="row total-row"><span>TOTAL</span> <span>₦<?= number_format($total) ?></span></div>
            </div>

            <a href="checkout.php" class="checkout-btn">
                Proceed to Checkout →
            </a>
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
           

                <script>
// Prevent any script from hijacking navigation links
document.querySelectorAll('.bottom-nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            // Small delay make sure no other script block am
            setTimeout(() => {
                window.location.href = href;
            }, 50);
        }
    });
});
</script>
</body>
</html>