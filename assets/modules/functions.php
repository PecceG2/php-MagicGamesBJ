<?php
//--------------------------------
//
//       SALAS FUNCTIONS
//
//-------------------------------

//Return true if sala exists.
function salaexistsandinfo($salacode){
    $extract['exists'] = false;
    if($salacode != ""){
        set_include_path($_SERVER['DOCUMENT_ROOT']);
        require("assets/config/database.php");
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
        $salacode = mysqli_real_escape_string($link, $salacode);
        $sql = "SELECT * FROM `salas` WHERE `salacodigo`='$salacode';";

        $result = mysqli_query($link, $sql);
        if(mysqli_num_rows($result)==1){
            $extract = mysqli_fetch_array($result);
            $salaexists = true;
            $extract['exists'] = $salaexists;

        }
    }

    return $extract;
}

//Introduce player to the game
function joinplayer($cookiecode, $identifycode, $salacode)
{
    if (!checkcookie($cookiecode, $identifycode)) {
        //Invalid data (XSS Injection or data lost)
        return ("516");
    }

    set_include_path($_SERVER['DOCUMENT_ROOT']);
    require("assets/config/database.php");
    $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);

    $stnzd_identifycode = mysqli_real_escape_string($link, $identifycode);
    $stnzd_salacode = mysqli_real_escape_string($link, $salacode);

    $sql_checkplayeronline = "SELECT `users_x_sala`.`id` FROM `users_x_sala` INNER JOIN salas ON (`users_x_sala`.`sala_id`=salas.id) WHERE `users_x_sala`.`user_id`='$stnzd_identifycode' AND salas.salacodigo='$stnzd_salacode'";
    if (mysqli_num_rows(mysqli_query($link, $sql_checkplayeronline)) > 0) {
        //El jugador se quiere conectar en la MISMA sala donde ya se encuentra, NO SE HACE NADA.
        return "517";
    } else {
        $sql_checkplayeronline = "SELECT `id` FROM `users_x_sala` WHERE `user_id`='$stnzd_identifycode'";
        if (mysqli_num_rows(mysqli_query($link, $sql_checkplayeronline)) > 0) {
            $sql_updatesala = "UPDATE salas INNER JOIN `users_x_sala` AS LINK ON (LINK.`sala_id`=`salas`.`id`) SET salas.players = salas.players-1 WHERE LINK.`user_id`='$stnzd_identifycode';";
            mysqli_query($link, $sql_updatesala);
        }
        //It not connected in any room
        $sql_updatesala = "UPDATE salas AS T1 SET T1.players = T1.players+1 WHERE `salacodigo`='$stnzd_salacode';";
        mysqli_query($link, $sql_updatesala);
        //You have a little consultation in your redundancy
        $sql_addplayer = "INSERT INTO users_x_sala (`user_id`, `sala_id`, `player_num`, `livetime`) VALUES ('$stnzd_identifycode', (SELECT `id` FROM salas WHERE `salacodigo`='$stnzd_salacode'),(SELECT `players` FROM `salas` WHERE `salacodigo`='$stnzd_salacode'), CURRENT_TIME()) ON DUPLICATE KEY UPDATE `sala_id`=(SELECT `id` FROM salas WHERE `salacodigo`='$stnzd_salacode'), `player_num`=(SELECT `players` FROM `salas` WHERE `salacodigo`='$stnzd_salacode'), `livetime`=CURRENT_TIME();";
        mysqli_query($link, $sql_addplayer);
    }
    return ("1");
}

/**
 * @param $identifycode
 * @param $salaid
 * @return array|bool|null
 */
function info($identifycode, $cookiecode)
{
    set_include_path($_SERVER['DOCUMENT_ROOT']);
    require("assets/config/database.php");
    $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    $stnzd_identifycode = mysqli_real_escape_string($link, $identifycode);
    $stnzd_cookiecode = mysqli_real_escape_string($link, $cookiecode);
    if (checkcookie($stnzd_cookiecode, $stnzd_identifycode)) {
        $sql_select = "SELECT `username`, `realbalance`, `player_num`, `salacodigo`, `players`, `turnojugador`, `cartas_p1`, `cartas_p2`, `cartas_p3`, `cartas_p4`, `cartas_casa` FROM `users` INNER JOIN `users_x_sala` ON (users.id = `users_x_sala`.`user_id`) INNER JOIN salas ON (`users_x_sala`.`sala_id`=salas.id) INNER JOIN barajas ON (`users_x_sala`.`sala_id`=salas.id AND barajas.id_salas=salas.id) WHERE users.id='$stnzd_identifycode'";
        $result = mysqli_query($link, $sql_select);
        if (mysqli_num_rows($result) < 1) {
            return $extract['valid'] = false;
        }
        $extract = mysqli_fetch_array($result);

        $extract['valid'] = true;
        return $extract;
    }
    return "0";
}

