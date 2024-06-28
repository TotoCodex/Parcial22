<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class verificarRol {

    public function __invoke(Request $request, RequestHandler $handler): Response {
      
      $rolesdeUsuario = $request->getParsedBody()['rol']; 
      
      $rolesPermitidos = ['cocinero', 'cervecero', 'mozo', 'bartender', 'socio'];
      if (!in_array($rolesdeUsuario, $rolesPermitidos)) {
        $response = new MWResponse();
        $response ->getBody()->write(json_encode([
            'error'=> 'Rol no valido, debe ser mozo, cervecero, socio, cocinero o bartender'
        ]));
       return $response->withHeader('Content-Type','application/json')->withStatus(400);
      }
      return $handler->handle($request);
    }
}


?>