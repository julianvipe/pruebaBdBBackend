<?php

function iniciarSesionDeUsuario()
{
    iniciarSesionSiNoEstaIniciada();
    $_SESSION["logueado"] = true;

}

function cerrarSesion()
{
    iniciarSesionSiNoEstaIniciada();
    session_destroy();
}

function usuarioEstaLogueado()
{
    iniciarSesionSiNoEstaIniciada();
    return isset($_SESSION["logueado"]);
}

function iniciarSesionSiNoEstaIniciada()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function doLogin($persona){
    $bd = obtenerConexion();
    $sentencia= $bd->prepare("SELECT id,nombre,email,contrasena,edad,sexo,cargo,salario FROM empleados WHERE email=? && contrasena=?");
    $sentencia->execute([$persona->email,$persona->contrasena]);
    return $sentencia->fetchObject();
}

function actualizarPersona($persona){
    $bd=obtenerConexion();
    $sentencia = $bd->prepare("UPDATE empleados SET nombre=?, email=?, contrasena=?, edad=?, sexo=?, cargo=?, salario=? WHERE id=?");
    return $sentencia->execute([$persona->nombre,$persona->email,$persona->contrasena,$persona->edad,$persona->sexo,$persona->cargo,$persona->salario,$persona->id]);
}

function obtenerPersona($id)
{
    $bd = obtenerConexion();
    $sentencia= $bd->prepare("SELECT id, nombre, email, contrasena, edad, sexo, cargo, salario FROM empleados WHERE id = ?");
    $sentencia->execute([$id]);
    return $sentencia->fetchObject();
}

function guardarRegistro($persona){
    $bd=obtenerConexion();
    $sentencia = $bd->prepare("INSERT INTO empleados(nombre,email,contrasena,edad,sexo,cargo,salario) VALUES (?,?,?,?,?,?,?)");
    return $sentencia->execute([$persona->nombre,$persona->email,$persona->contrasena,$persona->edad,$persona->sexo,$persona->cargo,$persona->salario]);
}

function obtenerVariableDelEntorno($key)
{
    if (defined("_ENV_CACHE")) {
        $vars = _ENV_CACHE;
    } else {
        $file = "env.php";
        if (!file_exists($file)) {
            throw new Exception("El archivo de las variables de entorno ($file) no existe. Favor de crearlo");
        }
        $vars = parse_ini_file($file);
        define("_ENV_CACHE", $vars);
    }
    if (isset($vars[$key])) {
        return $vars[$key];
    } else {
        throw new Exception("La clave especificada (" . $key . ") no existe en el archivo de las variables de entorno");
    }
}


function obtenerConexion()
{
    $password = obtenerVariableDelEntorno("MYSQL_PASSWORD");
    $user = obtenerVariableDelEntorno("MYSQL_USER");
    $dbName = obtenerVariableDelEntorno("MYSQL_DATABASE_NAME");
    $database = new PDO('mysql:host=localhost;dbname=' . $dbName, $user, $password);
    $database->query("set names utf8;");
    $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    return $database;
}