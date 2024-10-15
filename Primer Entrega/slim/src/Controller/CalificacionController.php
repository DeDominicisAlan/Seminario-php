<?php

namespace App\Controller;

use App\Models\Calificacion;
use App\Controller\Controlador;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CalificacionController extends Controlador
{

  public function crear(Request $request, Response $response)
  {
    $solicitud = $request->getParsedBody();
    $token = $request->getAttribute('jwt');

    
    $estaVacio = $this->validarCampo(
    $solicitud,
    'estrellas', 
    'El campo de estrellas esta vacio',
    $response
    );
    if($estaVacio != null){
      return $estaVacio;
    }
    
    $estaVacio = $this->validarCampo(
      $solicitud,
      'juego_id', 
      'El campo de id del juego esta vacio.',
      $response
      );
    
    if($estaVacio != null){
      return $estaVacio;
    }

    
    $calificacion = new Calificacion();
    
    $resultado = $calificacion->crearCalificacion($token->id, $solicitud);

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

  public function editar(Request $request, Response $response, $args)
  {
    $solicitud = $request->getParsedBody();
    

    $estaVacio = $this->validarCampo(
      $solicitud,
      'estrellas', 
      'El campo de estrellas esta vacio',
      $response
      );
      if($estaVacio != null){
        return $estaVacio;
      }
      
      $estaVacio = $this->validarCampo(
        $args,
        'id', 
        'El campo de id esta vacio.',
        $response
        );
      
      if($estaVacio != null){
        return $estaVacio;
      }

      $id = $args['id'];

    $calificacion = new Calificacion();

    $token = $request->getAttribute('jwt');

    $resultado = $calificacion->editarCalificacion($token->id,$token->a ,$solicitud, $id);     

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

  public function eliminar(Request $request, Response $response, $args)
  {
  
    $estaVacio = $this->validarCampo(
      $args,
      'id', 
      'El campo de id esta vacio.',
      $response
      );
    
    if($estaVacio != null){
      return $estaVacio;
    }

    $token = $request->getAttribute('jwt');

    $id = $args['id'];

    $calificacion = new Calificacion();
    $resultado = $calificacion->eliminarCalificacion($id, $token->id, $token->a);

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
  
}
?>