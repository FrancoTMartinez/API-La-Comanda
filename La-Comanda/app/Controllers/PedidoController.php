<?php
include_once './Models/Pedido.php';
include_once './Models/Producto.php';
include_once './Models/Mesa.php';
include_once './Models/Empleado.php';
include_once './Models/PedidoProducto.php';
include_once './Models/PedidoProducto.php';

class PedidoController extends Pedido
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nroDocumento_Cliente = $parametros['nroDocumento_Cliente'];
    $estado = Estado::PENDIENTE;
    $codigo_Mesa = $parametros['codigo_Mesa'];
    // $foto = $parametros['foto'];
    $arrProductos = $parametros['productos'];

    $productos = [];
    $tiempo_estimado_total = 0;
    $precio_total = 0;

    if ($arrProductos !== null) {

      foreach ($arrProductos as $productoData) {
        $productoFromBase = Producto::GetById($productoData['id']);

        if ($productoFromBase != null) {

          if ($productoFromBase->getTiempoEstimado() > $tiempo_estimado_total) {
            $tiempo_estimado_total = $productoFromBase->getTiempoEstimado();
          }

          $precio_total += $productoFromBase->getPrecio();
          $productos[] = $productoFromBase;
        } else {

          $payload = json_encode(array("mensaje" => "El producto solicitado no existe"));
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }
      }
    }

    $pedido = new Pedido();
    $pedido->setCodigoPedido();
    $pedido->setNroDocumentoCliente($nroDocumento_Cliente);
    $pedido->setEstado(strtolower($estado));
    $pedido->setCodigoMesa($codigo_Mesa);
    // $pedido->setFoto($foto);  hacerlo un array asi lo devovlemos de esa manera

    //el tiempo estimado total tiene que ser la fecha hora comienzo + los minutos que tarde el pedido
    $pedido->setTiempoEstimadoTotal($tiempo_estimado_total);
    $pedido->setPrecioTotal($precio_total);
    $pedido->setFacturado(false);

    Pedido::Create($pedido);

    //insert en la tabla intermedia pedidos_productos
    foreach ($productos as $producto) {
      $pedido_producto = new PedidoProducto();
      $pedido_producto->setCodigoPedido($pedido->getCodigoPedido());
      $pedido_producto->setIdProducto($producto->getId());
      $pedido_producto->setProductoEstado(Estado::PENDIENTE);

      PedidoProducto::Create($pedido_producto);
    }

    //cambio de estado de la mesa
    $mesa = Mesa::GetById($codigo_Mesa);
    $mesa->set_estado(Estado::ESPERANDO);
    Mesa::Update($mesa);

    $payload = json_encode(array("mensaje" => "Pedido creado con exito. " . "Su codigo de pedido es: " . $pedido->getCodigoPedido() . "Y su codigo de mesa es:" . $codigo_Mesa));
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
    } else {
      if ($pedido->getEstado() == Estado::PREPARACION) {

        $minutosRestantes = Pedido::CalcularMinutosPasados(substr($pedido->getFechaComienzo(), 11, 8), $pedido->getTiempoEstimadoTotal());

        if ($minutosRestantes > 0) {
          $payload = json_encode("El tiempo restante para su pedido es de " . $minutosRestantes . " minutos.");
        } else {
          $payload = json_encode("Su pedido ya deberia estar listo.");
        }
      } else if ($pedido->getEstado() == Estado::PENDIENTE) {
        $payload = json_encode("El pedido se encuentra en el estado pendiente, una vez que el estado cambie a EN PREPARACION, podra ver el tiempo estimado restante.");
      } else if ($pedido->getEstado() == Estado::ENTREGADO) {
        $payload = json_encode("El pedido se encuentra entregado.");
      }else {
        $payload = json_encode("El pedido no tiene el estado correspondiente para ver el tiempo restante de entrega.");
      }
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUnoCodigoPedido($request, $response, $args)
  {
    $codigo_pedido = $args['codigoPedido'];

    $pedido = Pedido::GetById($codigo_pedido);

    if ($pedido === false) {
      $payload = json_encode("Pedido no encontrado.");
    } else {
      $minutosRestantes = Pedido::CalcularMinutosPasados(substr($pedido->getFechaComienzo(), 11, 8), $pedido->getTiempoEstimadoTotal());
      $arrProductos = Pedido::GetProductosByCodigoPedido($codigo_pedido);
      if ($minutosRestantes > 0) {
        $payload = json_encode(array('pedido' => $pedido,'productos' => $arrProductos,'tiempo' => "El tiempo restante para su pedido es de " . $minutosRestantes . " minutos."));
      } else {
        $payload = json_encode(array('pedido' => $pedido, 'productos' => $arrProductos));
      }
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::GetAll();
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo_pedido = $args['codigo_pedido'];
    $id_producto = $parametros['id_producto'];
    // $id_empleado = $parametros['id_empleado'];
    $estado = $parametros['estado'];

    $header = $request->getHeaderLine(("Authorization"));
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);

    $pedido = Pedido::GetById($codigo_pedido);
    $empleado = Empleado::GetById($data->id);
    if ($pedido != false) {

      $pedidoProducto = PedidoProducto::GetByCodigoPedidoIdProducto($codigo_pedido, $id_producto);

      if (!$pedidoProducto) {
        $payload = json_encode(array("mensaje" => "No se encontro la combinacion de pedido producto que se envio. Verifque que este bien el codigo del producto y el id del producto."));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
      //valido que el que va a tomar el pedido no este dado de baja y sea de ese sector
      if (!$empleado->get_baja()) {
        $producto = Producto::GetById($id_producto);

        if (!$producto->ValidarSectorRol($empleado->get_rol())) {
          $payload = json_encode(array("mensaje" => "El empleado que intenta tomar esete pedido es de un sector distinto al producto. El sector del empleado es: " . $empleado->get_rol() . " y el sector del producto es: " . $producto->getSector()));
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }
      } else {
        $payload = json_encode(array("mensaje" => "El empleado que intenta tomar el pedido esta dado de baja. En la fecha: " . $empleado->get_fecha_baja()));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }

      //solo el mozo puede modificar el estado de la mesa
      if ($estado == Estado::ENTREGADO && $empleado->get_rol() != "mozo") {
        $payload = json_encode(array("mensaje" => "El estado del pedido Entregado solo puede ser cambiado por el mozo."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
      $pedidoProducto->setIdEmpleado($data->id);
      $pedidoProducto->setProductoEstado($estado);

      PedidoProducto::Update($pedidoProducto);

      $pedido->setEstado($estado);
      Pedido::UpdateEstado($pedido);

      $mesa = Mesa::GetById($pedido->getCodigoMesa());
      $mesa->set_estado(ESTADO::COMIENDO);
      MESA::Update($mesa);

      $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Codigo del pedido no coincide con ningun Pedido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function BorrarUno($request, $response, $args)
  {
    $codigo_pedido = $args['codigo_pedido'];
    $pedido = Pedido::GetById($codigo_pedido);

    if ($pedido) {
      Pedido::Delete($codigo_pedido);

      $mesa = Mesa::GetById($pedido->getCodigoMesa());
      $mesa->set_estado(ESTADO::CERRADA);
      Mesa::Update($mesa);
      $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "ID no coincide con ningun Pedido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerPedidosPendientes($request, $response, $args)
  {
    $header = $request->getHeaderLine(("Authorization"));
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);

    if ($data->rol == 'socio') {
      $lista = Pedido::GetAll();
      $payload = json_encode(array("listaPedidos" => $lista));
    } else if ($data->rol == 'mozo') {
      $payload = json_encode("El mozo no puede ver los pedidos pendientes.");
    } else {
      $lista = Pedido::GetAllPendientesByRol($data->rol, $data->id);
      $payload = json_encode(array("listaPedidosPendientes" => $lista));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function TraerPedidosPorEstado($request, $response, $args)
  {
    $header = $request->getHeaderLine(("Authorization"));
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);

    if ($data->rol == "mozo") {
      $lista = Pedido::GetAllByEstado(Estado::LISTO);
      $payload = json_encode(array($lista));
    } else {
      $payload = json_encode("Accion reservada solo para mozo.");
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
