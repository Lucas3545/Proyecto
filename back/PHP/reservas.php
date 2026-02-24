<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/includes/config.php';

function table_exists(mysqli $conn, string $tableName): bool {
    $safeName = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW TABLES LIKE '{$safeName}'");
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

$conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8');

$reservationsTable = null;
if (table_exists($conn, 'reservations')) {
    $reservationsTable = 'reservations';
} elseif (table_exists($conn, 'reservas')) {
    $reservationsTable = 'reservas';
}

if ($reservationsTable === null) {
    echo json_encode(['success' => false, 'message' => "No existe la tabla de reservas (reservations/reservas)"]);
    $conn->close();
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
    $anio = isset($_GET['anio']) ? intval($_GET['anio']) : null;

    if ($mes !== null && $anio !== null) {
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM {$reservationsTable} WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado = 'confirmada'");
        $stmt->bind_param('ii', $mes, $anio);
    } else {
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM {$reservationsTable} WHERE estado = 'confirmada'");
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

} elseif ($method === 'POST') {
    
    $data = json_decode(file_get_contents('php://input'), true);

    $fecha = $data['fecha'] ?? '';
    $nombre = $data['nombre'] ?? '';
    $email = $data['email'] ?? '';

    if (empty($fecha) || empty($nombre) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM {$reservationsTable} WHERE fecha = ? AND estado = 'confirmada'");
    if (!$check) {
        echo json_encode(['success' => false, 'message' => 'Error al validar disponibilidad: ' . $conn->error]);
        $conn->close();
        exit;
    }
    $check->bind_param('s', $fecha);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Esta fecha ya está reservada']);
        $check->close();
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
        echo json_encode(['success' => true, 'message' => 'Reserva realizada con éxito', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la reserva: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
