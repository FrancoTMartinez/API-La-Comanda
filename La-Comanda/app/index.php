<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Cookie\Cookie;


require __DIR__ . '/../vendor/autoload.php';

require_once './db/DataAccess.php';

require_once './Middlewares/AutentificadorJWT.php';
require_once './Middlewares/MWToken.php';
require_once './Middlewares/MWSocios.php';

require_once './controllers/EmpleadoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/FacturaController.php';
require_once './controllers/EncuestaController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Carga el archivo .env con la configuracion de la BD.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/Comanda');
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Routes
$app->group('/empleado', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EmpleadoController::class . '::CargarUno');
  $group->put('/{id}', \EmpleadoController::class . '::ModificarUno');
  $group->delete('/{id}', \EmpleadoController::class . '::BorrarUno');
  $group->get('[/]', \EmpleadoController::class . '::TraerTodos');
  $group->get('/{id}', \EmpleadoController::class . '::TraerUno');
  $group->post('/importar', \EmpleadoController::class . '::Importar');
  $group->get('/exportar/empleados', \EmpleadoController::class . '::Exportar');
})->add(new MWToken()) ->add(new MWSocios);

$app->group('/mesa', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . '::CargarUno')-> add(new MWSocios());
  $group->put('/{codigo_mesa}', \MesaController::class . '::ModificarUno');
  $group->delete('/{codigo_mesa}', \MesaController::class . '::BorrarUno');
  $group->get('/usos', \MesaController::class . '::TraerUsosDeMesa')->add(new MWSocios);
  $group->get('/{codigo_mesa}', \MesaController::class . '::TraerUno');
  $group->get('[/]', \MesaController::class . '::TraerTodos');
})->add(new MWToken());

$app->group('/producto', function (RouteCollectorProxy $group) {
  $group->post('[/]', \ProductoController::class . '::CargarUno');
  $group->get('/imprimir', \ProductoController::class . '::Imprimir');
  $group->put('/{id}', \ProductoController::class . '::ModificarUno');
  $group->delete('/{id}', \ProductoController::class . '::BorrarUno');
  $group->get('[/]', \ProductoController::class . '::TraerTodos');
  $group->get('/{id}', \ProductoController::class . '::TraerUno');
})->add(new MWToken()) ->add(new MWSocios);

$app->group('/pedido', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController::class . '::CargarUno');
  $group->put('/{codigo_pedido}', \PedidoController::class . '::ModificarUno');
  $group->delete('/{codigo_pedido}', \PedidoController::class . '::BorrarUno')->add(new MWSocios);
  $group->get('[/]', \PedidoController::class . '::TraerTodos')->add(new MWSocios);
  $group->get('/cliente/{codigoPedido}/{codigoMesa}', \PedidoController::class . '::TraerUno');
  $group->get('/codigo/{codigoPedido}', \PedidoController::class . '::TraerUnoCodigoPedido')->add(new MWSocios);
  $group->get('/pendientes', \PedidoController::class . '::TraerPedidosPendientes');
  $group->get('/listos', \PedidoController::class . '::TraerPedidosPorEstado');
})->add(new MWToken());

$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EmpleadoController::class . '::LogIn');
});

$app->group('/factura', function (RouteCollectorProxy $group) {
  $group->post('[/]', \FacturaController::class . '::CargarUno');
  $group->get('[/]', \FacturaController::class . '::TraerTodos');
  $group->get('/{codigo}/{tipoCodigo}', \FacturaController::class . '::TraerUno');
});

$app->group('/encuesta', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EncuestaController::class . '::CargarUno');
  $group->get('[/]', \EncuestaController::class . '::TraerTodos');
  $group->get('/pedido/{codigo_pedido}', \EncuestaController::class . '::TraerPorCodigoPedido');
  $group->get('/{puntuacion}', \EncuestaController::class . '::TraerPorPuntuacion');
});

$app->run();