<?php 
    interface IApiUsable{
        public static function GetById($request, $response, $args);
        public static function GetAll($request, $response, $args);
        public static function Save($request, $response, $args);
        public static function Delete($request, $response, $args);
        public static function Update($request, $response, $args);
    }
?>
