<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: blog-posts.php');
    exit();
}

$post_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['error'] = "Story not found!";
    header('Location: blog-posts.php');
    exit();
}

$success = $error = '';

// ABSOLUTE PATH TO BLOG IMAGES FOLDER (FIXES XAMPP ISSUE!)
$blog_dir = dirname(__DIR__) . '/images/blog/';
if (!is_dir($blog_dir)) {
    mkdir($blog_dir, 0755, true); // Create folder if missing
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $author   = trim($_POST['author']) ?: 'Royal Editor';
    $category = $_POST['category'];
    $status   = $_POST['status'];
    $content  = $_POST['content'];

    if (empty($title) || empty($content)) {
        $error = "Title and content are required!";
    } else {
        $image_name = $post['image']; // Keep old image by default

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $size     = $_FILES['image']['size'];
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $size <= 5 * 1024 * 1024) {
                $image_name = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_path = $blog_dir . $image_name;

                // MOVE WITH FULL ABSOLUTE PATH + ERROR CHECK
                if (move_uploaded_file($tmp_name, $upload_path)) {
                    // Delete old image if exists and it's different
                    if ($post['image'] && $post['image'] !== $image_name) {
                        $old_image = $blog_dir . $post['image'];
                        if (file_exists($old_image)) {
                            @unlink($old_image);
                        }
                    }
                } else {
                    $error = "Failed to save image. Check folder permissions (images/blog must be writable).";
                    $image_name = $post['image']; // fallback
                }
            } else {
                $error = "Invalid image format or too large (max 5MB). Allowed: JPG, PNG, WebP";
            }
        }

        if (!$error) {
            try {
                $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, author=?, category=?, status=?, content=?, image=? WHERE id=?");
                $stmt->execute([$title, $author, $category, $status, $content, $image_name, $post_id]);

                $_SESSION['success'] = "Royal Story updated successfully!";
                header("Location: blog-posts.php");
                exit();
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Story - Royal Journal</title>
    <link rel="icon" href="../images/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- YOUR OFFICIAL TINYMCE API KEY -->
    <script src="https://cdn.tiny.cloud/1/0pzyky4dyekgxu44qdska88bm3b9p0ky3syi06y70ersvmet/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        body { background: #0F172A; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .sidebar { background: #1E293B; min-height: 100vh; border-right: 4px solid #D4AF37; }
        .sidebar a { color: #cbd5e1; padding: 18px 25px; display: flex; align-items: center; transition: all 0.4s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #D4AF37, #F4D03F); color: #000 !important; font-weight: 700; border-left-color: #000; transform: translateX(8px); }
        .sidebar a i { width: 30px; font-size: 18px; margin-right: 12px; }
        .card { background: rgba(15,23,42,0.95); border: 2px solid #D4AF37; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); backdrop-filter: blur(10px); }
        .text-gold { color: #D4AF37 !important; }
        .btn-gold { background: linear-gradient(45deg, #D4AF37, #F4D03F); color: #000; border: none; font-weight: bold; padding: 14px 40px; border-radius: 50px; }
        .btn-gold:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(212,175,55,0.4); }
        .form-control, .form-select { background: #1e293b; border: 1px solid #475569; color: white; }
        .form-control:focus { background: #1e293b; border-color: #D4AF37; box-shadow: 0 0 0 0.2rem rgba(212,175,55,0.25); color: white; }
        .preview-img { max-width: 100%; max-height: 500px; object-fit: cover; border-radius: 16px; margin: 20px 0; border: 4px solid #D4AF37; }
        .current-image { border: 4px solid #D4AF37; border-radius: 16px; max-height: 400px; }
        .alert-fixed { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-fixed rounded-4 shadow-lg">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    </div>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <div class="text-center py-4" style="background: linear-gradient(135deg, #000, #1a1a2e); border-bottom: 4px solid #D4AF37;">
                <h4 class="text-gold mb-0" style="font-family: 'Playfair Display', serif;">Royals Admin</h4>
            </div>
            <div class="mt-4">
                <a href="index.php?page=dashboard">Dashboard</a>
                <a href="index.php?page=bookings">All Bookings</a>
                <a href="index.php?page=messages">Messages</a>
                <a href="blog-posts.php" class="active">Royal Journal</a>
                <a href="logout.php" class="text-danger">Logout</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="text-gold" style="font-family: 'Playfair Display', serif; font-size: 3.5rem;">
                    Edit Royal Story
                </h1>
                <a href="blog-posts.php" class="btn btn-outline-light px-4">Back to Journal</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4 p-4 mb-4 border border-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="card p-5">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="text-gold fs-5 mb-2">Story Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-4">
                            <label class="text-gold fs-5 mb-2">Author</label>
                            <input type="text" name="author" value="<?= htmlspecialchars($post['author']) ?>" class="form-control">
                        </div>
                    </div>

                    <div class="row g-4 mt-3">
                        <div class="col-md-6">
                            <label class="text-gold fs-5 mb-2">Category</label>
                            <select name="category" class="form-select form-select-lg" required>
                                <option value="LIFESTYLE" <?= $post['category']=='LIFESTYLE'?'selected':'' ?>>LIFESTYLE</option>
                                <option value="EXCLUSIVE" <?= $post['category']=='EXCLUSIVE'?'selected':'' ?>>EXCLUSIVE</option>
                                <option value="CULINARY" <?= $post['category']=='CULINARY'?'selected':'' ?>>CULINARY</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold fs-5 mb-2">Status</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="published" <?= $post['status']=='published'?'selected':'' ?>>Published</option>
                                <option value="draft" <?= $post['status']=='draft'?'selected':'' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-gold fs-5 mb-3">Current Featured Image</label>
                        <?php if ($post['image'] && file_exists("../images/blog/" . $post['image'])): ?>
                            <img src="../images/blog/<?= htmlspecialchars($post['image']) ?>" class="current-image d-block">
                            <p class="text-muted mt-2"><small>Upload new image to replace</small></p>
                        <?php else: ?>
                            <p class="text-muted">No image uploaded yet</p>
                        <?php endif; ?>
                        
                        <label class="text-gold fs-5 mb-2 mt-4">Change Image</label>
                        <input type="file" name="image" accept="image/*" class="form-control form-control-lg" onchange="previewImage(event)">
                        <div class="mt-3">
                            <img id="imagePreview" class="preview-img" style="display:none;">
                        </div>
                        <small class="text-muted">Max 5MB • JPG, PNG, WebP</small>
                    </div>

                    <div class="mt-5">
                        <label class="text-gold fs-5 mb-3">Story Content</label>
                        <textarea name="content" id="editor"><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-gold btn-lg px-5 py-4 fs-4">
                            Update Royal Story
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    if (event.target.files[0]) {
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.style.display = 'block';
    }
}

tinymce.init({
    selector: '#editor',
    height: 650,
    plugins: 'image lists link table media code preview fullscreen wordcount',
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code preview fullscreen',
    menubar: 'file edit view insert format tools',
    branding: false,
    promotion: false,
    content_style: "body { font-family: Inter; color: #e2e8f0; background: #1e293b; padding: 20px; }"
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>