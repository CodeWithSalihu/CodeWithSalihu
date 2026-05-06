<?php
session_start();
require_once '../includes/db_connect.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

$message = '';

if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'manager';

    if ($pass1 !== $pass2) {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } elseif (strlen($pass1) < 8) {
        $message = "<div class='alert alert-danger'>Password must be 8+ characters!</div>";
    } else {
        $hashed = password_hash($pass1, PASSWORD_DEFAULT);
        try {
           $stmt = $pdo->prepare("INSERT INTO admins (full_name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $hashed, $role]);

            $message = "<div class='alert alert-success'>Admin created successfully! You can now login.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Email already exists!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - The Royals Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0F172A, #1E293B); min-height: 100vh; display: flex; align-items: center; }
        .register-card {
            background: rgba(15,23,42,0.98);
            backdrop-filter: blur(20px);
            border: 3px solid #D4AF37;
            max-width: 500px;
            margin: 0 auto;
            padding: 50px;
            box-shadow: 0 30px 100px rgba(0,0,0,0.8);
        }
        .text-gold { color: #D4AF37 !important; }
        .btn-gold {
            background: linear-gradient(45deg, #D4AF37, #F4D03F);
            color: #0F172A;
            font-weight: 800;
            padding: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid #D4AF37; color: white; }
        .form-control:focus { background: rgba(212,175,55,0.2); border-color: #F4D03F; box-shadow: 0 0 20px rgba(212,175,55,0.4); color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="register-card rounded-0">
        <div class="text-center mb-5">
            <i class="fas fa-crown fa-4x text-gold mb-4"></i>
            <h2 class="text-gold" style="font-family: 'Playfair Display', serif;">Create Admin Account</h2>
            <p class="text-light">Only authorized personnel</p>
        </div>

        <?= $message ?>

        <form method="POST">
            <div class="mb-3">
                <label class="text-gold fw-bold">Full Name</label>
                <input type="text" name="name" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="text-gold fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="text-gold fw-bold">Phone (Optional)</label>
                <input type="text" name="phone" class="form-control form-control-lg">
            </div>
            <div class="mb-3">
                <label class="text-gold fw-bold">Role</label>
                <select name="role" class="form-control form-control-lg">
                    <option value="manager">Manager</option>
<option value="receptionist">Receptionist</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="text-gold fw-bold">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" required minlength="8">
            </div>
            <div class="mb-4">
                <label class="text-gold fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control form-control-lg" required>
            </div>

            <button type="submit" class="btn btn-gold btn-lg w-100">
                <i class="fas fa-user-plus me-2"></i> Create Admin Account
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="text-light">Already have account? Login here</a>
        </div>
    </div>
</div>

</body>
</html>