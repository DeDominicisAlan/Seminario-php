<?php

namespace App\Controller;

use App\Models\Plataforma;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PlataformaController extends Controlador{
  
  public function obtener(Request $request, Response $response){
    
    $plataformaModel = new Plataforma();
    $resultado = $plataformaModel->obtenerPlataformas();
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

}


?>