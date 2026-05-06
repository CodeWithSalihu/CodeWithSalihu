<?php 
$page_title   = "Royal Journal"; 
$current_page = "blog"; 
include 'includes/header.php'; 

// Connect to database
require_once 'includes/db_connect.php';

// Fetch only published posts
$stmt = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HANDLE SUBSCRIPTION FORM
$subscribe_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe_email'])) {
    $email = filter_var(trim($_POST['subscribe_email']), FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        try {
            $stmt = $pdo->prepare("INSERT INTO subscribers (email, subscribed_at) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE subscribed_at = NOW()");
            $stmt->execute([$email]);
            $subscribe_msg = "success";
        } catch (Exception $e) {
            $subscribe_msg = "error";
        }
    } else {
        $subscribe_msg = "invalid";
    }
}
?>

<!-- Page Header -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title text-center py-5">
                    <h1 class="display-2 text-gold mb-3" style="font-family: 'Playfair Display', serif; letter-spacing: 2px;">
                        De Grand Journal
                    </h1>
                    <p class="lead text-white opacity-9">Stories of Luxury, Culture & Eternal Elegance</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Royal Blog Section -->
<section class="blog py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center mb-5">
                <h2 class="display-4 text-gold mb-4" style="font-family: 'Playfair Display', serif;">
                    From The De Grand
                </h2>
                <p class="lead text-light-gray max-w-2xl mx-auto">
                    Discover insider stories, travel inspiration, and behind-the-scenes of Africa's most prestigious address.
                </p>
            </div>
        </div>

        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-scroll fa-5x text-gold mb-4 opacity-30"></i>
                <h3 class="text-gold">No Royal Stories Yet</h3>
                <p class="lead text-light-gray">The scribes are preparing something magnificent...</p>
            </div>
        <?php else: ?>
            <div class="row g-5 justify-content-center">
                <?php 
                $delay = 0;
                foreach ($posts as $post): 
                    $date = new DateTime($post['created_at']);
                    $day = $date->format('d');
                    $month = $date->format('M');
                    $read_time = round(str_word_count(strip_tags($post['content'])) / 200) . ' min read';
                ?>
                <div class="col-lg-4 col-md-6">
                    <article class="blog_box royal-blog-card h-100" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="blog_img overflow-hidden position-relative">
                            <?php if ($post['image'] && file_exists("images/blog/".$post['image'])): ?>
                                <img src="images/blog/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid w-100">
                            <?php else: ?>
                                <img src="images/blog-placeholder.jpg" alt="Royal Story" class="img-fluid w-100">
                            <?php endif; ?>
                            
                            <div class="blog-badge <?= strtolower($post['category']) ?>">
                                <?= $post['category'] ?>
                            </div>
                            <div class="blog-date">
                                <span><?= $day ?></span>
                                <small><?= $month ?></small>
                            </div>
                        </div>
                        <div class="blog_room p-5">
                            <h3 class="mb-3">
                                <a href="blog-single.php?id=<?= $post['id'] ?>" 
                                   class="text-decoration-none text-gold hover:text-golden transition-all duration-300 text-2xl leading-tight">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h3>
                            <div class="blog-meta mb-4 text-light-gray small flex items-center gap-4 flex-wrap">
                                <span><i class="far fa-user"></i> <?= htmlspecialchars($post['author']) ?></span>
                                <span><i class="far fa-clock"></i> <?= $read_time ?></span>
                                <span><i class="far fa-calendar"></i> <?= $date->format('d M Y') ?></span>
                            </div>
                            <p class="text-light-gray mb-5 line-clamp-3">
                                <?= htmlspecialchars(substr(strip_tags($post['content']), 0, 180)) ?>...
                            </p>
                            <a href="blog-single.php?id=<?= $post['id'] ?>" 
                               class="read-more text-gold font-semibold inline-flex items-center group">
                                Continue Reading 
                                <i class="fas fa-arrow-right ml-3 transition-transform group-hover:translate-x-2"></i>
                            </a>
                        </div>
                    </article>
                </div>
                <?php 
                $delay += 200;
                endforeach; 
                ?>
            </div>

            <div class="text-center mt-12">
                <a href="all-posts.php" class="btn btn-outline-gold btn-lg px-8 py-4 text-lg font-medium tracking-wider hover:bg-gold hover:text-dark transition-all duration-500 transform hover:scale-105">
                    View All De Grand Stories
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ROYAL SUBSCRIPTION SECTION – NOW FULLY WORKING! -->
<section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);">
    <div class="absolute inset-0 opacity-20">
        <img src="images/pattern-gold.png" alt="" class="w-full h-full object-cover">
    </div>
    
    <div class="container relative z-10">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <div class="max-w-4xl mx-auto">
                    <h2 class="display-4 text-gold mb-5" style="font-family: 'Playfair Display', serif;">
                        Join The Inner Circle
                    </h2>
                    <p class="text-2xl text-light-gray mb-8 leading-relaxed">
                        Subscribe for <span class="text-gold font-bold">exclusive offers</span>, 
                        <span class="text-gold font-bold">De Grand events</span>, and 
                        <span class="text-gold font-bold">insider access</span> reserved only for true De Grand.
                    </p>

                    <!-- WORKING SUBSCRIPTION FORM -->
                    <form method="POST" class="newsletter-form max-w-2xl mx-auto">
                        <div class="input-group d-flex flex-column flex-md-row gap-4 justify-content-center align-items-center">
                            <input type="email" name="subscribe_email" placeholder="Your Email Address" required
                                class="form-control py-4 px-6 text-white bg-slate-900 border border-gold/30 rounded-pill focus:border-gold focus:outline-none transition-all text-lg"
                                style="min-width: 320px;">
                            
                            <button type="submit" class="btn bg-gold text-dark px-8 py-4 font-bold tracking-wider rounded-pill hover:bg-golden transition-all transform hover:scale-105 hover:shadow-2xl">
                                Claim Your Throne
                            </button>
                        </div>
                        <small class="text-gray-400 mt-3 block">Your privacy is sacred. We never share your information.</small>
                    </form>

                    <!-- SUCCESS / ERROR MESSAGES -->
                    <?php if ($subscribe_msg === 'success'): ?>
                        <div class="alert alert-success mt-4 rounded-pill py-3 px-5 d-inline-block shadow-lg">
                            <i class="fas fa-crown text-gold me-2"></i>
                            <strong>Welcome to the Inner Circle!</strong>
                        </div>
                    <?php elseif ($subscribe_msg === 'invalid'): ?>
                        <div class="alert alert-danger mt-4 rounded-pill py-3 px-5 d-inline-block">
                            Please enter a valid email address.
                        </div>
                    <?php elseif ($subscribe_msg === 'error'): ?>
                        <div class="alert alert-warning mt-4 rounded-pill py-3 px-5 d-inline-block">
                            You're already in the Inner Circle!
                        </div>
                    <?php endif; ?>

                    <div class="mt-12 grid grid-cols-3 gap-8 text-center">
                        <div class="perk">
                            <i class="fas fa-gem text-gold text-4xl mb-3"></i>
                            <p class="text-white font-medium">Exclusive Discounts</p>
                        </div>
                        <div class="perk">
                            <i class="fas fa-calendar-star text-gold text-4xl mb-3"></i>
                            <p class="text-white font-medium">VIP Event Access</p>
                        </div>
                        <div class="perk">
                            <i class="fas fa-concierge-bell text-gold text-4xl mb-3"></i>
                            <p class="text-white font-medium">First-Class Treatment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>