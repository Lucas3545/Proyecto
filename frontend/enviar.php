<?php
// Enviar a la base de datos los datos de validacion de tarjeta.

header('Content-Type: application/json; charset=utf-8');
include('./includes/config.php');

function ensure_validation_cards_table(mysqli $conn): bool {
    $sql = "
        CREATE TABLE IF NOT EXISTS `ValidacionTarjetas` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero_tarjeta` VARCHAR(20) NOT NULL,
            `es_valida` TINYINT(1) NOT NULL,
            `tipo_tarjeta` VARCHAR(30) NOT NULL,
            `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_validacion_numero` (`numero_tarjeta`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    return (bool) $conn->query($sql);
}

try {
    $conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        echo json_encode(["mensaje" => "Conexion fallida: " . $conn->connect_error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $conn->set_charset('utf8mb4');

    if (!ensure_validation_cards_table($conn)) {
        echo json_encode(["mensaje" => "No se pudo crear/verificar la tabla ValidacionTarjetas: " . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        echo json_encode(["mensaje" => "JSON invalido"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $numero_tarjeta = isset($data['numero_tarjeta']) ? preg_replace('/\D+/', '', (string) $data['numero_tarjeta']) : '';
    $es_valida = isset($data['es_valida']) ? (int) ((bool) $data['es_valida']) : 0;
    $tipo_tarjeta = isset($data['tipo_tarjeta']) ? trim((string) $data['tipo_tarjeta']) : '';

    if ($numero_tarjeta === '' || $tipo_tarjeta === '') {
        echo json_encode(["mensaje" => "Faltan campos obligatorios"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO ValidacionTarjetas (numero_tarjeta, es_valida, tipo_tarjeta) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["mensaje" => "Error al preparar insercion: " . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param("sis", $numero_tarjeta, $es_valida, $tipo_tarjeta);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Datos guardados correctamente"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["mensaje" => "Error al guardar datos: " . $stmt->error], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(["mensaje" => "Error del servidor: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
