<?php

namespace App\Models;

use PDO;

class Calificacion extends Modelo
{

  public function crearCalificacion($id, $solicitud)
  {

    try {
      $connection = $this->connect();
      $estrellas = $solicitud['estrellas'];
      if ($estrellas >= 1 && $estrellas <= 5) {
        $tablaUsuario = $connection->prepare("SELECT * FROM usuario WHERE id = :id");
        $tablaUsuario->execute([
          ":id" => $id
        ]);
        $existeId = $tablaUsuario->fetchColumn();
        if ($existeId) {
          
          $tablaJuego = $connection->prepare("SELECT * FROM juego WHERE id = :id");
          $tablaJuego->execute([
            ":id" => $solicitud['juego_id']
          ]);
          $existeJuego = $tablaJuego->fetchColumn();
          if ($existeJuego) {
          
            $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE usuario_id = :usuario_id AND juego_id = :juego_id");
            $tablaCalificacion->execute([
            ":usuario_id" => $id,
            ":juego_id" => $solicitud['juego_id']
            ]);
            $existeCalificacion = $tablaCalificacion->fetchColumn();
            if(!$existeCalificacion) {
              $tablaCalificacion = $connection->prepare("INSERT INTO calificacion(estrellas, usuario_id, juego_id) VALUES (:estrellas, :usuario_id, :juego_id)");
              $tablaCalificacion->execute([
              ":estrellas" => $estrellas,
                ":usuario_id" => $id,
                ":juego_id" => $solicitud['juego_id']
              ]);
              $this->data['Mensaje'] = 'Calificacion creada con exito.';
              $this->data['Status'] = 'Success';
              $this->data['Codigo'] = 200;
              $this->data['Data']['id_usuario'] = $existeId;
            }else{
              $this->data['Mensaje'] = 'Ya has calificado este juego. Puedes actualizar tu calificación.';
              $this->data['Codigo'] = 409; // Conflicto
              $this->data['Status'] = 'Fail';
              return $this->data;
            }
          
            
          } else {
            $this->data['Mensaje'] = 'El id del juego no existe en la base de datos';
            $this->data['Codigo'] = 404;
            $this->data['Status'] = 'Fail';
            $this->data['Data'] = $solicitud['juego_id'];
          }    
        } else {
          $this->data['Mensaje'] = 'El id del usuario no existe en la base de datos';
          $this->data['Codigo'] = 404;
          $this->data['Status'] = 'Fail';
          $this->data['Data'] = $id;
        }
      } else {
        $this->data['Status'] = 'Fail';
        $this->data['Codigo'] = 400;
        $this->data['Mensaje'] = "El campo de estrellas es invalido. La puntuacion debe ser entre 1 y 5.";
      }    
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }

  public function eliminarCalificacion($id, $id_usuario, $es_admin)
  {

    try {

      $connection  = $this->connect();

      $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE id = :id");
      $tablaCalificacion->execute([
        ":id" => $id
      ]);
      $existeId = $tablaCalificacion->fetch(PDO::FETCH_ASSOC);

      if ($existeId) {
        
        if($id_usuario == $existeId['usuario_id'] || $es_admin == 1){
        
        $borrarCalificacion = $connection->prepare("DELETE FROM calificacion WHERE id = :id");
        $borrarCalificacion->execute([':id' => $id]);
        $this->data['Status'] = 'Success';
        $this->data['Mensaje'] = 'Calificacion eliminada correctamente.';
        $this->data['Data'] = $id;
        $this->data['Codigo'] = 200;
        
        }else{
          
          $this->data['Mensaje'] = 'No tienes permiso para editar esta calificacion.';
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 403;
          $this->data['Data'] = $id_usuario;
        }
      } else {
        $this->data['Mensaje'] = 'El id no existe en la base de datos';
        $this->data['Codigo'] = 404;
        $this->data['Status'] = 'Fail';
        $this->data['Data'] = $id;
      }
      
      
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }

    return $this->data;
  }

  public function editarCalificacion($idUsuario,  $es_admin, $solicitud, $id)
  {

    try {

      $connection = $this->connect();
      
      $estrellas = $solicitud['estrellas'];
      $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE id = :id");
      $tablaCalificacion->execute([
        ':id' => $id
      ]);
      $existeId = $tablaCalificacion->fetch(PDO::FETCH_ASSOC);

        if ($existeId) {
          
         //Solo podria editar la calificacion si es admin o si es su propia calificacion
          
          if($idUsuario == $existeId['usuario_id'] || $es_admin == 1){

          if ($estrellas >= 1 && $estrellas <= 5) {

            $editarCalificacion = $connection->prepare("UPDATE calificacion SET estrellas = :estrellas WHERE id = :id");
            $editarCalificacion->execute([
              ':estrellas' => $estrellas,
              ':id' => $id
            ]);

            $this->data['Mensaje'] = 'Calificacion actualizada con exito.';
            $this->data['Status'] = 'Success';
            $this->data['Codigo'] = 200;
            $this->data['Data'] = $id;
          } else {

            $this->data['Status'] = 'Fail';
            $this->data['Codigo'] = 400;
            $this->data['Mensaje'] = "El campo de estrellas es invalido. La puntuacion debe ser entre 1 y 5.";
          }
          
          }else{
          
            $this->data['Mensaje'] = 'No tienes permiso para editar esta calificacion.';
            $this->data['Status'] = 'Fail';
            $this->data['Codigo'] = 403;
            $this->data['Data'] = $idUsuario;
          }
          
          
        } else {

          $this->data['Mensaje'] = 'El id de la calificacion no existe en la base de datos';
          $this->data['Codigo'] = 404;
          $this->data['Status'] = 'Fail';
          $this->data['Data'] = $estrellas;
        }
       
      
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }
  
  public function obtenerCalificacion($id, $id_juego){
  
    try{
    
      $connection = $this->connect();
      
      $tablaJuego = $connection->prepare("SELECT id FROM juego WHERE id = :id_juego");
      $tablaJuego->execute([':id_juego' => $id_juego]);
      $Juego = $tablaJuego->fetchColumn();
      if($Juego){
      $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE
      juego_id = :juego_id AND usuario_id = :usuario_id");
        $tablaCalificacion->execute([
          ':juego_id' => $id_juego,
          ':usuario_id' => $id
        ]);
        $calificacion = $tablaCalificacion->fetch(PDO::FETCH_ASSOC);
        if($calificacion){
          $this->data['Mensaje'] = 'Calificacion obtenida con exito.';
          $this->data['Status'] = 'Success';
          $this->data['Codigo'] = 200;
          $this->data['Data'] = $calificacion;
        }else{
          $this->data['Mensaje'] = 'El usuario no tiene calificaciones de este juego.';
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 404;
        }
        
        }else{
          $this->data['Mensaje'] = 'El juego solicitado no existe.';
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 404;
        }
      
      
    
    }catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
  
    return $this->data;
  }
  
  public function obtenerCalificaciones($id){
  
    try{
      
      $connection = $this->connect();
      
      $tablaUsuarios = $connection->prepare("SELECT * FROM usuario WHERE id = :id");
      $tablaUsuarios->execute([
        ':id' => $id
      ]);
      $existeId = $tablaUsuarios->fetchColumn();
      if($existeId){
      
      $tablaCalificacion = $connection->prepare("SELECT * FROM calificacion WHERE usuario_id = :id");
      $tablaCalificacion->execute([
        ':id' => $id
      ]);
      $existeCalificaciones = $tablaCalificacion->fetchAll(PDO::FETCH_ASSOC);
      if($existeCalificaciones){
        $this->data['Status'] = 'Success';
        $this->data['Codigo'] = 200;
        $this->data['Data'] = $existeCalificaciones;
      }else{
        $this->data['Mensaje'] = 'El id solicitado no tiene calificaciones en la base de datos';
        $this->data['Codigo'] = 404;
        $this->data['Status'] = 'Fail';
        $this->data['Data'] = $id;
      }
      }else{
        $this->data['Mensaje'] = 'El id solicitado no existe en la base de datos';
        $this->data['Codigo'] = 404;
        $this->data['Status'] = 'Fail';
        $this->data['Data'] = $id;
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