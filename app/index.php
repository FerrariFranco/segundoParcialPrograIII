<?php

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './controllers/ProductoController.php';
require_once './controllers/VentaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/tienda', function (RouteCollectorProxy $group) {
    $group->post('/alta', ProductoController::class . ':CargarUno');
    $group->post('/consultar', \ProductoController::class . ':TraerUno');
    $group->get('/todos', \ProductoController::class . ':TraerTodos');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->post('/alta', \VentaController::class . ':CargarUno');
    $group->get('/consultar/email/{email}', \VentaController::class . ':ConsultarVentasPorUsuario');
    $group->get('/consultar/nombre/{nombre}/{tipo}', \VentaController::class . ':ConsultarVentasPorProducto');
    $group->get('/consultar/fecha/{fecha}', \VentaController::class . ':ConsultarVentasPorFecha');
    $group->get('/ingresos', \VentaController::class . ':ConsultarIngresosPorDia');
    $group->get('/masVendido', \VentaController::class . ':ConsultarProductoMasVendido');
    $group->put('/modificar', \VentaController::class . ':ModificarVenta');
});

$app->run();
