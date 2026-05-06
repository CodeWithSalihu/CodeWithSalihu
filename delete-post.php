<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    // Get image name first
    $image = $pdo->query("SELECT image FROM blog_posts WHERE id = $id")->fetchColumn();
    
    // Delete post
    $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
    
    // Delete image file
    if ($image && file_exists("../images/blog/$image")) {
        unlink("../images/blog/$image");
    }

    header("Location: index.php?page=blog-posts&deleted=1");
    exit();
} catch (Exception $e) {
    die("Delete failed: " . $e->getMessage());
}
?>