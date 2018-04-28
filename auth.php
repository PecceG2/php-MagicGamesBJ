<?php
require('assets/modules/functions.php');

if (isSet($_GET['register'])) {
    $user = $_POST['user'];
    $passwd = $_POST['passwd'];
    $email = $_POST['email'];
    echo(register($user, $passwd, $email));
} else if (isSet($_GET['login'])) {
    echo(login($_POST['username'], $_POST['password']));
}
