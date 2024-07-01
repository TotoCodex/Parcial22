<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as MWResponse;


class ConfirmacionPerfil {

    private $perfilesV;

    public function __construct(array $perfilesV) {
      $this->perfilesV = $perfilesV;
    }
    public function __invoke(Request $request, RequestHandler $handler): Response {
      
      $cabecera = $request->getHeaderLine('Authorization'); 
      
      $token = trim(explode("Bearer", $cabecera)[1]);
      
      try{
        AutentificadorJWT::VerificarToken($token);
        $data = AutentificadorJWT::ObtenerData($token);
        
        $perfil = $data->perfil ;
        
        
        if(!in_array($perfil,$this->perfilesV)){
          throw new Exception('Este perfil no tiene Permiso.');
        }

        $response = $handler -> handle($request);
      }catch(Exception $e){
        $response = new MWResponse();
        $payload = json_encode(['mensaje' => $e ->getMessage() ]);
        $response ->getBody()->write($payload);
      return $response->withHeader('Content-Type','application/json')->withStatus(400);
      }
      
      
        
      return $response;
    }
}


?>