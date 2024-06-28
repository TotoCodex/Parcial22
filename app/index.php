<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once '../app/class/producto.php';
require_once '../app/DB.php';
require_once '../vendor/autoload.php';
require_once '../app/controllers/ControladorProductos.php';
require_once '../app/middlewares/VerificacionRol.php';
require_once '../app/middlewares/VerificacionSocio.php';
require_once '../app/middlewares/VerificacionManipulador.php';
// Create App instance
$app = AppFactory::create();



//CrearUsuario
$app->post('/tienda/alta', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorProductos();	
	return $controlador-> crearProductos($request,$response,$args);
});

$app->post('/tienda/consultar', function (Request $request, Response $response, array $args) {
	$controlador = new ControladorProductos();	
	return $controlador-> existenciaProductos($request,$response,$args);
});

$app->run();
//php -S localhost:666 -t app
?>
