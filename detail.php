<?php

require 'db.php';
require 'user_required.php';

$all = 0;

$role = (int)$loggedUser['role'];

if (isset($_GET['order_det'])){
    $orderDet = $_GET['order_det'];
}else{
    die('Objednávka neexistuje');
}

$stmt = $db->prepare('SELECT sem_cartItems.*, sem_goods.name FROM sem_cartItems JOIN sem_goods ON sem_cartItems.product_id=sem_goods.id WHERE order_id=:order_id');
$stmt->execute([
    ':order_id' => $orderDet
]);
$orderItems = $stmt->fetchAll();

$statement = $db->prepare('SELECT sem_orders.*, sem_users.* FROM sem_orders LEFT JOIN sem_users ON sem_orders.user_id=sem_users.id WHERE order_id=:order_id');
$statement->execute([
    'order_id' => $orderDet
]);
$order = $statement->fetchAll();

$stmt4 = $db->prepare('SELECT * FROM sem_goods WHERE 1=?');
$stmt4->execute([1]);

$options = $stmt4->fetchAll();

require __DIR__ . '/incl/header.php';
include 'navbar.php';
?>
<div align="center">
    <h1 align="center">Přehled objednávky</h1><br/>

    <?php foreach ($order as $item){?>
        <p align="center">Zákazník: <?php echo htmlspecialchars($item['name'])?></p>
        <p align="center">Email: <?php echo htmlspecialchars($item['email'])?></p>
        <p align="center">Datum: <?php echo @$item['user_date']?></p>
    <?php } ?>


    <table>
        <thead>
            <tr>
                <th>Název</th>
                <th>Množství</th>
                <th>Cena za kus</th>
                <th></th>
                <th>Cena celkem</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orderItems as $item){
            $all += @$item['quantity']*@$item['price'];
        }?>

        <?php foreach ($orderItems as $item){?>
            <tr>
                <form action="rem_detail.php" method="get">
                  <td><?php echo @$item['name']?></td>
                  <td class="center"><?php echo htmlspecialchars(@$item['quantity']); ?></td>
                  <td class="right"><?php echo @$item['price'],' Kč'?></td>
                  <td class="right"><?php echo @$item['stav']?></td>
                  <td class="right"><?php echo @$item['quantity']*@$item['price'], ' Kč'?></td>
                  <input type="hidden" name="id" value="<?php echo @$item['id'];?>">
                  <input type="hidden" name="order" value="<?php echo @$item['order_id'];?>">
                  <input type="hidden" name="cost" value="<?php echo $all- @$item['price'] ;?>">
                  <?php if ($role > 1) {?>
                      <td class="right"><button type="submit">Smazat</button></td>
                  <?php } ?>
                </form>
            </tr>
        <?php } ?>
        </tbody>
        <?php if ($role > 1) { ?>
          <tr>
            <form action="add_detail.php" method="get">
              <td class="center">
                  <select name="option">
                    <?php foreach ($options as $option) {?>
                        <option value="<?php echo @$option['id'];?>"><?php echo @$option['name'];?></option>
                    <?php } ?>
                  </select>
              </td>
              <td class="center"><input type="text" name="count" value=1></td>
              <td class="center"><input type="submit" value="přidat"></td>
              <input type="hidden" name="price" value="<?php echo @$option['price'];?>">
              <input type="hidden" name="order" value="<?php echo $orderDet;?>">
              <input type="hidden" name="all" value="<?php echo $all;?>">
            </form>
          </tr>
        <?php } ?>
        <tfoot>
        <tr>
            <td>SUM</td>
            <td></td>
            <td></td>
            <td class="right"><?php echo $all, ' Kč' ?></td>
        </tr>
        </tfoot>
    </table>
    <br/><br/>

    <?php if ($role > 1) {?>
        <a href="ordersInfo.php" class="btn btn-primary">Zpátky na správu objednávek</a>
    <?php }else{?>
        <a href="my_orders.php" class="btn btn-primary">Zpátky na moje objednávky</a>
    <?php }?>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
