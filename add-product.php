<?php
session_start();
require_once '../includes/db_connect.php';

// RESTAURANT ADMIN ONLY
if (!isset($_SESSION['restaurant_admin_id'])) {
    header("Location: login.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Load product for editing
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$product = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: menu.php");
        exit();
    }
}


// FETCH CATEGORIES FROM THE NEW PERFECT TABLE — LIVE & SORTED!
$categories = [];
try {
    $stmt = $pdo->query("SELECT name FROM categories WHERE name IS NOT NULL AND TRIM(name) != '' ORDER BY sort_order ASC, name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($categories)) {
        $categories = ['Burger', 'Rice', 'Drinks', 'Soup', 'Swallow'];
    }
} catch (Exception $e) {
    $categories = ['Burger', 'Rice', 'Drinks', 'Soup'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($id) {
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        $image_name = $existing['image'] ?? 'placeholder.jpg';
    } else {
        $image_name = 'placeholder.jpg';
    }

    if (empty($name) || empty($category) || $price < 100) {
        $error = "Please fill all required fields correctly!";
    } else {

        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5000000) {

                $image_name = "prod_" . uniqid() . "_" . time() . "." . $ext;
                $target = "../assets/images/products/" . $image_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {

                    if ($id && !empty($_POST['old_image']) && $_POST['old_image'] !== 'placeholder.jpg') {
                        $old_path = "../assets/images/products/" . $_POST['old_image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id) {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET name=?, category=?, price=?, description=?, image=? 
                    WHERE id=?
                ");
                $stmt->execute([$name, $category, $price, $description, $image_name, $id]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO products (name, category, price, description, image) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $category, $price, $description, $image_name]);
            }

            header("Location: menu.php");
            exit();

        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
     <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
    <title><?= $edit_id ? 'Edit' : 'Add' ?> Product - De Grand Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 2rem; }
        .card { border-radius: 20px; box-shadow: 0 15px 40px rgba(0,166,81,0.15); }
        .form-control, .form-select { border-radius: 12px; height: 56px; border: 2px solid #e0e0e0; }
        .form-control:focus, .form-select:focus { border-color: #00A651; box-shadow: 0 0 0 0.3rem rgba(0,166,81,0.2); }
        .btn-success { background: #00A651; border: none; border-radius: 12px; padding: 14px 50px; font-weight: bold; }
        .btn-success:hover { background: #008040; transform: translateY(-3px); }
        .badge-live { background: #00A651; color: white; }
        .spinner { animation: spin 2s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="display-5 fw-bold" style="color: #00A651;">
                <?= $edit_id ? 'Edit Product' : 'Add New Product' ?>
            </h2>
            <div>
                <a href="menu.php" class="btn btn-outline-secondary btn-lg px-5">Back to Menu</a>
                <a href="logout.php" class="btn btn-outline-danger btn-lg px-4 ms-3">Logout</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger rounded-4 p-4 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card border-0">
            <div class="card-body p-5">

                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="id" value="<?= $edit_id ?>">
                        <input type="hidden" name="old_image" value="<?= $product['image'] ?? '' ?>">
                    <?php endif; ?>

                    <div class="row g-5">
                        <div class="col-lg-7">

                            <div class="mb-4">
                                <label class="form-label fs-5 fw-bold text-success">Food Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                            </div>

                            <!-- LIVE CATEGORY DROPDOWN FROM categories TABLE -->
                            <div class="mb-4">
                                <label class="form-label fs-5 fw-bold text-success">
                                    Category <span class="badge badge-live ms-2"><?= count($categories) ?></span>
                                    <i class="fas fa-sync-alt spinner text-success ms-2"></i>
                                </label>
                                <select name="category" class="form-select form-select-lg" required>
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat) ?>" 
                                            <?= ($product['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-success d-block mt-2">
                                    <strong>Live Sync:</strong> Categories update instantly from Manage Categories
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fs-5 fw-bold text-success">Price (₦)</label>
                                <input type="number" name="price" class="form-control form-control-lg" 
                                       value="<?= $product['price'] ?? '' ?>" min="100" step="50" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fs-5 fw-bold text-success">Description (Optional)</label>
                                <textarea name="description" rows="6" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="col-lg-5 text-center">
                            <label class="form-label fs-5 fw-bold text-success d-block mb-4">Product Image</label>

                            <?php if ($edit_id && !empty($product['image'])): ?>
                                <div class="mb-4">
                                    <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                                         class="img-fluid rounded-4 shadow" style="max-height:320px;">
                                    <p class="text-success mt-3 fw-bold">Current Image</p>
                                </div>
                            <?php endif; ?>

                            <div class="border-4 border-dashed border-success rounded-4 p-5 bg-light">
                                <input type="file" name="image" class="form-control form-control-lg" accept="image/*">
                                <p class="text-muted mt-3">Max 5MB • JPG/PNG/WEBP</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-success btn-lg px-5 py-4 fs-4 shadow-lg">
                            <?= $edit_id ? 'Update Product' : 'Add Product' ?>
                        </button>
                        <a href="menu.php" class="btn btn-secondary btn-lg px-5 py-4 fs-4 ms-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>