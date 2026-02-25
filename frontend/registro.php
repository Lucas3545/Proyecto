<?php
header('Content-Type: application/json');

include('./includes/config.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "mensaje" => "Método no permitido"]);
        exit();
    }

    if (
        empty($_POST['username']) || empty($_POST['register-email']) ||
        empty($_POST['register-password']) || empty($_POST['register-name'])
    ) {
        echo json_encode(["success" => false, "mensaje" => "Todos los campos son requeridos"]);
        exit();
    }

    $conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "mensaje" => "Error de conexión: " . $conn->connect_error]);
        exit();
    }


    $hashed_password = password_hash($_POST['register-password'], PASSWORD_DEFAULT);


    $stmt = $conn->prepare("INSERT INTO users (email, password, fullname, username) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['register-email'], $hashed_password, $_POST['register-name'], $_POST['username']);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Registro exitoso"]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode(["success" => false, "mensaje" => "El email o usuario ya está registrado"]);
        } else {
            echo json_encode(["success" => false, "mensaje" => "Error al guardar: " . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "mensaje" => "Error del servidor: " . $e->getMessage()]);
}
