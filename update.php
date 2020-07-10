<?php

require 'db.php';
require 'admin_required.php';

$role = 2;

$stmt = $db->prepare('SELECT sem_goods.*,sem_categories.name AS category_name FROM sem_goods JOIN sem_categories USING (category_id) WHERE id=:id');
$stmt->execute([':id' => @$_REQUEST['id']]);
$goods = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$goods) {
    die("Unable to find goods!");
}

$name = $goods['name'];
$description = $goods['description'];
$price = $goods['price'];


$error = [];

if (!empty($_POST)) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
     $description = trim($_POST['description']);

    if (!$name || !$price || !$description) {
        $errors['empty'] = 'Všechny pole musí být vyplněné';
    }

    if(preg_match("/<>/", $description)) {
      $errors['description'] = 'Popis nesprávné';
    }

    if (strlen($name) > 100) {
        $errors['name'] = 'Název musí obsahovat pouze písmena';
    }

    if (!preg_match('/^(0|[1-9][0-9]*)$/', $price)) {
        $errors['price'] = 'Cena musí být celé a nezáporné číslo';
    }


    if (strlen($description) > 1000) {
        $errors['description'] = 'Popis je příliš dlouhý';
    }

    if (!empty($_POST['category'])) {
        $categoryQuery = $db->prepare('SELECT * FROM sem_categories WHERE category_id=:category LIMIT 1;');
        $categoryQuery->execute([
            ':category' => $_POST['category']
        ]);
        if ($categoryQuery->rowCount() == 0) {
            $errors['category'] = 'Zvolená kategorie neexistuje!';
            $_POST['category'] = '';
        }
    } else {
        $errors['category'] = 'Musíte vybrat kategorii.';
    }

    if ($_POST['last_update'] != $goods['last_update']){
        die('Někdo jiný již provedl změny');
    }

    // pokud nejsou žádné chyby, updatuje se záznam v db
    if (empty($errors)) {
        $stmt = $db->prepare('UPDATE sem_goods SET name=:name, description=:description, price=:price, category_id=:category_id, last_update=now()  WHERE id=:id LIMIT 1;');
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':category_id' => $_POST['category'],
            ':id' => $_POST['id']
        ]);

        header('Location: index.php');
        exit();
    }
}

require __DIR__ . '/incl/header.php';
include 'navbar.php';
?>


<div align="center" class="cont">
    <h1>Upravovaní</h1>
    <br/>
    <form method="post">
        <?php
        if (!empty($errors['empty'])) {
            ?>
            <div class="alert alert-danger">
                <p><?php echo $errors['empty'] ?></p>
            </div>
            <?php

        }
        ?>
        <label for="category">Mechanik</label>
        <select name="category" id="category" required
                class="form-control" <?php echo(!empty($errors['category']) ? 'is-invalid' : ''); ?>">
        <?php
        $categoryQuery = $db->prepare('SELECT * FROM sem_categories ORDER BY name;');
        $categoryQuery->execute();
        $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                echo '<option value="' . $category['category_id'] . '" ' . ($category['category_id'] == @$goods['category_id'] ? 'selected="selected"' : '') . '>' . htmlspecialchars($category['name']) . '</option>';
            }
        }
        ?>
        </select>
        <?php
        if (!empty($errors['category'])) {
            echo '<div class="invalid-feedback">' . $errors['category'] . '</div>';
        }
        ?> <br/><br/>
        <label for="name">Name</label><br/>
        <input type="text" name="name" id="name"
               class="form-control <?php echo(!empty($errors['name']) ? ' is-invalid' : ''); ?>"
               value="<?php echo htmlspecialchars(@$name); ?>" required>
        <?php
        if (!empty($errors['name'])) {
            echo '<div class="invalid-feedback">' . $errors['name'] . '</div>';
        }
        ?><br/><br/>

        <label for="price">Price</label><br/>
        <input type="number" min="0" name="price" id="price" required
               class="form-control <?php echo(!empty($errors['price']) ? ' is-invalid' : ''); ?>"
               value="<?php echo htmlspecialchars(@$price) ?>">
        <?php
        if (!empty($errors['price'])) {
            echo '<div class="invalid-feedback">' . $errors['price'] . '</div>';
        }
        ?><br/><br/>


        <label for="description">Description</label><br/>
        <textarea name="description"
                  id="description"
                  class="form-control<?php echo(!empty($errors['description']) ? ' is-invalid' : ''); ?>"
        ><?php echo htmlspecialchars(@$description) ?></textarea>
        <?php
        if (!empty($errors['description'])) {
            echo '<div class="invalid-feedback">' . $errors['description'] . '</div>';
        }
        ?><br/><br/>

        <br/>

        <input type="hidden" name="id" value="<?php echo $goods['id']; ?>"/>
        <input type="hidden" name="last_update" value="<?php echo $goods['last_update']; ?>"/>

        <input class="btn btn-primary float-left" type="submit" value="Uložit"><a class="btn btn-secondary float-right" href="index.php">Zrušit</a>

    </form>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
