<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class VerificarTipo {

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $queryParams = $request->getQueryParams();
        $parsedBody = $request->getParsedBody();
        
        if($queryParams != NULL){
            $tipo = $request->getQueryParams()['tipo'];
        }
        else{
            $tipo = $request->getParsedBody()['tipo'];
        }

        
        
        
        $tiposPermitidos = ['Impresora','Cartucho'];
        
        if (!in_array($tipo, $tiposPermitidos)) {
            $response = new MWResponse();
            $response->getBody()->write(json_encode([
                'error' => 'Tipo no permitido, solo impresora o cartucho son validos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $handler->handle($request);
    }
    
}



?>