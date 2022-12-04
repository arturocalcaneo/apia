<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

include_once __DIR__ . '/../src/Models/db.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));

$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->options('/{routes:.+}', function ($request, $response, $args) {
   return $response;
});

$app->add(function ($request, $handler) {
   $response = $handler->handle($request);
   return $response
           ->withHeader('Access-Control-Allow-Origin', '*')
           ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
           ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Ruta de Inicio
$app->get('/', function (Request $request, Response $response) {
   $response->getBody()->write('Hello World from an Restful API!');
   return $response;
});

// Restful de Usuarios
include_once __DIR__ . '/../src/Models/usuarios.routes.php';

// Restful de Habitaciones
include_once __DIR__ . '/../src/Models/habitaciones.routes.php';

// Restful de Reservaciones

$app->run();

?>