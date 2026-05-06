<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db_connect.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: room_management.php');
    exit();
}

// Fetch room
$stmt = $pdo->prepare("SELECT * FROM room_inventory WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$room) die("Room not found!");

$success = $_GET['success'] ?? '';
$error = '';

// Handle image deletion
if (isset($_GET['delete_img'])) {
    $img_to_delete = basename($_GET['delete_img']);
    $current_images = $room['image'] ? array_map('trim', explode(',', $room['image'])) : [];
    $new_images = array_filter($current_images, fn($img) => $img !== $img_to_delete);
    
    if (file_exists("../images/$img_to_delete")) {
        unlink("../images/$img_to_delete");
    }
    
    $new_image_string = implode(',', $new_images);
    $pdo->prepare("UPDATE room_inventory SET image = ? WHERE id = ?")
        ->execute([$new_image_string, $id]);
    
    header("Location: edit_room.php?id=$id&success=Image+deleted");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_rooms     = (int)$_POST['total_rooms'];
    $price_raw       = $_POST['price_per_night'];
    $price_per_night = (float)preg_replace('/[^0-9.]/', '', $price_raw);

    if ($total_rooms < 1) {
        $error = "Total rooms must be at least 1!";
    } elseif ($price_per_night < 5000) {
        $error = "Price too low! Minimum ₦5,000";
    } else {
        $current_images = $room['image'] ? array_map('trim', explode(',', $room['image'])) : [];
        $uploaded_images = [];

        // Handle multiple image upload
        if (!empty($_FILES['room_images']['name'][0])) {
            $target_dir = "../images/";
            foreach ($_FILES['room_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['room_images']['error'][$key] === 0) {
                    $file_name = $_FILES['room_images']['name'][$key];
                    $file_size = $_FILES['room_images']['size'][$key];
                    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $error = "Only JPG, PNG, WEBP allowed!";
                        break;
                    }
                    if ($file_size > 5000000) {
                        $error = "Image too large! Max 5MB";
                        break;
                    }
                    
                    $new_name = time() . "_$key" . ".$ext";
                    if (move_uploaded_file($tmp_name, $target_dir . $new_name)) {
                        $uploaded_images[] = $new_name;
                    }
                }
            }
        }

        if (!$error) {
            $final_images = array_merge($current_images, $uploaded_images);
            $image_string = implode(',', $final_images);

            try {
                // REMOVED occupied_rooms FROM UPDATE — IT'S NOW USELESS!
                $stmt = $pdo->prepare("UPDATE room_inventory SET 
                    total_rooms = ?, price_per_night = ?, image = ? 
                    WHERE id = ?");
                $stmt->execute([$total_rooms, $price_per_night, $image_string, $id]);

                $success = "Room updated successfully! Availability is now 100% accurate.";
                header("Location: edit_room.php?id=$id&success=" . urlencode($success));
                exit();
            } catch (Exception $e) {
                $error = "Update failed. Try again.";
            }
        }
    }
}

// Load current images
$current_images = $room['image'] ? array_filter(array_map('trim', explode(',', $room['image']))) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">

    <title>Edit Room • De Grand Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root { --gold: #D4AF37; --dark: #000; --card: #111; --red: #dc2626; --green: #22c55e; }
        body { background: var(--dark); color: #e2e8f0; font-family: 'Inter', sans-serif; margin:0; }
        .header { text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #111, #000); border-bottom: 12px solid var(--gold); }
        .header img { height: 150px; border-radius: 50%; border: 8px solid var(--gold); filter: drop-shadow(0 0 80px gold); }
        .page-title { font-family: 'Cinzel', serif; font-size: 6rem; color: var(--gold); letter-spacing: 20px; text-shadow: 0 0 70px rgba(212,175,55,0.9); }
        .container { max-width: 1100px; margin: 4rem auto; padding: 3rem; background: var(--card); border-radius: 35px; border: 4px solid var(--gold); box-shadow: 0 40px 120px rgba(212,175,55,0.3); }
        .back-btn { display: inline-block; background: #333; color: white; padding: 16px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 2rem; }
        .back-btn:hover { background: var(--gold); color: #000; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 35px; margin-top: 2rem; }
        .form-group.full { grid-column: span 2; }
        label { color: var(--gold); font-weight: 900; font-size: 1.4rem; margin-bottom: 12px; letter-spacing: 3px; }
        input, select { padding: 20px; background: #000; border: 3px solid #333; color: white; border-radius: 18px; font-size: 1.3rem; }
        input:focus { border-color: var(--gold); box-shadow: 0 0 0 8px rgba(212,175,55,0.3); outline: none; }

        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin: 20px 0; }
        .image-item { position: relative; border-radius: 16px; overflow: hidden; border: 4px solid var(--gold); }
        .image-item img { width: 100%; height: 140px; object-fit: cover; }
        .delete-btn { position: absolute; top: 8px; right: 8px; background: var(--red); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; text-decoration: none; }
        .delete-btn:hover { background: #ef4444; }

        .btn-submit { grid-column: span 2; background: var(--gold); color: #000; padding: 25px; font-size: 2rem; font-weight: 900; border: none; border-radius: 50px; cursor: pointer; margin-top: 2rem; }
        .btn-submit:hover { background: white; transform: translateY(-12px); }

        .alert { padding: 25px; border-radius: 25px; text-align: center; font-size: 1.5rem; font-weight: 900; margin: 2rem 0; }
        .alert.success { background: #166534; color: #86efac; border: 5px solid var(--green); }
        .alert.error { background: #7f1d1d; color: #fca5a5; border: 5px solid var(--red); }

        .info-box { background: rgba(212,175,55,0.15); padding: 20px; border-radius: 20px; border: 2px solid var(--gold); margin: 20px 0; text-align: center; font-size: 1.3rem; }
    </style>
</head>
<body>

<div class="header">
    <img src="../images/logo.jpg" alt="De Grand Hotel">
    <h1 class="page-title">EDIT ROOM</h1>
</div>

<div class="container">
    <a href="index.php?page=inventory" class="back-btn">Back to Room Control</a>

    <h2 style="text-align:center; color:var(--gold); font-size:2.8rem; margin:2rem 0;">
        Editing: <strong><?= htmlspecialchars($room['room_name']) ?></strong>
    </h2>

    <!-- IMPORTANT NOTICE -->
    <div class="info-box">
        <strong>REAL-TIME AVAILABILITY SYSTEM ACTIVE</strong><br
    </div>

    <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Total Rooms (Physical Rooms)</label>
                <input type="number" name="total_rooms" value="<?= $room['total_rooms'] ?>" min="1" max="50" required>
                <small style="color:#888;">This is the real number of rooms you have</small>
            </div>

            <div class="form-group">
                <label>Price per Night (₦)</label>
                <input type="text" name="price_per_night" value="<?= number_format($room['price_per_night']) ?>" required placeholder="250,000">
            </div>

            <!-- Current Images -->
            <div class="form-group full">
                <label>Current Images (<?= count($current_images) ?>)</label>
                <?php if (!empty($current_images)): ?>
                    <div class="image-gallery">
                        <?php foreach ($current_images as $img): ?>
                            <div class="image-item">
                                <img src="../images/<?= htmlspecialchars($img) ?>" alt="Room">
                                <a href="edit_room.php?id=<?= $id ?>&delete_img=<?= urlencode($img) ?>" 
                                   class="delete-btn" onclick="return confirm('Delete forever?')">
                                    X
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color:#888;">No images yet</p>
                <?php endif; ?>
            </div>

            <div class="form-group full">
                <label>Add More Images</label>
                <input type="file" name="room_images[]" multiple accept="image/jpeg,image/png,image/webp">
                <small style="color:#888;">Max 5MB • Hold Ctrl/Cmd to select multiple</small>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            UPDATE ROOM SETTINGS
        </button>
    </form>
</div>

</body>
</html>