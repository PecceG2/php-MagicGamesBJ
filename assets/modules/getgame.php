<?php
require("functions.php");
$a_gameinfo = info($_COOKIE['identifycode'], $_COOKIE['cookiecode']);
//Username: Username
//Realbalance: Game balance
//player_num: You turn (player num)
//salacodigo: Respective code of it
//players: Total player count
//turnojugador: Game turn
//valid: 1/0
$bool_active = false;
if ($a_gameinfo['players'] > 1) {
    $bool_active = true;
}
?>
<div id="game">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img class="logo" src="resources/img/logo-final.png">
                </a>
            </div>
            <p class="navbar-text navbar-right">Conectado en la sala <?= $a_gameinfo['salacodigo'] ?> | <a
                        href="index.php" class="navbar-link">Abandonar</a></p>
        </div>
    </nav>
    <div class="container-fluid" id="waiting" <?php if ($bool_active) {
        echo('style="display:none;"');
    } ?>>
        <div class="row justify-content-center align-self-center">
            <div class="span6" id="waitingcube">
                <h3 style="padding-left:22%;" id="waitmessage">Esperando jugadores.</h3>
                <h4 style="padding-top:1px;">Comparte este código con tus amigos: <?= $a_gameinfo['salacodigo'] ?></h4>
            </div>
        </div>

    </div>
    <div class="container-fluid" <?php if (!$bool_active) {
        echo('style="display:none;"');
    } ?> id="content">
        <div class="row justify-content-center megamargined" id="header">
            <img src="resources/img/card-sheet0.png" class="mazo"/>
            <p id="home">CASA: 0</p>
        </div>
        <div class="row justify-content-center">
            <!-- Empty {APUESTAS} -->
        </div>
        <div class="row justify-content-start">
            <div class="col-4" id="p1" class="playerbox">
                Jugador uno
            </div>
            <div class="col-4">
                <!-- Empty -->
            </div>
            <div class="col-4" id="p4" class="playerbox">
                Jugador cuatro
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-4" id="p2" class="playerbox">
                Jugador dos
            </div>
            <div class="col-4" id="p3" class="playerbox">
                Jugador tres
            </div>
        </div>
        <h3 id="status"></h3>
        <div id="actions">
            <button type="button" id="btn-pedircarta">Pedir carta</button>
            <button type="button" id="btn-plantarse">Plantarse</button>
        </div>
    </div>
</div>
<script src="resources/js/magic-ingame.js"></script>