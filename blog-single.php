<?php
$current_page = "blog";
require_once 'includes/db_connect.php';

// Define the site URL
$site_url = "https://www.degrandhotel.com"; // <-- Change this to your live domain


if (!isset($_GET['id'])) {
    header('Location: blog.php');
    exit();
}

$post_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    $page_title = "Story Not Found";
}

include 'includes/header.php';
?>

<?php if (!$post): ?>
<!-- 404 ROYAL STYLE -->
<section class="min-vh-100 d-flex align-items-center justify-content-center bg-dark text-center py-5">
    <div class="container">
        <i class="fas fa-crown fa-6x text-gold mb-5 opacity-30"></i>
        <h1 class="display-1 text-gold" style="font-family: 'Playfair Display', serif;">De Grand Story Lost</h1>
        <p class="lead text-light-gray mb-5">The scribes could not find this tale in the kingdom's archives...</p>
        <a href="blog.php" class="btn btn-gold px-8 py-4 text-2xl">Return to De Grand Journal</a>
    </div>
</section>
<?php include 'includes/footer.php'; exit(); endif; ?>

<!-- HERO HEADER -->
<div class="position-relative overflow-hidden" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('images/blog/<?= htmlspecialchars($post['image'] ?? 'hero-bg.jpg') ?>') center/cover no-repeat; min-height: 80vh;">
    <div class="container position-relative" style="padding-top: 15vh;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8 text-center text-white">
                <span class="badge badge-<?= strtolower($post['category']) ?> fs-4 px-4 py-2 mb-4">
                    <?= $post['category'] ?>
                </span>
                <h1 class="display-1 fw-bold mb-4" style="font-family: 'Playfair Display', serif; line-height: 1.2;">
                    <?= htmlspecialchars($post['title']) ?>
                </h1>
                <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap mb-5 text-light-gray">
                    <span><i class="far fa-user text-gold"></i> <?= htmlspecialchars($post['author']) ?></span>
                    <span><i class="far fa-calendar text-gold"></i> <?= date('d F Y', strtotime($post['created_at'])) ?></span>
                    <span><i class="far fa-clock text-gold"></i> <?= round(str_word_count(strip_tags($post['content'])) / 200) ?> min read</span>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute bottom-0 start-50 translate-middle-x w-100" style="height: 150px; background: linear-gradient(transparent, #0F172A);"></div>
</div>

<!-- MAIN CONTENT -->
<article class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Share Buttons -->
                <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
    <span class="text-gold small">Share this De Grand tale:</span>

    <!-- WhatsApp -->
    <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' - The De Grand Hotel ' . $site_url . '/blog-single.php?id=' . $post['id']) ?>" 
       target="_blank" class="btn btn-success rounded-circle p-3" aria-label="Share on WhatsApp">
        <i class="fab fa-whatsapp fa-lg"></i>
    </a>

    <!-- Twitter -->
    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode($site_url . '/blog-single.php?id=' . $post['id']) ?>"
       target="_blank" class="btn btn-info rounded-circle p-3 text-white" aria-label="Share on Twitter">
        <i class="fab fa-twitter"></i>
    </a>

    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($site_url . '/blog-single.php?id=' . $post['id']) ?>"
       target="_blank" class="btn btn-primary rounded-circle p-3" aria-label="Share on Facebook">
        <i class="fab fa-facebook-f"></i>
    </a>
