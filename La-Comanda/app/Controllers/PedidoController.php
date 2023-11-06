<?php
include_once './Models/Pedido.php';
include_once './Models/Producto.php';
include_once './Models/Mesa.php';
class PedidoController extends Pedido
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id_Empleado = $parametros['id_empleado'];
    $nroDocumento_Cliente = $parametros['nroDocumento_Cliente'];
    $estado = Estado::PENDIENTE;
    $codigo_Mesa = $parametros['codigo_Mesa'];
    // $foto = $parametros['foto'];
    $id_Producto = $parametros['id_Producto'];
    $cantidad = $parametros['cantidad'];


    $pedido = new Pedido();
    $pedido->setIdEmpleado($id_Empleado);
    $pedido->setCodigoPedido();
    $pedido->setNroDocumentoCliente($nroDocumento_Cliente);
    $pedido->setEstado(strtolower($estado));
    $pedido->setCodigoMesa($codigo_Mesa);
    // $pedido->setFoto($foto);  hacerlo un array asi lo devovlemos de esa manera
    $pedido->setIdProducto($id_Producto);
    $pedido->setCantidad($cantidad);
    $pedido->setFacturado(false);

    //calculo del tiempo total estimado
    $producto = Producto::GetTiempoEstimadoById($id_Producto);
    $tiempoEstimadoTotal = Pedido::MultiplicarTiempo($producto->getTiempoEstimado(), floatval($cantidad));
    
    $pedido->setTiempoEstimadoTotal($tiempoEstimadoTotal);

    Pedido::Create($pedido);
    $payload = json_encode(array("mensaje" => "Pedido creado con exito". "Su codigo de pedido es: " . $pedido -> getCodigoPedido() . "Y su codigo de mesa es:" . $codigo_Mesa));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {
    $codigo_pedido = $args['codigoPedido'];
    $codigo_mesa = $args['codigoMesa'];
    $pedido = Pedido::GetByCodigoPedidoyMesa($codigo_pedido, $codigo_mesa);

    if ($pedido === false) {
      $payload = json_encode("Pedido no encontrado.");
    }else {
      if($pedido -> getEstado() == Estado::PREPARACION){
        $minutosRestantes = Pedido::CalcularMinutosPasados($pedido-> getFechaComienzo(), $pedido -> getTiempoEstimadoTotal());

        $payload = json_encode("El tiempo restante para su pedido es: ". $minutosRestantes );

      }else if($pedido -> getEstado() == Estado::PENDIENTE){
        $payload = json_encode("El pedido se encuentra en el estado pendiente, una vez que el estado cambie a EN PREPARACION, podra ver el tiempo estimado restante.");
      }else{
        $payload = json_encode("El pedido no tiene el estado correspondiente para ver el tiempo restante de entrega.");
      }
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::GetAll();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function ModificarUno($request, $response, $args)
  {
    $id = $args['id'];

    $producto = Producto::GetById($id);

    if ($producto != false) {
      $parametros = $request->getParsedBody();
      if (isset($parametros['sector']) && isset($parametros['nombre']) && isset($parametros['precio']) && isset($parametros['tiempoEstimado'])) {

        $sector = $parametros['sector'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
    
        $producto->setSector(strtolower($sector));
        $producto->setNombre($nombre);
        $producto->setPrecio($precio);
        $producto->setTiempoEstimado($tiempoEstimado);

        Producto::Update($producto);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "El producto no se modificar por falta de campos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "ID no coinciden con ningun Producto"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];

    if (Producto::GetById($id)) {
      Producto::Delete($id);
      $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "ID no coincide con ningun Producto"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
