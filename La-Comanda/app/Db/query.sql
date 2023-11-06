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
    CREATE TABLE Facturaciones(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_MESA INT NOT NULL,
    FECHA DATE NOT NULL,
    DETALLE VARCHAR(150),
    TOTAL DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ID_MESA) REFERENCES Mesas(ID)
    );

    -- /*-- Tabla Pedidos --*/
    -- CREATE TABLE Pedidos(
    -- ID INT AUTO_INCREMENT PRIMARY KEY,
    -- ID_EMPLEADO INT NOT NULL,
    -- NRODOCUMENTO_CLIENTE VARCHAR(255) NOT NULL,
    -- ESTADO VARCHAR(255) NOT NULL,
    -- TIEMPO_ESTIMADO_TOTAL TIME NOT NULL,
    -- FECHA_COMIENZO DATETIME NOT NULL,
    -- FECHA_FINALIZACION DATETIME NOT NULL,
    -- CODIGO_MESA VARCHAR(5) NOT NULL,
    -- FOTO BLOB,
    -- ID_PRODUCTO INT NOT NULL,
    -- CANTIDAD INT NOT NULL,
    -- FACTURADO BOOLEAN NOT NULL,
    -- FOREIGN KEY (CODIGO_MESA) REFERENCES Mesas(CODIGO_MESA),
    -- FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID),
    -- FOREIGN KEY (ID_EMPLEADO) REFERENCES Empleados(ID)
    -- );
    
     /*-- Tabla Pedidos --*/
    CREATE TABLE Pedidos(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    CODIGO_PEDIDO varchar(5) NOT NULL,
    ID_EMPLEADO INT NOT NULL,
    NRODOCUMENTO_CLIENTE VARCHAR(255) NOT NULL,
    ESTADO VARCHAR(255) NOT NULL,
    TIEMPO_ESTIMADO_TOTAL TIME NOT NULL,
    FECHA_COMIENZO DATETIME NULL,
    FECHA_FINALIZACION DATETIME NULL,
    CODIGO_MESA VARCHAR(5) NOT NULL,
    FOTO BLOB,
    ID_PRODUCTO INT NOT NULL,
    CANTIDAD INT NOT NULL,
    FACTURADO BOOLEAN NOT NULL,
    FOREIGN KEY (CODIGO_MESA) REFERENCES Mesas(CODIGO_MESA),
    FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID),
    FOREIGN KEY (ID_EMPLEADO) REFERENCES Empleados(ID)
    );