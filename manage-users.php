<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// DELETE SUBSCRIBER
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM subscribers WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = "Subscriber removed from the kingdom!";
    header("Location: manage-users.php");
    exit();
}

// FETCH ALL SUBSCRIBERS
$total = $pdo->query("SELECT COUNT(*) FROM subscribers")->fetchColumn();
$subscribers = $pdo->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage De Grand Subscribers </title>
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
        .card { background: rgba(15,23,42,0.95); border: 2px solid #D4AF37; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); }
        .text-gold { color: #D4AF37 !important; }
        .btn-gold { background: linear-gradient(45deg, #D4AF37, #F4D03F); color: #000; border: none; font-weight: bold; }
        .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(212,175,55,0.4); }
        .badge-royal { background: linear-gradient(45deg, #8b5cf6, #ec4899); color: white; }
        .table { background: #1e293b; border-radius: 16px; overflow: hidden; }
        .table th { background: #0F172A; color: #D4AF37; font-weight: 700; }
        .table td { vertical-align: middle; }
        .stat-card { background: linear-gradient(135deg, #1e293b, #0F172A); border: 2px solid #D4AF37; border-radius: 20px; padding: 30px; text-align: center; transition: all 0.4s; }
        .stat-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(212,175,55,0.3); }
        .stat-number { font-size: 4rem; font-weight: 900; color: #D4AF37; font-family: 'Playfair Display', serif; }
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
                <a href="blog-posts.php">De Grand Journal</a>
                <a href="manage-users.php" class="active">Subscribers (<?= $total ?>)</a>
                <a href="logout.php" class="text-danger">Logout</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="text-gold" style="font-family: 'Playfair Display', serif; font-size: 3.5rem;">
                    De Grand Subscribers
                </h1>
                <div class="stat-card">
                    <div class="stat-number"><?= $total ?></div>
                    <p class="text-gold fs-4 mb-0">Total Members</p>
                    <small class="text-light-gray">In The Inner Circle</small>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success rounded-4 p-4 mb-4 border border-success shadow">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if ($total == 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-5x text-gold mb-4 opacity-30"></i>
                    <h3 class="text-gold">No De Grand Yet</h3>
                    <p class="lead text-light-gray">The De Grand awaits its first members...</p>
                </div>
            <?php else: ?>
                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Email Address</th>
                                    <th>Joined De Grand</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscribers as $i => $sub): ?>
                                <tr>
                                    <td class="text-gold fw-bold"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($sub['email']) ?></td>
                                    <td><?= date('d M Y \a\t h:i A', strtotime($sub['subscribed_at'])) ?></td>
                                    <td>
                                        <span class="badge badge-royal px-3 py-2">
                                            <i class="fas fa-crown"></i> Inner Circle
                                        </span>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($sub['email']) ?>" class="btn btn-sm btn-outline-gold">
                                            <i class="fas fa-envelope"></i> Email
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $sub['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- DELETE CONFIRM MODAL -->
                                <div class="modal fade" id="deleteModal<?= $sub['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark text-light border-danger">
                                            <div class="modal-header border-gold">
                                                <h5 class="modal-title text-gold">Remove from De Grand?</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-5">
                                                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                                                <h4>Are you sure,?</h4>
                                                <p class="fw-bold text-gold"><?= htmlspecialchars($sub['email']) ?></p>
                                                <p>This De Grand will be banished forever.</p>
                                            </div>
                                            <div class="modal-footer justify-content-center border-0">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                                <a href="manage-users.php?delete=<?= $sub['id'] ?>" class="btn btn-danger px-5">
                                                    Yes, Remove
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>