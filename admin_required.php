<?php
require 'user_required.php';

if ($loggedUser['role'] < 2) {
    die ("Na tuto akci nemáte dostatečné pravomoce!");
}
?>