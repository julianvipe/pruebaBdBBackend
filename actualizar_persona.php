<?php
include_once "cors.php";
$persona=json_decode(file_get_contents("php://input"));
include_once "funciones.php";
$result = actualizarPersona($persona);
echo json_encode($result);