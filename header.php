<?php
// includes/header.php - DE GRAND HOTEL & ROOFTOP, CALABAR
$page_title   = $page_title ?? 'Luxury Hotel & Rooftop in Calabar';
$current_page = $current_page ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">

    <title>De Grand Hotel & Rooftop | <?php echo htmlspecialchars($page_title); ?></title>

    <meta name="description" content="De Grand Hotel & Rooftop – Nothing but LUXURY. Premium accommodation and the most iconic rooftop experience in Calabar, Cross River State.">
    <meta name="keywords" content="de grand hotel, luxury hotel calabar, rooftop calabar, boutique hotel nigeria, 5 star hotel cross river">
    <meta name="author" content="De Grand Hotel & Rooftop">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Cinzel:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">




<style>
    :root {
        --dg-gold: #D4AF37;
        --dg-deepgold: #B8860B;
        --dg-black: #000000;
        --dg-dark: #0f0f0f;
    }

    body { font-family: 'Inter', sans-serif; background: #0F172A; color: #fff; }
    h1, h2, h3, h4, .navbar-brand { font-family: 'Cinzel', serif; letter-spacing: 1.5px; }

    /* NAVBAR */
    .navbar {
        background: #0F172A;
        backdrop-filter: blur(15px);
        padding: 1.2rem 0;
        transition: all 0.5s ease;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 9999;
        border-bottom: 1px solid rgba(212,175,55,0.2);
    }
    .navbar.scrolled {
        padding: 0.7rem 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.7);
    }

    /* LOGO */
    .navbar-brand img {
        height: 65px;
        filter: drop-shadow(0 0 15px rgba(212,175,55,0.6));
        transition: all 0.4s ease;
        
    }
    .navbar-brand:hover img {
        transform: scale(1.08);
    }

    /* NAV LINKS */
    .nav-link {
        color: #e0e0e0 !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.95rem;
        letter-spacing: 2px;
        padding: 0.7rem 1.3rem !important;
        position: relative;
        transition: all 0.4s ease;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 3px;
        bottom: 5px;
        left: 50%;
        background: var(--dg-gold);
        transition: all 0.4s ease;
        transform: translateX(-50%);
    }
    .nav-link:hover::after,
    .nav-link.active::after {
        width: 80%;
    }
    .nav-link:hover,
    .nav-link.active {
        color: var(--dg-gold) !important;
    }

    .book-now-btn {
        background: linear-gradient(135deg, var(--dg-deepgold), var(--dg-gold));
        color: white !important;
        border: none;
        padding: 12px 30px !important;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        box-shadow: 0 8px 25px rgba(212,175,55,0.4);
        transition: all 0.4s ease;
    }
    .book-now-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(212,175,55,0.6);
    }

    /* GOLD HAMBURGER MENU - BIG, VISIBLE, SMOOTH */
    .navbar-toggler {
        border: none !important;
        padding: 12px 10px;
        margin-right: 10px;
        border-radius: 12px;
        transition: all 0.4s ease;
        position: relative;
        width: 80px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .navbar-toggler:focus,
    .navbar-toggler:hover {
        outline: none !important;
        box-shadow: 0 0 0 5px rgba(212, 175, 55, 0.3) !important;
        background: rgba(212, 175, 55, 0.15) !important;
    }

    /* GOLD HAMBURGER ICON (3 LINES) */
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23D4AF37' stroke-linecap='round' stroke-miterlimit='10' stroke-width='3.5' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        width: 34px !important;
        height: 34px !important;
        transition: all 0.4s ease;
    }

    /* WHEN MENU IS OPEN → BECOMES GOLD "X" */
    .navbar-toggler[aria-expanded="true"] {
        background: rgba(212, 175, 55, 0.25);
        box-shadow: 0 0 0 7px rgba(212, 175, 55, 0.4) !important;
        transform: rotate(90deg);
    }
    .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23D4AF37' stroke-linecap='round' stroke-miterlimit='10' stroke-width='4' d='M6 6L24 24M6 24L24 6'/%3e%3c/svg%3e") !important;
    }

    /* SMOOTH MENU OPEN/CLOSE ANIMATION */
    .navbar-collapse {
        transition: all 0.5s cubic-bezier(0.77, 0, 0.175, 1);
    }
/* LOADER */
 /* PRELOADER */
        .loader_bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: #000;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease;
        }
        .loader {
            width: 90px;
            height: 90px;
            border: 5px solid rgba(212,175,55,0.2);
            border-top-color: var(--dg-gold);
            border-radius: 50%;
            animation: spin 1.8s linear infinite;
            position: relative;
        }
        .loader::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 40px; height: 40px;
            background: url('images/logo.jpg') center/contain no-repeat;
            transform: translate(-50%, -50%);
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .navbar-brand img {
                height: 65px;
                margin-left: 10px;
            }
        }







/* DROPDOWN STYLING - LUXURY GOLDEN TOUCH */
.dropdown-toggle::after {
    color: var(--dg-gold);
    border-top-color: var(--dg-gold) !important;
    border-right-color: var(--dg-gold) !important;
    border-bottom: 0;
    border-left: 0;
    vertical-align: middle;
    margin-left: 8px;
    transition: all 0.4s ease;
}

.dropdown-item {
    color: #e0e0e0 !important;
    font-weight: 600;
    letter-spacing: 1.5px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.dropdown-item:hover {
    background: rgba(212,175,55,0.15);
    color: var(--dg-gold) !important;
    padding-left: 30px !important;
    transform: translateX(8px);
}

.dropdown-menu {
    background: rgba(15,15,15,0.98);
    border: 1px solid rgba(212,175,55,0.2);
    box-shadow: 0 20px 40px rgba(0,0,0,0.7);
    border-radius: 12px;
    overflow: hidden;
}




@keyframes spin {
    to { transform: rotate(360deg); }
}



    @media (max-width: 768px) {
   .navbar-brand img {
    height: 65px;
    filter: drop-shadow(0 0 15px rgba(212, 175, 55, 0.6));
    transition: all 0.4s 
ease;
    border-radius: 30px;
    margin-left: 40px;
}
}
</style>


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XHDSX542H0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XHDSX542H0');
</script>

</head>

<body class="main-layout">

    <!-- DE GRAND PRELOADER -->
<div class="loader_bg">
    <div class="loader">
        <div class="loader-logo"></div>
    </div>
</div>


    <!-- HEADER -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo.jpg" alt="De Grand Hotel Logo">
                   
                </a>

               <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

               <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto text-center">

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>" href="/">Home</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="about">About</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'rooms' ? 'active' : ''; ?>" href="rooms">Rooms</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'rooftop' ? 'active' : ''; ?>" href="rooftop">Rooftop</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'restaurant' ? 'active' : ''; ?>" href="/restaurant/index.php">Restaurant</a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                More
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow-lg bg-dark" aria-labelledby="moreDropdown">
                <a class="dropdown-item text-white py-3 px-4 text-uppercase font-weight-bold" href="blog-single">News & Events</a>
                <a class="dropdown-item text-white py-3 px-4 text-uppercase font-weight-bold" href="gallery">Gallery</a>
                <a class="dropdown-item text-white py-3 px-4 text-uppercase font-weight-bold" href="contact">Contact Us</a>
            </div>
        </li>

        <li class="nav-item">
            <a href="booking" class="nav-link book-now-btn">Book Now</a>
        </li>

    </ul>
</div>

            </div>
        </nav>
    </header>

    <!-- Scroll & Loader Script -->
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    window.addEventListener('load', function() {
        const loader = document.querySelector('.loader_bg');
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 500);
    });
</script>

<!-- Required JS (Correct Order) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>