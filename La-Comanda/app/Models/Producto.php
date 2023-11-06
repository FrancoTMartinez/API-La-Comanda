<?php 
include_once './Interfaces/ICrudBase.php';

class Producto implements ICrudBase{
    public $id;
    public $sector;
    public $nombre;
    public $precio;
    public $tiempo_estimado;

    public function __construct() {
    }
    #region getter y setter
    public function getId() {
        return $this->id;
    }

    public function getSector() {
        return $this->sector;
    }

    public function setSector($sector) {

        if(self::ValidarSector($sector)){
            $this->sector = $sector;
        }else{
            http_response_code(400);
            echo 'Sector no valido. (barra de tragos y vinos / barra de choperas / la cocina / Candy Bar)';
            exit();
        }
    }


    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function getTiempoEstimado() {
        return $this->tiempo_estimado;
    }

    public function setTiempoEstimado($tiempoEstimado) {
        $this->tiempo_estimado = $tiempoEstimado;
    }

    #endregion

    public static function ValidarSector($sector)
    {
        if ($sector != 'barra de tragos y vinos' && $sector != 'barra de choperas' && $sector != 'la cocina' && $sector != 'Candy Bar') {
            return false;
        }
        return true;
    }

    public static function Create($obj){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO Productos (sector, nombre, precio, tiempo_estimado) VALUES (:sector, :nombre, :precio, :tiempo_estimado)");
        $consulta->bindValue(':sector', strtolower($obj->getSector()), PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $obj->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $obj->getPrecio());
        $consulta->bindValue(':tiempo_estimado', $obj->getTiempoEstimado());
        $consulta->execute();
    }

    public static function Update($obj){
        $objAccesoDato = DataAccess::getInstance();
        $consulta = $objAccesoDato->prepareQuery("UPDATE Productos SET sector = :sector, nombre= :nombre, precio= :precio, tiempo_estimado= :tiempo_estimado WHERE id = :id");
        $consulta->bindValue(':sector', strtolower($obj->getSector()), PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $obj->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $obj->getPrecio());
        $consulta->bindValue(':tiempo_estimado', $obj->getTiempoEstimado());
        $consulta->bindValue(':id', $obj ->getId(), PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function Delete($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("DELETE FROM Productos WHERE id= :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, sector, nombre, precio, tiempo_estimado FROM Productos");

        $consulta->execute();
        // var_dump($consulta->fetchAll(PDO::FETCH_CLASS, 'Producto'));
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function GetById($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, sector, nombre, precio, tiempo_estimado FROM Productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }
    public static function GetTiempoEstimadoById($id){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, sector, nombre, precio, tiempo_estimado FROM Productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }
}
?>