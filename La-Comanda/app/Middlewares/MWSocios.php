<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//VALIDA EL TOKEN
class MWSocios{
    public function __invoke(Request $request,RequestHandler $handler)
    {
        $header = $request->getHeaderLine(("Authorization"));//-->Donde estara el token
        $response = new Response();
        try
        {   
            if ($header === "") {
                $response->getBody()->write(json_encode(array('Error' => "Token Invalido.")));
                    
            }else{
                
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT :: ObtenerData($token);

                if($data->rol == "socio"){
                    $response= $handler->handle($request);
                }else{
                    $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los socios")));
                }
            }
        }
        catch(Exception $ex){
            $response->getBody()->write(json_encode(array("Error" => $ex->getMessage())));
        }finally{
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}