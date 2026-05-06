<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db_connect.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type       = trim($_POST['room_type']);
    $room_name       = trim($_POST['room_name']);
    $total_rooms     = (int)$_POST['total_rooms'];
    $price_per_night = (float)preg_replace('/[^0-9.]/', '', $_POST['price_per_night']);
    $max_guests      = (int)$_POST['max_guests'];
    $size_sqm        = (int)$_POST['size_sqm'];
    $bed_type        = trim($_POST['bed_type']);

    // Validation
    if (empty($room_type) || empty($room_name)) {
        $error = "Room type and name are required!";
    } elseif ($total_rooms < 1) {
        $error = "At least 1 room required!";
    } elseif ($price_per_night < 10000) {
        $error = "Price too low! Minimum ₦10,000";
    } elseif (empty($_FILES['room_image']['name'])) {
        $error = "Please upload a room image!";
    } else {
        // Handle image upload
        $target_dir = "../images/";
        $image_name = time() . "_" . basename($_FILES["room_image"]["name"]);
        $target_file = $target_dir . $image_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($imageFileType, $allowed)) {
            $error = "Only JPG, JPEG, PNG & WEBP allowed!";
        } elseif ($_FILES["room_image"]["size"] > 5000000) {
            $error = "Image too large! Max 5MB";
        } elseif (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
            try {
                // CORRECT INSERT — NO occupied_rooms ANYMORE!
                $stmt = $pdo->prepare("
                    INSERT INTO room_inventory 
                    (room_type, room_name, total_rooms, price_per_night, image, max_guests, size_sqm, bed_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $room_type,
                    $room_name,
                    $total_rooms,
                    $price_per_night,
                    $image_name,
                    $max_guests,
                    $size_sqm,
                    $bed_type
                ]);

                $success = "New room type added successfully!";
                echo "<script>
                    Swal.fire('Success!', '$success', 'success').then(() => {
                        window.location.href = 'room_management.php';
                    });
                </script>";
            } catch (Exception $e) {
                $error = "Failed to save room. Try again.";
                unlink($target_file);
            }
        } else {
            $error = "Image upload failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Room • De Grand Hotel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --gold: #D4AF37; --dark: #000; }
        body { background: var(--dark); color: #e2e8f0; font-family: 'Inter', sans-serif; margin:0; }
        .header { text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #111, #000); border-bottom: 10px solid var(--gold); }
        .header img { height: 130px; filter: drop-shadow(0 0 60px gold); border-radius: 50%; border: 6px solid var(--gold); }
        .page-title { font-family: 'Cinzel', serif; font-size: 5.5rem; color: var(--gold); letter-spacing: 15px; text-shadow: 0 0 50px rgba(212,175,55,0.8); }
        .container { max-width: 900px; margin: 4rem auto; padding: 3rem; background: #111; border-radius: 30px; border: 3px solid var(--gold); box-shadow: 0 30px 100px rgba(212,175,55,0.3); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 2rem; }
        .form-group.full { grid-column: span 2; }
        label { color: var(--gold); font-weight: 900; font-size: 1.3rem; margin-bottom: 10px; letter-spacing: 2px; }
        input, select { padding: 18px; background: #000; border: 2px solid #333; color: white; border-radius: 16px; font-size: 1.2rem; }
        input:focus, select:focus { border-color: var(--gold); box-shadow: 0 0 0 6px rgba(212,175,55,0.3); outline: none; }
        .image-preview { width: 100%; height: 300px; object-fit: cover; border-radius: 20px; margin-top: 15px; border: 4px dashed var(--gold); display: none; }
        .btn-submit { grid-column: span 2; background: var(--gold); color: #000; padding: 22px; font-size: 1.8rem; font-weight: 900; border: none; border-radius: 50px; cursor: pointer; margin-top: 2rem; }
        .btn-submit:hover { background: white; transform: translateY(-10px); box-shadow: 0 30px 80px rgba(212,175,55,0.6); }
        .back-btn { display: inline-block; background: #333; color: white; padding: 16px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-bottom: 2rem; }
        .back-btn:hover { background: var(--gold); color: #000; }
        .alert { padding: 20px; border-radius: 20px; text-align: center; font-size: 1.4rem; font-weight: 900; margin: 2rem 0; }
        .alert.success { background: #166534; color: #86efac; border: 4px solid #22c55e; }
        .alert.error   { background: #7f1d1d; color: #fca5a5; border: 4px solid #ef4444; }
    </style>
</head>
<body>

<div class="header">
    <img src="../images/logo.jpg" alt="De Grand Hotel">
    <h1 class="page-title">ADD NEW ROOM</h1>
    <p class="subtitle">Create Luxury • Define Excellence</p>
</div>

<div class="container">
    <a href="room_management.php" class="back-btn">Back to Room Management</a>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Room Type (URL friendly)</label>
                <input type="text" name="room_type" required placeholder="deluxe-suite" value="<?= htmlspecialchars($_POST['room_type'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Room Name (Display)</label>
                <input type="text" name="room_name" required placeholder="Deluxe King Suite" value="<?= htmlspecialchars($_POST['room_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Total Physical Rooms</label>
                <input type="number" name="total_rooms" min="1" value="<?= $_POST['total_rooms'] ?? '5' ?>" required>
            </div>
            <div class="form-group">
                <label>Price per Night (₦)</label>
                <input type="text" name="price_per_night" required placeholder="250,000" value="<?= htmlspecialchars($_POST['price_per_night'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Max Guests</label>
                <select name="max_guests" required>
                    <option value="2" <?= ($_POST['max_guests'] ?? '') == '2' ? 'selected' : '' ?>>2 Adults</option>
                    <option value="3" <?= ($_POST['max_guests'] ?? '') == '3' ? 'selected' : '' ?>>3 Adults</option>
                    <option value="4" <?= ($_POST['max_guests'] ?? '') == '4' ? 'selected' : '' ?>>4 Adults</option>
                    <option value="6" <?= ($_POST['max_guests'] ?? '') == '6' ? 'selected' : '' ?>>6 Adults (Family)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Room Size (m²)</label>
                <input type="number" name="size_sqm" value="<?= $_POST['size_sqm'] ?? '60' ?>" required>
            </div>
            </div>
            <div class="form-group">
                <label>Bed Type</label>
                <select name="bed_type" required>
                    <option value="1 King Bed">1 King Bed</option>
                    <option value="2 Queen Beds">2 Queen Beds</option>
                    <option value="1 King + Sofa Bed">1 King + Sofa Bed</option>
                    <option value="2 Double Beds">2 Double Beds</option>
                </select>
            </div>
            <div class="form-group full">
                <label>Upload Main Image</label>
                <input type="file" name="room_image" id="room_image" accept="image/*" required>
                <img id="preview" class="image-preview">
            </div>
        </div>

        <button type="submit" class="btn-submit">
            CREATE NEW ROOM TYPE
        </button>
    </form>
</div>

<script>
document.getElementById('room_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('preview');
            img.src = e.target.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>