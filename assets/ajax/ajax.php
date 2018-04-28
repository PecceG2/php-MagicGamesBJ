<?php
require('../modules/functions.php');
if (isSet($_GET['pedircarta'])) {
    $cartadada = darcarta($_COOKIE['identifycode'], $_COOKIE['cookiecode']);
    if ($cartadada == "") {
        echo('Internal exception: empty deck');
    } else {
        //Reiniciando turno..
        $arr = explode('/', $cartadada);
        if ($arr[0] != "10") {
            echo $arr[0];
        } else {
            echo $arr[1];
        }
    }
} else if (isSet($_GET['plantarse'])) {
    plantarse($_COOKIE['identifycode'], $_COOKIE['cookiecode']);
} else if (isSet($_GET['update'])) {
    $a_update = info($_COOKIE['identifycode'], $_COOKIE['cookiecode']);
    //$imploded = $a_update['username'].",".$a_update['realbalance'].",".$a_update['player_num'].",".$a_update['salacodigo'].",".$a_update['players'].",".$a_update['turnojugador'].','.$a_update['cartas'].','.$a_update['cartas_p1'].','.$a_update['cartas_p2'].','.$a_update['cartas_p3'].','.$a_update['cartas_p4'].','.$a_update['cartas_casa'];
    foreach ($a_update as $key => $val)
        if (is_numeric($key))
            unset($a_update[$key]);
    echo(implode(",", $a_update));
} else if (isSet($_GET['checkcode'])) {
    $a_salainfo = salaexistsandinfo($_POST['salacode']);
    if ($a_salainfo['exists']) {
        die('1');
    } else {
        die('0');
    }
} else if (isSet($_GET['createroom'])) {
    //Creating game room...
    echo(crearsala($_POST['name']));
} else if (isSet($_GET['joinplayer'])) {
    echo(joinplayer($_COOKIE['cookiecode'], $_COOKIE['identifycode'], $_POST['salacode']));
} else if (isSet($_GET['finish'])) {
    reloadgame($_COOKIE['identifycode']);
} else {
    //Null action
    echo 'Silence is golden..';
}