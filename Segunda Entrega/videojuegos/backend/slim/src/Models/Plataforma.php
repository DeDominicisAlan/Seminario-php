<?php

namespace App\Models;

use PDO;

class Plataforma extends Modelo{

    public function obtenerPlataformas(){
      try{
      
        $connection = $this->connect();
        
        $tablaPlataformas = $connection->prepare("SELECT * FROM plataforma");
        $tablaPlataformas->execute();
        $plataformas = $tablaPlataformas->fetchAll(PDO::FETCH_ASSOC);
        
        if($plataformas){
          $this->data['Status'] = 'Success';
          $this->data['Codigo'] = 200;
          $this->data['Data']['plataformas'] = $plataformas;
        }else{
          $this->data['Status'] = 'Fail';
          $this->data['Mensaje'] = 'No se encontraron plataformas.';
          $this->data['Codigo'] = 404;
        }
        
      }catch(\PDOException $e){
        $this->data['Status'] = 'Throw Server/DB Error';
        $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
        $this->data['Codigo'] = 500;
      }
      return $this->data;
    }

}

?>