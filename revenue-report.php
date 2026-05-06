<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['manager_access'])) {
    header('Location: login.php');
    exit();
}
require_once '../includes/db_connect.php';

// ====================== AJAX DATA FETCH ======================
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    $filter = $_GET['filter'] ?? 'all';
    $from = $_GET['from'] ?? '';
    $to   = $_GET['to'] ?? '';

    $where = "status = 'confirmed'";
    $params = [];

    switch ($filter) {
        case 'today':
            $where .= " AND DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $where .= " AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $where .= " AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
            break;
        case 'year':
            $where .= " AND YEAR(created_at) = YEAR(CURDATE())";
            break;
        case 'custom':
            if ($from && $to) {
                $where .= " AND DATE(created_at) BETWEEN ? AND ?";
                $params = [$from, $to];
            }
            break;
    }

    // Bookings
    $stmt = $pdo->prepare("
        SELECT *, 
               DATE_FORMAT(check_in, '%d %b %Y') AS in_date,
               DATE_FORMAT(check_out, '%d %b %Y') AS out_date
        FROM net_bookings 
        WHERE $where 
        ORDER BY created_at DESC
    ");
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Totals
    $total_all   = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM net_bookings WHERE status='confirmed'")->fetchColumn();
    $total_month = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM net_bookings WHERE status='confirmed' AND YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn();
    $total_year  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM net_bookings WHERE status='confirmed' AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();

    $filtered_total = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM net_bookings WHERE $where");
    $filtered_total->execute($params);
    $period_total = $filtered_total->fetchColumn();

    echo json_encode([
        'bookings' => $bookings,
        'totals' => [
            'all'     => $total_all,
            'month'   => $total_month,
            'year'    => $total_year,
            'period'  => $period_total
        ]
    ]);
    exit();
}

