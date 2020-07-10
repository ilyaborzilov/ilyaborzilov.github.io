<?php
require 'db.php';
require 'admin_required.php';

$pid = $_GET['option'];
$count = (int)$_GET['count'];
$price = $_GET['price'];
$order = $_GET['order'];
$all = $_GET['all'];

$stmt2 = $db->prepare("SELECT * FROM sem_goods WHERE id=?");
$stmt2->execute([$pid]);

$product = $stmt2->fetchAll();
foreach ($product as $prod) {
  $price = @$prod['price'];
}

$cost = $all + $price;

$errors = [];
if (!preg_match('/^(0|[1-9][0-9]*)$/', $count)) {
  $errors['price'] = 'Cena musí být celé a nezáporné číslo';
} else {
  $stmt = "INSERT INTO `sem_cartItems` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES (NULL, $order, $pid, $price, $count)";
  $db->exec($stmt);

  $stmt3 = $db->prepare("UPDATE sem_orders SET total_price=$cost WHERE order_id=?");
  $stmt3->execute([$order]);
}

header('Location: detail.php?order_det='.$order);
?>
