<?php 

class Encuesta{
    public $id;
    public $codigo_pedido;
    public $puntuacion_mozo;
    public $puntuacion_comida;
    public $comentario;
    public $fecha;

    public function __construct() {
    }

    #region Getter Setter

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

    public function getPuntuacionMozo() {
        return $this->puntuacion_mozo;
    }
    public function setPuntuacionMozo($puntuacion_mozo) {
        if(self::ValidarPuntuacion($puntuacion_mozo)){
            $this->puntuacion_mozo = $puntuacion_mozo;
        }else{
            http_response_code(400);
            echo 'Puntuacion del mozo no valida, tiene que ser de 0 a 10';
            exit();
        }
    }

    public function getPuntuacionComida() {
        return $this->puntuacion_comida;
    }
    public function setPuntuacionComida($puntuacion_comida) {
        if(self::ValidarPuntuacion($puntuacion_comida)){
        $this->puntuacion_comida = $puntuacion_comida;
        }else{
            http_response_code(400);
            echo 'Puntuacion de la comida no valida, tiene que ser de 0 a 10';
            exit();
        }
    }
    public function getComentario() {
        return $this->comentario;
    }
    public function setComentario($comentario) {
        if(strlen($comentario) < 66){
            $this->comentario = $comentario;
            }else{
                http_response_code(400);
                echo 'Comentario demaciado largo. Solo puede contener 66 caracteres';
                exit();
            }
    }
    public function getFecha() {
        return $this->fecha;
    }
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }


    #endregion

    public static function ValidarPuntuacion($puntuacion){
        if($puntuacion < 0 && $puntuacion > 10){
            return false;
        }
        return true;
    }
    public static function Create($obj){

        
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("INSERT INTO Encuestas (codigo_pedido, puntuacion_mozo, puntuacion_comida, comentario, fecha) VALUES (:codigo_pedido, :puntuacion_mozo, :puntuacion_comida, :comentario, :fecha)");
        
        $consulta->bindValue(":codigo_pedido", $obj -> getCodigoPedido());
        $consulta->bindValue(":puntuacion_mozo", $obj -> getPuntuacionMozo());
        $consulta->bindValue(":puntuacion_comida", $obj -> getPuntuacionComida());
        $consulta->bindValue(":comentario", $obj -> getComentario());
        $consulta->bindValue(":fecha", $obj -> getFecha());
        $consulta->execute();
    }

    public static function GetAll(){
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido, puntuacion_mozo, puntuacion_comida, comentario, fecha FROM Encuestas");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function GetEncuestasByPuntacion($puntuacion)
    {
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido, puntuacion_mozo, puntuacion_comida, comentario, fecha FROM Encuestas WHERE puntuacion_mozo = :puntuacion_mozo AND puntuacion_comida = :puntuacion_comida");
        $consulta->bindValue(":puntuacion_mozo", $puntuacion, PDO::PARAM_INT);
        $consulta->bindValue(":puntuacion_comida", $puntuacion, PDO::PARAM_INT);


        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function GetEncuestasByCodigoPedido($codigo_pedido)
    {
        $objAccesoDatos = DataAccess::getInstance();
        $consulta = $objAccesoDatos->prepareQuery("SELECT id, codigo_pedido, puntuacion_mozo, puntuacion_comida, comentario, fecha FROM Encuestas
        WHERE  codigo_pedido= :codigo_pedido");
         $consulta->bindValue(":codigo_pedido", $codigo_pedido);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}
?>