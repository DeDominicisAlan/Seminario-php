<?php

namespace App\Models;

use PDO;

class Juego extends Modelo
{
  protected $patronAlfanumericoJuego = "/^[A-Za-z0-9ñ ]{1,45}$/";
  protected $patronImg = "/^data:image\/(?:png|jpeg|jpg);$/i";

  public function crearJuego($solicitud, $files)
  {
    try {
      $connection = $this->connect();

        $nombreDelJuegoValido = $this->verificarEspacio($solicitud['nombre']) && $this->validator($this->patronAlfanumericoJuego, $solicitud["nombre"]); //; 
        $imagenValida = $files["imagen"]; //Verificar que sea imagen y que sea valida
        $clasificacion = $solicitud["clasificacion_edad"];
        //Determina si existe el campo
  
        $existeJuego = false;
        
        if ($nombreDelJuegoValido) {
          $tablaJuegos = $connection->prepare('SELECT nombre FROM juego WHERE nombre = :nombre');
          $tablaJuegos->execute([':nombre' => $solicitud['nombre']]);
          $existeJuego = $tablaJuegos->fetchColumn();
        }
  
        $existeClasificacion = false;
        
        
        //valido la clasificacion de edad
        $existeClasificacion =  $this->verificarEdad($solicitud['clasificacion_edad']);
        
  
        if ($imagenValida) {
          $imagen64 = $this->convertirImg64($files);
        }
  
        if (!$existeJuego && $nombreDelJuegoValido && $existeClasificacion && $imagenValida) {
          $tablaEdades = $connection->prepare("INSERT INTO juego (nombre, descripcion, imagen, clasificacion_edad) VALUES(:nombre, :descripcion, :imagen,:clasificacion_edad)");
          $tablaEdades->execute([
            ':nombre' => $solicitud['nombre'],
            ':descripcion' => $solicitud['descripcion'],
            ':imagen' => $imagen64,
            ':clasificacion_edad' => $solicitud['clasificacion_edad']
          ]);
          
          $juegoId = $connection->lastInsertId();
          
          
          
          $this->data['Mensaje'] = "Juego ingresado con exito a la base de datos.";
          $this->data['Codigo'] = 200;
          $this->data['Status'] = 'Success';
          $this->data['Data'] = [
            'nombre' => $solicitud['nombre'], //Para que envie bien el dato sin espacios, ya que verifique que es correcto el nombre
            'descripcion' => $solicitud['descripcion'],
            'imagen' => $imagen64,
            'clasificacion_edad' => $solicitud['clasificacion_edad'],
            'juegoId' => $juegoId,
          ];
          
          
        } else {
          if ($existeJuego)
            $this->data['Mensaje']['existe_juego'] = "Ya existe un juego con ese nombre.";
  
          if (!$nombreDelJuegoValido)
            $this->data['Mensaje']['nombre'] = "El nombre del juego tiene un formato incorrecto.";
  
          if (!$existeClasificacion)
            $this->data['Mensaje']['clasificacion_edad_existe'] = "La clasificacion de edad no existe en la base de datos.";
  
          if (!$imagenValida)
            $this->data['Mensaje']['imagen'] = "La imagen subida no es valida o el campo esta vacio.";
  
          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 400;
        }
      
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }

    return $this->data;
  }

