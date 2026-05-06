<?php 
$page_title   = "All Royal Stories"; 
$current_page = "blog"; 
include 'includes/header.php'; 

require_once 'includes/db_connect.php';

// Fetch all published posts
$stmt = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY id DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count by category
$categories = [
    'LIFESTYLE' => $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE category='LIFESTYLE' AND status='published'")->fetchColumn(),
    'EXCLUSIVE' => $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE category='EXCLUSIVE' AND status='published'")->fetchColumn(),
    'CULINARY'  => $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE category='CULINARY' AND status='published'")->fetchColumn(),
];
$total_posts = array_sum($categories);

// Recent posts for sidebar
$recent = $pdo->query("SELECT id, title, image, created_at FROM blog_posts WHERE status='published' ORDER BY created_at DESC LIMIT 4")->fetchAll();
?>

<!-- Page Header -->
<div class="back_re position-relative">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title text-center py-5">
                    <h1 class="text-gold" style="font-family: 'Playfair Display', serif; font-size: 4.5rem;">
                        All Royal Stories
                    </h1>
                    <p class="lead text-white opacity-9">Every Moment • Every Memory • Every Crown</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Archive Section -->
<section class="blog-archive py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-5">
                <div class="sidebar sticky-top" style="top: 120px;">
                    <!-- Search -->
                    <div class="widget mb-5 p-4 bg-dark rounded-4 border border-gold">
                        <h4 class="text-gold mb-4">Search Stories</h4>
                        <form class="search-form" action="search.php" method="GET">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control royal-input bg-dark border-gold text-white" placeholder="Search royal stories..." required>
                                <button type="submit" class="btn btn-gold">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="widget mb-5 p-4 bg-dark rounded-4 border border-gold">
                        <h4 class="text-gold mb-4">Categories</h4>
                        <ul class="category-list list-unstyled">
                            <li class="mb-3"><a href="all-posts.php" class="text-white active">All Stories <span class="float-end badge bg-gold text-dark"><?= $total_posts ?></span></a></li>
                            <li class="mb-3"><a href="all-posts.php?cat=LIFESTYLE" class="text-light">Lifestyle <span class="float-end badge bg-secondary"><?= $categories['LIFESTYLE'] ?></span></a></li>
                            <li class="mb-3"><a href="all-posts.php?cat=EXCLUSIVE" class="text-light">Exclusive <span class="float-end badge bg-danger"><?= $categories['EXCLUSIVE'] ?></span></a></li>
                            <li class="mb-3"><a href="all-posts.php?cat=CULINARY" class="text-light">Culinary <span class="float-end badge bg-warning text-dark"><?= $categories['CULINARY'] ?></span></a></li>
                        </ul>
                    </div>

                    <!-- Recent Posts -->
                    <div class="widget p-4 bg-dark rounded-4 border border-gold">
                        <h4 class="text-gold mb-4">Recent Stories</h4>
                        <?php foreach($recent as $r): ?>
                        <div class="recent-post d-flex mb-4 align-items-center">
                            <?php if($r['image'] && file_exists("images/blog/{$r['image']}")): ?>
                                <img src="images/blog/<?= $r['image'] ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="me-3 rounded" style="width:80px;height:80px;object-fit:cover;">
                            <?php else: ?>
                                <img src="images/blog-placeholder.jpg" class="me-3 rounded" style="width:80px;height:80px;object-fit:cover;">
                            <?php endif; ?>
                            <div>
                                <h6 class="text-gold mb-1">
                                    <a href="blog-single.php?id=<?= $r['id'] ?>" class="text-gold text-decoration-none">
                                        <?= strlen($r['title']) > 40 ? substr(htmlspecialchars($r['title']),0,40).'...' : htmlspecialchars($r['title']) ?>
                                    </a>
                                </h6>
                                <small class="text-light-gray">
                                    <?= date('d M Y', strtotime($r['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <?php if (empty($posts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-crown fa-5x text-gold opacity-20 mb-4"></i>
                        <h3 class="text-gold">No Royal Stories Yet</h3>
                        <p class="text-light-gray">The scribes are preparing legendary tales...</p>
                    </div>
                <?php else: ?>
                    <div class="row g-5">
                        <?php foreach($posts as $post): ?>
                        <div class="col-lg-6">
                            <article class="blog_box royal-blog-card h-100 bg-dark border border-gold rounded-4 overflow-hidden shadow-lg hover-lift">
                                <div class="blog_img position-relative overflow-hidden">
                                    <?php if($post['image'] && file_exists("images/blog/{$post['image']}")): ?>
                                        <img src="images/blog/<?= $post['image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid w-100" style="height:350px;object-fit:cover;transition:0.5s;">
                                    <?php else: ?>
                                        <img src="images/blog-placeholder.jpg" class="img-fluid w-100" style="height:350px;object-fit:cover;">
                                    <?php endif; ?>
                                    <div class="blog-badge position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill text-white fw-bold" 
                                         style="background: <?= $post['category']=='EXCLUSIVE'?'#dc2626':($post['category']=='CULINARY'?'#f59e0b':'#8b5cf6') ?>;">
                                        <?= $post['category'] ?>
                                    </div>
                                </div>
                                <div class="blog_room p-4">
                                    <h3 class="text-gold mb-3 fs-4">
                                        <a href="blog-single.php?id=<?= $post['id'] ?>" class="text-gold text-decoration-none hover-gold">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h3>
                                    <div class="blog-meta mb-3 text-light-gray small">
                                        <span><i class="fas fa-user me-1"></i> <?= htmlspecialchars($post['author']) ?></span> • 
                                        <span><i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($post['created_at'])) ?></span> • 
                                        <span><i class="fas fa-clock me-1"></i> <?= round(str_word_count(strip_tags($post['content'])) / 200) ?> min read</span>
                                    </div>
                                    <p class="text-light-gray line-clamp-3">
                                        <?= strlen(strip_tags($post['content'])) > 180 ? substr(strip_tags($post['content']), 0, 180).'...' : strip_tags($post['content']) ?>
                                    </p>
                                    <a href="blog-single.php?id=<?= $post['id'] ?>" class="read-more text-gold fw-bold">
                                        Read Full Story <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination (Simple for now - can be enhanced) -->
                    <?php if (count($posts) > 6): ?>
                    <div class="text-center mt-5">
                        <a href="#" class="btn btn-gold px-5 py-3">Load More Stories</a>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.hover-lift:hover { transform: translateY(-15px); box-shadow: 0 30px 60px rgba(212,175,55,0.3) !important; }
.hover-lift img:hover { transform: scale(1.08); }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.blog-badge { font-size: 0.9rem; letter-spacing: 1px; }
</style>

<?php include 'includes/footer.php'; ?>