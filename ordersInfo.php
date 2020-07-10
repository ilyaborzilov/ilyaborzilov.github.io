<?php
require 'db.php';
require 'admin_required.php';

$role = 2;

if (isset($_GET['id'])){
    $stmt = $db->prepare('SELECT sem_cartItems.*, sem_goods.* FROM sem_cartItems left join sem_goods on sem_cartItems.product_id=sem_goods.id WHERE order_id=:item');
    $stmt->execute([
        ':item' => $_GET['id']
    ]);
    $items= $stmt->fetchAll();
}

$statement = $db->prepare('SELECT sem_orders.*,sem_users.email FROM sem_orders left join sem_users on sem_orders.user_id=sem_users.id order by user_date desc');
$statement->execute();
$orders=$statement->fetchAll();

require __DIR__ . '/incl/header.php';
include 'navbar.php';
?>


<div align="center">
    <h1>Správa objednávek</h1><br/>
    <table>
        <thead>
            <tr>
                <th>Číslo objednávky</th>
                <th>Datum přijetí</th>
                <th>Zákazník</th>
                <th></th>
                <th>Cena</th>
                <th>Datum</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order){?>
            <tr>
                <td><?php echo @$order['order_id']?></td>
                <td><?php $date = strtotime($order['order_date']);
                echo date('j.n.Y G:i:s', $date)?></td>
                <td><?php echo htmlspecialchars($order['email']);?></td>
                <td><?php echo @$order['stav'];?></td>
                <td class="right"><?php echo @$order['total_price'], ' Kč';?></td>
                <td><?php echo @$order['user_date'];?></td>
                <td class="center"><a class="btn btn-primary" href="detail.php?order_det=<?php echo @$order['order_id']?>">Detail</a>
                <a class="btn btn-primary" href="cancel.php?order_id=<?php echo @$order['order_id']?>">Zrušit</a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
