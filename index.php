<?php

require 'db.php';
require 'user_required.php';

if (isset($_GET['offset'])) {
    $offset = (int)$_GET['offset'];
} else {
    $offset = 0;
}

//zjištění role přihlášeného uživatele
if ($loggedUser) {
    $role = (int)$loggedUser['role'];
}

//načtení položek podle vybrané kategorie
if (!empty($_GET['category'])) {
    ##region zjištění počtu zboží pro stránkování
    $count = $db->prepare("SELECT COUNT(id) FROM sem_goods WHERE category_id = :category");
    $count->execute([
        'category' => $_GET['category']
    ]);
    $count = $count->fetchColumn();

    $stmt = $db->prepare("SELECT sem_goods.*,sem_categories.name AS category_name FROM sem_goods JOIN sem_categories USING (category_id) WHERE category_id = ? ORDER BY id DESC LIMIT 9 OFFSET ?");
    $stmt->bindValue(1, $_GET['category'], PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();

} // načtení všech položek
else {

    $count = $db->query("SELECT COUNT(id) FROM sem_goods")->fetchColumn();

    $stmt = $db->prepare("SELECT sem_goods.*,sem_categories.name AS category_name FROM sem_goods JOIN sem_categories USING (category_id) ORDER BY id DESC LIMIT 9 OFFSET ?");
    $stmt->bindValue(1, $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$goods = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/incl/header.php';
include 'navbar.php';

echo '<form align="center" method="get" id="categoryFilterForm">
          <label for="category">Mechanik:</label>
          <select align="center" class="form-control w-25" name="category" id="category" onchange="document.getElementById(\'categoryFilterForm\').submit();">
            <option value="">--Nevybráno--</option><br/>';

//načtení Mechaniku
$categories = $db->query('SELECT * FROM sem_categories ORDER BY name;')->fetchAll(PDO::FETCH_ASSOC);
if (!empty($categories)) {
    foreach ($categories as $category) {
        echo '<option value="' . $category['category_id'] . '"';
        if ($category['category_id'] == @$_GET['category']) {
            echo ' selected="selected" ';
        }
        echo '>' . htmlspecialchars($category['name']) . '</option>';
    }
}
echo '  </select>
          <input  type="submit" value="OK" class="d-none" />
        </form>';
?>
<br/>
<p align="center">Celkem položek: <strong><?php echo $count; ?></strong></p>
<br/>
<div align="center" >
<?php if ($role > 1) { ?>
    <a href="new.php">Nová položka</a>
<?php } ?>
<?php if ($role > 1) { ?>
    <a href="ordersInfo.php">Správa objednávek</a>
    <br/><br/>
<?php } ?>
</div>

<?php if ($count) { ?>
    <div align="center" >
        <?php foreach ($goods as $row) { ?>
            <div align="center" class="col-lg-4 mb-4">
                <div align="center" class="card h-100 product" id="productCard">
                    <div align="center" class="card-header">
                        <h4 class="card-title"><?php echo htmlspecialchars($row['name']) ?></h4>
                    </div>
                    <div class="card-body">
                        <h5 align="center"><?php echo htmlspecialchars($row['category_name']) ?></h5>

                        <p class="card-text"><?php echo $row['description'] ?></p>
                    </div>
                    <div align="center" class="card-footer">
                        <div class="float-left"><h5><?php echo number_format($row['price']), ' Kč' ?></h5></div>
                        <div class="float-right">
                            <?php if ($role < 2) { ?>
                                <a class="btn btn-primary" href='buy.php?id=<?php echo $row['id']; ?>'>Přidat do košíku</a>
                            <?php } ?>
                            <?php if ($role > 1) { ?>
                                <a class="btn btn-secondary" href='update.php?id=<?php echo $row['id']; ?>'>Upravit</a>
                                <a class="btn btn-secondary" href='delete.php?id=<?php echo $row['id']; ?>'>Smazat</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <br/>
    <div align="center" class="pagination">
        <?php
        for ($i = 1; $i <= ceil($count / 9); $i++) {
            echo '<a class="' . ($offset / 9 + 1 == $i ? 'active' : '') . '" href="index.php?offset=' . (($i - 1) * 9) . '">' . $i . '</a>';
        }
        ?>
    </div>
<?php }
require __DIR__ . '/incl/footer.php';
?>
