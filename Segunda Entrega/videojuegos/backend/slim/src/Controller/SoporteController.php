<?php

namespace App\Controller;

use App\Models\Soporte;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SoporteController extends Controlador{
  
  public function agregar(Request $request, Response $response){
    
    $token = $request->getAttribute('jwt');
    //valido que el usuario sea administrador
    if($token->a){
      $solicitud = $request->getParsedBody();
    
      $estaVacio = $this->validarCampo(
        $solicitud,
        'juego_id',
        'El campo de id de juego esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }
      
      $estaVacio = $this->validarCampo(
        $solicitud,
        'plataforma_id',
        'El campo de id de plataforma esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }
      
      $soporte = new Soporte();
      $resultado = $soporte->agregarSoporte($solicitud);
    }else{
      $resultado = [];
      $resultado['Mensaje'] = 'El usuario logueado no es administrador.';
      $resultado['Codigo'] = 401;
      $resultado['Status'] = 'Fail';
      $resultado['Data']['es_admin'] = $token->a;
      $resultado['Data']['id'] = $token->id;
    }
    
    
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

}


?>