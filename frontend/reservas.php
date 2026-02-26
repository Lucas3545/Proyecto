<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/includes/config.php';

function table_exists(mysqli $conn, string $tableName): bool {
    $safeName = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW TABLES LIKE '{$safeName}'");
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function has_column(mysqli $conn, string $tableName, string $columnName): bool {
    $safeTable = str_replace('`', '``', $tableName);
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM `{$safeTable}` LIKE '{$safeColumn}'");
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function create_reservations_table(mysqli $conn): bool {
    $sql = "
        CREATE TABLE IF NOT EXISTS `reservations` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `fecha` DATE NOT NULL,
            `nombre` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `estado` ENUM('confirmada', 'cancelada') NOT NULL DEFAULT 'confirmada',
            `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_reservations_fecha` (`fecha`),
            KEY `idx_reservations_email` (`email`),
            KEY `idx_reservations_estado` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    return (bool) $conn->query($sql);
}

$conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexion: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');

$reservationsTable = null;
if (table_exists($conn, 'reservations')) {
    $reservationsTable = 'reservations';
} elseif (table_exists($conn, 'reservas')) {
    $reservationsTable = 'reservas';
}

if ($reservationsTable === null) {
    if (create_reservations_table($conn)) {
        $reservationsTable = 'reservations';
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No existe la tabla de reservas (reservations/reservas) y no se pudo crear: ' . $conn->error
        ]);
        $conn->close();
        exit;
    }
}

$hasEstadoColumn = has_column($conn, $reservationsTable, 'estado');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    echo json_encode(['success' => true]);
    $conn->close();
    exit;
}

if ($method === 'GET') {
    $mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
    $anio = isset($_GET['anio']) ? intval($_GET['anio']) : null;
    $estadoFilter = $hasEstadoColumn ? " AND estado = 'confirmada'" : '';

    if ($mes !== null && $anio !== null) {
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM {$reservationsTable} WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?{$estadoFilter}");
        $stmt->bind_param('ii', $mes, $anio);
    } else {
        $baseWhere = $hasEstadoColumn ? " WHERE estado = 'confirmada'" : '';
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM {$reservationsTable}{$baseWhere}");
    }

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar consulta de reservas: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $reservas = [];

    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }

    echo json_encode(['success' => true, 'reservas' => $reservas]);
    $stmt->close();
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = strtolower(trim((string) ($data['action'] ?? 'create')));

    if ($action === 'cancel') {
        $fecha = trim((string) ($data['fecha'] ?? ''));

        if ($fecha === '') {
            echo json_encode(['success' => false, 'message' => 'Fecha obligatoria para cancelar']);
            $conn->close();
            exit;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            echo json_encode(['success' => false, 'message' => 'Formato de fecha invalido']);
            $conn->close();
            exit;
        }

        if ($hasEstadoColumn) {
            $stmt = $conn->prepare("UPDATE {$reservationsTable} SET estado = 'cancelada' WHERE fecha = ? AND estado = 'confirmada'");
        } else {
            $stmt = $conn->prepare("DELETE FROM {$reservationsTable} WHERE fecha = ?");
        }

        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error al preparar cancelacion: ' . $conn->error]);
            $conn->close();
            exit;
        }

        $stmt->bind_param('s', $fecha);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Reserva cancelada con exito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontro una reserva activa en esa fecha']);
        }

        $stmt->close();
        $conn->close();
        exit;
    }

    $fecha = trim((string) ($data['fecha'] ?? ''));
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));

    if ($fecha === '' || $nombre === '' || $email === '') {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        $conn->close();
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['success' => false, 'message' => 'Formato de fecha invalido']);
        $conn->close();
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo electronico invalido']);
        $conn->close();
        exit;
    }

    $dateCheckQuery = $hasEstadoColumn
        ? "SELECT 1 FROM {$reservationsTable} WHERE fecha = ? AND estado = 'confirmada'"
        : "SELECT 1 FROM {$reservationsTable} WHERE fecha = ?";
    $check = $conn->prepare($dateCheckQuery);
    if (!$check) {
        echo json_encode(['success' => false, 'message' => 'Error al validar disponibilidad: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $check->bind_param('s', $fecha);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Esta fecha ya esta reservada']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO {$reservationsTable} (fecha, nombre, email) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar insercion: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param('sss', $fecha, $nombre, $email);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reserva realizada con exito', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la reserva: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
