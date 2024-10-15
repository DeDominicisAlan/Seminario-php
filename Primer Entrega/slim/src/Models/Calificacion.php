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
        $this->data['Mensaje'] = 'Calificacion eliminada correctamente de la base de datos.';
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
}
?>