<?php
require 'db.php';
require 'admin_required.php';

$cost = $_GET['cost'];

$stmt3 = $db->prepare("UPDATE sem_orders SET total_price=$cost WHERE order_id=?");
$stmt3->execute([$_GET['order']]);

$stmt = $db->prepare("DELETE FROM sem_cartItems WHERE id=?");
$stmt->execute([$_GET['id']]);

header('Location: ordersInfo.php');
?>
