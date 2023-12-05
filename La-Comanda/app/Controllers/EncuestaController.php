<?php
include_once './Models/Encuesta.php';
include_once './Models/Pedido.php';

class EncuestaController extends Encuesta
{

  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $codigo_pedido = $parametros["codigo_pedido"];
    $puntuacion_mozo = $parametros["puntuacion_mozo"];
    $puntuacion_comida = $parametros["puntuacion_comida"];
    $comentario = $parametros["comentario"];

    if (
      !empty($comentario) && !empty($codigo_pedido) && $puntuacion_mozo >= 1 && $puntuacion_mozo <= 10 &&
      $puntuacion_comida >= 1 && $puntuacion_comida <= 10
    ) {

    
      if (!Pedido::GetById($codigo_pedido)) {
        $payload = json_encode(array("Mensaje" => "El pedido no existe."));
      } else {
        $encuesta = new Encuesta();
        $encuesta->setCodigoPedido($codigo_pedido);
        $encuesta->setPuntuacionMozo($puntuacion_mozo);
        $encuesta->setPuntuacionComida($puntuacion_comida);
        $encuesta->setComentario($comentario);
        $encuesta->setFecha(date("Y-m-d"));
 
        // var_dump($encuesta);
        Encuesta::Create($encuesta);

        $payload = json_encode(array("Mensaje" => "Encuesta cargada con exito!"));
      }
    } else {
      $payload = json_encode(array("Error" => "Revise los datos! (Puntaciones 1-10)"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader("Content-type", "application/json");
  }

  public static function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::GetAll();

    empty($lista) ? $payload = json_encode("No hay encuestas cargadas.") : $payload = json_encode($lista);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function TraerPorPuntuacion($request, $response, $args)
  {
    $puntuacion = $args['puntuacion'];
    $lista = Encuesta::GetEncuestasByPuntacion(intval($puntuacion));

    empty($lista) ? $payload = json_encode("No hay encuestas con la puntuacion de " . $puntuacion . " puntos.") : $payload = json_encode($lista);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function TraerPorCodigoPedido($request, $response, $args)
  {
    $codigo_pedido = $args['codigo_pedido'];

    if (!Pedido::GetById($codigo_pedido)) {
      $payload = json_encode(array("Mensaje" => "El pedido no existe."));
    } else {
      $lista = Encuesta::GetEncuestasByCodigoPedido($codigo_pedido);
      empty($lista) ? $payload = json_encode("El pedido no tiene encuesta.") : $payload = json_encode($lista);
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
