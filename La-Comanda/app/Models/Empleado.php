<?php 
include_once './Interfaces/ICrudBase.php';

class Empleado implements ICrudBase{
    public $id;
    public $rol;
    public $nombre;
    public $baja;
    public $fecha_alta;
    public $fecha_baja;

    public function __construct() {
    }

    #region Getter Setter

    public function get_id() {
        return $this->id;
    }

    public function get_rol() {
        return $this->rol;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_baja() {
        return $this->baja;
    }

    public function get_fecha_alta() {
        return $this->fecha_alta;
    }

    public function get_fecha_baja() {
        return $this->fecha_baja;
    }

    public function set_rol($rol) {
        if(self::ValidarRol($rol)){
            $this->rol = $rol;
        }else{
            http_response_code(400);
            echo 'Rol Invalido.';
            exit();
        }
    }

    public function set_nombre($nombre) {
        $this->nombre = $nombre;
    }

    public function set_baja($baja) {
        $this->baja = $baja;
    }

    public function set_fecha_alta($fechaAlta) {
        $this->fecha_alta = $fechaAlta;
    }

    public function set_fecha_baja($fechaBaja) {
        $this->fecha_baja = $fechaBaja;
    }
    #endregion

    public static function ValidarRol($rol)
    {
        if ($rol != 'SOCIO' && $rol != 'BARTENDER' && $rol != 'CERVECERO' && $rol != 'COCINERO' && $rol != 'MOZO' && $rol != 'CANDYBAR') {
            return false;
        }
        return true;
    }

    public static function Create($obj){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO Empleados (rol, nombre, baja, fecha_alta) VALUES (:rol, :nombre, :baja, :fecha_alta)");

        $consulta->bindValue(':rol', strtolower($obj->rol), PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $obj->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':baja', $obj->baja, PDO::PARAM_BOOL);
        $consulta->bindValue(':fecha_alta', $obj->fecha_alta);
        $consulta->execute();
    }

    public static function Update($obj){
        $objAccesoDato = DataAccess::getInstance();
        $consulta = $objAccesoDato->prepareQuery("UPDATE Empleados SET rol = :rol, nombre = :nombre WHERE id = :id");
        $consulta->bindValue(':rol', strtolower($obj->rol), PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $obj->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':id', $obj -> id, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function Delete($obj){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("UPDATE Empleados SET Baja=1, Fecha_Baja= :fecha_baja WHERE id= :id");
        $consulta->bindValue(':id', $obj, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_baja', date('Y-m-d H:i:s'));

        var_dump($consulta);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, rol, nombre, baja, fecha_alta, fecha_baja FROM Empleados");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }
    public static function GetAllByBaja($baja = false){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT * FROM Empleados WHERE baja = :baja");
        $consulta->bindValue(':baja', $baja, PDO::PARAM_BOOL);
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function GetById($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, rol, nombre, baja, fecha_alta, fecha_baja FROM Empleados WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }
}
?>