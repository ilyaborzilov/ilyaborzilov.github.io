<?php

$user_date = $_GET["date"];
if ($user_date!='') {
  require 'db.php';
  require 'user_required.php';

  $goodsCart = @$_SESSION['cart'];
  $question_marks = str_repeat('?,', count($goodsCart) - 1) . '?';

  $stmt = $db->prepare("SELECT * FROM sem_goods WHERE id IN ($question_marks) ORDER BY name");
  $stmt->execute(array_keys($goodsCart));
  $goods = $stmt->fetchAll();

  $customer = $loggedUser['id'];
  $statement = $db->prepare('INSERT INTO sem_orders (user_id, order_date) VALUES (:customer, NOW())');
  $statement->execute([
      'customer' => $customer
  ]);
  $order_id = $db->lastInsertId();

  foreach ($goods as $product) {
      $cartItem = $db->prepare('INSERT INTO sem_cartItems (order_id, product_id, price, quantity) VALUES (:order_id, :product_id, :price, :quantity)');
      $cartItem->execute([
          'order_id' => $order_id,
          'product_id' => $product['id'],
          'price' => $product['price'],
          'quantity' => $goodsCart[$product['id']]
      ]);
  }

  $stmt = $db->prepare('SELECT sum(price*quantity) AS total_price FROM sem_cartItems WHERE order_id = :order_id');
  $stmt->execute([
      'order_id' => $order_id
  ]);
  $totalPrice = $stmt->fetchColumn();

  $conditions = ['order_id' => $order_id];
  $args = ['total_price' => $totalPrice];


  if (isset($totalPrice)) {
      $stmt2 = $db->prepare('UPDATE sem_orders SET total_price=:total_price, user_date=:user_date WHERE order_id=:order_id');
      $stmt2->execute([
          ':total_price' => $totalPrice,
          ':user_date' => $user_date,
          ':order_id' => $order_id
      ]);

      unset($_SESSION['cart']);
      header('Location: index.php');
      die();

  } else {
      die('chyba');
  }
} else {
  echo "<script type='text/javascript'>alert('Musíte Zadat datum a čas')</script>";
  echo "<script type='text/javascript'>window.location.href='cart.php'</script>";
}


?>
