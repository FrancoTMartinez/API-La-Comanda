<?php
include_once './Models/Mesa.php';
class MesaController extends Mesa
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $estado = $parametros['estado'];

    $mesa = new Mesa();
    $mesa->set_estado(strtolower($estado));
    $mesa->set_codigo_mesa();

    Mesa::Create($mesa);

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {
    $codigoMesa = $args['codigo_mesa'];
    $mesa = Mesa::GetById($codigoMesa);

    if ($mesa === false) {
      $payload = json_encode("Mesa no encontrada.");
    } else {
      $payload = json_encode($mesa);
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::GetAll();
    $payload = json_encode(array("listaMesas" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function ModificarUno($request, $response, $args)
  {
      $codigoMesa = $args['codigo_mesa'];
      
      $mesa = Mesa::GetById($codigoMesa);
  
      if ($mesa == false) {
          $payload = json_encode(array("mensaje" => "ID no coincide con ninguna Mesa"));
      } else {
          $parametros = $request->getParsedBody();
  
          if (isset($parametros['estado'])) {
              $estado = $parametros['estado'];
              $header = $request->getHeaderLine("Authorization");
              $token = trim(explode("Bearer", $header)[1]);
              $data = AutentificadorJWT::ObtenerData($token);
  
              // Verificar roles y estado permitido
              if (($data->rol == "mozo" && strtolower($estado) !== Estado::CERRADA) ||
                  ($data->rol == "socio" && strtolower($estado) === Estado::CERRADA)) {

                  $mesa->set_estado(strtolower($estado));
                  Mesa::Update($mesa);
  
                  $payload = json_encode(array("mensaje" => "Estado de la mesa modificado con éxito"));
              } else {
                  $payload = json_encode(array("mensaje" => "Acceso no autorizado para modificar el estado de la mesa"));
              }
          } else {
              $payload = json_encode(array("mensaje" => "Falta el campo 'estado' para modificar el estado de la mesa"));
          }
      }
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }
  

  public static function BorrarUno($request, $response, $args)
  {
    $codigoMesa = $args['codigo_mesa'];

    if (Mesa::GetById($codigoMesa)) {
      Mesa::Delete($codigoMesa);
      $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "ID no coincide con ninguna Mesa"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUsosDeMesa($request, $response, $args)
  {
      $payload = json_encode(Mesa::GetUsosMesas());

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
