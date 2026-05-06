<?php
session_start();
require_once '../includes/db_connect.php';  // Fixed path (was wrong before)

// If already logged in → go straight to DASHBOARD
if (isset($_SESSION['restaurant_admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, full_name, email, password, role FROM admins WHERE email = ? AND status = 'active' LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // SUCCESSFUL LOGIN
                $_SESSION['restaurant_admin_id']   = $user['id'];
                $_SESSION['restaurant_admin_name'] = $user['full_name'];
                $_SESSION['restaurant_admin_role'] = $user['role'];
                $_SESSION['restaurant_admin_email']= $user['email'];
                $_SESSION['is_admin'] = true; // Extra flag for dashboard check

                // Update last login
                $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")
                    ->execute([$user['id']]);

                // REDIRECT TO DASHBOARD, NOT MENU!
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } catch (Exception $e) {
            $error = "Login error. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>Restaurant Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00A651, #008040);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-box {
            background: white;
            width: 100%;
            max-width: 420px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }
        .login-header {
            background: linear-gradient(135deg, #00A651, #008040);
            color: white;
            text-align: center;
            padding: 3rem 1.5rem;
        }
        .login-header h1 {
            font-weight: 900;
            font-size: 3rem;
            margin: 0;
            letter-spacing: 2px;
        }
        .login-header p {
            margin: 12px 0 0;
            opacity: 0.95;
            font-size: 1.2rem;
            font-weight: 500;
        }
        .login-body {
            padding: 2.5rem;
        }
        .form-control {
            height: 58px;
            border-radius: 16px;
            padding-left: 50px;
            font-size: 1.1rem;
            border: 2px solid #eee;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #00A651;
            box-shadow: 0 0 0 4px rgba(0,166,81,0.15);
        }
        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #00A651;
            font-size: 1.3rem;
        }
        .btn-login {
            background: #00A651;
            border: none;
            height: 58px;
            border-radius: 16px;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: 1px;
            transition: all 0.4s;
        }
        .btn-login:hover {
            background: #008040;
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0,166,81,0.5);
        }
        .alert {
            border-radius: 16px;
            font-weight: 600;
        }
        .test-accounts {
            background: #f8fff8;
            border: 2px dashed #00A651;
            border-radius: 16px;
            padding: 16px;
            margin-top: 20px;
            font-size: 0.95rem;
        }
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: #aaa;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-header">
        <h1>DE GRAND</h1>
        <p>Restaurant Admin Panel</p>
    </div>

    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4 position-relative">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="admin@degrandhotel.com" value="<?= $_POST['email'] ?? '' ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4 position-relative">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-login w-100">
                Login to Dashboard
            </button>
        </form>

        <!--<div class="test-accounts text-center">-->
        <!--    <strong>Test Login:</strong><br>-->
        <!--    Email: <code>admin@degrandhotel.com</code><br>-->
        <!--    Password: <code>password</code>-->
        <!--</div>-->
    </div>

    <div class="footer-text pb-3">
        © <?= date('Y') ?> De Grand Hotel • Calabar
    </div>
</div>

</body>
</html>