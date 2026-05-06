<?php
session_start();
if ($_POST && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];
    // Save to file or database later
    file_put_contents('subscribers.txt', $email . PHP_EOL, FILE_APPEND);
    $_SESSION['subscribed'] = true;
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>