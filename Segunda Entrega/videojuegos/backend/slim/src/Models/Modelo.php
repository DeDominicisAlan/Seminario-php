<?php

namespace App\Models;

use DateTime;
use PDO;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Exception;
use App\Config\Config;

abstract class Modelo
{

  //Estructura de la respuesta
  protected $data = [
    'Status' => '',
    'Mensaje' => [],
    'Codigo' => '',
    'Data' => null,
  ];

  //Conexion a la base de datos
  public function connect()
  {
    $dbhost = "DB";
    $dbname = "seminariophp";
    $dbuser = "seminariophp";
    $dbpass = "seminariophp";

    $connection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connection;
  }

  protected function validator($patron, $cadena)
  {

    return (preg_match($patron, $cadena) === 1);
  }

  protected function verificarEspacio($string)
  { //Verificar que los nombres de los juegos no empiecen ni terminen con espacio, y que tenga las espacios correctos

    if ($string == "") //Si esta vacio, retorno false
      return false;

    if ($string !== trim($string))
      return false;

    $palabras = explode(' ', $string); //Divido el string en palabras en un vector, para comprobar si hay espacios seguidos

    foreach ($palabras as $palabra) {
      if ($palabra == "")
        return false; //Hubo 2 espacios seguidos
    }

    return true;
  }

  public function verificarToken($token)
  {
  try{
    $tokenDecoded = JWT::decode($token, new Key(Config::auth()['secret'], 'HS256'));
    
    $fechaActual = new DateTime();
    $fechaExpiracion = new DateTime();
    $fechaExpiracion->setTimestamp($tokenDecoded->exp);
    
      if ($fechaActual < $fechaExpiracion)
        return $tokenDecoded; //token es valido y no expiró
  }catch(Exception $e){
    return false;
    } //token expirado o no es valido
  
    return false; 
  }
  
}
?>