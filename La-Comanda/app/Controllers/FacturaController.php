<?php
include_once './Models/Factura.php';
include_once './Models/Mesa.php';
include_once './Models/Pedido.php';
class FacturaController extends Factura
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo_pedido = $parametros['codigo_pedido'];
    $codigo_mesa = $parametros['codigo_mesa'];

    $pedido = Pedido::GetById($codigo_pedido);
    $mesa = Mesa::GetById($codigo_mesa);

    if (!$pedido || !$mesa ){
      $payload = json_encode(array("mensaje" => "El codigo de mesa o el codigo del pedido no existe. Valide."));
    }else{
      $fecha = date("Y-m-d H:i:s");
      $total =  $pedido -> getPrecioTotal();
  
      $factura = new Factura();
      $factura->setCodigoMesa($codigo_mesa);
      $factura->setCodigoPedido($codigo_pedido);
      $factura->setFecha($fecha);
      $factura->setTotal($total);
  
      Factura::Create($factura);

      $pedido-> setFacturado(1);
      Pedido::UpdateFacturado($pedido);

      $payload = json_encode(array("mensaje" => "Factura creada con exito para el pedido: " . $codigo_pedido . " de la mesa: ". $codigo_mesa ));
    }
    

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {
    //Codigo puede representar el codigo de mesa, para traer todas las facturas de una mesa, o un codigo de pedido para traer una factura de ese pedido
    $codigo = $args['codigo'] ?? null;
    $tipoCodigo = $args['tipoCodigo'] ?? NULL;

    if ($codigo == null || $tipoCodigo == null){
      $payload = json_encode("Complete los campos obligatorios.");
    }else{
      $factura = Factura::GetByCodigo($codigo, strtoupper($tipoCodigo));

      if ($factura === false ||empty($factura)) {
        $payload = json_encode("Factura no encontrada.");
      } else {
        $payload = json_encode($factura);
      }
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Factura::GetAll();
    $payload = json_encode(array("listaFacturas" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
