<?php
//Includes..
require('assets/modules/functions.php');

//Check login..
if (isSet($_COOKIE['cookiecode']) && isSet($_COOKIE['identifycode'])) {
    $bool_sessionvalid = checkcookie($_COOKIE['cookiecode'], $_COOKIE['identifycode']);
    if (!$bool_sessionvalid) {
        deleteallcookies();
    } else {
        $str_username = $bool_sessionvalid;
    }
}
?>
<html>
<head>
    <title>Inicio | Juega BlackJack en línea</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans:300i" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="resources/css/style.css"/>
</head>
<body>
<div class="container-fluid" id="start-content">
    <div class="row justify-content-center" id="header">
        <div id="col-sm-10 offset-sm-1 text-center">
            <img class="display-3" src="resources/img/logo-final.png" id="logo"/>
            <div class="loader">
                <div class="sk-cube-grid">
                    <div class="sk-cube sk-cube1"></div>
                    <div class="sk-cube sk-cube2"></div>
                    <div class="sk-cube sk-cube3"></div>
                    <div class="sk-cube sk-cube4"></div>
                    <div class="sk-cube sk-cube5"></div>
                    <div class="sk-cube sk-cube6"></div>
                    <div class="sk-cube sk-cube7"></div>
                    <div class="sk-cube sk-cube8"></div>
                    <div class="sk-cube sk-cube9"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="index-content">
        <div id="mainmenu" style="<?php if (!$bool_sessionvalid) {
            echo('display:none;');
        } ?>">
            <div class="row justify-content-center margined">
                <div class="col-6 menu titlemenu">
                    Bienvenido, <?= $str_username ?>!<a style="text-align:right;" id="deletesession"> Cerrar sesión</a>
                </div>

            </div>
            <div class="row justify-content-center">
                <div class="col-3 menu selected" id="div-join">
                    Unirse a una partida
                </div>
                <div class="col-3 menu" id="div-create">
                    Crear partida
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6 menu content">
                    <div id="join">
                        <label>Ingresa el codigo de la sala y haz click en continuar.</label>
                        <label id="lbl-error" style="color:#cb0000; padding-left:3px; display:none;">El codigo ingresado
                            no es válido.</label>
                        <input type="text" placeholder="Ejemplo: 91MO2J"
                               class="form-control form-control-sm input-medium" id="salacode" name="salacode"/>
                        <button id="btn-join-go" class="btn btn-outline-secondary button-align" type="button">
                            Continuar
                        </button>
                    </div>
                    <div id="create" style="display:none;">
                        <label>Ingrese un nombre para la sala y haz click en continuar</label>
                        <label id="lbl-error-create" style="color:#cb0000; padding-left:3px; display:none;">El codigo
                            ingresado no es válido.</label>
                        <input type="text" class="form-control form-control-sm input-medium" id="salaname"
                               name="salacode" placeholder="Ejemplo: Sala de Tito"/>
                        <button id="btn-create-go" class="btn btn-outline-secondary button-align" type="button">
                            Continuar
                        </button>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center publicrow">
                <div class="col-6 menu content">
                    <h5>Salas públicas</h5>
                </div>
            </div>
            <div class="row justify-content-center publicrow">
                <div class="col-2 menu content public">
                    <label>Sala 1</label>
                    <div class="players" style="color:#a82317;">
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col-2 menu content public">
                    <label>Sala 2</label>
                    <div class="players" style="color:#a82317;">
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col-2 menu content public">
                    <label>Sala 3</label>
                    <div class="players" style="color:#3aa82a;">
                        <i class="fas fa-user"></i>
                        <i class="fas fa-user"></i>
                        <i class="far fa-user"></i>
                        <i class="far fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div id="authmenu" style="<?php if ($bool_sessionvalid) {
            echo('display:none;');
        } ?>">
            <div class="row justify-content-center margined">
                <div class="col-3 menu selected" id="div-login">
                    Iniciar sesión
                </div>
                <div class="col-3 menu" id="div-register">
                    Registrarse
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6 menu content">
                    <div id="login">
                        <label>Inicia sesión para continuar</label>
                        <label id="lbl-error-login" style="color:#cb0000; padding-left:3px; display:none;">Usuario o
                            clave incorrectos.</label>
                        <input type="text" placeholder="Nombre de usuario"
                               class="form-control form-control-sm input-medium login-input" id="username"
                               name="username"/>
                        <input type="password" placeholder="Clave"
                               class="form-control form-control-sm input-medium padding-top login-input" id="passwd"
                               name="passwd"/>
                        <button id="btn-login" class="btn btn-outline-secondary button-align" type="button">Iniciar
                            sesión
                        </button>
                    </div>
                    <div id="register" style="display:none;">
                        <label>Registrate a continuación:</label>
                        <label id="lbl-error-register" style="color:#cb0000; padding-left:3px; display:none;"></label>
                        <input type="text" class="form-control form-control-sm input-medium" id="rgstr-username"
                               name="rgstr-username" placeholder="Nombre de usuario"/>
                        <input type="text" class="form-control form-control-sm input-medium padding-top"
                               id="rgstr-passwd" name="rgstr-passwd" placeholder="Clave"/>
                        <input type="text" class="form-control form-control-sm input-medium padding-top"
                               id="rgstr-email" name="rgstr-mail" placeholder="Email"/>
                        <button id="btn-register" class="btn btn-outline-secondary button-align" type="button">
                            Registrarse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<nav id="bottom-nav" class="navbar fixed-bottom">
    <p class="text">Magic Games © Todos los derechos reservados</p>
</nav>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="resources/js/jquery.cookie.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="resources/js/sweetalert2.js"></script>
<script src="resources/js/magic.js"></script>
</body>
</html>
