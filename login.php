<?php
include_once "cors.php";
$persona = json_decode(file_get_contents("php://input"));
include_once "funciones.php";
$rest = doLogin($persona);
echo json_encode($rest);

