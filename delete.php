<?php
require 'db.php';
require 'admin_required.php';

$stmt = $db->prepare("DELETE FROM sem_goods WHERE id=?");
$stmt->execute([$_GET['id']]);

header('Location: index.php');
?>