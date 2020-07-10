<?php
require 'db.php';

require 'user_required.php';

$id = @$_POST['goodToRemove'];
unset($_SESSION['cart'][$id]);
header('Location: cart.php');
die();

