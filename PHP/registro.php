<?php

include('./includes/config.php');
try {

    $conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    //$data = json_decode(file_get_contents("php://input"), true);
    //echo $data;
    //die();

    $stmt = $conn->prepare("INSERT INTO users (email, password, fullname, username) VALUES (?, ?, ?, ?)"); //TODO: change fullname to username
    $stmt->bind_param("ssss", $_POST['register-email'], $_POST['register-password'], $_POST['register-name'], $_POST['register-name']);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Datos guardados correctamente"]);
        die();
    } else {
        echo json_encode(["mensaje" => "Error al guardar datos"]);
        die();
    }

    $stmt->close();
    $conn->close();
}
catch(Exception $e) {
    echo $e;
}

