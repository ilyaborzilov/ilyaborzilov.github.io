<?php
session_start();

require 'db.php';
require_once 'incl/facebook.php';

$errors = [];

if (!empty($_POST)) {
    $email = @$_POST['email'];
    $password = @$_POST['password'];

    $stmt = $db->prepare("SELECT * FROM sem_users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Zadejte správně e-mail!';
    }

    if (empty($errors)) {

        if (($existingUser = $stmt->fetch(PDO::FETCH_ASSOC)) && password_verify($password, @$existingUser['password'])) {

            $_SESSION['user_id'] = $existingUser['id'];
            header('Location: index.php');
        } else {
            $formError = "Spatný Email nebo password!";
        }
    }
}

$fbHelper = $fb->getRedirectLoginHelper();
$permissions = ['email'];
$callbackUrl = htmlspecialchars('https://eso.vse.cz/~bori00/eshop2/fb-callback.php');

$fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permissions);

require __DIR__ . '/incl/header.php';
?>
<div class="cont">
    <div>
        <h2 align="center">Přihlášení</h2>
    </div>
    <br/><br/>
    <?php
    if (!empty($formError)) {
        echo '<p align="center" style="color:red;">' . $formError . '</p>';
    }
    ?>

    <form class="d-flex flex-column" method="post">
        <div align="center">
            <label for="email">Your Email</label><br/>
            <input class="form-control <?php echo(!empty($errors['email']) ? 'is-invalid' : ''); ?>" type="text"
                   name="email" id="email"
                   value="<?php echo htmlspecialchars(@$_POST['email']) ?>">
            <?php
            if (!empty($errors['email'])) {
                echo '<div class="invalid-feedback">' . $errors['email'] . '</div>';
            }
            ?></div>
        <br/><br/>
        <div align="center">
            <label for="password">Password</label><br/>
            <input class="form-control" type="password" name="password" id="password" value=""><br/><br/>
        </div>
        <div align="center">
            <input  type="submit" value="Přihlásit">
        </div>
        <br/>
        <div align="center">
            <?php echo '<a  href="' . $fbLoginUrl . '">Přihlásit přes Facebook</a>'; ?>
        </div>
    </form>
    <br/>
    <div align="center">
        <a  href="signup.php">Registrace</a>
    </div>
 <br/><br/>
 <br/><br/>
 <br/><br/>
<div align="center">
        <a  href="privacy-policy.php">Privacy Policy</a>
    </div>

</div>

<?php
require __DIR__ . '/incl/footer.php';
?>