// ====================== DELETE BOOKING ======================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = $pdo->prepare("SELECT booking_ref, full_name FROM net_bookings WHERE id = ?");
    $check->execute([$id]);
    $b = $check->fetch();

    if ($b) {
        $pdo->prepare("DELETE FROM net_bookings WHERE id = ?")->execute([$id]);
    }
    // Will be handled by JS → reload data
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
<title>Manager Revenue De Grand Hotel</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700&family=Inter:wght400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
:root{--gold:#D4AF37;--dark:#0a0e17;--red:#dc2626;--green:#22c55e}
body{background:var(--dark);color:#e2e8f0;font-family:'Inter',sans-serif;margin:0;min-height:100vh}
.header{padding:4rem 2rem;background:linear-gradient(135deg,#000,#16213e);text-align:center;border-bottom:10px solid var(--gold)}
.header h1{font-family:'Cinzel',serif;font-size:4rem;color:var(--gold);letter-spacing:12px;text-shadow:0 0 60px rgba(212,175,55,.7);margin:20px 0}
.container{max-width:1450px;margin:2rem auto;padding:0 1rem}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:30px;margin:40px 0}
.card{background:rgba(212,175,55,.12);border:4px solid var(--gold);border-radius:28px;padding:35px;text-align:center;transition:.5s;box-shadow:0 10px 30px rgba(0,0,0,.7)}
.card:hover{transform:translateY(-15px) scale(1.03);box-shadow:0 30px 80px rgba(212,175,55,.4)}
.card-label{font-size:1.4rem;color:#aaa;margin-bottom:12px}
.card-amount{font-size:4.5rem;font-weight:900;background:linear-gradient(135deg,#FFD700,#FFA500);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.filter-bar{background:#111;border:3px solid var(--gold);border-radius:20px;padding:25px;margin:30px 0;display:flex;flex-wrap:wrap;gap:15px;align-items:end;justify-content:center}
.filter-bar select,.filter-bar input,.filter-bar button{padding:14px 20px;border-radius:12px;border:none;background:#000;color:#fff;font-weight:600}
.filter-bar button,.export-btn{background:var(--gold);color:#000;cursor:pointer;transition:.4s}
.filter-bar button:hover,.export-btn:hover{transform:scale(1.08)}
.table-wrapper{background:#111;border:4px solid var(--gold);border-radius:25px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th{background:linear-gradient(135deg,var(--gold),#f4d03f);color:#000;padding:20px;font-size:1.2rem;font-weight:900}
td{padding:18px;text-align:center;border-bottom:1px solid #333}
tr:hover{background:rgba(212,175,55,.1)}
.delete-btn{background:var(--red);color:#fff;border:none;padding:10px 20px;border-radius:50px;font-weight:bold;cursor:pointer;transition:.3s}
.delete-btn:hover{background:#b91c1c;transform:scale(1.15)}
.confidential{background:rgba(220,38,38,.2);color:#fca5a5;padding:15px;border-radius:15px;text-align:center;border:2px solid var(--red);font-weight:bold}
.back-btn{background:var(--gold);color:#000;padding:18px 50px;border-radius:50px;font-weight:900;text-decoration:none;display:inline-block;margin:30px 0;transition:.4s}
.back-btn:hover{transform:scale(1.1);box-shadow:0 15px 40px rgba(212,175,55,.6)}
@media(max-width:768px){.header h1{font-size:3rem}.card-amount{font-size:3.2rem}.filter-bar{flex-direction:column;align-items:stretch}}
</style>
</head>
<body>

<div class="header">
    <h1>REVENUE EMPIRE</h1>
    <p style="font-size:2rem;color:#94a3b8">Manager Financial Control Center</p>
</div>

<div class="container">

    <div class="confidential">
        HIGHLY CONFIDENTIAL • MANAGER ACCESS • Logged in as: <strong><?= htmlspecialchars($_SESSION['manager_name'] ?? 'Manager') ?></strong>
    </div>

    <!-- REVENUE CARDS -->
    <div class="stats" id="statsCards">
        <div class="card"><div class="card-label">Total Revenue Ever</div><div class="card-amount" id="total">₦0</div></div>
        <div class="card"><div class="card-label">This Month</div><div class="card-amount" id="month">₦0</div></div>
        <div class="card"><div class="card-label">This Year</div><div class="card-amount" id="year">₦0</div></div>
        <div class="card"><div class="card-label">Filtered Period</div><div class="card-amount" id="period">₦0</div></div>
    </div>

    <!-- FILTERS -->
    <div class="filter-bar">
        <div>
            <label>Quick Filter</label>
            <select id="quickFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
                <option value="year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div id="customRange" style="display:none">
            <input type="date" id="fromDate">
            <input type="date" id="toDate">
        </div>
        <div>
            <label>Search</label>
            <input type="text" id="search" placeholder="Ref / Guest / Room...">
        </div>
        <button onclick="loadData()">APPLY</button>
        <a href="javascript:exportExcel()" class="export-btn">EXPORT EXCEL</a>
    </div>

    <!-- BOOKINGS TABLE -->
    <div class="table-wrapper">
        <table id="bookingsTable">
            <thead>
                <tr>
                    <th>Ref</th><th>Guest</th><th>Room Type</th><th>Check-in → Out</th><th>Nights</th><th>Amount</th><th>Booked On</th><th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>

    <div style="text-align:center">
        <a href="index.php?page=dashboard" class="back-btn">BACK TO DASHBOARD</a>
    </div>
</div>

<script>
function format(n){return '₦'+parseInt(n).toLocaleString()}

function loadData(){
    const filter = document.getElementById('quickFilter').value;
    let url = '?ajax=1&filter='+filter;
    if(filter==='custom'){
        const f=document.getElementById('fromDate').value, t=document.getElementById('toDate').value;
        if(!f||!t) return Swal.fire('Error','Select both dates','error');
        url += '&from='+f+'&to='+t;
    }
    fetch(url)
    .then(r=>r.json())
    .then(d=>{
        // Update cards
        document.getElementById('total').textContent = format(d.totals.all);
        document.getElementById('month').textContent = format(d.totals.month);
        document.getElementById('year').textContent = format(d.totals.year);
        document.getElementById('period').textContent = format(d.totals.period);

        // Fill table
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        d.bookings.forEach(b=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong style="color:var(--gold)">${b.booking_ref}</strong></td>
                <td>${b.full_name}</td>
                <td>${b.room_type.replace(/[-_]/g,' ').toUpperCase()}</td>
                <td>${b.in_date} → ${b.out_date}</td>
                <td><strong>${b.nights || '—'}</strong></td>
                <td style="color:var(--gold);font-size:1.4rem">${format(b.total_amount)}</td>
                <td>${new Date(b.created_at).toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})}</td>
                <td>
                    <button class="delete-btn" onclick="del(${b.id},'${b.booking_ref}','${b.full_name}')">
                         DELETE
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });
    });
}

function del(id,ref,name){
    Swal.fire({
        title:'DELETE FOREVER?',
        html:`Ref: <b>${ref}</b><br>Guest: <b>${name}</b>`,
        icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',
        confirmButtonText:'YES, DELETE',background:'#111',color:'#fff'
    }).then(r=>{
        if(r.isConfirmed){
            fetch('?delete='+id).then(()=>loadData());
            Swal.fire({title:'Deleted!',text:'Booking removed.',icon:'success',background:'#111',color:'#fff'});
        }
    });
}

function exportExcel(){
    const rows = Array.from(document.querySelectorAll('#bookingsTable tr')).slice(1).map(row=> {
        const cells = row.querySelectorAll('td');
        return {
            Ref: cells[0].textContent,
            Guest: cells[1].textContent,
            Room: cells[2].textContent,
            Period: cells[3].textContent,
            Nights: cells[4].textContent,
            Amount: cells[5].textContent.replace(/[₦,]/g,''),
            'Booked On': cells[6].textContent
        };
    });
    const ws = XLSX.utils.json_to_sheet(rows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Revenue");
    XLSX.writeFile(wb, "DeGrand_Revenue_"+new Date().toISOString().slice(0,10)+".xlsx");
}

// Live search
document.getElementById('search').addEventListener('input', e=>{
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(tr=>{
        tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});

// Show/hide custom dates
document.getElementById('quickFilter').addEventListener('change', function(){
    document.getElementById('customRange').style.display = this.value==='custom' ? 'flex' : 'none';
});

// Initial load
loadData();
</script>
</body>
</html>