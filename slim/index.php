<?php

use Slim\Factory\AppFactory;

use App\Controller\JuegoController;
use App\Controller\UsuarioController;
use App\Controller\CalificacionController;
use App\Config\Config;
use App\Config\Middleware;
use App\Config\MiddlewareToken;

//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;
require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$container = $app->getContainer();
$container['jwt'] = Config::jwt();

$middleware = new Middleware($app); // Instancia de tu clase Middleware
$app->add($middleware); //Agregar middleware a la app

$middlewareToken = new MiddlewareToken($app);

/*$app->add( function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, PATCH, DELETE')
        ->withHeader('Content-Type', 'application/json')
    ;
});*/


// ACÁ VAN LOS ENDPOINTS

//Rutas que necesitan Token

$app->group('', function () use ($app) {
    // <----- Usuario ------>

    $app->post('/usuario', UsuarioController::class . ':crear');
    $app->put('/usuario/{id}', UsuarioController::class . ':editar');
    $app->delete('/usuario/{id}', UsuarioController::class . ':eliminar');
    $app->get('/usuario/{id}', UsuarioController::class . ':obtener');

    // <----- Juego ------>

    $app->post('/juego', JuegoController::class . ':crear');
    $app->put('/juego/{id}', JuegoController::class . ':editar');
    
    $app->delete('/juego/{id}', JuegoController::class . ':eliminar');
    

    // <----- Calificacion ------>

    $app->post('/calificacion', CalificacionController::class . ':crear');
    $app->delete('/calificacion/{id}', CalificacionController::class . ':eliminar');
    $app->put('/calificacion/{id}', CalificacionController::class . ':editar');
})->add($middlewareToken);

// <----- Juego ----->

$app->get('/juegos/{id}', JuegoController::class . ':solicitar');
$app->get('/juegos', JuegoController::class . ':listarJuegos');

// <----- Login ------>

$app->post('/login', UsuarioController::class . ':login');

// <----- Registro ---->

$app->post('/register', UsuarioController::class . ':registro');

$app->run();

?>