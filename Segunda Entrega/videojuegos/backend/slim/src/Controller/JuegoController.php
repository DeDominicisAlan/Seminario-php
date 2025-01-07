<?php

namespace App\Controller;

use App\Models\Juego;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JuegoController extends Controlador
{

  public function crear(Request $request, Response $response)
  {
    $token = $request->getAttribute('jwt');

    //valido que el usuario sea administrador
    if ($token->a) {
      $solicitud = $request->getParsedBody();
      $files = $request->getUploadedFiles(); //Recibo archivos subidos (IMG)

      // Valido que los campos no esten vacios
      $estaVacio = $this->validarCampo(
        $solicitud,
        'nombre',
        'El campo de nombre del juego esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $estaVacio = $this->validarCampo(
        $solicitud,
        'clasificacion_edad',
        'El campo de clasificacion de edad esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $estaVacio = $this->validarCampo(
        $files,
        'imagen',
        'El campo de imagen esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $estaVacio = $this->validarCampo(
        $solicitud,
        'descripcion',
        'El campo de descripcion esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $juegoModel = new Juego();
      $resultado = $juegoModel->crearJuego($solicitud, $files);
    } else {
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

  public function editar(Request $request, Response $response, $args)
  {
    $token = $request->getAttribute('jwt');

    //valido que el usuario sea administrador
    if ($token->a) {
      $solicitud = $request->getParsedBody();
      $files = $request->getUploadedFiles(); //Recibo archivos subidos (IMG)
      $id = $args['id'];

      // Valido que los campos no esten vacios
      $estaVacio = $this->validarCampo(
        $solicitud,
        'nombre',
        'El campo de nombre del juego esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $estaVacio = $this->validarCampo(
        $solicitud,
        'clasificacion_edad',
        'El campo de clasificacion de edad esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $estaVacio = $this->validarCampo(
        $solicitud,
        'descripcion',
        'El campo de descripcion esta vacio',
        $response
      );
      if ($estaVacio != null) {
        return $estaVacio;
      }

      $juegoModel = new Juego();
      $resultado = $juegoModel->editarJuego($solicitud, $id, $files);
    } else {
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

  public function solicitar(Request $request, Response $response, $args)
  {
    $id = $args['id'];

    $juegoModel = new Juego();
    $resultado = $juegoModel->obtenerJuego($id);

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

  public function obtenerJuegos(Request $request, Response $response)
  {

    $juegoModel = new Juego();
    $resultado = $juegoModel->obtenerJuegosCompletos();

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }

  public function eliminar(Request $request, Response $response, $args)
  {

    $token = $request->getAttribute('jwt');

    if ($token->a) {
      $solicitud = $request->getParsedBody();
      $id = $args['id'];
      $juegoModel = new Juego();
      $resultado = $juegoModel->borrarJuego($id);
    } else {
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

  public function listarJuegos(Request $request, Response $response)
  {
    //Obtener parametros de la URL con getQueryParams()
    $parametros = $request->getQueryParams();
    $pagina = $parametros['pagina'] ?? 1;
    $clasificacion = $parametros['clasificacion'] ?? null;
    $texto = $parametros['texto'] ?? null;
    $plataforma = $parametros['plataforma'] ?? null;
    //public function obtenerJuegos($pagina, $clasificacion, $texto, $plataforma)

    $juego = new Juego();
    $resultado = $juego->obtenerJuegos($pagina, $clasificacion, $texto, $plataforma);

    $response->getBody()->write(json_encode($resultado));
    return $response->withStatus($resultado['Codigo']);
  }
}
