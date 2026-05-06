<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);

    if ($image && file_exists("../images/blog/$image")) {
        unlink("../images/blog/$image");
    }

    $_SESSION['success'] = "Royal story deleted successfully!";
    header("Location: blog-posts.php");
    exit();
}

// Fetch all posts
$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>De Grand Admin</title>
    <link rel="icon" href="../images/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #0F172A; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .sidebar { background: #1E293B; min-height: 100vh; border-right: 4px solid #D4AF37; }
        .sidebar a { color: #cbd5e1; padding: 18px 25px; display: flex; align-items: center; transition: all 0.4s; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #D4AF37, #F4D03F); color: #000 !important; font-weight: 700; border-left-color: #000; transform: translateX(8px); }
        .sidebar a i { width: 30px; font-size: 18px; margin-right: 12px; }
        .card { background: rgba(15,23,42,0.95); border: 2px solid #D4AF37; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.6); }
        .text-gold { color: #D4AF37 !important; }
        .btn-gold { background: linear-gradient(45deg, #D4AF37, #F4D03F); color: #000; border: none; font-weight: bold; }
        .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(212,175,55,0.4); }
        .badge-lifestyle { background: #8b5cf6; }
        .badge-exclusive { background: #ec4899; }
        .badge-culinary { background: #dc2626; }
        .badge-published { background: #10b981; }
        .badge-draft { background: #f59e0b; color: #000; }
        .table img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }

        /* CUSTOM DELETE MODAL - PURE LUXURY */
        .delete-modal .modal-content {
            background: linear-gradient(135deg, #1e1b4b, #0F172A);
            border: 3px solid #D4AF37;
            border-radius: 20px;
            color: white;
        }
        .delete-modal .modal-header {
            border-bottom: 2px solid #D4AF37;
            padding: 20px;
        }
        .delete-modal .modal-title {
            font-family: 'Playfair Display', serif;
            color: #D4AF37;
            font-size: 1.8rem;
        }
        .delete-modal .btn-close { filter: invert(1); }
        .delete-modal .modal-body {
            font-size: 1.1rem;
            text-align: center;
            padding: 40px 20px;
        }
        .delete-modal .btn-delete {
            background: linear-gradient(45deg, #dc2626, #ef4444);
            color: white;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: bold;
        }
        .delete-modal .btn-cancel {
            background: #475569;
            color: white;
            padding: 12px 40px;
            border-radius: 50px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <div class="text-center py-4" style="background: linear-gradient(135deg, #000, #1a1a2e); border-bottom: 4px solid #D4AF37;">
                <h4 class="text-gold mb-0" style="font-family: 'Playfair Display', serif;">
                    De Grand Admin
                </h4>
            </div>
            <div class="mt-4">
                <a href="index.php?page=dashboard">Dashboard</a>
                <a href="index.php?page=bookings">All Bookings</a>
                <a href="index.php?page=messages">Messages</a>
                <a href="blog-posts.php" class="active">De Grand Journal</a>
                <a href="logout.php" class="text-danger">Logout</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-5">

            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="text-gold" style="font-family: 'Playfair Display', serif; font-size: 3rem;">
                    De Grand Journal
                </h1>
                <a href="add-post.php" class="btn btn-gold px-4 py-3 fs-5">
                    Write New Story
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success rounded-4 p-4 mb-4 border border-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-scroll fa-5x text-gold mb-4 opacity-50"></i>
                    <h3 class="text-gold">No De Grand Stories Yet</h3>
                    <p class="lead">Be the first to share the luxury experience!</p>
                    <a href="add-post.php" class="btn btn-gold btn-lg px-5 py-3 mt-3">Write First Story</a>
                </div>
            <?php else: ?>
                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-gold">
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <?php if ($post['image']): ?>
                                            <img src="../images/blog/<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                                        <?php else: ?>
                                            <div class="bg-secondary text-center" style="width:80px;height:60px;border-radius:8px;line-height:60px;font-size:12px;">
                                                No Image
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($post['title']) ?></td>
                                    <td><?= htmlspecialchars($post['author']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($post['category']) ?>">
                                            <?= $post['category'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($post['created_at'])) ?></td>
                                    <td>
                                        <span class="badge <?= $post['status']=='published' ? 'badge-published' : 'badge-draft' ?>">
                                            <?= ucfirst($post['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit-post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning text-dark">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                data-id="<?= $post['id'] ?>" data-title="<?= htmlspecialchars($post['title']) ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- LUXURY DELETE CONFIRMATION MODAL -->
<div class="modal fade delete-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Delete De Grand Story
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                <h4>Are you sure?</h4>
                <p class="lead">You are about to permanently delete:</p>
                <p class="fw-bold text-gold fs-5" id="postTitle">Loading...</p>
                <p>This action <strong>cannot be undone</strong>.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-cancel px-5" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDelete" href="#" class="btn btn-delete px-5">
                    Yes, Delete Forever
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Beautiful Delete Confirmation
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const postId = button.getAttribute('data-id');
    const postTitle = button.getAttribute('data-title');

    const modalTitle = deleteModal.querySelector('#postTitle');
    const confirmLink = deleteModal.querySelector('#confirmDelete');

    modalTitle.textContent = postTitle;
    confirmLink.href = `blog-posts.php?delete=${postId}`;
});
</script>
</body>
</html>