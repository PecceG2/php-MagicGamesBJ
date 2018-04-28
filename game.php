<?php
//Includes..
require('assets/modules/functions.php');

//Getting sala info...
/*$a_salainfo = salaexistsandinfo($_COOKIE['salacode']);
if(!$a_salainfo['exists']){
    header("Location: index.php?error=2");
    die('--');
}*/

if (!isSet($_COOKIE['identify-code'])) {
    //Si no tiene el codigo de identificacion (y por ende se da como que nunca ha ingresado), se ingresa ese jugador a la sala correspondiente.
    $a_playerinfo = joinplayer("John", $a_salainfo['id'], $a_salainfo['players']);
    //Sumo el jugador que se unió (debido a que la información de la sala se obtiene ANTES de ingresar al nuevo jugador.
    $a_salainfo['players']++;
} else {
    //Si tiene el codigo, puede que haya recargado la página, se obtiene su información.
    $a_playerinfo = getplayerinfo($_COOKIE['identify-code'], $_COOKIE['salacode']);

    if (!$a_playerinfo['valid']) {
        //El jugador no existe, o no se vincula con la sala, por ende, pudo haber sucedido que las cookies le quedaron pero la sala ya finalizó.
        header("Location: index.php?error=15");
        die('--');
    }
}
//INFORMACIÓN:
// Jugador: $a_playerinfo (Array)
// Sala: $a_salainfo (Array)

//El jugador existe, tenemos su información, la información de la sala. Se prosigue con el juego.
$a_barajas = obtenerbarajas($a_salainfo['id']);
// a_cartas['PJUGADOR'][NroCarta];
?>
<html>
<head>
    <title>Sala <?= $a_salainfo['id'] ?> | BlackJack MagicGames</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="resources/css/style.css"/>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                <img class="logo" src="resources/img/logo-final.png">
            </a>
        </div>
        <p class="navbar-text navbar-right">Conectado en la sala <?= $a_salainfo['id'] ?> | <a href="index.php"
                                                                                               class="navbar-link">Abandonar</a>
        </p>
    </div>
</nav>
<?php
if ($a_salainfo['players'] > 1){
    echo('<div class="container-fluid" id="content">');
}else{
?>
<div class="container-fluid" id="waiting">
    <div class="row justify-content-center align-self-center">
        <div class="span6" id="waitingcube">
            <h3 style="padding-left:22%;" id="waitmessage">Esperando jugadores.</h3>
            <h4 style="padding-top:1px;">Comparte este código con tus amigos: <?= $a_salainfo['salacodigo'] ?></h4>
        </div>
    </div>

</div>
<div class="container-fluid" style="display:none;" id="content">
    <?php
    }


    ?>

    <div class="row justify-content-center megamargined" id="header">
        <img src="resources/img/card-sheet0.png" class="mazo"/>
    </div>
    <div class="row justify-content-center">
        <!-- Empty {APUESTAS} -->
    </div>
    <div class="row justify-content-start">
        <div class="col-4" id="p1">
            Jugador uno
        </div>
        <div class="col-4">
            <!-- Empty -->
        </div>
        <div class="col-4" id="p4">
            Jugador cuatro
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-4" id="p2">
            Jugador dos
        </div>
        <div class="col-4" id="p3">
            Jugador tres
        </div>
    </div>
    <?php
    echo ('Sos el jugador:') . $a_playerinfo['player_num'] . ' de ' . $a_salainfo['players'] . ' conectados.<br>';
    $realvalue = getrealvalue($a_cartas['p1']);
    echo $realvalue;
    ?>
    <h3 id="status"><?= $status ?></h3>
    <div id="actions">
        <button type="button" id="btn-pedircarta">Pedir carta</button>
        <button type="button" id="btn-plantarse">Plantarse</button>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="resources/js/jquery.cookie.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        <?php
        echo('var playernum = "' . $a_playerinfo['player_num'] . '";');
        if ($a_salainfo['players'] > 1) {
            echo('var active = true;');
        } else {
            echo('var active = false;');
        }
        ?>
        var count = 0;
        if (!active) {
            setInterval(function () {
                count++;
                if (count >= 4) {
                    count = 0;
                }
                var dots = new Array(count % 10).join('.');
                document.getElementById('waitmessage').innerHTML = "Esperando jugadores." + dots;
            }, 1000);
        }
        (function update() {

            $.ajax({
                url: 'assets/ajax/ajax.php?update',
                success: function (data) {
                    if (data != "0") {
                        $("#content").show();
                        $("#waiting").hide();
                        var updatedata = data.split(",");
                        if (playernum == updatedata['0']) {
                            //su turno
                            $("#actions").show();

                        } else {
                            $("#actions").hide();
                        }
                        for (var i = 1; i <= 4; i++) {
                            var pnum = "#p" + i;
                            if (i == updatedata['0']) {
                                $(pnum).addClass('selectedpl');
                            } else {
                                $(pnum).removeClass('selectedpl');
                            }
                            if (updatedata[i] != 0) {
                                $(pnum).html(updatedata[i]);
                            } else {
                                $(pnum).html("0");
                            }
                        }
                    }
                    //alert(data);
                },
                complete: function () {
                    setTimeout(update, 2000);
                }
            });
        })();
        //Button functions
        $.cookie('create', "", {expires: 1, path: '/'});
        $("#btn-pedircarta").click(function () {
            $(this).prop('disabled', true);
            $.ajax({
                url: "assets/ajax/ajax.php?pedircarta",
                success: function (data) {
                    $("#you").append(' ' + data);
                    $("#btn-pedircarta").removeAttr('disabled');
                }
            });
        });
        $("#btn-plantarse").click(function () {
            $(this).prop('disabled', true);
            $.ajax({
                url: "assets/ajax/ajax.php?plantarse",
                success: function (data) {
                    $("#btn-plantarse").removeAttr('disabled');
                    $("#actions").hide();
                }
            });
        });
    });
</script>
</body>
</html>
