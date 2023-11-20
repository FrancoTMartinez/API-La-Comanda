<?php
include_once './Interfaces/ICrudBase.php';


class Pedido implements ICrudBase
{
    public $id;
    public $codigo_pedido;
    public $nroDocumento_Cliente;
    public $estado;
    public $tiempo_estimado_total;
    public $fecha_comienzo;
    public $fecha_finalizacion;
    public $codigo_mesa;
    public $foto;
    public $facturado;
    public $precio_total;

    public function __construct()
    {
    }
    #region getter y setter

    public function getId()
    {
        return $this->id;
    }
    public function getCodigoPedido()
    {
        return $this->codigo_pedido;
    }

    public function setCodigoPedido()
    {
        $this->codigo_pedido = self::generarCodigoPedidoUnico();
    }

    public function getNroDocumentoCliente()
    {
        return $this->nroDocumento_Cliente;
    }

    public function setNroDocumentoCliente($nroDocumentoCliente)
    {
        $this->nroDocumento_Cliente = $nroDocumentoCliente;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        if (self::ValidarEstado($estado)) {
            $this->estado = $estado;
        } else {
            http_response_code(400);
            echo 'Estado de pedido no valido. (con cliente esperando pedido / con cliente comiendo / con cliente pagando / cerrada)';
            exit();
        }
    }

    public function getTiempoEstimadoTotal()
    {
        return $this->tiempo_estimado_total;
    }

    public function setTiempoEstimadoTotal($tiempoEstimadoTotal)
    {
        $this->tiempo_estimado_total = $tiempoEstimadoTotal;
    }

    public function getFechaComienzo()
    {
        return $this->fecha_comienzo;
    }

    public function setFechaComienzo($fechaComienzo)
    {
        $this->fecha_comienzo = $fechaComienzo;
    }

    public function getFechaFinalizacion()
    {
        return $this->fecha_finalizacion;
    }

    public function setFechaFinalizacion($fechaFinalizacion)
    {
        $this->fecha_finalizacion = $fechaFinalizacion;
    }

    public function getCodigoMesa()
    {
        return $this->codigo_mesa;
    }

