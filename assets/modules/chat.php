<?php
//Generar chat, funciones, visuales, js, etc.
//Al enviar un mensaje, ejecuta una función, esa función sube el texto encriptado a la base de datos con el remitente y el destinatario
//El "updatelive" tiene que ejecutar A SU VEZ una función de acá que verifique si tenes mensajes nuevos
//Se arranca el timeout en 3000ms, si no recibe 3 veces, lo pasamos a 4000, 3 veces, 5000, y así sucesivamente hasta 8000ms
// Mensaje, destinario_user_id,
//Includes..
require("../config/database.php");

//Connection
if (!empty($config['hostname'])) {
    $link = mysqli_connect($config['hostname'], $config['dbuser'], $config['dbpasswd'], $config['dbname'], $config['dbport']);
} else {
    die('ERROR DATABASE CONNECTION');
}

function sendmessage($message, $userid, $usercookie, $dest)
{

}

function getmessages($userid, $usercookie)
{

}

function crypt($cadena, $bool)
{
    if ($bool) {
        $key = '';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
        return $encrypted; //Devuelve el string encriptado
    } else {
        $key = '';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
        return $decrypted;  //Devuelve el string desencriptado
    }
}