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


class Middleware implements MiddlewareInterface
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

    function getJwtMiddleware()
    {
        return $this->jwtMiddleware;
    }
}


?>