    public function setCodigoMesa($codigoMesa)
    {
        $this->codigo_mesa = $codigoMesa;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function getFacturado()
    {
        return $this->facturado;
    }

    public function setFacturado($facturado)
    {
        $this->facturado = $facturado;
    }

    public function getPrecioTotal()
    {
        return $this->precio_total;
    }

    public function setPrecioTotal($precio_total)
    {
        $this->precio_total = $precio_total;
    }


    #endregion
    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::PENDIENTE && $estado != Estado::PREPARACION && $estado != Estado::LISTO && $estado != Estado::ENTREGADO) {
            return false;
        }
        return true;
    }

    public static function Create($obj)
    {
        // $objAccesoDatos = DataAccess::getInstance();
        // $consulta = $objAccesoDatos->prepareQuery("INSERT INTO pedidos (codigo_pedido, id_empleado, nroDocumento_Cliente, estado, id_mesa, foto, id_producto, cantidad, facturado)
        //                                                         VALUES (:codigo_pedido, :idEmpleado, :nroDocumento_Cliente, :estado, :idMesa, :foto, :idProducto, :cantidad, :facturado)");

        // $consulta->bindValue(':id_Empleado', $obj->getIdEmpleado(), PDO::PARAM_STR);
        // $consulta->bindValue(':codigo_pedido', $obj->getIdEmpleado(), PDO::PARAM_STR);
        // $consulta->bindValue(':nroDocumento_Cliente', $obj->getNroDocumentoCliente(), PDO::PARAM_INT);
        // $consulta->bindValue(':estado', strtolower($obj->getEstado()));
        // $consulta->bindValue(':id_Mesa', $obj->getIdMesa());
        // $consulta->bindValue(':foto', $obj->getFoto());
        // $consulta->bindValue(':id_Producto', $obj->getIdProducto());
        // $consulta->bindValue(':cantidad', $obj->getCantidad());
        // $consulta->bindValue(':facturado', $obj->getFacturado());

        //sin foto
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO pedidos (codigo_pedido, nroDocumento_Cliente, estado, tiempo_Estimado_Total, codigo_mesa, facturado, precio_total)
                                                                VALUES (:codigo_pedido, :nroDocumento_Cliente,:estado, :tiempo_Estimado_Total, :codigoMesa, :facturado, :precio_total)");

        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':nroDocumento_Cliente', $obj->getNroDocumentoCliente(), PDO::PARAM_INT);
        $consulta->bindValue(':estado', strtolower($obj->getEstado()));
        $consulta->bindValue(':tiempo_Estimado_Total', $obj->getTiempoEstimadoTotal());
        $consulta->bindValue(':codigoMesa', $obj->getCodigoMesa());
        $consulta->bindValue(':facturado', $obj->getFacturado());
        $consulta->bindValue(':precio_total', $obj->getPrecioTotal());

        $consulta->execute();
    }

    public static function Update($obj)
    {
        // $objAccesoDato = DataAccess::getInstance();
        // $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado, nombre= :nombre, precio= :precio, tiempo_estimado= :tiempo_estimado WHERE id = :id");
        // $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
        // $consulta->bindValue(':tiempo_Estimado_Total', $obj->getTiempoEstimado());
        // $consulta->bindValue(':fecha_Comienzo', $obj->getFechaComienzo());
        // $consulta->bindValue(':fecha_Finalizacion', $obj->getFechaFinalizacion());
        // $consulta->bindValue(':id', $obj ->getId(), PDO::PARAM_INT);

        // $consulta->execute();
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function UpdateEstado($obj)
    {
        // pendiente / en preparacion / listo para servir / entregado
        if ($obj->getEstado() == Estado::PREPARACION) {

            $objAccesoDato = DataAccess::getInstance();

            $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado, fecha_Comienzo= :fecha_Comienzo  WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
            $consulta->bindValue(':fecha_Comienzo', date('Y-m-d H:i:s'));
            $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_INT);

            $consulta->execute();
        } else if ($obj->getEstado() == Estado::LISTO) {
            $objAccesoDato = DataAccess::getInstance();

            $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado, fecha_Finalizacion = :fecha_Finalizacion WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
            $consulta->bindValue(':fecha_Finalizacion', date('Y-m-d H:i:s'));
            $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_INT);

            $consulta->execute();
        } else if ($obj->getEstado() == Estado::ENTREGADO) {
            $objAccesoDato = DataAccess::getInstance();

            $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
            $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_INT);

            $consulta->execute();
        }

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function Delete($id)
    {
        $objAccesoDatos = DataAccess::getInstance();

        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Pedidos_Productos WHERE codigo_pedido= :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $id, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Pedidos WHERE codigo_pedido= :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $id, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function GetAll()
    {
        $arrRetornar = array();

        //agarrar todos los pedidos
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_pedido , nroDocumento_Cliente, estado, tiempo_estimado_total, fecha_comienzo, fecha_finalizacion, codigo_mesa, foto, facturado, precio_total FROM pedidos");
        $consulta->execute();
        $pedidosArr = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");

        foreach ($pedidosArr as $pedido) {
            //agarrar todos los prodcutos de 1 pedido de la tabla intermedia, y sus estados
            $objAccesoDatos = DataAccess::getInstance();
            $consulta = $objAccesoDatos->prepareQuery("SELECT id_producto, producto_estado FROM pedidos_productos WHERE codigo_pedido = :codigo_pedido");
            $consulta->bindValue(':codigo_pedido', $pedido->getCodigoPedido(), PDO::PARAM_STR);
            $consulta->execute();
            $pedidosProductosArr = $consulta->fetchAll(PDO::FETCH_CLASS, "PedidoProducto");

            //agarar el procuto correspondiente
            $productosArr = array();
            foreach ($pedidosProductosArr as $pedidoProductos) {
                $objAccesoDatos = DataAccess::getInstance();

                $consulta = $objAccesoDatos->prepareQuery("SELECT id,sector, nombre, precio, tiempo_estimado FROM productos WHERE id = :id_producto");
                $consulta->bindValue(':id_producto', $pedidoProductos->getIdProducto(), PDO::PARAM_INT);
                $consulta->execute();
                $producto = $consulta->fetch(PDO::FETCH_OBJ);

                array_push($productosArr, $producto);
            }

            $obj = [
                'codigo_pedido' => $pedido->getCodigoPedido(),
                'nroDocumento_Cliente' => $pedido->getNroDocumentoCliente(),
                'estado' => $pedido->getEstado(),
                'tiempo_estimado_total' => $pedido->getTiempoEstimadoTotal(),
                'fecha_comienzo' => $pedido->getFechaComienzo(),
                'fecha_finalizacion' => $pedido->getFechaFinalizacion(),
                'codigo_mesa' => $pedido->getCodigoMesa(),
                'facturado' => $pedido->getFacturado(),
                'precio_total' => $pedido->getPrecioTotal(),
                'productos' => $productosArr,
            ];

            array_push($arrRetornar, $obj);
        }

        return $arrRetornar;
    }
    public static function GetById($id)
    {
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido, nroDocumento_Cliente, estado, tiempo_estimado_total, fecha_comienzo,
        fecha_finalizacion, codigo_mesa, foto, facturado, precio_total FROM pedidos WHERE codigo_pedido= :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function GetByCodigoPedidoyMesa($codigoPedido, $codigoMesa)
    {
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT P.id, P.codigo_pedido, P.nroDocumento_Cliente, P.estado, P.tiempo_estimado_total, P.fecha_comienzo,
        P.fecha_finalizacion, P.codigo_mesa, P.foto, P.facturado, P.precio_total
        FROM Pedidos P
        INNER JOIN MESAS M
            ON M.Codigo_Mesa = :codigoMesa
        WHERE codigo_pedido= :codigoPedido");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function generarCodigoPedidoUnico($longitud = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $codigoPedido = '';

        $existeCodigo = true;
        while ($existeCodigo) {
            for ($i = 0; $i < $longitud; $i++) {
                $codigoPedido .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }

            $objAccesoDatos = DataAccess::getInstance();
            $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_pedido FROM Pedidos WHERE codigo_pedido = :codigoPedido");
            $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
            $consulta->execute();
            $existeCodigo = $consulta->fetchObject('Mesa');

            if ($existeCodigo === false) {
                return $codigoPedido;
            }
        }
        throw new Exception('No se pudo generar un código de pedido único.');
    }

    public static function CalcularMinutosPasados($horaInicio, $horaFin)
    {
        $fechaFin = new DateTime($horaInicio);
        $tiempo_array = explode(":", $horaFin);

        $minutos = $tiempo_array[1];

        $fechaFin->modify("+" . $minutos . " minutes");

        // Obtén la fecha y hora actual
        $fecha_actual = new DateTime();

        // Calcula la diferencia entre las dos fechas
        $intervalo = $fecha_actual->diff($fechaFin);

        // Obtiene el número total de minutos restantes
        $minutos_restantes = $intervalo->format('%r%I');

        return intval($minutos_restantes);
    }
}
