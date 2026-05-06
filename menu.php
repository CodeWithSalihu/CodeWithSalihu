<?php
session_start();
require_once '../includes/db_connect.php'; // Fixed path

// FIXED: Proper admin check – NO MORE FATAL ERROR!
if (empty($_SESSION['restaurant_admin_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Extra security (recommended)
if (!isset($_SESSION['restaurant_admin_name'])) {
    header("Location: login.php");
    exit();
}

// ========================================
// DELETE PRODUCT (SECURE + CLEAN)
// ========================================
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();

        // Get old image
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $old_image = $stmt->fetchColumn();

        // Delete product
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

        // Delete image file if not placeholder
        if ($old_image && $old_image !== 'placeholder.jpg') {
            $path = "../assets/images/products/" . $old_image;
            if (file_exists($path)) @unlink($path);
        }

        $pdo->commit();

        $_SESSION['msg'] = [
            'type' => 'success',
            'text' => 'Product deleted successfully!'
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = ['type' => 'danger', 'text' => 'Delete failed!'];
    }

    header("Location: menu.php");
    exit();
}

// ========================================
// ADD / EDIT PRODUCT
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $name        = trim($_POST['name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    // Default image
    $image_name = 'placeholder.jpg';
    if ($id) {
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $image_name = $stmt->fetchColumn() ?: 'placeholder.jpg';
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5_000_000) {
            $image_name = "prod_" . uniqid() . "_" . time() . "." . $ext;
            $target = "../assets/images/products/" . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Delete old image
                if ($id && !empty($_POST['old_image']) && $_POST['old_image'] !== 'placeholder.jpg') {
                    $old_path = "../assets/images/products/" . $_POST['old_image'];
                    if (file_exists($old_path)) @unlink($old_path);
                }
            }
        }
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
            $stmt->execute([$name, $category, $price, $description, $image_name, $id]);
            $msg = "Product updated successfully!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $price, $description, $image_name]);
            $msg = "New product added successfully!";
        }

        $_SESSION['msg'] = ['type' => 'success', 'text' => $msg];
    } catch (Exception $e) {
        $_SESSION['msg'] = ['type' => 'danger', 'text' => 'Operation failed!'];
    }

    header("Location: menu.php");
    exit();
}