</div>


                <!-- Article Content -->
                <div class="blog-content lead fs-4 text-light-gray" style="line-height: 2; font-family: 'Georgia', serif;">
                    <?= $post['content'] ?>
                </div>

                <!-- Tags -->
                <div class="mt-8 pt-8 border-top border-gold/30">
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <span class="badge bg-gold text-dark px-4 py-3 fs-6">#DeGrandsHotel</span>
                        <span class="badge bg-gold text-dark px-4 py-3 fs-6">#LagosLuxury</span>
                        <span class="badge bg-gold text-dark px-4 py-3 fs-6">#<?= $post['category'] ?></span>
                        <span class="badge bg-gold text-dark px-4 py-3 fs-6">#DeGrandLiving</span>
                    </div>
                </div>

                <!-- Author Bio -->
                <div class="card bg-dark border-gold/50 mt-8 p-5 rounded-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="images/author.jpg" alt="Author" class="rounded-circle border border-gold border-4" width="120">
                        </div>
                        <div class="col">
                            <h4 class="text-gold mb-2" style="font-family: 'Playfair Display', serif;">
                                <?= htmlspecialchars($post['author']) ?>
                            </h4>
                            <p class="text-light-gray mb-0">
                                Royal Scribe & Chief Storyteller at The Royals Hotel. 
                                Capturing moments of luxury, culture, and African excellence.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="d-flex justify-content-between mt-8 pt-5 border-top border-gold/30 flex-wrap gap-4">
                    <?php
                    $prev = $pdo->query("SELECT id, title FROM blog_posts WHERE status='published' AND id < $post_id ORDER BY id DESC LIMIT 1")->fetch();
                    $next = $pdo->query("SELECT id, title FROM blog_posts WHERE status='published' AND id > $post_id ORDER BY id ASC LIMIT 1")->fetch();
                    ?>
                    <div>
                        <?php if ($prev): ?>
                            <a href="blog-single.php?id=<?= $prev['id'] ?>" class="text-gold hover:text-golden d-inline-flex align-items-center gap-3">
                                <i class="fas fa-arrow-left fa-2x"></i>
                                <div>
                                    <small class="d-block text-light-gray">Previous Story</small>
                                    <strong><?= htmlspecialchars($prev['title']) ?></strong>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="text-center">
                        <a href="blog.php" class="btn btn-outline-gold px-6 py-3">
                            All De Grand Stories
                        </a>
                    </div>
                    <div class="text-end">
                        <?php if ($next): ?>
                            <a href="blog-single.php?id=<?= $next['id'] ?>" class="text-gold hover:text-golden d-inline-flex align-items-center gap-3 flex-row-reverse">
                                <i class="fas fa-arrow-right fa-2x"></i>
                                <div>
                                    <small class="d-block text-light-gray">Next Story</small>
                                    <strong><?= htmlspecialchars($next['title']) ?></strong>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- RELATED POSTS -->
<section class="py-20" style="background: #0F172A;">
    <div class="container">
        <h2 class="display-4 text-center text-gold mb-5" style="font-family: 'Playfair Display', serif;">
            More De Grand Tales
        </h2>
        <div class="row g-5 justify-content-center">
            <?php
            $related = $pdo->prepare("SELECT * FROM blog_posts WHERE status='published' AND category = ? AND id != ? ORDER BY RAND() LIMIT 3");
            $related->execute([$post['category'], $post_id]);
            foreach ($related->fetchAll() as $rel): 
                $date = date('d M', strtotime($rel['created_at']));
            ?>
            <div class="col-lg-4">
                <article class="card bg-dark border-gold/30 h-100 overflow-hidden rounded-4 hover:shadow-2xl transition-all">
                    <div class="position-relative">
                        <img src="images/blog/<?= htmlspecialchars($rel['image'] ?? 'placeholder.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($rel['title']) ?>" style="height: 280px; object-fit: cover;">
                        <div class="position-absolute top-0 start-0 p-4">
                            <span class="badge badge-<?= strtolower($rel['category']) ?> fs-6"><?= $rel['category'] ?></span>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <h3 class="card-title text-gold h4 mb-3" style="font-family: 'Playfair Display', serif;">
                            <a href="blog-single.php?id=<?= $rel['id'] ?>" class="text-decoration-none hover:text-golden">
                                <?= htmlspecialchars($rel['title']) ?>
                            </a>
                        </h3>
                        <p class="text-light-gray line-clamp-3">
                            <?= substr(strip_tags($rel['content']), 0, 120) ?>...
                        </p>
                        <a href="blog-single.php?id=<?= $rel['id'] ?>" class="text-gold font-semibold mt-3 d-inline-block">
                            Read More <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>