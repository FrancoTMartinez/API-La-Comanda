<?php 
include_once './Interfaces/ICrudBase.php';
include_once './Models/Estado.php';

class Mesa implements ICrudBase{
    public $id;
    public $codigo_mesa;
    public $estado;

    public function __construct() {
    }

    #region Getter Setter

    public function get_id() {
        return $this->id;
    }

    public function get_estado() {
        return $this->estado;
    }

    public function set_estado($estado) {

        if(self::ValidarEstado($estado)){
            $this->estado = $estado;
        }else{
            http_response_code(400);
            echo 'Estado de mesa no valido. (con cliente esperando pedido / con cliente comiendo / con cliente pagando / cerrada)';
            exit();
        }
    }

    public function get_codigo_mesa() {
        return $this->codigo_mesa;
    }

    public function set_codigo_mesa() {
        $this->codigo_mesa = self::generarCodigoMesaUnico();
    }


    #endregion

    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::ESPERANDO && $estado != Estado::COMIENDO && $estado != Estado::PAGANDO && $estado != Estado::CERRADA && $estado != Estado::VACIA) {
            return false;
        }
        return true;
    }

    public static function Create($obj){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO Mesas (estado, codigo_mesa) VALUES (:estado, :codigo_mesa)");

        $consulta->bindValue(':estado', strtolower($obj->get_estado()), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $obj->get_codigo_mesa(), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function Update($obj){
        $objAccesoDato = DataAccess::getInstance();
        $consulta = $objAccesoDato->prepareQuery("UPDATE Mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', strtolower($obj->get_estado()), PDO::PARAM_STR);
        $consulta->bindValue(':id', $obj -> id, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function Delete($codigo_mesa){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Mesas WHERE codigo_mesa= :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, estado, codigo_mesa FROM Mesas");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function GetById($codigo_mesa){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, estado, codigo_mesa FROM Mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public function generarCodigoMesaUnico($longitud = 5) {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $codigoMesa = '';
        
        $existeCodigo = true;
        while($existeCodigo){
            for ($i = 0; $i < $longitud; $i++) {
                $codigoMesa .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
    
            $objAccesoDatos = DataAccess::getInstance();
            $consulta = $objAccesoDatos->prepareQuery("SELECT codigo_mesa FROM Mesas WHERE codigo_mesa = :codigoMesa");
            $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
            $consulta->execute();
            $existeCodigo = $consulta->fetchObject('Mesa');

            if ($existeCodigo === false) {
                return $codigoMesa;
            }
        }
        throw new Exception('No se pudo generar un código de mesa único.');
    }
}
?>