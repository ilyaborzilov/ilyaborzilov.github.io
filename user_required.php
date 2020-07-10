<?php
session_start();

$user_id = $_SESSION['user_id'];
if (!isset($_SESSION["user_id"])) {
    header('Location: signin.php');
    die();
}

$stmt = $db->prepare("SELECT * FROM sem_users WHERE id = ? LIMIT 1");
$stmt->execute(array($_SESSION["user_id"]));

$loggedUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loggedUser) {
    session_destroy();
    header('Location: index.php');
    die();
}
?>
