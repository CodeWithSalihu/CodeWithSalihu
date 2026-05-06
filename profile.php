<?php
// NO SESSION, NO LOGIN, NO DATABASE → PURE BEAUTY ONLY!
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>My Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --green: #00A651; --darkgreen: #008040; --shadow: 0 20px 50px rgba(0,166,81,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            background:linear-gradient(135deg,#f5f7fa,#e4f5e9);
            color:#222; min-height:100vh; padding-bottom:120px;
        }
        .header {
            background:linear-gradient(135deg,var(--green),var(--darkgreen));
            color:white; text-align:center; padding:50px 20px 70px;
            border-radius:0 0 40px 40px; position:relative; overflow:hidden;
        }
        .header::before {
            content:''; position:absolute; inset:0;
            background:url('images/pattern.png') repeat; opacity:0.1;
        }
        .logo-text { font-size:38px; font-weight:900; letter-spacing:2px; }
        .page-title { font-size:28px; margin-top:20px; opacity:0.95; font-weight:600; }

        .container { max-width:500px; margin:0 auto; padding:0 20px; margin-top:-50px; }

        .profile-card {
            background:white; border-radius:28px; padding:35px; margin-bottom:30px;
            box-shadow:var(--shadow); text-align:center; position:relative;
        }
        .avatar {
            width:140px; height:140px; border-radius:50%; background:var(--green);
            margin:0 auto -70px; border:8px solid white; box-shadow:0 15px 40px rgba(0,166,81,0.3);
            display:flex; align-items:center; justify-content:center; font-size:60px; color:white; font-weight:900;
        }
        .user-name { font-size:28px; font-weight:800; margin:80px 0 8px; color:var(--green); }
        .user-email { font-size:16px; color:#666; margin-bottom:20px; }

        .info-grid {
            display:grid; grid-template-columns:1fr 1fr; gap:20px; margin:30px 0;
        }
        .info-item {
            background:#f8fff8; padding:20px; border-radius:20px; text-align:center;
            border:2px solid #e8f7f0;
        }
        .info-item i { font-size:28px; color:var(--green); margin-bottom:10px; }
        .info-label { font-size:12px; color:#888; text-transform:uppercase; letter-spacing:1px; }
        .info-value { font-size:18px; font-weight:700; margin-top:5px; }

        .form-card {
            background:white; border-radius:28px; padding:35px; box-shadow:var(--shadow);
        }
        .form-card h3 {
            color:var(--green); font-weight:700; margin-bottom:25px; font-size:22px; text-align:center;
        }
        input, button {
            width:100%; padding:18px; margin:12px 0; border-radius:16px; font-size:16px;
            border:2px solid #eee; transition:0.3s;
        }
        input:focus {
            outline:none; border-color:var(--green); box-shadow:0 0 0 4px rgba(0,166,81,0.1);
        }
        .btn-update {
            background:var(--green); color:white; border:none; font-weight:800; cursor:pointer;
            font-size:18px; margin-top:20px;
        }
        .btn-update:hover {
            background:var(--darkgreen); transform:translateY(-4px); box-shadow:0 15px 35px rgba(0,166,81,0.4);
        }

        .bottom-nav {
            position:fixed; bottom:0; left:0; right:0; background:white;
            display:flex; justify-content:space-around; padding:15px 0 30px;
            box-shadow:0 -10px 40px rgba(0,0,0,0.12); border-radius:34px 34px 0 0; z-index:1000;
        }
        .nav-item {
            color:#888; text-decoration:none; text-align:center; font-size:11px; font-weight:600;
        }
        .nav-item i { font-size:26px; display:block; margin-bottom:6px; }
        .nav-item.active { color:var(--green); }
        .nav-item.active i { transform:scale(1.2); }

        .guest-notice {
            background:#fff3cd; color:#856404; padding:20px; border-radius:16px; text-align:center;
            margin:20px 0; font-weight:600; border:2px solid #ffeaa7;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo-text">De Grand</div>
        <div class="page-title">My Profile</div>
    </header>

    <div class="container">

        <!-- Guest Notice -->
        <div class="guest-notice">
            You are viewing as Guest • Login to edit profile
        </div>

        <!-- Profile Card (Static) -->
        <div class="profile-card">
            <div class="avatar">G</div>
            <div class="user-name">Guest User</div>
            <div class="user-email">guest@degrand.com</div>

            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="info-label">Phone</div>
                    <div class="info-value">Not Available</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <div class="info-label">Member Since</div>
                    <div class="info-value">—</div>
                </div>
            </div>
        </div>

        <!-- Fake Update Form (Does Nothing) -->
        <div class="form-card">
            <h3>Update Profile (Login Required)</h3>
            <form onsubmit="event.preventDefault(); alert('Please login to update your profile!');">
                <input type="text" placeholder="Full Name" disabled>
                <input type="email" placeholder="Email Address" disabled>
                <input type="tel" placeholder="Phone Number" disabled>
                <button type="submit" class="btn-update" disabled>
                    Update Profile (Disabled)
                </button>
            </form>
        </div>

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

</body>
</html>