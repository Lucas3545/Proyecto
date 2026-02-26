<?php
header('Content-Type: application/json');

include('./includes/config.php');

function ensure_users_table(mysqli $conn): bool {
    $sql = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(100) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `fullname` VARCHAR(120) NOT NULL,
            `username` VARCHAR(50) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_users_email` (`email`),
            UNIQUE KEY `uniq_users_username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    return (bool) $conn->query($sql);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "mensaje" => "Metodo no permitido"]);
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
        echo json_encode(["success" => false, "mensaje" => "Error de conexion: " . $conn->connect_error]);
        exit();
    }

    $conn->set_charset('utf8mb4');

    if (!ensure_users_table($conn)) {
        echo json_encode(["success" => false, "mensaje" => "No se pudo crear/verificar la tabla users: " . $conn->error]);
        exit();
    }

    $hashed_password = password_hash($_POST['register-password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, password, fullname, username) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['register-email'], $hashed_password, $_POST['register-name'], $_POST['username']);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Registro exitoso"]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode(["success" => false, "mensaje" => "El email o usuario ya esta registrado"]);
        } else {
            echo json_encode(["success" => false, "mensaje" => "Error al guardar: " . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(["success" => false, "mensaje" => "Error del servidor: " . $e->getMessage()]);
}
