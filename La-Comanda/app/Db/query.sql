SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*Creacion de la base de datos "La Comanda"*/
    CREATE DATABASE LaComanda;

    /*-- Tabla Empleados --*/
    CREATE TABLE Empleados(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ROL VARCHAR(50) NOT NULL,
        NOMBRE VARCHAR(255) NOT NULL,
        CLAVE VARCHAR(50) NOT NULL
        BAJA BOOLEAN NOT NULL,
        FECHA_ALTA DATE NOT NULL,
        FECHA_BAJA DATE
    );

    /*-- Tabla Mesas --*/
    CREATE TABLE Mesas(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ESTADO VARCHAR(50) NOT NULL,
        CODIGO_MESA VARCHAR(20) NOT NULL
    );

    /*-- Tabla Productos --*/
    CREATE TABLE Productos(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        SECTOR VARCHAR(50) NOT NULL,
        NOMBRE VARCHAR(255) NOT NULL,
        PRECIO DECIMAL(10,2) NOT NULL,
        TIEMPO_ESTIMADO TIME
    );

        /*-- Tabla Facturaciones --*/
 CREATE TABLE Facturas(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CODIGO_MESA VARCHAR(5) NOT NULL,
        CODIGO_PEDIDO VARCHAR(5) NOT NULL,
        FECHA DATE NOT NULL,
        TOTAL DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (CODIGO_MESA) REFERENCES Mesas(CODIGO_MESA),
        FOREIGN KEY (CODIGO_PEDIDO) REFERENCES Pedidos(CODIGO_PEDIDO)
    );


    /*-- Tabla Pedidos --*/
    CREATE TABLE Pedidos(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CODIGO_PEDIDO varchar(5) NOT NULL,
    NRODOCUMENTO_CLIENTE VARCHAR(255) NOT NULL,
    ESTADO VARCHAR(255) NOT NULL,
    TIEMPO_ESTIMADO_TOTAL TIME NOT NULL,
    FECHA_COMIENZO DATETIME NULL,
    FECHA_FINALIZACION DATETIME NULL,
    CODIGO_MESA VARCHAR(5) NOT NULL,
    FOTO BLOB,
    FACTURADO BOOLEAN NOT NULL,
    PRECIO_TOTAL DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (CODIGO_MESA) REFERENCES Mesas(CODIGO_MESA)
    );

    /*-- Tabla Pedidos_Productos --*/
    CREATE TABLE Pedidos_Productos(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CODIGO_PEDIDO varchar(5) NOT NULL,
        ID_PRODUCTO INT NOT NULL,
        PRODUCTO_ESTADO varchar(30) NOT NULL,
        ID_EMPLEADO INT NULL,
        FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID),
        FOREIGN KEY (CODIGO_PEDIDO) REFERENCES PEDIDOS(CODIGO_PEDIDO)
    )

        public $id;
    public $codigo_pedido;
    public $puntuacion_mozo;
    public $puntuacion_comida;
    public $comentario;
    public $fecha;

    CREATE TABLE Encuestas(
        ID INT AUTO_INCREMENT PRIMARY KEY,
        CODIGO_PEDIDO varchar(5) NOT NULL,
        PUNTUACION_MOZO INT NOT NULL,
        PUNTUACION_COMIDA INT NOT NULL,
        COMENTARIO VARCHAR(66) NOT NULL,
        FECHA DATE NOT NULL,
        FOREIGN KEY (CODIGO_PEDIDO) REFERENCES PEDIDOS(CODIGO_PEDIDO)
    )