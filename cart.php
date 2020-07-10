<?php
//načteme připojení k databázi
require 'db.php';
require 'user_required.php';

$role = 1;

if (isset($_GET['id'])) {
    $Product = $db->prepare('SELECT * FROM sem_goods WHERE id=:good_id');
    $Product->execute(['good_id' => $_GET['id']]);
    $Product->fetchAll();
    if (!$Product) {
        die("Produkt nebyl nalezen");
    }
}

$goodsCart = @$_SESSION['cart'];
$sumPrice = 0;
$sumPiece = 0;

//načtení zboží a přidělení množství kusů a celkové ceny podle jejich id
if (is_array($goodsCart) && count($goodsCart)) {
    foreach ($goodsCart as $goods_id => $pieces) {
        $Product = $db->prepare('SELECT * FROM sem_goods WHERE id=:good_id');
        $Product->execute(['good_id' => $goods_id]);
        $result = $Product->fetchAll(PDO::FETCH_ASSOC);
        $sumPrice += (int)($result[0]['price']) * $pieces;
        $sumPiece += $pieces;
    }

    $question_marks = str_repeat('?,', count($goodsCart) - 1) . '?';

    $stmt = $db->prepare("SELECT * FROM sem_goods WHERE id IN ($question_marks) ORDER BY name");
    $stmt->execute(array_keys($goodsCart));
    $goods = $stmt->fetchAll();



}
require __DIR__ . '/incl/header.php';
include 'navbar.php';
?>

<h2 align="center">Můj košík</h2>



<br/>
<div align="center">
<?php
if (!empty($goods)) {
    $sum = 0;
    ?>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>Název</th>
            <th>Cena</th>
            <th>Popis</th>
            <th>Množství</th>
            <th>Datum</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($goods as $good) {
            ?>
            <tr>
                <td class="center">
                    <form action="remove.php" method="post">
                        <input type="hidden" class="d-none" name="goodToRemove" value="<?php echo $good['id'] ?>">
                        <button type="submit" class="btn btn-dark">Smazat</button>
                    </form>
                </td>
                <td><?php echo htmlspecialchars($good['name']) ?></td>
                <td class="right"><?php echo $good['price'], ' Kč' ?></td>
                <td><?php echo htmlspecialchars($good['description']) ?></td>



                <td align="center">
                    <form action="buy.php?id=<?php echo $good['id'] ?>" method="post">
                        <input id="btn1" class="btn btn-primary w-75" type="submit" name="add" value="+">

                    </form>
                    <?php echo $goodsCart[$good['id']] ?>
                    <form action="buy.php?id=<?php echo $good['id'] ?>" method="post">
                        <input id="btn2" class="btn btn-primary w-75" type="submit" name="remove"
                               value="-" <?php echo ((int)$goodsCart[$good['id']] < 2) ? 'disabled' : '' ?>>
                    </form>
                </td>
                <td class="center">
                  <form action="order.php" method="get" id="get_date">
                    <input type="datetime-local" name="date">
                  </form>
                </td>
            </tr> </div>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td>SUM</td>
            <td></td>
            <td class="right"><?php echo $sumPrice, ' Kč' ?></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
    <br/><br/>
<div align="center">

    <a href="index.php">Pokračovat v nákupu</a>
    <a href="#" onClick="document.getElementById('get_date').submit();">Odeslat objednavku</a> </div>
    <?php
} else {
    echo 'V košíku nemáte žádné zboží';
}

require __DIR__ . '/incl/footer.php';
?>
