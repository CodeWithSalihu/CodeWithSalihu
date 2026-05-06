<?php
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db_connect.php';

// ===== FORCE FULLY BOOKED SWITCH =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_force_full') {

    $pdo->query("UPDATE hotel_system_flags SET force_full = IF(force_full=1,0,1) WHERE id=1");

    $state = $pdo->query("SELECT force_full FROM hotel_system_flags WHERE id=1")->fetchColumn();

    echo json_encode([
        'success'=>true,
        'state'=>$state
    ]);
    exit();
}

$force_full = $pdo->query("SELECT force_full FROM hotel_system_flags WHERE id=1")->fetchColumn();


// === HANDLE CHECK-OUT (NEW FEATURE) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $booking_ref = trim($_POST['booking_ref']);
    
    try {
        $stmt = $pdo->prepare("UPDATE net_bookings SET status = 'checked_out' WHERE booking_ref = ? AND status = 'confirmed'");
        $stmt->execute([$booking_ref]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Guest checked out! Room freed.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found or already checked out!']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error!']);
    }
    exit();
}

// Handle Delete Room Type (unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    try {
        $stmt = $pdo->prepare("SELECT image FROM room_inventory WHERE id = ?");
        $stmt->execute([$id]);
        $room = $stmt->fetch();

        $stmt = $pdo->prepare("DELETE FROM room_inventory WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result && $room && $room['image']) {
            $images = array_map('trim', explode(',', $room['image']));
            foreach ($images as $img) if ($img !== 'default-suite.jpg') {
                $path = "../images/" . $img;
                if (file_exists($path)) unlink($path);
            }
        }
        echo json_encode(['success' => true, 'message' => 'Room type deleted!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Delete failed!']);
    }
    exit();
}

