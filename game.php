<?php
//Includes..
require('assets/modules/functions.php');

//Check cookies
if(!isSet($_COOKIE['salacode'])){
    header("Location: index.php?error=2");
}

//Getting sala salas...
$salainfo = salaexistsandinfo($_COOKIE['salacode']);
if(!$salainfo['exists']){
    header("Location: index.php?error=2");
}

joinplayer("Pepito", $salainfo['id']);