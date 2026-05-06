<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title    = trim($_POST['title']);
    $author   = trim($_POST['author']);
    $category = $_POST['category'];
    $status   = $_POST['status'];
    $content  = $_POST['content'];
    $id       = $_POST['id'] ?? null;

    // Handle image upload
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image_name = 'blog_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $upload_path = '../images/blog/' . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
        }
    }

    try {
        if ($id) {
            // UPDATE
            $sql = "UPDATE blog_posts SET title=?, author=?, category=?, status=?, content=?";
            $params = [$title, $author, $category, $status, $content];

            if ($image_name) {
                // Get old image to delete
                $old = $pdo->query("SELECT image FROM blog_posts WHERE id = $id")->fetchColumn();
                if ($old && file_exists('../images/blog/'.$old)) {
                    unlink('../images/blog/'.$old);
                }
                $sql .= ", image=?";
                $params[] = $image_name;
            }
            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $msg = "Post updated like a king!";
        } else {
            // CREATE
            $sql = "INSERT INTO blog_posts (title, author, category, status, content, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $params = [$title, $author, $category, $status, $content, $image_name];

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $msg = "New royal story published!";
        }

        header("Location: index.php?page=blog-posts&success=" . urlencode($msg));
        exit();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>