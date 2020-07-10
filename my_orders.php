<?php
require 'db.php';
require 'user_required.php';

$role = 1;

//načtení objednávek podle id uživatele
$stmt = $db->prepare('SELECT * FROM sem_orders WHERE user_id=:user_id order by order_date desc');
$stmt->execute([
    'user_id' => $loggedUser['id']
]);
$orders = $stmt->fetchAll();

require __DIR__ . '/incl/header.php';
include 'navbar.php';
?>
<div>
    <h1 align="center">Moje objednávky</h1>
    <br/>
    <table align="center">
        <thead>
        <tr>
            <th>Číslo objednávky</th>
            <th>Datum přijetí</th>
            <th>Cena</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order){?>
            <tr>
                <td><?php echo @$order['order_id']?></td>
                <td><?php $date = strtotime($order['order_date']);
                    echo date('j.n.Y G:i:s', $date)?></td>
                <td><?php echo @$order['total_price'], ' Kč';?></td>
                <td><?php echo @$order['stav'];?></td>
                <td class="center">
                    <a class="btn btn-primary" href="detail.php?order_det=<?php echo @$order['order_id']?>">Detail</a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
