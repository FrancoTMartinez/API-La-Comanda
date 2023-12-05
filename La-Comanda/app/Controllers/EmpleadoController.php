<?php
include_once './Models/Empleado.php';
class EmpleadoController extends Empleado
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $rol = $parametros['rol'];
    $nombre = $parametros['nombre'];
    $clave = $parametros['clave'];

    $empleado = new Empleado();
    $empleado->set_rol(strtoupper($rol));
    $empleado->set_nombre($nombre);
    $empleado->set_clave($clave);
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

  public static function LogIn($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $nombre = $parametros['nombre'];
    $clave = $parametros['clave'];

    $empleado = Empleado::GetByNombreClave($nombre, $clave);
    if (!$empleado) {
      $payload = json_encode(array("mensaje" => "Usuario o password incorrectos"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    $data = array('empleado' => $empleado->get_nombre(), 'rol' => $empleado->get_rol(), 'id' => $empleado->get_id());
    $creacionToken = AutentificadorJWT::CrearToken($data);

    $response = $response->withHeader('Set-Cookie', 'token=' . $creacionToken['jwt']);

    $payload = json_encode(array("mensaje" => "Usuario logueado correctamente", "token" => $creacionToken['jwt']));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function Importar($request, $response, $args)
  {
    $archivo = $_FILES['archivo']['tmp_name'];

    if($archivo){
      $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $primerLinea = true;
      // Iterar sobre las líneas del archivo
      foreach ($lineas as $linea) {
        if (!$primerLinea) {
          // Dividir la línea en columnas usando explode
          $columnas = explode(',', $linea);
  
          $empleado = new Empleado();
          $empleado->set_rol(strtoupper($columnas[0]));
          $empleado->set_nombre($columnas[1]);
          $empleado->set_clave($columnas[2]);
          $empleado->set_baja($columnas[3]);
          $empleado->set_fecha_alta($columnas[4]);
          $empleado->set_fecha_baja($columnas[5]);
  
          var_dump($empleado);
          Empleado::create($empleado);
        }
        $primerLinea = false;
      }
  
      $payload = json_encode(array("mensaje" => "Usuarios importados con exito."));
      $response->getBody()->write($payload);
    }else{
      $payload = json_encode(array("mensaje" => "Error no se adjunto ningun archivo."));
      $response->getBody()->write($payload);
    }
    
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function Exportar($request, $response, $args)
  {
    $rutaArchivo = './Archivos';
    $nombreArchivo = 'Empleados_Exportados.csv';
    // Verificar si el directorio existe, si no, intentar crearlo
    if (!is_dir($rutaArchivo)) {
      // Intentar crear el directorio con permisos de escritura
      if (!mkdir($rutaArchivo, 0755, true)) {
        die('Error al crear el directorio');
      }
    }

    $rutaCompleta = $rutaArchivo . '/' . $nombreArchivo;
    // Abrir el archivo CSV para escribir
    $archivo = fopen($rutaCompleta, 'w');

    // Escribir la fila de encabezado
    fputcsv($archivo, ['id', 'rol', 'nombre', 'clave', 'baja', 'fecha_alta', 'fecha_baja']);

    $empleados = Empleado::GetAll();
    // Escribir los datos de cada usuario
    foreach ($empleados as $empleado) {
      fputcsv($archivo, (array)$empleado);
    }

    // Cerrar el archivo
    fclose($archivo);

    $payload = json_encode(array("mensaje" => "Usuarios exportados con exito."));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
