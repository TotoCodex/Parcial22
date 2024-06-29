<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once '../app/class/venta.php';
require_once '../app/DB.php';
require_once '../vendor/autoload.php';
require_once '../app/controllers/ControladorVenta.php';
require_once '../app/controllers/ControladorTienda.php';
require_once '../app/middlewares/VerificacionRol.php';
require_once '../app/middlewares/VerificacionSocio.php';
require_once '../app/middlewares/VerificacionManipulador.php';
// Create App instance
$app = AppFactory::create();



//CrearUsuario
$app->post('/tienda/alta', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorTienda();	
	return $controlador-> crearProductos($request,$response,$args);
});

$app->post('/tienda/consultar', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorTienda();	
	return $controlador-> existenciaProductos($request,$response,$args);
});

$app->post('/ventas/alta', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> crearVenta($request,$response,$args);
});

$app->get('/productos/vendidos', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> productosVendidos($request,$response,$args);
});

$app->get('/ventas/porUsuario', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> ventasPorUsuario($request,$response,$args);
});

$app->get('/ventas/porProducto', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> ventasPorProducto($request,$response,$args);
});

$app->get('/productos/entreValores', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> productosEntreValores($request,$response,$args);
});

$app->get('/ventas/ingresos', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> ventasIngresos($request,$response,$args);
});

$app->get('/producto/masVendido', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador-> productoMasVendido($request,$response,$args);
});

$app->run();
//php -S localhost:666 -t app
?>