// Get all unique categories
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category")
                  ->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>Menu Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --green:#00A651; --darkgreen:#008040; --shadow:0 20px 50px rgba(0,166,81,0.18); }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#f5f7fa; color:#222; }
        
        .header {
            background:linear-gradient(135deg,var(--green),var(--darkgreen));
            color:white; padding:30px; text-align:center; box-shadow:0 10px 30px rgba(0,166,81,0.3);
        }
        .header h1 { font-size:2.8rem; font-weight:900; letter-spacing:1px; }
        .header p { font-size:1.1rem; opacity:0.9; margin-top:8px; }

        .container { max-width:1500px; margin:40px auto; padding:0 20px; }

        .page-title {
            font-size:2.2rem; color:var(--green); font-weight:800; margin-bottom:30px;
            text-align:center; padding-bottom:15px; border-bottom:4px solid #e8f7f0;
        }

        .actions-bar {
            display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; flex-wrap:wrap; gap:15px;
        }

        .card {
            background:white; border-radius:24px; overflow:hidden; box-shadow:var(--shadow); margin-bottom:30px;
        }
        .card-header {
            background:var(--green); color:white; padding:20px 30px; font-size:1.4rem; font-weight:700;
        }

        table {
            width:100%; border-collapse:collapse;
        }
        th { background:#f8f9fa; color:#333; padding:18px; font-weight:600; text-align:left; }
        td { padding:8px; border-bottom:1px solid #eee; vertical-align:middle; }
        tr:hover { background:#f8fff8; }

        .product-img {
            width:90px; height:90px; object-fit:cover; border-radius:16px; border:3px solid #eee;
            transition:0.3s;
        }
        .product-img:hover { transform:scale(1.1); border-color:var(--green); }

        .badge {
            background:var(--green); color:white; padding:8px 18px; border-radius:50px; font-weight:700;
        }

        .btn {
            padding:10px 20px; border-radius:50px; font-weight:700; text-decoration:none;
            transition:all 0.3s; display:inline-block; margin:5px 5px 5px 0;
        }
        .btn-success { background:var(--green); color:white; }
        .btn-success:hover { background:var(--darkgreen); transform:translateY(-3px); box-shadow:0 10px 25px rgba(0,166,81,0.3); }
        .btn-warning { background:#FF9500; color:white; }
        .btn-danger { background:#FF3B30; color:white; }
        .btn-warning:hover, .btn-danger:hover { transform:translateY(-3px); }

        .alert {
            border-radius:16px; padding:18px; margin:20px 0; font-weight:600; text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
        }
        .alert-success { background:#d4edda; color:#155724; border:2px solid #c3e6cb; }
        .alert-danger { background:#f8d7da; color:#721c24; border:2px solid #f5c6cb; }

        .back-btn {
            position:fixed; bottom:30px; right:30px; background:var(--green); color:white;
            padding:18px 28px; border-radius:50px; font-size:1.3rem; font-weight:800;
            box-shadow:0 15px 40px rgba(0,166,81,0.4); z-index:1000; text-decoration:none;
            transition:0.4s;
        }
        .back-btn:hover { background:var(--darkgreen); transform:scale(1.1); }
    </style>
</head>
<body>

    <!-- Floating Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">
        Back to Dashboard
    </a>

    <div class="header">
        <h1>DE GRAND RESTAURANT</h1>
        <p>Menu Management • Calabar</p>
    </div>

    <div class="container">

        <h2 class="page-title">Menu Manager</h2>

        <!-- Success/Error Message -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-<?= $_SESSION['msg']['type'] ?>">
                <strong><?= $_SESSION['msg']['text'] ?></strong>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="actions-bar">
            <div>
                <h4>Welcome, <strong><?= htmlspecialchars($_SESSION['restaurant_admin_name'] ?? 'Admin') ?></strong></h4>
            </div>
            <div>
                <a href="add-product.php" class="btn btn-success">
                    Add New Product
                </a>
                <a href="categories.php" class="btn btn-success" style="background:#8E44AD;">
                    Manage Categories
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                All Menu Items
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
                        if ($stmt->rowCount() == 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:60px; color:#888;">
                                    <h3>No products yet. Click "Add New Product" to get started!</h3>
                                </td>
                            </tr>
                        <?php else: while ($p = $stmt->fetch()): 
                            $img = !empty($p['image']) && file_exists("../assets/images/products/".$p['image'])
                                ? "../assets/images/products/".$p['image']
                                : "../assets/images/products/placeholder.jpg";
                        ?>
                            <tr>
                                <td>
                                    <img src="<?= $img ?>" class="product-img" alt="<?= htmlspecialchars($p['name']) ?>">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                                    <?php if (!empty($p['description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(substr($p['description'], 0, 100)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge"><?= htmlspecialchars($p['category'] ?: 'Uncategorized') ?></span></td>
                                <td><strong style="color:var(--green); font-size:1.3rem;">₦<?= number_format($p['price']) ?></strong></td>
                                <td>
                                    <a href="add-product.php?edit=<?= $p['id'] ?>" class="btn btn-warning">
                                        Edit
                                    </a>
                                    <a href="menu.php?action=delete&id=<?= $p['id'] ?>"
                                       onclick="return confirm('Delete <?= addslashes($p['name']) ?>? This cannot be undone.')"
                                       class="btn btn-danger">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="logout.php" class="btn btn-outline-danger" style="padding:15px 40px; font-size:1.1rem;">
                Logout
            </a>
        </div>
    </div>

</body>
</html>