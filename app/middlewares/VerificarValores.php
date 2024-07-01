<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class VerificarValores {

    public function __invoke(Request $request, RequestHandler $handler): Response {
  
        
        $precio_minimo = $request->getQueryParams()['precio_minimo'];
        $precio_maximo = $request->getQueryParams()['precio_maximo'];

        if ($precio_minimo == NULL || $precio_maximo == NULL) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode([
                'error' => 'Hay 1 o mas campos incompletos, por favor completelos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $handler->handle($request);
    }
    
}



?>