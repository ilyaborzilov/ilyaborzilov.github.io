<?php
session_start();

require 'db.php';

$errors = [];
if (!empty($_POST)) {

    $name = @$_POST['name'];
    $email = @$_POST['email'];
    $password = @$_POST['password'];
    $passwordConfirm = @$_POST['passwordConfirm'];

    if (!$name || !$email) {
        $errors['empty'] = 'Vyplňte všechna pole';
    }

    if (!preg_match('/^[a-zá-žA-ZÁ-Ž]+.+[a-zá-žA-ZÁ-Ž]+$/', $name)) {
        $errors['name'] = 'Špatně zadané jméno';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Zadejte správně e-mail!';
    }

    if (strlen($password) < 9) {
        $errors['password'] = 'Heslo musí mít alespoň 9 znaků';
    }

    if ($password !== $passwordConfirm) {
        $errors['passwordConfirm'] = 'Hesla se neshodují';
    }


    if (empty($errors)) {
        $stmt = $db->prepare('SELECT * FROM sem_users WHERE email=:email');
        $stmt->execute([
            ':email' => $email
        ]);
        $usedEmail = $stmt->fetchAll();
        if ($usedEmail) {
            $errors['emailUsed'] = 'Tento e-mail je již zaregistrovaný';
        } else {


            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO sem_users(email, name, password) VALUES (?,?, ?)");
            $stmt->execute([$email, $name, $passwordHash]);

            $stmt = $db->prepare("SELECT id FROM sem_users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            $_SESSION['user_id'] = (int)$stmt->fetchColumn();

            header('Location: index.php');
        }
    }
}
require __DIR__ . '/incl/header.php';
?>
<div class="cont">
    <div>
        <h2 align="center">Registrace</h2>
    </div>
    <br/>
    <?php
    if (!empty($errors['empty'])) {
        ?>
        <div class="alert alert-danger">
            <p><?php echo $errors['empty'] ?></p>
        </div>
        <?php

    }
    ?>
    <form method="post">
        <div align="center">
            <label for="email">Váš e-mail</label><br/>
            <input class="form-control
    <?php echo(!empty($errors['emailUsed'] || $errors['email']) ? 'is-invalid' : ''); ?>"
                   type="text"
                   name="email" id="email" required
                   value="<?php echo htmlspecialchars(@$_POST['email']); ?>">
            <?php
            if (!empty($errors['emailUsed'])) {
                echo '<div class="invalid-feedback">' . $errors['emailUsed'] . '</div>';
            }
            if (!empty($errors['email'])) {
                echo '<div class="invalid-feedback">' . $errors['email'] . '</div>';
            }
            ?>
        </div>
        <br/><br/>
        <div align="center">
            <label for="name">Vaše jméno</label><br/>
            <input class="form-control <?php echo(!empty($errors['name']) ? 'is-invalid' : ''); ?>" type="text"
                   name="name" id="name" required
                   value="<?php echo htmlspecialchars(@$_POST['name']); ?>">
            <?php
            if (!empty($errors['name'])) {
                echo '<div class="invalid-feedback">' . $errors['name'] . '</div>';
            }
            ?>
        </div>
        <br/><br/>
        <div align="center">
            <label for="password">Heslo</label><br/>
            <input class="form-control <?php echo(!empty($errors['password']) ? 'is-invalid' : ''); ?>"
                   type="password"
                   name="password" id="password" required value="">
            <?php
            if (!empty($errors['password'])) {
                echo '<div class="invalid-feedback">' . $errors['password'] . '</div>';
            }
            ?>
        </div>
        <br/>
        <div align="center">
            <label for="passwordConfirm">Heslo znovu</label><br/>
            <input class="form-control <?php echo(!empty($errors['passwordConfirm']) ? 'is-invalid' : ''); ?>"
                   type="password"
                   name="passwordConfirm" id="passwordConfirm" required value="">
            <?php
            if (!empty($errors['passwordConfirm'])) {
                echo '<div class="invalid-feedback">' . $errors['passwordConfirm'] . '</div>';
            }
            ?><br/><br/>
        </div>
        <div align="center">
            <input type="submit" value="Vytvořit účet"> <a class="btn btn-secondary w-25 float-right"
                                                                                   href="index.php">Zrušit</a>
        </div>
    </form>
</div>
<?php
require __DIR__ . '/incl/footer.php';
?>
