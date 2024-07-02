<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require_once '../app/class/venta.php';
require_once '../app/class/usuario.php';
require_once '../app/DB.php';
require_once '../vendor/autoload.php';
require_once '../app/controllers/ControladorVenta.php';
require_once '../app/controllers/ControladorTienda.php';
require_once '../app/controllers/ControladorUsuario.php';
require_once '../app/middlewares/ConfirmacionPerfil.php';
require_once '../app/middlewares/VerificarUsuario.php';
require_once '../app/middlewares/VerificarTipo.php';
require_once '../app/middlewares/VerificarValores.php';


// Create App instance
$app = AppFactory::create();


$app->group('/tienda',function(RouteCollectorProxy $group){
	$group->post('/alta', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorTienda();	
		return $controlador-> crearProductos($request,$response,$args);
	})->add(new VerificarTipo());
	
	$group->put('/ventas/modificar', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> ventasModificar($request,$response,$args);
	});
	$group->get('/consultar/ventas/ingresos', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> ventasIngresos($request,$response,$args);
	});
	$group->get('/ventas/descargar', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> descargarVentasCSV($request,$response,$args);
	});
})->add(new ConfirmacionPerfil(['admin']));


$app->group('/tienda',function(RouteCollectorProxy $group){
	
	$group->get('/consultar/ventas/porUsuario', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> ventasPorUsuario($request,$response,$args);
	})->add(new VerificarUsuario());
	$group->get('/consultar/producto/masVendido', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> productoMasVendido($request,$response,$args);
	});
	$group->get('/consultar/ventas/porProducto', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> ventasPorProducto($request,$response,$args);
	})->add(new VerificarTipo());
	$group->get('/consultar/productos/vendidos', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> productosVendidos($request,$response,$args);
	});
	$group->get('/consultar/productos/entreValores', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> productosEntreValores($request,$response,$args);
	})->add(new VerificarValores());
	$group->post('/consultar', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorTienda();	
		return $controlador-> existenciaProductos($request,$response,$args);
	});
	
})->add(new ConfirmacionPerfil(['admin','empleado']));


$app->group('/ventas',function(RouteCollectorProxy $group){
	$group->post('/alta', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> crearVenta($request,$response,$args);
	});
	
})->add(new ConfirmacionPerfil(['admin','empleado']));



$app->post('/registro', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorUsuario();	
	return $controlador->registroUsuario($request,$response,$args);
});

$app->post('/login', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorUsuario();	
	return $controlador->loginUsuario($request,$response,$args);
});

$app->get('/ventas/pdf', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorVenta();	
	return $controlador->descargarVentasPDF($request,$response,$args);
})->add(new ConfirmacionPerfil(['admin']));

$app->group('/recuperatorio/consultas',function(RouteCollectorProxy $group){
	$group->get('/productos/porStock', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorTienda();	
		return $controlador-> traerproductoPorStock($request,$response,$args);
	});
	$group->get('/productos/porPrecio', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorTienda();	
		return $controlador-> traerproductoPorPrecio($request,$response,$args);
	});
	$group->get('/productos/menosVendido', function (Request $request, Response $response, array $args) {
		$controlador = new ControladorVenta();	
		return $controlador-> productoMenosVendido($request,$response,$args);
	});
	
})->add(new ConfirmacionPerfil(['admin','empleado']));

$app->get('/usuarios/pdf', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorUsuario();	
	return $controlador->descargarUsuariosPDF($request,$response,$args);
})->add(new ConfirmacionPerfil(['admin']));

$app->addBodyParsingMiddleware();

$app->run();
//php -S localhost:666 -t app
?>
