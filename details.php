<?php
session_start();
// FORCE SESSION SETTINGS FOR SUBFOLDER
ini_set('session.cookie_path', '/restaurant/');  // Match your folder
ini_set('session.cookie_domain', '.degrandhotel.com');  // Allow subdomain if any
ini_set('session.gc_maxlifetime', 3600);  // 1 hour, prevent early expire
require_once '../includes/db_connect.php';

$product_id = (int)$_GET['id'];

// Fetch product
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category = c.name WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ../restaurant.php");
    exit();
}

// Add to cart logic
if (isset($_POST['add_to_cart'])) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $qty
        ];
    }
    
    $_SESSION['msg'] = "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x p-4 shadow-lg' style='z-index:9999; margin-top:20px; border-radius:20px;'>
        <strong>Added to Cart!</strong> {$product['name']} × $qty
    </div>";
    header("Location: details.php?id=$product_id");
    exit();
}

// Related products (same category, exclude current)
$related = $pdo->prepare("SELECT id, name, price, image FROM products WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 6");
$related->execute([$product['category'], $product_id]);
$related_products = $related->fetchAll(PDO::FETCH_ASSOC);

$imagePath = !empty($product['image']) 
    ? "../assets/images/products/" . $product['image'] 
    : "../assets/images/products/placeholder.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> • De Grand Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
    :root {
        --green: #00A651;
        --green-light: #e8f7f0;
        --green-dark: #008040;
        --red: #FF3B30;
        --gray: #f8f9fa;
        --text: #1a1a1a;
        --shadow: 0 20px 50px rgba(0,166,81,0.2);
        --radius: 32px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4f5e9 100%);
        color: var(--text);
        padding-bottom: 140px;
        line-height: 1.6;
    }

    /* ================== HEADER & HERO IMAGE ================== */
    .back-btn {
        position: fixed;
        top: 20px; left: 20px;
        width: 52px; height: 52px;
        background: rgba(255,255,255,0.3);
        backdrop-filter: blur(12px);
        border-radius: 50%;
        display: grid;
        place-items: center;
        z-index: 1000;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        transition: all 0.3s;
        color: white;
        font-size: 20px;
    }
    .back-btn:hover {
        background: white;
        color: var(--green-dark);
        transform: scale(1.1);
    }
    .product-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-bottom-left-radius: var(--radius);
        border-bottom-right-radius: var(--radius);
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    /* ================== MAIN CARD ================== */
    .container {
        max-width: 560px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .details-card {
        background: white;
        margin-top: -80px;
        border-radius: var(--radius);
        padding: 36px;
        box-shadow: var(--shadow);
        position: relative;
        z-index: 5;
        margin-bottom: 40px;
    }
    .category-tag {
        display: inline-block;
        background: var(--green-light);
        color: var(--green);
        padding: 10px 24px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 16px;
        letter-spacing: 0.5px;
    }
    .product-name {
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 12px;
        color: #111;
        line-height: 1.2;
    }
    .product-price {
        font-size: 40px;
        font-weight: 900;
        color: var(--green);
        margin: 16px 0 24px;
    }
    .description {
        font-size: 16.5px;
        color: #444;
        margin: 24px 0;
        line-height: 1.8;
    }

    /* ================== QUANTITY & ADD TO CART ================== */
    .quantity-selector {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        margin: 36px 0;
        padding: 16px;
        background: var(--green-light);
        border-radius: 50px;
    }
    .qty-btn {
        width: 56px; height: 56px;
        background: var(--green);
        color: white;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        user-select: none;
        transition: all 0.3s;
        box-shadow: 0 8px 20px rgba(0,166,81,0.3);
    }
    .qty-btn:hover {
        background: var(--green-dark);
        transform: scale(1.1);
    }
    .qty-display {
        font-size: 32px;
        font-weight: 900;
        min-width: 80px;
        text-align: center;
        color: var(--green-dark);
    }
    .add-to-cart {
        width: 100%;
        padding: 22px;
        background: var(--green);
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 20px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.4s;
        box-shadow: 0 15px 40px rgba(0,166,81,0.4);
    }
    .add-to-cart:hover {
        background: var(--green-dark);
        transform: translateY(-6px);
        box-shadow: 0 25px 60px rgba(0,166,81,0.5);
    }

    /* ================== RELATED PRODUCTS ================== */
    .related-title {
        font-size: 26px;
        font-weight: 800;
        text-align: center;
        margin: 60px 0 30px;
        color: #111;
    }
    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .related-card {
        background: white;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
        transition: all 0.4s;
    }
    .related-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,166,81,0.25);
    }
    .related-img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    .related-info {
        padding: 16px;
        text-align: center;
    }
    .related-name {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 8px;
        color: #222;
    }
    .related-price {
        color: var(--green);
        font-weight: 900;
        font-size: 17px;
    }

    /* ================== BOTTOM NAV ================== */
    .bottom-nav {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: white;
        display: flex;
        justify-content: space-around;
        padding: 16px 0 36px;
        box-shadow: 0 -15px 40px rgba(0,0,0,0.15);
        border-radius: 38px 38px 0 0;
        z-index: 1000;
        margin: 0 10px;
    }
    .nav-item {
        color: #888;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: 0.3s;
    }
    .nav-item.active, .nav-item:hover { color: var(--green); }
    .nav-item i { font-size: 28px; display: block; margin-bottom: 6px; }
    .cart-badge {
        position: absolute;
        top: -8px; right: -10px;
        background: var(--red);
        color: white;
        font-size: 12px;
        font-weight: 800;
        min-width: 24px;
        height: 24px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%,100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    /* ================== MOBILE OPTIMIZATION ================== */
    @media (max-width: 768px) {
        .product-image { height: 380px; }
        .details-card {
            margin-top: -70px;
            padding: 28px 24px;
        }
        .product-name { font-size: 28px; }
        .product-price { font-size: 36px; }
        .description { font-size: 16px; }
        .quantity-selector { gap: 20px; padding: 14px; }
        .qty-btn { width: 52px; height: 52px; font-size: 26px; }
        .qty-display { font-size: 28px; }
        .add-to-cart { padding: 20px; font-size: 19px; }
        .related-title { font-size: 24px; margin: 50px 0 24px; }
        .related-grid { gap: 18px; }
        .bottom-nav { padding: 14px 0 32px; margin: 0 8px; }
        .nav-item i { font-size: 26px; }
    }

    @media (max-width: 480px) {
        .back-btn { top: 16px; left: 16px; width: 48px; height: 48px; }
        .product-image { height: auto; }
        .details-card { margin-top: -60px; padding: 24px 20px; }
        .product-name { font-size: 26px; }
        .product-price { font-size: 32px; }
        .quantity-selector { padding: 12px; }
        .qty-btn { width: 48px; height: 48px; font-size: 24px; }
        .related-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
</head>
<body>

    <a href="javascript:history.back()" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>

    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image"
         onerror="this.src='../assets/images/products/placeholder.jpg'">

    <div class="container">
        <div class="details-card">
            <div class="category-tag"><?= htmlspecialchars($product['category_name'] ?? $product['category']) ?></div>
            <h1 class="product-name"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="product-price">₦<?= number_format($product['price']) ?></div>
            
            <p class="description">
                <?= nl2br(htmlspecialchars($product['description'] ?? 'Delicious and freshly prepared just for you. Made with love at De Grand Restaurant.')) ?>
            </p>

            <form method="POST">
                <div class="quantity-selector">
                    <div class="qty-btn" onclick="changeQty(-1)">−</div>
                    <div class="qty-display">1</div>
                    <div class="qty-btn" onclick="changeQty(1)">+</div>
                </div>
                <input type="hidden" name="quantity" value="1" id="qtyInput">
                <button type="submit" name="add_to_cart" class="add-to-cart">
                    Add to Cart
                </button>
            </form>
        </div>

        <?php if (!empty($related_products)): ?>
            <h3 class="related-title">You Might Also Like</h3>
            <div class="related-grid">
                <?php foreach ($related_products as $r): 
                    $rImg = !empty($r['image']) ? "../assets/images/products/".$r['image'] : "../assets/images/products/placeholder.jpg";
                ?>
                    <a href="details.php?id=<?= $r['id'] ?>" class="related-card">
                        <img src="<?= $rImg ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="related-img"
                             onerror="this.src='../assets/images/products/placeholder.jpg'">
                        <div class="related-info">
                            <div class="related-name"><?= htmlspecialchars($r['name']) ?></div>
                            <div class="related-price">₦<?= number_format($r['price']) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success Message -->
    <?php if (isset($_SESSION['msg'])): ?>
        <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
        <script>setTimeout(() => document.querySelector('.alert')?.remove(), 3000);</script>
    <?php endif; ?>

        <!-- Bottom Nav - FIXED & PREMIUM -->
    <nav class="bottom-nav">
        <a href="../restaurant.php" class="nav-item">
            <i class="fas fa-home"></i>Home
        </a>

        <a href="cart.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'cart.php') ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i>Cart
            <?php
            // Calculate cart item count properly
            $item_count = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $item_count += $item['quantity'] ?? 1;
                }
            }
            if ($item_count > 0):
            ?>
                <span class="cart-badge"><?= $item_count ?></span>
            <?php endif; ?>
        </a>

        <a href="../restaurant/my_orders.php" class="nav-item">
            <i class="fas fa-receipt"></i>Orders
        </a>

        <a href="../restaurant/index.php" class="nav-item">
            <i class="fas fa-user"></i>Profile
        </a>
    </nav>

    <script>
        function changeQty(change) {
            let input = document.getElementById('qtyInput');
            let display = document.querySelector('.qty-display');
            let current = parseInt(input.value);
            let newQty = current + change;
            if (newQty < 1) newQty = 1;
            input.value = newQty;
            display.textContent = newQty;
        }
    </script>
</body>
</html>