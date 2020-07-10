<?php
require 'db.php';
require 'user_required.php';

if (!isset($_GET['order_id'])) {
    die('Taková objednávka neexistuje');
}
$stmt = $db->prepare("SELECT * FROM sem_orders WHERE order_id=:order_id");
$stmt->execute([
    ':order_id' => $_GET['order_id']
]);
$cancelOrder = $stmt->fetchAll();

if (!$cancelOrder) {
    die('Objednávka nenalezena');
}

$deleteOrder = $db->prepare('DELETE FROM sem_orders WHERE order_id=:order_id');
$deleteOrder->execute([
    ':order_id' => $_GET['order_id']
]);
header('Location: ordersInfo.php?order&deleted=true');
die();

?>