<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class VerificarUsuario {

    public function __invoke(Request $request, RequestHandler $handler): Response {
  
        
        $usuario = $request->getQueryParams()['usuario'];
        
        
        
        if ($usuario == NULL) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode([
                'error' => 'Ingrese el usuario por favor'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $handler->handle($request);
    }
    
}



?>