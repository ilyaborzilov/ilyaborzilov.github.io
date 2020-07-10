<div class="navbar navbar-light bg-primary sticky-top text-light mb-4">
    <div align="center">
        <a  href="index.php">Naše nabídka</a>
        <?php if ($role < 2) { ?>
            <a  href="cart.php">| Můj košík</a> |
            <a  href="my_orders.php">Moje objednávky</a>
        <?php } ?>
    </div>
    <?php
    if (!empty($_SESSION['user_id'])) {
        ?>
        <div align="right">
            Přihlášen: <?php echo htmlspecialchars($loggedUser['name']); ?> |
            <a class="btn btn-primary mr-2" href="signout.php">Odhlásit</a>
        </div>
    <?php } else {
        echo '<p>Nejste přihlášený</p>';
    } ?>
</div>
