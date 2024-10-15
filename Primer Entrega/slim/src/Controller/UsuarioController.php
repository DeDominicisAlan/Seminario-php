<?php

namespace App\Controller; 

use App\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController extends Controlador
{
  /*
  ● POST /usuario: Crear un nuevo usuario.
  ● PUT /usuario/{id}: Editar un usuario existente.
  ● DELETE /usuario/{id}: Eliminar un usuario.
  ● GET /usuario/{id}: Obtener información de un usuario específico.
    ○ En los últimos 3 casos (donde se recibe el id) se debe validar que el
    usuario se haya logueado
    
    Entonces el POST /usuario puede hacerse sin necesidad de estar logueado
  */
  
  
  public function login(Request $request, Response $response){
    $solicitud = $request->getParsedBody();
    
    $estaVacio = $this->validarCampo($solicitud,'nombre_usuario', 
      'El campo de nombre esta vacio',$response);
      if($estaVacio != null){
        return $estaVacio;
    }
    
    $estaVacio = $this->validarCampo($solicitud,'clave', 
    'El campo de contraseña esta vacio',$response);
    if($estaVacio != null){
      return $estaVacio;
  }
    
    $usuario = new Usuario();
    $resultado = $usuario->Login($solicitud);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
  
  public function registro(Request $request, Response $response){
    $solicitud = $request->getParsedBody();
    
    $estaVacio = $this->validarCampo($solicitud,'nombre_usuario', 
      'El campo de nombre esta vacio',$response);
      if($estaVacio != null){
        return $estaVacio;
    }
    
    $estaVacio = $this->validarCampo($solicitud,'clave', 
    'El campo de contraseña esta vacio',$response);
    if($estaVacio != null){
      return $estaVacio;
    }
    
    $usuario = new Usuario();
    $resultado = $usuario->crearUsuario($solicitud);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
  
  public function crear(Request $request, Response $response){
    $solicitud = $request->getParsedBody();
    
    $estaVacio = $this->validarCampo($solicitud,'nombre_usuario', 
      'El campo de nombre esta vacio',$response);
      if($estaVacio != null){
        return $estaVacio;
    }
    
    $estaVacio = $this->validarCampo($solicitud,'clave', 
    'El campo de contraseña esta vacio',$response);
    if($estaVacio != null){
      return $estaVacio;
    }
    
    $usuario = new Usuario();
    $resultado = $usuario->crearUsuario($solicitud);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
  
  public function editar(Request $request, Response $response, $args){
    $id = $args['id'];
    $solicitud = $request->getParsedBody();
    
    $token = $request->getAttribute('jwt');
    
    $estaVacio = $this->validarCampo($solicitud,'nombre_usuario', 
      'El campo de nombre esta vacio',$response);
      if($estaVacio != null){
        return $estaVacio;
    }
    
    $estaVacio = $this->validarCampo($solicitud,'clave', 
    'El campo de contraseña esta vacio',$response);
    if($estaVacio != null){
      return $estaVacio;
    }
    
    $usuario = new Usuario();
    $resultado = $usuario->editarUsuario($solicitud, $id, $token->id, $token->a);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
  
  public function eliminar(Request $request,Response $response, $args){
    $id = $args['id'];
    $token = $request->getAttribute('jwt');

    $usuario = new Usuario();
    $resultado = $usuario->eliminarUsuario($id, $token->id, $token->a);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

  public function obtener(Request $request, Response $response, $args){
  
    $id = $args['id'];
    
    $usuario = new Usuario();
    $resultado = $usuario->obtenerUsuario($id);
    
    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  
  }
  
  

}

  

?>