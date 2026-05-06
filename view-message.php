<?php
// ================================================
//  view-message.php  —  fixed & Tailwind-ready version
// ================================================

require_once '../includes/db_connect.php';

// Force Tailwind to be available (development / fallback)
// Remove or comment out once you confirm header.php loads Tailwind properly
if (!defined('TAILWIND_LOADED')) {
    echo '<script src="https://cdn.tailwindcss.com"></script>';
    define('TAILWIND_LOADED', true);
}

// Try to include your normal admin header (this is probably what's missing or failing)
if (file_exists($headerPath)) {
    require_once $headerPath;
} else {
    // Fallback minimal header if your real one is missing
    ?>
    <!DOCTYPE html>
    <html lang="en" class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Message | De Grand Admin</title>
        <!-- Tailwind Play CDN – remove in production and use built CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- If you also use Bootstrap or Font Awesome, add them here -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
    </head>
    <body class="bg-gray-950 text-gray-100 min-h-screen antialiased">
    <?php
}
?>

<?php
// ── Security & data fetch ────────────────────────────────────────────────

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="max-w-4xl mx-auto mt-10 p-6 bg-red-950/60 border border-red-700 rounded-xl text-red-200 text-center">No valid message ID provided.</div>';
    goto footer;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    echo '<div class="max-w-4xl mx-auto mt-10 p-6 bg-amber-950/60 border border-amber-700 rounded-xl text-amber-200 text-center">Message not found.</div>';
    goto footer;
}

// Auto-mark as read
if ($message['status'] === 'new') {
    $upd = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $upd->execute([$id]);
    $message['status'] = 'read';
}
?>

<div class="min-h-screen bg-gray-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900/80 backdrop-blur-sm shadow-2xl shadow-black/40">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-b border-gray-800 bg-gray-950/90 px-6 py-6 sm:px-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-yellow-500 tracking-tight">
                        Message #<?= htmlspecialchars($message['message_ref'] ?? $message['id']) ?>
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-400">
                        <?= date('d M Y \a\t H:i', strtotime($message['created_at'])) ?>
                    </p>
                </div>

                <?php
                $statusMap = [
                    'new'     => 'bg-red-600/90 text-white border-red-500/40',
                    'read'    => 'bg-blue-600/90 text-white border-blue-500/40',
                    'replied' => 'bg-emerald-600/90 text-white border-emerald-500/40',
                    'archived'=> 'bg-gray-700 text-gray-200 border-gray-600'
                ];
                $st = $statusMap[$message['status']] ?? 'bg-gray-700 text-gray-300';
                ?>
                <span class="inline-flex px-4 py-1.5 rounded-full text-sm font-semibold border <?=$st?> uppercase tracking-wide">
                    <?= ucfirst($message['status']) ?>
                </span>
            </div>

            <!-- Content -->
            <div class="p-2 sm:p-3 lg:p-4 space-y-4">

                <!-- Guest info grid -->
                <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                    <div class="space-y-2">
                        <div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-gray-500 mb-1.5">Guest Name</div>
                            <div class="text-lg font-medium text-white"><?= htmlspecialchars($message['full_name'] ?: '—') ?></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-gray-500 mb-1.5">Email</div>
                            <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="text-blue-400 hover:text-blue-300 transition">
                                <?= htmlspecialchars($message['email'] ?: '—') ?>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-gray-500 mb-1.5">Phone</div>
                            <div class="text-lg text-white"><?= htmlspecialchars($message['phone'] ?: '—') ?></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-gray-500 mb-1.5">Subject</div>
                            <div class="text-lg font-medium text-white capitalize"><?= htmlspecialchars($message['subject'] ?: '—') ?></div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-400 my-2">

                <!-- Message -->
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-yellow-400">Message</h2>
                    <div class="bg-gray-950/70 border border-gray-800 rounded-xl p-2 sm:p-4 prose prose-invert max-w-none leading-relaxed whitespace-pre-wrap text-gray-200">
                        <?= nl2br(htmlspecialchars($message['message'] ?: '(empty message)')) ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-8 border-t border-gray-800">
                    <a href="/all/index?page=messages"
                       class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-xl text-gray-200 hover:text-white transition font-medium">
                        ← Back to Messages
                    </a>

                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>"
                       class="inline-flex items-center px-8 py-3 bg-yellow-500 hover:bg-yellow-400 text-black font-semibold rounded-xl shadow-lg shadow-yellow-900/30 transition">
                        Reply via Email
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
footer:
if (function_exists('render_admin_footer') || file_exists(__DIR__ . '/../includes/footer.php')) {
} else {
    ?>
    </body>
    </html>
    <?php
}
?>