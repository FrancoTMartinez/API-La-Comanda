<?php
include_once './Models/Empleado.php';
class EmpleadoController extends Empleado
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $rol = $parametros['rol'];
    $nombre = $parametros['nombre'];

    $empleado = new Empleado();
    $empleado->set_rol(strtoupper($rol));
    $empleado->set_nombre($nombre);
    $empleado->set_baja(false);
    $empleado->fecha_alta = date('Y-m-d H:i:s');

    Empleado::Create($empleado);

    $payload = json_encode(array("mensaje" => "Empleado creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $empleado = Empleado::GetById($id);

    if ($empleado === false) {
      $payload = json_encode("Empleado no encontrado.");
    } else {
      $payload = json_encode($empleado);
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public static function TraerTodos($request, $response, $args)
  {
    $lista = Empleado::GetAll();
    $payload = json_encode(array("listaEmpleados" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public static function ModificarUno($request, $response, $args)
  {
    $id = $args['id'];
    $empleado = Empleado::GetById($id);

    if ($empleado != false) {
      $parametros = $request->getParsedBody();
      if (isset($parametros['rol']) && isset($parametros['nombre'])) {

        $empleado->rol = $parametros['rol'];
        $empleado->nombre = $parametros['nombre'];

        Empleado::Update($empleado);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Producto no modificar por falta de campos"));
      }
    } else {
      $payload = json_encode(array("mensaje" => "ID no coinciden con ningun Empleado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];

    if (Empleado::GetById($id)) {
      Empleado::Delete($id);
      $payload = json_encode(array("mensaje" => "Empleado borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "ID no coincide con un Empleado"));
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