// REAL AVAILABILITY FUNCTION (NOW EXCLUDES checked_out)
function getRealAvailability($pdo, $room_type, $force_full, $date = null)
 {
    if (!$date) $date = date('Y-m-d');
    if($force_full==1){
    $real_available = 0;
}
if($force_full == 1){
    return 0;
}

    
    
    $stmt = $pdo->prepare("SELECT total_rooms FROM room_inventory WHERE room_type = ?");
    $stmt->execute([$room_type]);
    $total = (int)$stmt->fetchColumn();

    $check = $pdo->prepare("
        SELECT COUNT(*) FROM net_bookings 
        WHERE room_type = ? 
          AND status IN ('confirmed', 'checked_in')
          AND ? >= check_in 
          AND ? < check_out
    ");
    $check->execute([$room_type, $date, $date]);
    $booked = (int)$check->fetchColumn();

    return max(0, $total - $booked);
}

// Get current guests for TODAY (for check-out button)
$today = date('Y-m-d');
$guests_today = $pdo->prepare("
    SELECT booking_ref, full_name, room_type, check_out 
    FROM net_bookings 
    WHERE status = 'confirmed' 
      AND ? >= check_in 
      AND ? < check_out
    ORDER BY full_name
");
$guests_today->execute([$today, $today]);
$current_guests = $guests_today->fetchAll(PDO::FETCH_ASSOC);

$rooms = $pdo->query("SELECT * FROM room_inventory ORDER BY price_per_night DESC")->fetchAll(PDO::FETCH_ASSOC);
$today_display = date('j M Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Control De Grand Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --gold: #D4AF37; --dark: #0a0e17; --card: #111; --red: #dc2626; --green: #22c55e; --orange: #f97316; }
        body { background: var(--dark); color: #e2e8f0; font-family: 'Inter', sans-serif; margin:0; padding-bottom:100px; }

        .header { text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #000, #111); border-bottom: 12px solid var(--gold); }
        .header img { height: 140px; filter: drop-shadow(0 0 70px gold); border-radius: 50%; border: 7px solid var(--gold); }
        .page-title { font-family: 'Cinzel', serif; font-size: 6rem; color: var(--gold); letter-spacing: 18px; text-shadow: 0 0 60px rgba(212,175,55,0.9); }
        .subtitle { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-style: italic; color: #94a3b8; }

        .container { max-width: 2400px; margin: 4rem auto; padding: 0 2rem; }

        .today-info { background: rgba(212,175,55,0.15); padding: 20px; border-radius: 20px; border: 3px solid var(--gold); text-align: center; font-size: 1.5rem; margin-bottom: 30px; }
        .today-info strong { color: var(--gold); font-size: 1.8rem; }

        .checkout-section { background: rgba(34,197,94,0.1); border: 3px solid var(--green); border-radius: 25px; padding: 25px; margin-bottom: 40px; }
        .checkout-title { color: var(--green); font-size: 2.2rem; text-align: center; margin-bottom: 20px; font-weight: 900; }
        .guest-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; }
        .guest-card { background: rgba(0,0,0,0.4); padding: 18px; border-radius: 16px; border-left: 6px solid var(--green); }
        .guest-name { font-weight: 900; color: var(--gold); font-size: 1.3rem; }
        .checkout-btn { background: var(--green); color: white; padding: 12px 20px; border: none; border-radius: 50px; font-weight: 900; cursor: pointer; margin-top: 10px; }
        .checkout-btn:hover { background: #16a34a; transform: scale(1.05); }

        table { width: 100%; border-collapse: separate; border-spacing: 0 25px; }
        thead th { background: linear-gradient(135deg, var(--gold), #f4d03f); color: #000; padding: 25px; font-size: 1.5rem; font-weight: 900; }
        tbody tr { background: var(--card); border-radius: 30px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.7); transition: all 0.5s; }
        tbody tr:hover { transform: translateY(-15px); box-shadow: 0 40px 100px rgba(212,175,55,0.4); }
        tbody td { padding: 30px; text-align: center; vertical-align: middle; font-size: 1.3rem; }

        .room-img { width: 160px; height: 100px; object-fit: cover; border-radius: 20px; border: 5px solid var(--gold); }
        .availability { font-size: 3.8rem; font-weight: 900; }
        .avail-good { color: var(--green); }
        .avail-low { color: var(--orange); animation: pulse 2s infinite; }
        .avail-zero { color: var(--red); animation: pulse 1.5s infinite; }

        .status-badge { padding: 16px 45px; border-radius: 50px; font-weight: 900; font-size: 1.4rem; }
        .status-good { background: #166534; color: #86efac; }
        .status-low { background: #9a3412; color: #fdba74; }
        .status-full { background: var(--red); color: white; }

        .btn-edit, .btn-delete { padding: 16px 32px; border: none; border-radius: 50px; font-weight: 900; cursor: pointer; margin: 0 8px; }
        .btn-edit { background: var(--gold); color: #000; }
        .btn-delete { background: var(--red); color: white; }

        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.6; } }
    </style>
</head>
<body>

<div class="header">
    <img src="../images/logo.jpg" alt="De Grand Hotel">
    <h1 class="page-title">ROOM CONTROL</h1>
    <p class="subtitle">Real-Time Availability â€¢ <?= $today_display ?></p>
</div>



    <!-- CHECK-OUT SECTION -->
    <?php if (!empty($current_guests)): ?>
    <div class="checkout-section">
        <h2 class="checkout-title">GUESTS CHECKING OUT TODAY</h2>
        <div class="guest-list">
            <?php foreach ($current_guests as $guest): ?>
<div class="guest-card" id="guest_<?= $guest['booking_ref'] ?>">
                <div class="guest-name"><?= htmlspecialchars($guest['full_name']) ?></div>
                <div style="color:#94a3b8; margin:8px 0;">
                    <?= ucwords(str_replace('-', ' ', $guest['room_type'])) ?> â€¢ Ref: <?= $guest['booking_ref'] ?>
                </div>
               <button onclick="checkoutGuest('<?= $guest['booking_ref'] ?>','<?= $guest['room_type'] ?>')" class="checkout-btn">
    CHECK-OUT NOW
</button>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="checkout-section" style="opacity:0.6;">
        <h2 class="checkout-title">NO GUESTS CHECKING OUT TODAY</h2>
        <p style="text-align:center; color:#94a3b8; font-size:1.3rem;">All quiet on the western front</p>
    </div>
    <?php endif; ?>

    <div style="text-align:center; margin:40px 0;">
        <a href="add_room.php" class="add-btn">ADD NEW ROOM TYPE</a>
    </div>
<div style="text-align:center;margin-bottom:30px;">
<button onclick="toggleForceFull()" 
style="background:#dc2626;color:white;padding:18px 40px;
border:none;border-radius:50px;font-weight:900;font-size:1.3rem;cursor:pointer;">
⚠️ FORCE ALL ROOMS FULLY BOOKED
</button>
</div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Type</th>
                <th>Name</th>
                <th>Total</th>
                <th>Available Today</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room):
$real_available = getRealAvailability($pdo, $room['room_type'], $force_full);
                $status = $real_available == 0 ? 'full' : ($real_available <= 2 ? 'low' : 'good');
                $img = !empty($room['image']) ? explode(',', $room['image'])[0] : 'default-suite.jpg';
            ?>
            <tr>
                <td><img src="../images/<?= htmlspecialchars($img) ?>" class="room-img"></td>
                <td style="color:var(--gold); font-weight:900;">
                    <?= ucwords(str_replace(['-', '_'], ' ', $room['room_type'])) ?>
                </td>
                <td class="room-name"><?= htmlspecialchars($room['room_name']) ?></td>
                <td><?= $room['total_rooms'] ?></td>
                <td><div class="availability avail-<?= $status ?>"><?= $real_available ?></div></td>
                <td class="price">â‚¦<?= number_format($room['price_per_night']) ?></td>
                <td>
                    <span class="status-badge status-<?= $status ?>">
                        <?= $real_available == 0 ? 'FULLY BOOKED' : ($real_available == 1 ? 'LAST ROOM!' : "$real_available FREE") ?>
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="edit_room.php?id=<?= $room['id'] ?>" class="btn-edit">EDIT</a>
                        <button onclick="deleteRoom(<?= $room['id'] ?>)" class="btn-delete">DELETE</button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function checkoutGuest(ref, roomType) {
    Swal.fire({
        title: 'CHECK-OUT GUEST?',
        text: "Room will be freed immediately!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'YES, CHECK-OUT!',
        background: '#111',
        color: '#e2e8f0'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=checkout&booking_ref=' + encodeURIComponent(ref)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Done!', data.message, 'success');

                    // ðŸ”¥ Remove guest card instantly
                    const card = document.getElementById("guest_" + ref);
                    if (card) card.remove();

                    // ðŸ”¥ Update table availability dynamically
                    const rows = document.querySelectorAll("tbody tr");
                    rows.forEach(row => {
                        const typeCell = row.querySelector("td:nth-child(2)").textContent.trim().toLowerCase().replace(/ /g, '-');
                        if(typeCell === roomType) {
                            const availDiv = row.querySelector(".availability");
                            let current = parseInt(availDiv.textContent);
                            current += 1; // +1 because guest checked out
                            availDiv.textContent = current;

                            // Update badge
                            const badge = row.querySelector(".status-badge");
                            if(current === 0){
                                badge.textContent = "FULLY BOOKED";
                                badge.className = "status-badge status-full";
                                availDiv.className = "availability avail-zero";
                            } else if(current <= 2){
                                badge.textContent = current === 1 ? "LAST ROOM!" : `${current} FREE`;
                                badge.className = "status-badge status-low";
                                availDiv.className = "availability avail-low";
                            } else {
                                badge.textContent = `${current} FREE`;
                                badge.className = "status-badge status-good";
                                availDiv.className = "availability avail-good";
                            }
                        }
                    });

                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}



function deleteRoom(id) {
    Swal.fire({
        title: 'DELETE ROOM TYPE?',
        text: "All bookings will be lost!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'YES, DELETE!',
        background: '#111',
        color: '#e2e8f0'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                Swal.fire('Done!', data.message, 'success')
                .then(() => location.reload()); // <â€“ good for delete
            });
        }
    });
}



function toggleForceFull(){

    Swal.fire({
        title:'Toggle FULL HOTEL Mode?',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#dc2626',
        background:'#111',
        color:'#fff'
    }).then((result)=>{
        if(result.isConfirmed){

            fetch(window.location.href,{
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:'action=toggle_force_full'
            })
            .then(r=>r.json())
            .then(()=>{
                location.reload();
            });

        }
    });

}

</script>

</body>
</html>