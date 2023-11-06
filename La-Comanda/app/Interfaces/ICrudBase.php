<?php
    interface ICrudBase{
        public static function Create($obj);
        public static function Update($obj);
        public static function Delete($obj);
        public static function GetAll();
        public static function GetById($id);
    }
?>
