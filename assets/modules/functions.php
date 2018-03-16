<?php
//--------------------------------
//
//       SALAS FUNCTIONS
//
//-------------------------------

//Return true if sala exists.
function salaexistsandinfo($salacode){
    $salaexists = false;
    if($salacode != ""){
        require("assets/config/database.php");
        $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
        $salacode = mysqli_real_escape_string($link, $salacode);
        $sql = "SELECT `id`, `name`, `status`, `owner`, `players` FROM `salas` WHERE `salacodigo`='$salacode';";

        $result = mysqli_query($link, $sql);
        if(mysqli_num_rows($result)==1){
            $extract = mysqli_fetch_array($result);
            $salaexists = true;
            $extract['exists'] = $salaexists;

        }
    }

    return $extract;
}

//Join player to game
function joinplayer($nameplayer, $salaid){
    $str_random = generateRandomString();
    $sql_addplayers = "UPDATE salas AS T1 SET T1.players = T1.players+1 WHERE `salacodigo`='$salaid'; INSERT INTO `jugadores` (`id`, `name`, `identify-code`, `sala_id`) VALUES (NULL, $nameplayer, $str_random, $salaid);";
    setcookie("identify-code", $str_random);
    return $str_random;
}

function generateRandomString($length = 12) {
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
        setcookie( $key, $value, $past, '/' );
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
        $sala = mysqli_real_escape_string($link, $uniquecode);
        $sql = 'SELECT `id` FROM `salas` WHERE `owner`="'.$ownercode.'" AND `uniquecode`="'.$uniquecode.'";';
        $query = mysqli_query($link, $sql);
        if(mysqli_num_rows($query)>=1){
            $isowner = true;
        }

    }

    return $isowner;
}