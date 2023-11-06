<?php
class DataAccess{
    private static $objDataAccess;
    private $objectPDO;

    private function __construct()
    {
        try {
            $this->objectPDO = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objectPDO->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$objDataAccess)) {
            self::$objDataAccess = new DataAccess();
        }
        return self::$objDataAccess;
    }

    public function prepareQuery($sql)
    {
        return $this->objectPDO->prepare($sql);
    }

    public function getLastInsertedID()
    {
        return $this->objectPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }
}
?>