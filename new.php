<?php

$role = 2;

require 'db.php';
require 'admin_required.php';

$errors = [];
if (!empty($_POST)) {
    $name = htmlspecialchars(trim($_POST['name']));
    $price = htmlspecialchars(trim($_POST['price']));
    $description = trim($_POST['description']);

    if (!$name || !$price || !$description) {
        $errors['empty'] = 'Všechny pole musí být vyplněné';
    }

    if (preg_match("/<>/", $description)) {
      $errors['description'] = 'Popis nesprávné';
    }

    if (!preg_match('/^[a-zá-žA-ZÁ-Ž]+.+[a-zá-žA-ZÁ-Ž]+$/', $name)) {
        $errors['name'] = 'Špatně zadaný název';
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

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO sem_goods(name, description, price,category_id) VALUES (:name, :description, :price, :category_id)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => floatval($price),
            ':category_id' => $_POST['category'],

        ]);
        header('Location: index.php');
        exit();
    }


}
require __DIR__ . '/incl/header.php';
include 'navbar.php' ?>



<div align="center" class="cont">
    <h1>Nové zboží</h1>
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
        <label for="category">Mechanik:</label>
        <select name="category" id="category" required
                class="form-control" <?php echo(!empty($errors['category']) ? 'is-invalid' : ''); ?>">
        <option value="">--vyberte--</option>
        <?php
        $categoryQuery = $db->prepare('SELECT * FROM sem_categories ORDER BY name;');
        $categoryQuery->execute();
        $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                echo '<option value="' . $category['category_id'] . '" ' . ($category['category_id'] == @$_POST['category'] ? 'selected="selected"' : '') . '>' . htmlspecialchars($category['name']) . '</option>';
            }
        }
        ?>
        </select>
        <?php
        if (!empty($errors['category'])) {
            echo '<div class="invalid-feedback">' . $errors['category'] . '</div>';
        }
        ?>
        <br/><br/>
        <label for="name">Name</label><br/>
        <input type="text" name="name" id="name"
               class="form-control <?php echo(!empty($errors['name']) ? ' is-invalid' : ''); ?>"
               value="<?php echo @$name ?>"
               required>
        <?php
        if (!empty($errors['name'])) {
            echo '<div class="invalid-feedback">' . $errors['name'] . '</div>';
        }
        ?>
        <br/><br/>
        <label for="price">Price</label><br/>
        <input type="number" min="0" name="price" id="price" required
               class="form-control <?php echo(!empty($errors['price']) ? ' is-invalid' : ''); ?>"
               value="<?php echo @$price ?>">
        <?php
        if (!empty($errors['price'])) {
            echo '<div class="invalid-feedback">' . $errors['price'] . '</div>';
        }
        ?>
        <br/><br/>


        <label for="description">Description</label><br/>
        <textarea name="description" id="description" required
                  class="form-control <?php echo(!empty($errors['description']) ? ' is-invalid' : ''); ?>"><?php echo @$description ?></textarea>
        <?php
        if (!empty($errors['description'])) {
            echo '<div class="invalid-feedback">' . $errors['description'] . '</div>';
        }
        ?>
        <br/><br/>

        <br/>

        <input class="btn btn-primary float-left" type="submit" value="Uložit"><a class="btn btn-secondary float-right"
                                                                         href="index.php">Zrušit</a>
    </form>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
