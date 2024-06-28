<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;
require_once '../app/DB.php';

class verificarManipulador {

    public function __invoke(Request $request, RequestHandler $handler): Response {
      
        $usuarioManipulador = $request->getParsedBody()['usuario_manipulador'];
        $id_producto = $request->getParsedBody()['id_producto'];

        $conn = DB::Connect();
    
        $select = "SELECT producto_categoria FROM productos WHERE producto_id = :id_producto";
        $stmt = $conn->prepare($select);
        
        try {
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_STR);
            $stmt->execute();
            $categoriaProducto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoriaProducto !== false) {
                $nombrecategoriaProducto = $categoriaProducto['producto_categoria'];
                $valor=self::selectManipulador($usuarioManipulador, $nombrecategoriaProducto);// el return tiene que ir si o si 
                if ($valor != "ok"){
                  $response = new MWResponse();
                  $response->getBody()->write(json_encode(['error' => $valor]));
                  return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
                return $handler->handle($request);
            } else {
                $response = new MWResponse();
                $response->getBody()->write(json_encode(['error' => 'Producto no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch (Exception $e) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode([
                'error' => 'Error al traer producto: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
    }

    public static function selectManipulador($usuarioManipulador, $nombrecategoriaProducto) {
        $conn = DB::Connect();
    
        $select = "SELECT usuario_rol FROM usuarios WHERE usuario_nombre = :usuario_manipulador";
        $stmt = $conn->prepare($select);
        
        try {
            $stmt->bindParam(':usuario_manipulador', $usuarioManipulador, PDO::PARAM_STR);
            $stmt->execute();
            $usuarioRol = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuarioRol !== false) {
                $usuarioRolVerificado = $usuarioRol['usuario_rol'];
                var_dump($usuarioRolVerificado);
                var_dump($nombrecategoriaProducto);
                
                if ($usuarioRolVerificado == 'cocinero' && $nombrecategoriaProducto == 'Comida') {
                  $resultado="ok";
                  return $resultado;
                } 
                
                if ($usuarioRolVerificado == 'bartender' && $nombrecategoriaProducto == 'Trago') {
                  $resultado="ok";
                  return $resultado;
                }
                if ($usuarioRolVerificado == 'cervecero' && $nombrecategoriaProducto == 'Cerveza') {
                  $resultado="ok";
                  return $resultado;
                }
                if ($usuarioRolVerificado == 'cocinero' && $nombrecategoriaProducto == 'Postre') {
                  $resultado="ok";
                  return $resultado;
                    
                }
                if ($usuarioRolVerificado == 'mozo') {
                  $resultado="ok";
                  return $resultado;
                    
                }
                else {
                    // combinacion invalida
                    $resultado="Un ".$usuarioRolVerificado. " no puede manipular ".$nombrecategoriaProducto;
                    return $resultado;
                }
                
            } 
        } catch (Exception $e) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode(['error' => 'Error al traer usuario: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
    }

}
?>
