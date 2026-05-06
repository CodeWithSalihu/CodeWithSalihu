<?php
session_start();
require_once 'includes/db_connect.php';

// RESTAURANT ADMIN ONLY
if (!isset($_SESSION['restaurant_admin_id'])) {
    header("Location: login.php");
    exit();
}

// Create folder if not exists
if (!is_dir("../images/categories/")) {
    mkdir("../images/categories/", 0755, true);
}

// Add sort_order column if missing
try {
    $pdo->exec("ALTER TABLE categories ADD COLUMN IF NOT EXISTS sort_order INT DEFAULT 999 AFTER icon");
} catch (Exception $e) {}

// ========================================
// HANDLE ADD / EDIT CATEGORY
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = trim($_POST['name'] ?? '');
    $id   = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (empty($name)) {
        $_SESSION['msg'] = "<div class='alert alert-danger alert-dismissible fade show'>
            <strong>Error!</strong> Category name is required!
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } else {
        $icon_name = 'default.png';

        // Handle icon upload
        if (!empty($_FILES['icon']['name']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION));
            $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];

            if (in_array($ext, $allowed) && $_FILES['icon']['size'] <= 2000000) {
                $icon_name = "cat_" . uniqid() . "_" . time() . "." . $ext;
                $target = "../images/categories/" . $icon_name;

                if (!move_uploaded_file($_FILES['icon']['tmp_name'], $target)) {
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Image upload failed — category saved without new icon</div>";
                    $icon_name = $id ? $pdo->prepare("SELECT icon FROM categories WHERE id=?")->execute([$id]) ? $pdo->query("SELECT icon FROM categories WHERE id=$id")->fetchColumn() : 'default.png' : 'default.png';
                }
            }
        } else {
            // Keep old icon when editing
            if ($id) {
                $stmt = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $icon_name = $stmt->fetchColumn() ?: 'default.png';
            }
        }

        // Delete old icon if new one uploaded
        if ($id && !empty($_FILES['icon']['name']) && $icon_name !== 'default.png') {
            $old = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
            $old->execute([$id]);
            $old_icon = $old->fetchColumn();
            if ($old_icon && $old_icon !== 'default.png' && $old_icon !== $icon_name) {
                @unlink("../images/categories/" . $old_icon);
            }
        }

        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
                $stmt->execute([$name, $icon_name, $id]);
                $msg = "Category updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, icon, sort_order) 
                                       VALUES (?, ?, COALESCE((SELECT MAX(sort_order)+1 FROM categories), 1))");
                $stmt->execute([$name, $icon_name]);
                $msg = "New category added!";
            }

            $_SESSION['msg'] = "<div class='alert alert-success alert-dismissible fade show shadow-lg'>
                <strong>Success!</strong> $msg
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";

        } catch (Exception $e) {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // THIS IS THE FIX: Redirect instead of echo + exit()
    header("Location: categories.php");
    exit();
}

// ========================================
// HANDLE DELETE
// ========================================
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $icon = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);

    if ($icon && $icon !== 'default.png') {
        @unlink("../images/categories/" . $icon);
    }

    $_SESSION['msg'] = "<div class='alert alert-success alert-dismissible fade show'>
        <strong>Deleted!</strong> Category removed successfully!
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
    header("Location: categories.php");
    exit();
}

// ========================================
// HANDLE REORDER (AJAX)
// ========================================
if (isset($_POST['order'])) {
    $order = json_decode($_POST['order'], true);
    if (is_array($order)) {
        $stmt = $pdo->prepare("UPDATE categories SET sort_order = ? WHERE id = ?");
        foreach ($order as $index => $cat_id) {
            $stmt->execute([$index + 1, $cat_id]);
        }
    }
    echo json_encode(['status' => 'success']);
    exit();
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
    <title>Manage Categories </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        body { background: linear-gradient(135deg, #f8f9fa, #e9f5e9); font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 3rem 1rem; }
        .card { border-radius: 20px; transition: all 0.4s; }
        .card:hover { transform: translateY(-15px); box-shadow: 0 30px 60px rgba(0,166,81,0.3)!important; }
        .draggable { cursor: move; }
        .btn-success { background: #00A651; border: none; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="display-5 fw-bold" style="color: #00A651;">Manage Menu Categories</h2>
            <div>
                <a href="menu.php" class="btn btn-outline-secondary btn-lg px-5">Back to Menu</a>
                <button class="btn btn-success btn-lg px-5 shadow ms-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    Add New Category
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <h3 class="text-muted">No categories yet — click the green button to add one!</h3>
            </div>
        <?php else: ?>
            <div class="alert alert-success rounded-4 mb-4">
                <strong>Drag & drop</strong> cards to reorder categories
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <div class="row g-4" id="categoriesList">
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-lg border-0 draggable h-100" data-id="<?= $cat['id'] ?>">
                            <div class="card-body text-center p-5">
                                <img src="../images/categories/<?= htmlspecialchars($cat['icon'] ?? 'default.png') ?>"
                                     class="rounded-circle shadow-lg mb-4"
                                     width="140" height="140"
                                     style="object-fit: cover; border: 8px solid #00A651;"
                                     onerror="this.src='../images/categories/default.png'">
                                <h4 class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></h4>
                                <div class="mt-4">
                                    <button class="btn btn-warning btn-lg px-4" 
                                            onclick="editCategory(<?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>')">
                                        Edit
                                    </button>
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" 
                                       class="btn btn-danger btn-lg px-4 ms-2"
                                       onclick="return confirm('Delete this category?')">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fs-4" id="modalTitle">Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 text-center">
                    <input type="hidden" name="id" id="catId">
                    <input type="hidden" name="action" value="save">

                    <img id="iconPreview" src="../images/categories/default.png" 
                         class="rounded-circle shadow-lg mb-4" width="160" height="160"
                         style="object-fit: cover; border: 8px solid #00A651;">

                    <div class="mb-4">
                        <label class="btn btn-outline-success btn-lg px-5">
                            Choose Icon
                            <input type="file" name="icon" accept="image/*" onchange="previewIcon(event)" style="display:none;">
                        </label>
                    </div>

                    <input type="text" name="name" id="catName" class="form-control form-control-lg text-center fs-3" 
                           placeholder="e.g. Jollof Rice, Drinks, Swallow" required>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg px-5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg px-5">Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewIcon(e) {
    const file = e.target.files[0];
    if (file) document.getElementById('iconPreview').src = URL.createObjectURL(file);
}

function editCategory(id, name) {
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('catId').value = id;
    document.getElementById('catName').value = name;
    new bootstrap.Modal('#addCategoryModal').show();
}

// Reset modal on close
document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', () => {
    document.getElementById('modalTitle').textContent = 'Add New Category';
    document.getElementById('catId').value = '';
    document.getElementById('catName').value = '';
    document.getElementById('iconPreview').src = '../images/categories/default.png';
    document.querySelector('input[name="icon"]').value = '';
});

// Drag & Drop
new Sortable(document.getElementById('categoriesList'), {
    animation: 400,
    onEnd: function() {
        const order = Array.from(document.querySelectorAll('.draggable')).map(el => el.dataset.id);
        fetch('categories.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'order=' + JSON.stringify(order)
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>