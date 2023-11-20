<?php 
include_once './Interfaces/ICrudBase.php';

class PedidoProducto{
    public $id;
    public $codigo_pedido;
    public $id_producto;
    public $producto_estado;
    public $id_empleado;

    public function __construct() {
    }
    #region getter y setter
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function getCodigoPedido() {
        return $this->codigo_pedido;
    }
    public function setCodigoPedido($codigo_pedido) {
        $this->codigo_pedido = $codigo_pedido;
    }
    public function getIdProducto() {
        return $this->id_producto;
    }
    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }
    public function getProductoEstado() {
        return $this->producto_estado;
    }
    public function setProductoEstado($estado)
    {
        if(self::ValidarEstado($estado)){
            $this->producto_estado = $estado;
        }else{
            http_response_code(400);
            echo 'Estado de pedido no valido. (pendiente / en preparacion / listo para servir / entregado)';
            exit();
        }
    }
    public function getIdEmpleado() {
        return $this->id_empleado;
    }
    public function setIdEmpleado($id_empleado) {
        $this->id_empleado = $id_empleado;
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
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO Pedidos_Productos (codigo_pedido, id_producto, producto_estado) 
                                                    VALUES (:codigo_pedido, :id_producto, :producto_estado)");

        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $obj->getIdProducto(), PDO::PARAM_INT);
        $consulta->bindValue(':producto_estado', $obj->getProductoEstado(), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function Update($obj){
        $objAccesoDatos = DataAccess::getInstance();

        $consulta = $objAccesoDatos->prepareQuery("UPDATE Pedidos_Productos SET producto_estado = :producto_estado, id_empleado= :id_empleado WHERE codigo_pedido = :codigo_pedido AND id_producto = :id_producto");
        $consulta->bindValue(':producto_estado', $obj -> getProductoEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $obj -> getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $obj -> getIdProducto(), PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $obj -> getIdEmpleado(), PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('PedidoProducto');
    }

    public static function Delete($obj){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Pedidos_Productos WHERE codigo_pedido= :codigo_pedido AND id_producto = :id_producto");
        $consulta->bindValue(':codigo_pedido', $obj -> getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $obj -> getIdProducto(), PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido_Producto');
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_pedido, id_producto, producto_estado, id_empleado FROM Pedidos_Productos");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function GetByIdCodigoPedido($codigoPedido){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_pedido, id_producto, producto_estado, id_empleado FROM Pedidos_Productos WHERE codigo_pedido = :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function GetByCodigoPedidoIdProducto($codigo_pedido, $id_producto){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_pedido, id_producto, producto_estado, id_empleado FROM Pedidos_Productos WHERE codigo_pedido = :codigo_pedido AND id_producto= :id_producto");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);

        $consulta->execute();

        return $consulta->fetchObject('PedidoProducto');
    }

    // public static function updateEstadoProducto($obj){
    //     $objAccesoDatos = DataAccess::getInstance();
    //     $consulta = $objAccesoDatos->prepareQuery("UPDATE Pedidos_Productos SET estado_pedido = :estado_pedido WHERE codigo_pedido = :codigo_pedido AND id_producto = :id_producto");
    //     $consulta->bindValue(':estado_pedido', $obj -> getEstado(), PDO::PARAM_INT);
    //     $consulta->bindValue(':codigo_pedido', $obj -> getCodigoPedido(), PDO::PARAM_INT);
    //     $consulta->bindValue(':id_producto', $obj -> getEstado(), PDO::PARAM_INT);
    //     $consulta->execute();

    //     return $consulta->fetchObject('Pedidos_Productos');
    // }
}
?>