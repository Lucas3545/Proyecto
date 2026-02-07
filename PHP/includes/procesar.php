<?php
$Nombre = $_POST['nombre']; 
$correo = $_POST['correo'];
$comentario = $_POST['comentario'];
$edad = $_POST['edad'];
$fecha_de_nacimiento = $_POST['fecha_de_nacimiento'];
$btn_enviar = "";
    
switch ($btn_enviar) {
    case $edad:
        $edad >= 18;
        echo ('Bienvenido, su usuario ah sido registrado');
    break;

    case $edad: {
        $edad <= 17;
        echo ('Lo siento no te podemos registrar');
    }
    break;

    default :
        $edad <= 6;
        echo ('Error de inicio de secion');
}

    header("Location: ../formulario.php?mensaje=Bienvenido" .urlencode($Nombre));
    exit();