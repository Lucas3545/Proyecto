<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

function normalize_expiration(string $value): string {
    $value = trim($value);
    if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $value, $m)) {
        return $m[1] . '/20' . $m[2];
    }
    return $value;
}

function ensure_cards_table(mysqli $conn): bool {
    $sql = "
        CREATE TABLE IF NOT EXISTS `cards` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `numero` VARCHAR(20) NOT NULL,
            `nombre_tarjeta` VARCHAR(100) NOT NULL,
            `vencimiento` VARCHAR(7) NOT NULL,
            `cvv` VARCHAR(4) NOT NULL,
            `banco` VARCHAR(50) NULL,
            `red_de_pago` VARCHAR(20) NULL,
            `email_usuario` VARCHAR(100) NULL,
            `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_cards_email` (`email_usuario`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    return (bool) $conn->query($sql);
}

try {
    $conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    if ($conn->connect_error) {
        echo json_encode(['mensaje' => 'Conexion fallida: ' . $conn->connect_error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $conn->set_charset('utf8mb4');

    if (!ensure_cards_table($conn)) {
        echo json_encode(['mensaje' => 'No se pudo crear la tabla cards: ' . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $numero = $_POST['numero'] ?? $_POST['register-numero'] ?? '';
    $nombre_tarjeta = $_POST['nombre_tarjeta'] ?? $_POST['register-nombre'] ?? '';
    $vencimiento = $_POST['vencimiento'] ?? $_POST['register-vencimiento'] ?? '';
    $cvv = $_POST['cvv'] ?? $_POST['register-cvv'] ?? '';
    $banco = $_POST['banco'] ?? '';
    $red_de_pago = $_POST['red_de_pago'] ?? '';
    $email_usuario = $_POST['email_usuario'] ?? '';

    $numero = preg_replace('/\D+/', '', trim((string) $numero));
    $nombre_tarjeta = trim((string) $nombre_tarjeta);
    $vencimiento = normalize_expiration((string) $vencimiento);
    $cvv = preg_replace('/\D+/', '', trim((string) $cvv));
    $banco = trim((string) $banco);
    $red_de_pago = trim((string) $red_de_pago);
    $email_usuario = trim((string) $email_usuario);

    if ($numero === '' || $nombre_tarjeta === '' || $vencimiento === '' || $cvv === '') {
        echo json_encode(['mensaje' => 'Faltan datos obligatorios de la tarjeta'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!preg_match('/^[0-9]{13,19}$/', $numero)) {
        echo json_encode(['mensaje' => 'Numero de tarjeta invalido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        echo json_encode(['mensaje' => 'CVV invalido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{4}$/', $vencimiento)) {
        echo json_encode(['mensaje' => 'Formato de vencimiento invalido (MM/YYYY)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO cards (numero, nombre_tarjeta, vencimiento, cvv, banco, red_de_pago, email_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        echo json_encode(['mensaje' => 'Error al preparar insercion: ' . $conn->error], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param('sssssss', $numero, $nombre_tarjeta, $vencimiento, $cvv, $banco, $red_de_pago, $email_usuario);

    if ($stmt->execute()) {
        echo json_encode(['mensaje' => 'Tarjeta guardada correctamente', 'id' => $stmt->insert_id], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['mensaje' => 'Error al guardar la tarjeta: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(['mensaje' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
