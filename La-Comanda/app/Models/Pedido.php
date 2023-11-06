<?php 
include_once './Interfaces/ICrudBase.php';

class Pedido implements ICrudBase{
    public $id;
    public $codigo_pedido;
    public $id_empleado;
    public $nroDocumento_Cliente;
    public $estado;
    public $tiempo_estimado_total;
    public $fecha_comienzo;
    public $fecha_finalizacion;
    public $codigo_mesa;
    public $foto;
    public $id_producto;
    public $cantidad;
    public $facturado;
    public function __construct() {
    }
    #region getter y setter

    public function getId()
    {
        return $this->id;
    }

    public function getIdEmpleado()
    {
        return $this->id_empleado;
    }

    public function getCodigoPedido()
    {
        return $this->codigo_pedido;
    }

    public function setCodigoPedido()
    {
        $this->codigo_pedido = self::generarCodigoPedidoUnico();
    }

    public function setIdEmpleado($idEmpleado)
    {
        $this->id_empleado = $idEmpleado;
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
        if(self::ValidarEstado($estado)){
            $this->estado = $estado;
        }else{
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

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($idProducto)
    {
        $this->id_producto = $idProducto;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function getFacturado()
    {
        return $this->facturado;
    }

    public function setFacturado($facturado)
    {
        $this->facturado = $facturado;
    }
    #endregion
    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::PENDIENTE && $estado != Estado::PREPARACION && $estado != Estado::LISTO && $estado != Estado::ENTREGADO) {
            return false;
        }
        return true;
    }
    public static function Create($obj){
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
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO pedidos (codigo_pedido, id_empleado, nroDocumento_Cliente, estado, tiempo_Estimado_Total, codigo_mesa, id_producto, cantidad, facturado)
                                                                VALUES (:codigo_pedido, :idEmpleado, :nroDocumento_Cliente,:estado, :tiempo_Estimado_Total, :codigoMesa, :idProducto, :cantidad, :facturado)");
        
        $consulta->bindValue(':idEmpleado', $obj->getIdEmpleado(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':nroDocumento_Cliente', $obj->getNroDocumentoCliente(), PDO::PARAM_INT);
        $consulta->bindValue(':estado', strtolower($obj->getEstado()));
        $consulta->bindValue(':tiempo_Estimado_Total', $obj-> getTiempoEstimadoTotal());
        $consulta->bindValue(':codigoMesa', $obj->getCodigoMesa());
        $consulta->bindValue(':idProducto', $obj->getIdProducto());
        $consulta->bindValue(':cantidad', $obj->getCantidad());
        $consulta->bindValue(':facturado', $obj->getFacturado());

        $consulta->execute();
    }

    public static function Update($obj){
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

    public static function UpdateEstado($obj){
        // si es 'en preparacion' agregarle el tiempo estimado de finalizacion

        if($obj -> getEstado == Estado::PREPARACION){

            $objAccesoDato = DataAccess::getInstance();

            $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado, tiempo_Estimado_Total = :tiempo_Estimado_Total, fecha_Comienzo= :fecha_Comienzo  WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_Estimado_Total', $obj->getTiempoEstimado());
            $consulta->bindValue(':fecha_Comienzo', $obj->getFechaComienzo());
            $consulta->bindValue(':id', $obj ->getId(), PDO::PARAM_INT);

        }else if($obj -> getEstado == Estado::LISTO){
            $objAccesoDato = DataAccess::getInstance();

            $consulta = $objAccesoDato->prepareQuery("UPDATE Pedidos SET estado = :estado, fecha_Finalizacion = :fecha_Finalizacion WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($obj->getSector()), PDO::PARAM_STR);
            $consulta->bindValue(':fecha_Finalizacion', $obj->getFechaFinalizacion());
            $consulta->bindValue(':id', $obj ->getId(), PDO::PARAM_INT);
        }

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function Delete($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Pedidos WHERE id= :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido ,id_empleado, nroDocumento_Cliente, estado, tiempo_estimado_total, fecha_comienzo,
                                                            fecha_finalizacion, codigo_mesa, foto, id_producto, cantidad, facturado FROM Pedidos");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
    public static function GetById($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido, id_empleado, nroDocumento_Cliente, estado, tiempo_estimado_total, fecha_comienzo,
        fecha_finalizacion, codigo_mesa, foto, id_producto, cantidad, facturado FROM pedidos WHERE  AND id= :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function GetByCodigoPedidoyMesa($codigoPedido, $codigoMesa){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT P.id, P.codigo_pedido, P.id_empleado, P.nroDocumento_Cliente, P.estado, P.tiempo_estimado_total, P.fecha_comienzo,
        P.fecha_finalizacion, P.codigo_mesa, P.foto, P.id_producto, P.cantidad, P.facturado
        FROM Pedidos P
        INNER JOIN MESAS M
            ON M.Codigo_Mesa = :codigoMesa
        WHERE codigo_pedido= :codigoPedido");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function generarCodigoPedidoUnico($longitud = 5) {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $codigoPedido = '';
        
        $existeCodigo = true;
        while($existeCodigo){
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

    public static function MultiplicarTiempo($tiempoOriginal, $valorMultiplicador) {
        list($horas, $minutos, $segundos) = explode(':', $tiempoOriginal);
        $totalMinutos = $horas * 60 + $minutos;
        $totalMinutosMultiplicados = $totalMinutos * $valorMultiplicador;
        $nuevasHoras = floor($totalMinutosMultiplicados / 60);
        $nuevosMinutos = $totalMinutosMultiplicados % 60;
    
        return sprintf("%02d:%02d:%02d", $nuevasHoras, $nuevosMinutos, $segundos);
    }

    public static function CalcularMinutosPasados($horaInicio, $horaFin) {
        $horaInicio = strtotime($horaInicio);
        $horaFin = strtotime($horaFin);
        $diferencia = $horaFin - $horaInicio;
    
        // 3600 segundos es equivalente a 1 hora
        $horas = floor($diferencia / 3600);
        $minutos = floor(($diferencia - ($horas * 3600)) / 60);
        $segundos = $diferencia - ($horas * 3600) - ($minutos * 60);
    
        $tiempoPasado = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
        
        return $tiempoPasado;
    }
}
?>