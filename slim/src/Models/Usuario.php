<?php

namespace App\Models;

use PDO;
use Firebase\JWT\JWT;
use DateTime;
use App\Config\Config;

class Usuario extends Modelo
{

  protected $patronAlfanumerico = "/^[A-Za-z0-9ñ]{6,20}$/";
  protected $patronContraseña = "/^[A-Za-z0-9!#$%&'*+@?¡¿'ñ-_]{8,16}$/";

  public function crearUsuario($solicitud)
  {

    try {

      $connection = $this->connect();

      $usuario = $this->validator($this->patronAlfanumerico, $solicitud["nombre_usuario"]);
      $contraseña = $this->validator($this->patronContraseña, $solicitud["clave"]);

      if ($usuario && $contraseña) {
        $tablaUsuarios = $connection->prepare('SELECT * FROM usuario WHERE nombre_usuario = :nombre_usuario');
        $tablaUsuarios->execute([':nombre_usuario' => $solicitud['nombre_usuario']]);
        $usuarioExiste = $tablaUsuarios->fetchColumn();

        if (!$usuarioExiste) {
          $tablaUsuarios = $connection->prepare('INSERT INTO usuario (nombre_usuario, clave) VALUES (:nombre_usuario, :clave)');
          $tablaUsuarios->execute([

            ':nombre_usuario' => $solicitud['nombre_usuario'],
            ':clave' => $solicitud['clave']

          ]);

          $this->data['Status'] = 'Success';
          $this->data['Mensaje'] = 'Usuario registrado correctamente';
          $this->data['Data'] = $solicitud['nombre_usuario'];
          $this->data['Codigo'] = 200;
        } else {

          $this->data['Status'] = 'Fail';
          $this->data['Mensaje'] = 'El nombre de usuario ingresado ya se encuentra registrado en la base de datos.';
          $this->data['Data'] = ['El usuario ya existe' => $usuarioExiste];
          $this->data['Codigo'] = 400;
        }
      } else {

        $this->data['Status'] = 'Fail';
        $this->data['Codigo'] = 400;

        if (!$usuario)
          $this->data['Mensaje'] = array_merge($this->data['Mensaje'], ['usuario' => "El formato del campo es incorrecto, no se permite ningun caracter que no sea alfanumerico y el usuario debe tener mas de 4 caracteres."]);

        if (!$contraseña)
          $this->data['Mensaje'] = array_merge($this->data['Mensaje'], ['contraseña' => 'El formato del campo es incorrecto.']);
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }

  public function editarUsuario($solicitud, $id, $id_usuario, $es_admin)
  {

    try {

      $connection = $this->connect();

      //Si es Admin, puede modificar cualquier usuario
      //Si no es admin, verificar que el id de la solicitud, sea el mismo que el id que recibo
      //Es decir, un usuario solo puede modificar su propia cuenta

      $nuevoUsuario = $this->validator($this->patronAlfanumerico, $solicitud['nombre_usuario']);
      $nuevaClave = $this->validator($this->patronContraseña, $solicitud["clave"]);

      $tablaUsuarios = $connection->prepare('SELECT * FROM usuario WHERE id = :id');
      $tablaUsuarios->execute([':id' => $id]);
      $idExiste = $tablaUsuarios->fetchColumn();

      if ($idExiste) {
        
        if($id == $id_usuario || $es_admin){
        
        if ($nuevoUsuario || $nuevaClave) {

          $actualizoUsuario = false;
          $actualizoClave = false;

          if ($nuevoUsuario) {

            $tablaUsuarios = $connection->prepare('SELECT * FROM usuario WHERE nombre_usuario = :nombre_usuario');
            $tablaUsuarios->execute([':nombre_usuario' => $solicitud['nombre_usuario']]);
            $nombreUsuarioExiste = $tablaUsuarios->fetchColumn();

            if (!$nombreUsuarioExiste) { //Si el nombre de usuario no existe, actualizo

              $tablaUsuarios = $connection->prepare('UPDATE usuario SET nombre_usuario = :nombre_usuario WHERE id = :num');
              $tablaUsuarios->execute([
                ':nombre_usuario' => $solicitud['nombre_usuario'],
                ':num' => $id
              ]);
              $actualizoUsuario = true;
            } else {
              $this->data['Status'] = 'Fail';
              $this->data['Mensaje'] = 'El nombre de usuario ya está en uso.';
              $this->data['Codigo'] = 400;
              return $this->data;
            }
          }

          if ($nuevaClave) {
            //$clave = password_hash($solicitud['clave'], PASSWORD_DEFAULT);
            //La clave es muy larga y da error en la base de datos con Hash

            $tablaUsuarios = $connection->prepare('UPDATE usuario SET clave = :clave WHERE id = :num');
            $tablaUsuarios->execute([
              ':clave' => $solicitud['clave'],
              ':num' => $id
            ]);
            $actualizoClave = true;
          }
          //Se actualiza la clave o el usuario

          if ($actualizoUsuario && !$actualizoClave){
            $this->data['Mensaje'] = 'Nombre de usuario actualizado correctamente.';
            $this->data['Data'] = $solicitud['nombre_usuario'];
            }
          else if (!$actualizoUsuario && $actualizoClave)
            $this->data['Mensaje'] = 'Contraseña actualizada correctamente.';
          else if ($actualizoClave && $actualizoUsuario){
            $this->data['Mensaje'] = 'Se actualizo la contraseña y el nombre de usuario correctamente.';
            $this->data['Data'] = $solicitud['nombre_usuario'];
            }

          $this->data['Status'] = 'Success';
          $this->data['Codigo'] = 200;
        } else {
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 400;

          if (!$nuevoUsuario)
            $this->data['Mensaje'] = array_merge($this->data['Mensaje'], ['usuario' => "El formato del campo es incorrecto, no se permite ningun caracter que no sea alfanumerico y el usuario debe tener mas de 4 caracteres."]);

          if (!$nuevaClave)
            $this->data['Mensaje'] = array_merge($this->data['Mensaje'], ['contraseña' => 'El formato del campo es incorrecto.']);
        }
        
         }else{
          $this->data['Mensaje'] = 'El usuario no tiene los permisos necesarios.';
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 401;
          $this->data['Data']['id_usuario'] = $id_usuario;
          $this->data['Data']['es_admin'] = $es_admin;
          }
      } else {
        $this->data['Status'] = 'Fail';
        $this->data['Mensaje'] = 'El id ingresado no existe en la base de datos.';
        $this->data['Data'] = $id;
        $this->data['Codigo'] = 404;
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }

  public function eliminarUsuario($id,$id_usuario, $es_admin)
  {

    try {

      $connection = $this->connect();

      $tablaUsuarios = $connection->prepare("SELECT * FROM usuario WHERE id = :num");
      $tablaUsuarios->execute([":num" => $id]);
      $idExiste = $tablaUsuarios->fetchColumn();

      if ($idExiste) {

        if($id == $id_usuario || $es_admin){

        $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE usuario_id = :id");
        $tablaCalificacion->execute([":id" => $id]);
        $idExisteCalificacion = $tablaCalificacion->fetchColumn();

        if (!$idExisteCalificacion) {

          $tablaUsuarios = $connection->prepare("DELETE FROM usuario WHERE id = :num");
          $tablaUsuarios->execute([":num" => $id]);
          $this->data['Status'] = 'Success';
          $this->data['Mensaje'] = 'Usuario eliminado correctamente de la base de datos.';
          $this->data['Data'] = $id;
          $this->data['Codigo'] = 200;
        } else {
          $this->data["Status"]  = "Fail";
          $this->data['Mensaje'] = 'El usuario no se puede eliminar porque tiene una calificacion en un videojuego.';
          $this->data['Data'] = $id;
          $this->data['Codigo'] = 409;
        }
        }else{
        $this->data['Mensaje'] = 'El usuario no tiene los permisos necesarios.';
        $this->data['Status'] = 'Fail';
        $this->data['Codigo'] = 401;
        $this->data['Data']['id_usuario'] = $id_usuario;
        $this->data['Data']['es_admin'] = $es_admin;
        }
        
      } else {
        $this->data["Status"]  = "Fail";
        $this->data['Mensaje'] = 'El id ingresado no existe en la base de datos.';
        $this->data['Data'] = $id;
        $this->data['Codigo'] = 400;
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }

  public function obtenerUsuario($id)
  {

    try {
      $connection = $this->connect();
      $tablaUsuarios = $connection->prepare("SELECT * FROM usuario WHERE id = :id");
      $tablaUsuarios->execute([':id' => $id]);
      $existeId = $tablaUsuarios->fetch(PDO::FETCH_ASSOC);

      if ($existeId) {
        $this->data['Status'] = 'Success';
        $this->data['Mensaje'] = 'Cliente obtenido correctamente.';
        $this->data['Codigo'] = 200;
        $this->data['Data'] = [
          'id' => $existeId['id'],
          'nombre_usuario' => $existeId['nombre_usuario'],
          'es_admin' => $existeId['es_admin']
        ];
      } else {
        $this->data['Status']  = "Fail";
        $this->data['Mensaje'] = 'El usuario ingresado no existe en la base de datos.';
        $this->data['Data'] = $id;
        $this->data['Codigo'] = 400;
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }


  public function Login($solicitud)
  {
  
   //Validamos nombre y contraseña
    if (!$this->validator($this->patronAlfanumerico, $solicitud['nombre_usuario'])) {
    $this->data['Status'] = 'Fail';
    $this->data['Mensaje'] = 'El formato del usuario no es valido.';
    $this->data['Data'] = $solicitud;
    $this->data['Codigo'] = 400;
    return $this->data;
    }
  
    if (!$this->validator($this->patronContraseña, $solicitud['clave'])) {
    $this->data['Status'] = 'Fail';
    $this->data['Mensaje'] = 'El formato de la contraseña no es valido.';
    $this->data['Data'] = $solicitud;
    $this->data['Codigo'] = 400;
    return $this->data;
    }
  
    try {
    
      $connection = $this->connect();
      //Busco que el usuario exista
      $usuario = $connection->prepare("SELECT * FROM usuario WHERE nombre_usuario = :nombre_usuario AND clave = :clave");
      $usuario->execute([
        ':nombre_usuario' => $solicitud['nombre_usuario'],
        ':clave' => $solicitud['clave']
      ]);
      $existeUsuario = $usuario->fetch(PDO::FETCH_ASSOC);
      //Existe el usuario y la contraseña es incorrecta o el usuario no existe
      if (!$existeUsuario) {
        $this->data['Status'] = 'Fail';
        $this->data['Mensaje'] = 'Nombre de usuario o contraseña incorrecta.';
        $this->data['Codigo'] = 401;
        return $this->data;
      } else {

        $vencimiento =  time() + 3600; // token expiration
        //se guarda en la base de datos y se pone vencimiento_token a 1 hora en el futuro.        
        
        /*$payload = json_encode([
          'id' => $existeUsuario['id'],
          'es_admin' => $existeUsuario['es_admin'],
          'vencimiento' => $vencimiento
        ]);
        
        $token = hash('sha512', $payload); //Token de 128 caracteres
        */
        
        $payload = [
          'exp' => $vencimiento,
          'id' => $existeUsuario['id'],
          'a' => $existeUsuario['es_admin'] // es_admin tiene de nombre "a" por temas de longitud en el token
        ];
        
        $token = JWT::encode($payload, Config::auth()['secret'], 'HS256');
        
        $vencimientoFecha = new DateTime();
        $vencimientoFecha->setTimestamp($vencimiento);
        $vencimientoString = $vencimientoFecha->format('Y-m-d H:i:s'); // Convertir a formato string para la db

        
        $actualizarToken = $connection->prepare('UPDATE usuario SET token = :token, vencimiento_token = :vencimiento_token WHERE id = :id');
        $actualizarToken->execute([
          ':token' => $token,
          ':vencimiento_token' => $vencimientoString,
          ':id' => $existeUsuario['id'],
        ]);

        

        $this->data['Status'] = 'Success';
        $this->data['Mensaje'] = 'Usuario logueado correctamente.';
        $this->data['Codigo'] = 200;
        $this->data['Data']['token'] = $token;
        $this->data['Data']['id'] = $existeUsuario['id'];
        $this->data['Data']['vencimiento'] = $vencimientoString;
      }
      
    
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }
  
  
  
}


?>