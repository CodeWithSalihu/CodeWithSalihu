<!-- admin/messages.php -->
<div class="card">
    <div class="card-body">
        <h3 class="text-gold mb-4">Guest Messages</h3>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email/Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                    while($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?= date('d M H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= $row['email'] ?><br><small><?= $row['phone'] ?></small></td>
                        <td><?= ucfirst($row['subject']) ?></td>
                        <td><?= substr(htmlspecialchars($row['message']), 0, 80) ?>...</td>
                        <td>
                          <a href="/all/view-message?id=<?= $row['id'] ?>" 
   class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i> View
</a>

<a href="mailto:<?= $row['email'] ?>" 
   class="btn btn-sm btn-warning">
    <i class="fas fa-reply"></i>
</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>