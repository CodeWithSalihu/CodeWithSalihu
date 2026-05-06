<?php
session_start();
require_once 'includes/db_connect.php';

$user_name = $_SESSION['user_name'] ?? 'Foodie';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'] ?? 1;
    }
}

// FETCH CATEGORIES WITH ICONS — LIVE FROM DATABASE!
$stmt = $pdo->query("SELECT name, icon FROM categories ORDER BY sort_order ASC, name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallback categories if empty
if (empty($categories)) {
    $categories = [
        ['name' => 'Burger', 'icon' => 'default.png'],
        ['name' => 'Rice', 'icon' => 'default.png'],
        ['name' => 'Drinks', 'icon' => 'default.png'],
        ['name' => 'Swallow', 'icon' => 'default.png'],
    ];
}

// SMART ICON LOADER — WORKS WITH YOUR UPLOADED IMAGES!
function get_category_icon($icon_name) {
    if (empty($icon_name) || $icon_name === 'default.png') {
        return "images/categories/default.png";
    }
    
    $path = "images/categories/" . $icon_name;
    if (file_exists(__DIR__ . "/" . $path)) {
        return $path;
    }
    
    return "images/categories/default.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>De Grand Restaurant • Order Food</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
    :root {
        --green: #00A651;
        --green-light: #e8f7f0;
        --white: #fff;
        --gray: #f8f9fa;
        --text: #1a1a1a;
        --shadow: 0 15px 35px rgba(0,166,81,0.15);
        --radius: 24px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4f5e9 100%);
        color: var(--text);
        min-height: 100vh;
        padding-bottom: 110px;
        opacity: 0;
        transition: opacity 0.5s;
    }
    body.loaded { opacity: 1; }

    /* ================== DESKTOP (default) ================== */
    .header {
        background: linear-gradient(135deg, var(--green), #008040);
        color: white;
        padding: 32px 20px 40px;
        border-bottom-left-radius: var(--radius);
        border-bottom-right-radius: var(--radius);
        position: relative;
        overflow: hidden;
    }
    .header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('images/pattern.png') repeat;
        opacity: 0.08;
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .greeting { font-size: 16px; opacity: 0.95; font-weight: 500; }
    .user-name { font-size: 28px; font-weight: 900; letter-spacing: -0.5px; }

    .search-container { position: relative; margin-top: 24px; z-index: 2; }
    .search-bar {
        width: 100%;
        padding: 18px 20px 18px 60px;
        border: none;
        border-radius: 20px;
        background: white;
        font-size: 17px;
        box-shadow: var(--shadow);
        outline: none;
        transition: all 0.3s;
    }
    .search-bar:focus {
        transform: scale(1.02);
        box-shadow: 0 20px 50px rgba(0,166,81,0.25);
    }
    .search-icon {
        position: absolute;
        left: 20px; top: 50%;
        transform: translateY(-50%);
        color: var(--green);
        font-size: 24px;
    }

    .section-title {
        padding: 0 24px;
        font-size: 24px;
        font-weight: 800;
        margin: 40px 0 24px;
        color: #111;
        position: relative;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -8px; left: 24px;
        width: 60px; height: 5px;
        background: var(--green);
        border-radius: 10px;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 0 20px;
    }

    .cat-card {
        text-decoration: none;
        color: inherit;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        padding: 12px 8px;
        border-radius: 20px;
    }
    .cat-card:hover {
        transform: translateY(-10px) scale(1.04);
        background: rgba(255,255,255,0.4);
    }
    .cat-icon {
        width: 96px; height: 96px;
        background: white;
        border-radius: 28px;
        display: grid;
        place-items: center;
        margin: 0 auto 14px;
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 4px solid var(--green-light);
        transition: all 0.4s;
    }
    .cat-card:hover .cat-icon {
        background: var(--green-light);
        border-color: var(--green);
        transform: scale(1.12);
    }
    .cat-card img {
        width: 68px; height: 68px;
        object-fit: cover;
        border-radius: 16px;
    }
    .cat-name {
        font-size: 14px;
        font-weight: 700;
        color: #222;
    }

    .bottom-nav {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: white;
        display: flex;
        justify-content: space-around;
        padding: 14px 0 30px;
        box-shadow: 0 -15px 40px rgba(0,0,0,0.12);
        border-radius: 34px 34px 0 0;
        z-index: 1000;
    }
    .nav-item {
        color: #888;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        position: relative;
        transition: 0.3s;
    }
    .nav-item.active { color: var(--green); }
    .nav-item i { font-size: 26px; display: block; margin-bottom: 6px; }
    .cart-badge {
        position: absolute;
        top: -6px; right: -10px;
        background: #FF3B30;
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
        50% { transform: scale(1.15); }
    }

    /* ================== MOBILE — 3 PER ROW, PREMIUM TOUCH ================== */
    @media (max-width: 768px) {
        .header {
            padding: 28px 16px 36px;
            border-radius: 0 0 28px 28px;
        }
        .header-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .greeting { font-size: 15px; order: 2; }
        .user-name { font-size: 26px; order: 1; }
        .profile-btn img {
            width: 52px; height: 52px;
            position: absolute;
            top: 16px; right: 16px;
        }
        .search-bar {
            padding: 16px 16px 16px 52px;
            font-size: 16px;
            border-radius: 18px;
        }
        .search-icon { left: 16px; font-size: 22px; }

        .section-title {
            padding: 0 16px;
            font-size: 22px;
            margin: 32px 0 20px;
        }
        .section-title::after { left: 16px; width: 50px; }

        /* Force 3 columns on mobile — PRO look */
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 18px 14px;
            padding: 0 14px;
        }
        .cat-card {
            padding: 10px 6px;
        }
        .cat-icon {
            width: 84px;
            height: 84px;
            border-radius: 24px;
            border-width: 3px;
        }
        .cat-card img {
            width: 58px;
            height: 58px;
            border-radius: 14px;
        }
        .cat-name {
            font-size: 13px;
            font-weight: 800;
        }

        .bottom-nav {
            padding: 12px 0 28px;
            border-radius: 30px 30px 0 0;
            margin: 0 8px;
        }
        .nav-item i { font-size: 24px; }
        .nav-item { font-size: 10px; }
    }

    /* Extra small phones — still 3 columns, perfectly balanced */
    @media (max-width: 380px) {
        .cat-icon { width: 76px; height: 76px; }
        .cat-card img { width: 52px; height: 52px; }
        .cat-name { font-size: 12.5px; }
        .categories-grid { gap: 16px 10px; padding: 0 10px; }
    }
</style>
</head>
<body>

    <!-- HEADER -->
    <header class="header">
        <div class="header-top">
                        <div class="user-name"><?= htmlspecialchars(explode(' ', $user_name)[0]) ?>!</div>

            <div class="greeting">Welcome back,</div>
            <a href="/profile/index.php" class="profile-btn">
                <img src="images/avatar.jpg" alt="Profile"
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=00A651&color=fff&bold=true&size=128'"
                     style="width:58px;height:58px;border-radius:50%;border:4px solid white;box-shadow:0 8px 25px rgba(0,0,0,0.3);">
            </a>
        </div>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-bar" placeholder="Search food categories..." id="searchInput">
        </div>
    </header>

    <!-- CATEGORIES -->
    <section class="categories-section">
        <h2 class="section-title">Choose Category</h2>
        <div class="categories-grid" id="categoriesGrid">
            <?php foreach ($categories as $cat): 
                $slug = strtolower(str_replace([' ', '&', '/'], '-', $cat['name']));
            ?>
                <a href="restaurant/index.php?cat=<?= urlencode($slug) ?>" class="cat-card">
                    <div class="cat-icon">
                        <img src="<?= get_category_icon($cat['icon']) ?>" 
                             alt="<?= htmlspecialchars($cat['name']) ?>"
                             onerror="this.src='images/categories/default.png'">
                    </div>
                    <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

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
        // Live Search
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.cat-card').forEach(card => {
                const name = card.querySelector('.cat-name').textContent.toLowerCase();
                const visible = query === '' || name.includes(query);
                card.style.opacity = visible ? '1' : '0.3';
                card.style.transform = visible ? 'scale(1)' : 'scale(0.95)';
                card.style.pointerEvents = visible ? 'auto' : 'none';
            });
        });

        // Loading effect
        window.addEventListener('load', () => {
            document.body.style.opacity = '1';
        });
    </script>
</body>
</html>