function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function deleteallcookies(){
    $past = time() - 3600;
    foreach ( $_COOKIE as $key => $value )
    {
        setcookie($key, $value, $past, '/');
    }
}

function isOwner($ownercode, $uniquecode){
    $isowner = false;
    if($ownercode!="" && !empty($ownercode)){
        require_once("../config/database.php");
        if (!empty($config['hostname'])) {
            $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
        }
        $ownercode = mysqli_real_escape_string($link, $ownercode);
        $uniquecode = mysqli_real_escape_string($link, $uniquecode);
        $sql = 'SELECT `id` FROM `salas` WHERE `owner_id`="' . $ownercode . '" AND `uniquecode`="' . $uniquecode . '";';
        $query = mysqli_query($link, $sql);
        if(mysqli_num_rows($query)>=1){
            $isowner = true;
        }

    }

    return $isowner;
}

function obtenerbarajas($salaid)
{
    //Deprecated function
    //Obtener la sala y bajara de esa partida
    $sql_obtenerbarajas = "SELECT `cartas_p1`, `cartas_p2`, `cartas_p3`, `cartas_p4` FROM `barajas` WHERE `id_salas`=$salaid";
    require($_SERVER['DOCUMENT_ROOT'] . "/assets/config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $result = mysqli_query($link, $sql_obtenerbarajas);
    $extract = mysqli_fetch_array($result);
    for ($i = 1; $i <= 4; $i++) {
        $arraypos = "cartas_p$i";
        if ($extract[$arraypos] != "0") {
            $baraja[$i] = $extract[$arraypos];
        }
    }
    return $baraja;
}

function plantarse($identifycode, $cookiecode)
{
    //Cambiar el turno!
    //El SQL puede ser un UPDATE que vea si es tu turno, si es así le sumas uno a TURNOS en la DB :)
    require("../config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $stnzd_identifycode = mysqli_real_escape_string($link, $identifycode);
    $stnzd_cookiecode = mysqli_real_escape_string($link, $cookiecode);
    $sql_cambiarturno = "UPDATE `salas` INNER JOIN `users_x_sala` ON (`users_x_sala`.`player_num`=salas.turnojugador) INNER JOIN sessions ON (sessions.id_username=`users_x_sala`.`user_id`) SET `turnojugador`=`turnojugador`+1 WHERE `turnojugador`<`players` AND `sessions`.`id_username`='$stnzd_identifycode' AND `sessions`.`cookiecode`='$stnzd_cookiecode'";
    mysqli_query($link, $sql_cambiarturno);
    if (mysqli_affected_rows($link) == 1) {
        //Success
    } else {
        $sql_cambiarturno = "UPDATE `salas` INNER JOIN `users_x_sala` ON (`users_x_sala`.`player_num`=salas.turnojugador) INNER JOIN sessions ON (sessions.id_username=`users_x_sala`.`user_id`) SET `turnojugador`=1 WHERE `turnojugador`=`players` AND `sessions`.`id_username`='$stnzd_identifycode' AND `sessions`.`cookiecode`='$stnzd_cookiecode'";
        mysqli_query($link, $sql_cambiarturno);
        if (mysqli_affected_rows($link) == 1) {
            $int_lastid = mysqli_insert_id($link);
            //Last player finished
            playhouse($stnzd_identifycode, $stnzd_cookiecode);
            ganador();
            reiniciar($stnzd_identifycode);
        } else {
            echo('Internal exception: Invalid recived data');
            //echo($sql_cambiarturno);
        }
    }
}

function darcarta($identifycode, $cookiecode)
{
    //Sacar una carta de la base de datos sector "baraja en juego" y pasarla a "jugadorX"
    //Checkeamos que el jugador esté en la sala correspondiente, además de eso, obtenemos el turno y el jugador.
    require("../config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $stnzd_identifycode = mysqli_real_escape_string($link, $identifycode);
    $stnzd_cookiecode = mysqli_real_escape_string($link, $cookiecode);
    $sql_obtenerjugador = "SELECT `sala_id`, `player_num` FROM `users_x_sala` INNER JOIN `sessions` ON (`sessions`.`id_username`=`user_id`) WHERE sessions.id_username='$stnzd_identifycode' AND sessions.cookiecode='$stnzd_cookiecode';";
    $result = mysqli_query($link, $sql_obtenerjugador);
    if (mysqli_num_rows($result) == 1) {
        $extract = mysqli_fetch_array($result);
        $salaid = $extract['sala_id'];
        $plnum = "cartas_p" . $extract['player_num'];
        $sql_obtenerbaraja = "SELECT * FROM `barajas` WHERE `id_salas`='$salaid'";
        $result = mysqli_query($link, $sql_obtenerbaraja);
        $extract = mysqli_fetch_array($result);
        $a_baraja = explode(" ", $extract['cartas']);
        $cartaadar = array_pop($a_baraja);
        if ($extract[$plnum] == "0") {
            $setcartas = $cartaadar;
        } else {
            $a_cartaspl = explode(" ", $extract[$plnum]);
            $a_cartaspl[] = $cartaadar;
            $realval = getrealvalue($a_cartaspl);
            if ($realval >= 21) {
                //Una vez dada la carta, si la suma da 21 o superior le decimos por ajax..
                $cartaadar = "10/" . $cartaadar;
            }
            $setcartas = implode(" ", $a_cartaspl);
        }
        $a_baraja = implode(" ", $a_baraja);
        $sql_actualizarbaraja = "UPDATE `barajas` SET `$plnum` = '$setcartas', `cartas` = '$a_baraja' WHERE `barajas`.`id_salas` = $salaid;";
        $result = mysqli_query($link, $sql_actualizarbaraja);
        if ($realval >= 21) {
            plantarse($_COOKIE['identifycode'], $_COOKIE['cookiecode']);
        }
    } else {
        echo('Internal exception: Your turn does not match. Inconsistent data?');
        return false;
    }
    return $cartaadar;
}

function crearsala($namesala)
{
    //Creo la sala en su tabla correspondiente.
    $random = generateRandomString("6");
    set_include_path($_SERVER['DOCUMENT_ROOT']);
    require("assets/config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $namesala = mysqli_real_escape_string($link, $namesala);
    $sql_insertsala = "INSERT INTO `salas` (`id`, `name`, `players`, `status`, `salacodigo`, `owner_id`, `turnojugador`) VALUES (NULL, '$namesala', '0', '1', '$random', '0', '1');";
    $result = mysqli_query($link, $sql_insertsala);
    $ultimoinsertid = mysqli_insert_id($link);
    $sql_obtenersala = "SELECT `salacodigo` FROM `salas` WHERE `id`='$ultimoinsertid';";
    $result = mysqli_query($link, $sql_obtenersala);
    $extract = mysqli_fetch_array($result);
    //Crear, mezclar y repartir baraja.
    $cartas = "P!A P!2 P!3 P!4 P!5 P!6 P!7 P!8 P!9 P!10 P!J P!Q P!K C!A C!2 C!3 C!4 C!5 C!6 C!7 C!8 C!9 C!10 C!J C!Q C!K T!A T!2 T!3 T!4 T!5 T!6 T!7 T!8 T!9 T!10 T!J T!Q T!K D!A D!2 D!3 D!4 D!5 D!6 D!7 D!8 D!9 D!10 D!J D!Q D!K";
    $baraja = mezclar($cartas);
    $baraja = implode(" ", $baraja);
    $sql_crearbaraja = "INSERT INTO `barajas` (`id`, `id_salas`, `cartas`, `cartas_p1`, `cartas_p2`, `cartas_p3`, `cartas_p4`) VALUES (NULL, '$ultimoinsertid', '$baraja', '0', '0', '0', '0');";
    mysqli_query($link, $sql_crearbaraja);
    mysqli_close($link);
    setcookie("create", "", time() - 3600);
    return $extract['salacodigo'];
}

function mezclar($cartas)
{
    $a_cartas = explode(" ", $cartas);
    shuffle($a_cartas);
    return $a_cartas;
}

function getrealvalue($cartas)
{
    $suma = 0;
    $wait = 0;
    foreach ($cartas as $val) {
        $a_val = explode("!", $val);
        if ($a_val[1] == "Q" || $a_val[1] == "K" || $a_val[1] == "J") {
            $suma += 10;
        } else if ($a_val[1] == "A") {
            $wait += 1;
        } else {
            $suma += $a_val[1];
        }
    }
    if ($wait >= 1) {
        for ($i = 0; $i < $wait; $i++) {
            if ($suma <= 10) {
                $suma += 11;
            } else {
                $suma++;
            }
        }
    }
    return $suma;
}

function playhouse($identifycode, $cookiecode)
{
    require("../config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $sql_select_cards = "SELECT * FROM barajas WHERE";
    $sql_play = "";
}

function reiniciar($identifycode)
{
    //WARN: Use this function ONLY if you have already processed the data before (OTHERWISE IT IS A BIG SECURITY ERROR)
    $cartas = "P!A P!2 P!3 P!4 P!5 P!6 P!7 P!8 P!9 P!10 P!J P!Q P!K C!A C!2 C!3 C!4 C!5 C!6 C!7 C!8 C!9 C!10 C!J C!Q C!K T!A T!2 T!3 T!4 T!5 T!6 T!7 T!8 T!9 T!10 T!J T!Q T!K D!A D!2 D!3 D!4 D!5 D!6 D!7 D!8 D!9 D!10 D!J D!Q D!K";
    $baraja = mezclar($cartas);
    $baraja = implode(" ", $baraja);
    require("../config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $sql_restart = "UPDATE `barajas` INNER JOIN `salas` ON `barajas`.id_salas=`salas`.id INNER JOIN `users_x_sala` ON (`users_x_sala`.`sala_id`=`salas`.`id`) SET `barajas`.cartas='$baraja', `barajas`.cartas_p1='0', `barajas`.cartas_p2='0', `barajas`.cartas_p3='0', `barajas`.cartas_p4='0' WHERE `users_x_sala`.`user_id`='$identifycode'";
    $result = mysqli_query($link, $sql_restart);
    mysqli_close($link);
}

function ganador()
{
    $sql_obtenerganador = "";
}

function checkcookie($cookiecode, $userid)
{
    set_include_path($_SERVER['DOCUMENT_ROOT']);
    require("assets/config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $stnzd_cookiecode = mysqli_real_escape_string($link, $cookiecode);
    $stnzd_userid = mysqli_real_escape_string($link, $userid);
    $sql_checkdata = "SELECT `username` FROM `users` INNER JOIN sessions ON (sessions.id_username=users.id) WHERE sessions.cookiecode='$stnzd_cookiecode' AND sessions.id_username='$stnzd_userid';";
    $result = mysqli_query($link, $sql_checkdata);
    if (mysqli_num_rows($result) >= 1) {
        $returnvar = mysqli_fetch_array($result);
        return $returnvar['username'];
    } else {
        return false;
    }
}

function login($user, $passwd)
{
    require("assets/config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $stnzd_user = mysqli_real_escape_string($link, $user);
    $stnzd_passwd = mysqli_real_escape_string($link, $passwd);
    $stnzd_md5_passwd = md5($stnzd_passwd);
    $ip = getclientip();
    $rand = random(16);
    $sql_createsession = "INSERT INTO sessions (`id_username`, `cookiecode`, `ipv4`) VALUES ((SELECT id FROM users WHERE username='$stnzd_user' AND password='$stnzd_md5_passwd'), '$rand', '$ip');";
    if (mysqli_query($link, $sql_createsession)) {
        //se creó la sesion, user y clave correctos.
        $userid = mysqli_fetch_array(mysqli_query($link, "SELECT `id_username` FROM sessions WHERE `id`='" . mysqli_insert_id($link) . "'"));
        return $userid['id_username'] . '&' . $rand;
    } else {
        //El usuario o la clave no coinciden
        //Warn: If an error occurs, this condition will be accessed anyway. (Duplicate primary key, link error, etc.) When a problem occurs, enable debugging
        return '0';
    }
}

function getclientip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function random($count)
{
    $s = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $count)), 0, $count);
    return $s;
}

function register($user, $passwd, $email)
{
    require("assets/config/database.php");
    if (!empty($config['hostname'])) {
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
    }
    $stnzd_user = mysqli_real_escape_string($link, $user);
    $stnzd_email = mysqli_real_escape_string($link, $email);
    $stnzd_passwd = mysqli_real_escape_string($link, $passwd);
    $sql_checkuserandmail = "SELECT `id` FROM `users` WHERE `username`='$stnzd_user' OR `email`='$stnzd_email'";
    if (mysqli_num_rows(mysqli_query($link, $sql_checkuserandmail)) > 0) {
        return 2;
    } else {
        if (!valid_email($stnzd_email)) {
            return 3;
        } else {
            $a_email = implode("@", $stnzd_email);

            $stnzd_passwd_md5 = md5($stnzd_passwd);
            $sql_insert = "INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES (NULL, '$stnzd_user', '$stnzd_passwd_md5', '$stnzd_email');";
            $query = mysqli_query($link, $sql_insert);
            echo "1&";
            echo(login($stnzd_user, $stnzd_passwd));

        }
    }
}

function valid_email($email)
{
    if (is_array($email) || is_numeric($email) || is_bool($email) || is_float($email) || is_file($email) || is_dir($email) || is_int($email))
        return false;
    else {
        $email = trim(strtolower($email));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) return $email;
        else {
            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
            return (preg_match($pattern, $email) === 1) ? $email : false;
        }
    }
}

function isDomainAvailible($domain)
{
    //check, if a valid url is provided (double verification, if top verification failed.)
    if (!filter_var($domain, FILTER_VALIDATE_URL)) {
        return false;
    }
    //initialize curl
    $curlInit = curl_init($domain);
    curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curlInit, CURLOPT_HEADER, true);
    curl_setopt($curlInit, CURLOPT_NOBODY, true);
    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
    //get answer
    $response = curl_exec($curlInit);
    curl_close($curlInit);

    if ($response) return true;
    return false;
}