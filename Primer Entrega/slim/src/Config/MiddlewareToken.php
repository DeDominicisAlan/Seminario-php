<?php

namespace App\Config;

use JimTools\JwtAuth\Middleware\JwtAuthentication;
use JimTools\JwtAuth\Decoder\FirebaseDecoder;
use JimTools\JwtAuth\Options;
use JimTools\JwtAuth\Secret;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Exception;
use DateTime;


class MiddlewareToken implements MiddlewareInterface
{
    private $app;
    private $jwtMiddleware;

    // Constructor de la clase, inicializa la aplicación y el middleware JWT
    function __construct($app)
    {
        $this->app = $app;
        $this->jwt();
    }

    // CORS 
    function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $response = new \Slim\Psr7\Response();
        
        //Obtener token de la cabecera
        //Sacamos la parte de Bearer y los espacios adicionales en caso que haya
        $token = trim(str_replace('Bearer ', '', $request->getHeaderLine('Authorization')));
            
        if ($this->tokenVacio($token, $response)) {
            return $response;
        }
        
        $tokenVerificado = $this->verificarToken($token);
        
        if ($this->tokenInvalido($tokenVerificado, $response)) {
            return $response;
        }   
        //Almaceno el token
        $request = $request->withAttribute('jwt', $tokenVerificado);

        //Si el token es valido
        $response = $handler->handle($request);
        
        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json');
    }
    
    // JWT Authentication (tuupola/slim-jwt-auth)
    // Uso JWT Auth (jimmtools/jwt-auth) porque el de tuupola, esta desactualizado/descontinuado
    function jwt()
    {
        $secret = new Secret(Config::auth()['secret'], 'HS256');
        
        $this->jwtMiddleware = new JwtAuthentication(
            new Options(),
            new FirebaseDecoder($secret)
        );
    }

    public function verificarToken($token)
  {
  try{
    $tokenDecoded = JWT::decode($token, new Key(Config::auth()['secret'], 'HS256'));
    
    $fechaActual = new DateTime();
    $fechaExpiracion = new DateTime();
    $fechaExpiracion->setTimestamp($tokenDecoded->exp);
    
      if ($fechaActual < $fechaExpiracion)
        return $tokenDecoded; //token es valido y no expiró
  }catch(Exception $e){
    return false;
    } //token expirado o no es valido
  
    return false; 
  }

    protected function tokenVacio($token, Response $response)
  {
      if (empty($token)) {
          $data = [
              'Status' => 'Fail',
              'Mensaje' => 'El token no se proporcionó.',
              'Codigo' => 401,
          ];
          $response->getBody()->write(json_encode($data));
          return true; // Indica que el token está vacío
      }
      return false; // El token no está vacío
  }

    // Función para verificar si el token es inválido
  protected function tokenInvalido($tokenVerificado, Response $response)
  {
      if (!$tokenVerificado) {
          $data = [
              'Status' => 'Fail',
              'Mensaje' => 'El token es inválido o ha expirado.',
              'Codigo' => 401,
          ];
          $response->getBody()->write(json_encode($data));
          return true; // Indica que el token es inválido
      }
      return false; // El token es válido
  }

    function getJwtMiddleware()
    {
        return $this->jwtMiddleware;
    }
}


?>