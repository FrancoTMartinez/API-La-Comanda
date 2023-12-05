<?php
include_once './Models/Producto.php';
class ProductoController extends Producto
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $sector = $parametros['sector'];
    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $tiempoEstimado = $parametros['tiempoEstimado'];

    $producto = new Producto();
    $producto->setSector($sector);
    $producto->setNombre($nombre);
    $producto->setPrecio($precio);
    $producto->setTiempoEstimado($tiempoEstimado);

    Producto::Create($producto);

    $payload = json_encode(array("mensaje" => "Producto creadO con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $producto = Producto::GetById($id);

    if ($producto === false) {
      $payload = json_encode("Producto no encontrado.");
    } else {
      $payload = json_encode($producto);
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Producto::GetAll();
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
    
        $producto->setSector($sector);
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

  public static function Imprimir($request, $response, $args)
  {
      Producto::ImprimirPDF();


      $payload = json_encode(array("mensaje" => ""));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
