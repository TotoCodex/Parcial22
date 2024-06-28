<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class verificarSocio {

    public function __invoke(Request $request, RequestHandler $handler): Response {
  
        $bodyParams = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        
        $rol_identificador = null;
        
        if (isset($bodyParams['rol_identificador'])) {
            $rol_identificador = $bodyParams['rol_identificador'];
        } elseif (isset($queryParams['rol_identificador'])) {
            $rol_identificador = $queryParams['rol_identificador'];
        }
        $rolesPermitidos = ['socio1', 'socio2', 'socio3'];
        
        if (!in_array($rol_identificador, $rolesPermitidos)) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode([
                'error' => 'Rol no valido, solo socio 1, 2 o 3 puede agregar Usuario'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $handler->handle($request);
    }
    
}



?>