<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $subject = $_POST['subject'];
    $message = trim($_POST['message']);

    // Royal reference — now DE GRAND STYLE!
    $message_ref = 'DEGRAND' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

    try {
        $sql = "INSERT INTO contact_messages 
                (message_ref, full_name, email, phone, subject, message, ip_address, created_at) 
                VALUES 
                (:ref, :name, :email, :phone, :subject, :message, :ip, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ref'     => $message_ref,
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':subject' => $subject,
            ':message' => $message,
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        // === EMAIL TO DE GRAND HOTEL (REAL CALABAR) ===
        $to      = "reservations@degrandhotel.com";  // Real hotel email
        $subject_line = "New Message | $message_ref | De Grand Hotel Calabar";

        $email_body = "DE GRAND HOTEL & ROOFTOP - CALABAR\n";
        $email_body .= str_repeat("=", 50) . "\n\n";
        $email_body .= "NEW GUEST MESSAGE RECEIVED\n\n";
        $email_body .= "Reference     : $message_ref\n";
        $email_body .= "Name          : $name\n";
        $email_body .= "Email         : $email\n";
        $email_body .= "Phone         : $phone\n";
        $email_body .= "Subject       : $subject\n";
        $email_body .= "Date & Time   : " . date('d M Y \a\t h:i A') . " (Calabar Time)\n\n";
        $email_body .= "MESSAGE:\n$message\n\n";
        $email_body .= str_repeat("-", 50) . "\n";
        $email_body .= "Location: 1c Felix Nsemo Drive, Diamond Hill, Calabar\n";
        $email_body .= "Website : De Grand Hotel and Rooftop\n";

        $headers  = "From: no-reply@degrandhotel.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($to, $subject_line, $email_body, $headers);

        // Optional: BCC to backup email
        // mail("backup@degrandhotel.com", $subject_line, $email_body, $headers);

        // Success!
        $_SESSION['contact_success'] = true;
        $_SESSION['last_ref'] = $message_ref;

    } catch (Exception $e) {
        error_log("De Grand Contact Form Error: " . $e->getMessage());
    }
}

// Redirect back to contact page
header('Location: contact.php');
exit();
?>