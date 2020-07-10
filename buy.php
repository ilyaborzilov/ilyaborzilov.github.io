<?php
require 'db.php';
require 'user_required.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$stmt = $db->prepare("SELECT * FROM sem_goods WHERE id=?");
$stmt->execute([$_GET['id']]);
$goods = $stmt->fetch();

if (!$goods) {
    die("Unable to find goods!");
}

$productToCart = $goods['id'];
$productCount = 1;
if (isset($_POST['add'])) {
    $productCount += (int)$_SESSION['cart'][$productToCart];
} else if (isset($_POST['remove'])) {
    $productCount = (int)$_SESSION['cart'][$productToCart] - 1;
} else {
    $productCount += (int)$_SESSION['cart'][$productToCart];
}
$_SESSION['cart'][$productToCart] = $productCount;

header('Location: cart.php');
