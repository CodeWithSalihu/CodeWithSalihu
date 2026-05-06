<!-- admin/dashboard_content.php -->
<div class="text-center mb-5">
    <h1 class="text-gold" style="font-family: 'Cinzel', serif; font-size: 5rem; letter-spacing: 8px;">
        ROYAL DASHBOARD
    </h1>
    <p class="lead fs-2">Welcome back, <strong class="text-gold"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Your Majesty') ?></strong></p>
</div>

<!-- MANAGER REVENUE PORTAL — ONLY FOR REAL MANAGERS -->
<div class="text-center mb-5">
    <button type="button" class="btn btn-warning btn-lg px-5 py-4 rounded-pill shadow-lg" 
            style="background: linear-gradient(135deg, #FFD700, #B8860B); font-weight:900; font-size:1.4rem;"
            data-bs-toggle="modal" data-bs-target="#managerLoginModal">
        <i class="fas fa-user-crown"></i> MANAGER REVENUE PORTAL
    </button>
    <p class="text-muted mt-3">Restricted Access • Manager Login Required</p>
</div>

<!-- MANAGER LOGIN MODAL -->
<div class="modal fade" id="managerLoginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-gold" style="border-width: 4px !important; border-radius: 20px;">
            <div class="modal-header text-center border-0 pb-0">
                <h3 class="modal-title text-gold w-100" style="font-family: 'Cinzel', serif; font-size: 2.2rem;">
                    <i class="fas fa-crown fa-fw"></i> MANAGER ACCESS
                </h3>
            </div>
            <div class="modal-body text-center py-5">
                <p class="lead text-white mb-4">Login as Manager to view Revenue Report</p>
                
                <form id="managerLoginForm">
                    <div class="mb-4">
                        <input type="email" name="email" id="managerEmail" class="form-control form-control-lg royal-input text-center" 
                               placeholder="Manager Email" required style="font-size:1.2rem;">
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" id="managerPassword" class="form-control form-control-lg royal-input text-center" 
                               placeholder="Password" required style="font-size:1.2rem;">
                    </div>
                    
                    <button type="submit" class="btn btn-gold btn-lg px-5 py-3">
                        <i class="fas fa-unlock-open"></i> ACCESS REVENUE REPORT
                    </button>
                </form>

                <div id="loginError" class="text-danger mt-4 fw-bold fs-5" style="display:none;">
                    <i class="fas fa-exclamation-triangle"></i> Invalid credentials or insufficient privileges!
                </div>
                <div id="loginSuccess" class="text-success mt-4 fw-bold fs-5" style="display:none;">
                    <i class="fas fa-check-circle"></i> Access Granted! Redirecting...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RECENT BOOKINGS -->
<h2 class="text-gold mb-4" style="font-family: 'Playfair Display', serif; font-size: 2.8rem;">
    Recent Royal Guests
</h2>

<?php if (count($recent_bookings) > 0): ?>
    <div class="table-responsive">
     <table class="table table-dark table-hover">
         <thead>
             <tr>
                 <th>Date</th>
                 <th>Ref</th>
                 <th>Guest</th>
                 <th>Room</th>
                 <th>Check-in</th>
                 <th>Amount</th>
                 <th>Status</th>
                 <th>Receipt</th>
             </tr>
         </thead>
         <tbody>
             <?php foreach ($recent_bookings as $b): ?>
                 <tr>
                     <td><?= date('d M', strtotime($b['created_at'])) ?><br><small><?= date('h:i A', strtotime($b['created_at'])) ?></small></td>
                     <td><strong class="text-gold fs-5"><?= $b['booking_ref'] ?></strong></td>
                     <td><strong><?= htmlspecialchars($b['full_name']) ?></strong></td>
                     <td><strong><?= $b['room_display'] ?? ucwords(str_replace(['-', '_'], ' ', $b['room_type'])) . ' Suite' ?></strong></td>
                     <td><?= date('d M Y', strtotime($b['check_in'])) ?></td>
                     <td><strong class="text-gold">₦<?= number_format($b['total_amount']) ?></strong></td>
                     <td><span class="status-<?= $b['status'] ?>"><?= strtoupper($b['status']) ?></span></td>
                     <td>
                         <a href="../booking-success.php?ref=<?= $b['booking_ref'] ?>" 
                            target="_blank" class="btn-receipt">
                             View Receipt
                         </a>
                     </td>
                 </tr>
             <?php endforeach; ?>
         </tbody>
     </table>
 </div>
 <div class="text-end mt-4">
     <a href="index.php?page=bookings" class="btn px-5 py-3" style="background:var(--gold); color:#000; font-weight:900; border-radius:50px; font-size:1.2rem;">
         View All Bookings
     </a>
 </div>
<?php else: ?>
 <div class="text-center py-5">
     <i class="fas fa-crown fa-8x text-gold mb-4" style="opacity:0.2;"></i>
     <p class="fs-2 text-muted">The palace awaits its first royal guest...</p>
 </div>
<?php endif; ?>