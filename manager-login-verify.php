<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, full_name, password, role FROM admins WHERE email = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        
        // Update last login
        $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

        // Set session for extra security (optional)
        $_SESSION['manager_access'] = true;
        $_SESSION['manager_id'] = $admin['id'];
        $_SESSION['manager_name'] = $admin['full_name'];

        echo json_encode([
            'success' => true,
            'role' => $admin['role'],
            'name' => $admin['full_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid login']);
    }
} catch (Exception $e) {
    error_log("Manager login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>