  public function editarJuego($solicitud, $id, $files)
  {

    try {
      $connection = $this->connect();
      $buscarJuego = $connection->prepare('SELECT * FROM juego WHERE id = :id');
      $buscarJuego->execute([':id' => $id]);
      $juego = $buscarJuego->fetch(PDO::FETCH_ASSOC);


      if ($juego) {
        
        $nombre = $this->validator($this->patronAlfanumericoJuego, $solicitud['nombre']) && $this->verificarEspacio($solicitud['nombre']);

        $campoImagenVacio = isset($files['imagen']);

        if($campoImagenVacio){
        $imagen64 = $this->convertirImg64($files);
        }else
          $imagen64 = $juego['imagen'];
        //Mantener la imagen existente asi no se sube una invalida

        $clasificacionEdad = $this->verificarEdad($solicitud['clasificacion_edad']);

        if ($nombre && $clasificacionEdad) {
          $tablaJuegos = $connection->prepare('UPDATE juego SET nombre = :nombre, descripcion = :descripcion, clasificacion_edad = :clasificacion_edad, imagen = :imagen WHERE id = :id');
          $tablaJuegos->execute([
            ':nombre' => $solicitud['nombre'],
            ':descripcion' => $solicitud['descripcion'],
            ':clasificacion_edad' => $solicitud['clasificacion_edad'],
            ':imagen' => $imagen64,
            ':id' => $id
          ]);

          $this->data['Mensaje'] = "Juego ingresado con exito a la base de datos.";
          $this->data['Codigo'] = 200;
          $this->data['Status'] = 'Success';
          $this->data['Data'] = [
            'nuevo juego' => $solicitud,
            'juego anterior' => $juego
          ];
        } else {

          $this->data['Status'] = 'Fail';
          $this->data['Codigo'] = 400;
          $this->data['Data'] = $solicitud;

          if (!$nombre)
            $this->data['Mensaje']['nombre'] = "El nombre del juego tiene un formato incorrecto.";

          if (!$clasificacionEdad)
            $this->data['Mensaje']['clasificacion_edad_existe'] = "La clasificacion de edad no existe en la base de datos.";

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

  public function obtenerJuego($id)
  {
    try {

      $connection = $this->connect();
      $buscarJuego = $connection->prepare('SELECT * FROM juego WHERE id = :id');
      $buscarJuego->execute([':id' => $id]);
      $juego = $buscarJuego->fetch(PDO::FETCH_ASSOC);

      if ($juego) {

        $tablaSoporte = $connection->prepare('
        SELECT p.*, s.juego_id, s.plataforma_id 
        FROM plataforma p
        INNER JOIN soporte s ON p.id = s.plataforma_id
        WHERE s.juego_id = :juego_id
        ');

        $tablaSoporte->execute([':juego_id' => $id]);
        $soportes = $tablaSoporte->fetchAll(PDO::FETCH_ASSOC);

        $tablaCalificaciones = $connection->prepare('SELECT c.*, u.nombre_usuario FROM calificacion c
        LEFT JOIN usuario u ON c.usuario_id = u.id
        WHERE c.juego_id = :juego_id');
        $tablaCalificaciones->execute([':juego_id' => $id]);
        $Calificaciones = $tablaCalificaciones->fetchAll(PDO::FETCH_ASSOC);

        if ($Calificaciones)
          $this->data['Data']['Calificaciones'] = $Calificaciones;

        $this->data['Status'] = 'Success';
        $this->data['Codigo'] = 200;
        $this->data['Data']['Juego'] = $juego;
        $this->data['Data']['Soporte'] = $soportes;
      } else {

        $this->data['Status'] = 'Fail';
        $this->data['Codigo'] = 404;
        $this->data['Mensaje'] = 'El juego con la ID solicitada no existe.';
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    return $this->data;
  }

  public function borrarJuego($id)
  {

    try {

      $connection = $this->connect();


      $tablaId = $connection->prepare("SELECT id FROM juego WHERE id = :id ");
      $tablaId->execute([
        ":id" => $id
      ]);
      $existeId = $tablaId->fetchColumn();

      if ($existeId) {

        $tablaCalificaciones = $connection->prepare("SELECT * FROM calificacion WHERE juego_id = :id");
        $tablaCalificaciones->execute([":id" => $id]);
        $existeClasificacionId = $tablaCalificaciones->fetchColumn();
        if (!$existeClasificacionId) {
          $tablaEliminar = $connection->prepare("DELETE FROM juego WHERE id = :id");
          $tablaEliminar->execute([":id" => $id]);
          $this->data['Status'] = 'Success';
          $this->data['Mensaje'] = 'Juego eliminado correctamente de la base de datos.';
          $this->data['Data'] = $id;
          $this->data['Codigo'] = 200;
        } else {
          $this->data['Mensaje'] = 'El juego no se puede eliminar porque tiene calificaciones.';
          $this->data['Codigo'] = 409;
          $this->data['Status'] = 'Fail';
          $this->data['Data'] = $id;
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

  public function obtenerJuegos($pagina, $clasificacion, $texto, $plataforma)
  {

    //juegos?pagina={pagina}&clasificacion={clasificacion}&texto={texto}&plataforma={plataforma} 
    //Listar los juegos de la página según los parámetros de búsqueda incluyendo la puntuación promedio del juego.

    //.=  es igual a +=

    try {

      $connection = $this->connect();

      $limite = 5; //paginado de a 5 juegos por vez
      $offset = ($pagina - 1) * $limite; //La pagina - 1, porque la pagina 1 seria 0 en mysql
      //si offset = 0, muestra los primeros 5... desde el 0..4
      //si offset = 1, muetra los siguientes 5... desde 5..9

      $consulta = "SELECT j.*, AVG(c.estrellas) AS puntuacion_promedio, GROUP_CONCAT(DISTINCT p.nombre) AS plataformas
      FROM juego j
      LEFT JOIN calificacion c ON j.id = c.juego_id 
      LEFT JOIN soporte s ON j.id = s.juego_id
      LEFT JOIN plataforma p ON s.plataforma_id = p.id
      WHERE 1=1"; 
      //WHERE 1=1 porque si no, no funcionan los filtros

      $parametros = [];

      if (!empty($texto)) {

        $consulta .= ' AND j.nombre LIKE :texto';
        $parametros['texto'] = '%' . $texto . '%'; //%texto& = entre
        //%texto = final, texto% comienzo
      }
      
      if(!empty($clasificacion)){
        //si se elige ATP aparecen solo esos, pero
        //si pone +13 aparecen los +13 y los ATP y si pone +18 aparecen todos.
      
        if($clasificacion === 'ATP'){
          $consulta .= ' AND j.clasificacion_edad = :clasificacion';
          $parametros['clasificacion'] = $clasificacion; // SOLO ATP
          
        }elseif($clasificacion === '13' || $clasificacion === ' 13' || $clasificacion === '+13'){
          $consulta .= ' AND (j.clasificacion_edad = :clasificacion OR j.clasificacion_edad = "ATP")';
          $parametros['clasificacion'] = '+13'; //ATP Y +13
          
        }elseif($clasificacion === '18' || $clasificacion === ' 18' || $clasificacion === '+18'){
          $consulta .= ' AND (j.clasificacion_edad = :clasificacion OR 
                              j.clasificacion_edad = "ATP" OR
                              j.clasificacion_edad = "+13")';
        $parametros['clasificacion'] = '+18';
        }
        //El navegador no interpreta el signo '+' entonces tomamos si ingresan 18 o 13
        //o en caso que asignen el signo '+13' lo tomamos como ' 13'
      }
      
      if($plataforma){
        $consulta .= ' AND p.nombre LIKE :plataforma';
        $parametros['plataforma'] = '%'.$plataforma. '%';
      }
      
      //PAGINACION
      
      //LIMIT = es el limite de registros
      //OFFSET = numero de registros que se saltan
      
      //OFFSET seria el num de pagina y LIMIT la cantidad de juegos(registros) que se muestran
      
      $consulta .= ' GROUP BY j.id ORDER BY puntuacion_promedio DESC, j.nombre ASC ';
      
      $consultaTotal = $consulta;
      
      $tablaJuegosTotal = $connection->prepare($consultaTotal);
      
      //Asigno los parametros
      if(isset($parametros['texto'])){
        $tablaJuegosTotal->bindParam(':texto', $parametros['texto'], PDO::PARAM_STR);
      }
      if(isset($parametros['clasificacion'])){
        $tablaJuegosTotal->bindParam(':clasificacion', $parametros['clasificacion'], PDO::PARAM_STR);
      }
      if(isset($parametros['plataforma'])){
        $tablaJuegosTotal->bindParam(':plataforma', $parametros['plataforma'], PDO::PARAM_STR);
      }
      

      $tablaJuegosTotal->execute();
      $juegosTotal = $tablaJuegosTotal->fetchAll(PDO::FETCH_ASSOC);
      if($tablaJuegosTotal){
        $this->data['Data']['size'] = sizeof($juegosTotal);

        $consultaPaginas = $consultaTotal . "LIMIT :limite OFFSET :offset";
      
        $tablaJuegosPaginas = $connection->prepare($consultaPaginas);
      
        if(isset($parametros['texto'])){
          $tablaJuegosPaginas->bindParam(':texto', $parametros['texto'], PDO::PARAM_STR);
        }
        if(isset($parametros['clasificacion'])){
          $tablaJuegosPaginas->bindParam(':clasificacion', $parametros['clasificacion'], PDO::PARAM_STR);
        }
        if(isset($parametros['plataforma'])){
          $tablaJuegosPaginas->bindParam(':plataforma', $parametros['plataforma'], PDO::PARAM_STR);
        }
        
        $tablaJuegosPaginas->bindParam(':limite', $limite, PDO::PARAM_INT);
        $tablaJuegosPaginas->bindParam(':offset', $offset, PDO::PARAM_INT);
        $tablaJuegosPaginas->execute();
        $juegos = $tablaJuegosPaginas->fetchAll(PDO::FETCH_ASSOC);
        
        if($tablaJuegosPaginas){
          $this->data['Status'] = 'Success';
          $this->data['Codigo'] = 200;
          $this->data['Data']['juegos'] = $juegos;
        }
      
      }else{
      $this->data['Status'] = 'Fail';
      $this->data['Mensaje'] = 'No se encontraron juegos.';
      $this->data['Codigo'] = 404;
      }
    } catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    
    return $this->data;
  }

  public function obtenerJuegosCompletos(){
    try{
      
      $connection = $this->connect();

      $tablaJuegos = $connection->prepare("SELECT * FROM juego");
      $tablaJuegos->execute();
      $juegos = $tablaJuegos->fetchAll(PDO::FETCH_ASSOC);
      
      if($juegos){
        $this->data['Status'] = 'Success';
        $this->data['Codigo'] = 200;
        $this->data['Data']['juegos'] = $juegos;
      }else{
        $this->data['Status'] = 'Fail';
        $this->data['Mensaje'] = 'No se encontraron juegos.';
        $this->data['Codigo'] = 404;
        }

      
    }catch (\PDOException $e) {
      $this->data['Status'] = 'Throw Server/DB Error';
      $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
      $this->data['Codigo'] = 500;
    }
    
    return $this->data;
  }

  private function convertirImg64($files)
  {

    //Accedo a la imagen
    $imagen = $files['imagen'];

    //Obtener el stream del archivo
    $stream = $imagen->getStream();

    //Obtener la ruta temporal del archivo en el servidor
    $ruta = $stream->getMetadata('uri');

    //Obtener el contenido del archivo como cadena de datos binarios
    $imagenBinaria = file_get_contents($ruta);

    //Para convertir la imagen a base64, tengo que convertir la imagen que me llega a codigo binario
    $imagen64 = base64_encode($imagenBinaria);

    return $imagen64;
  }

  

  private function verificarEdad($clasificacion)
  {
    $tablaEnum = $this->connect()->prepare('SELECT clasificacion_edad FROM juego WHERE clasificacion_edad = :edad');
    $tablaEnum->execute([':edad' => $clasificacion]);
    return $tablaEnum->fetchColumn();
  }
  
  
  
}


?>