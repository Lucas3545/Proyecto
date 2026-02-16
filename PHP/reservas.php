<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'includes/config.php';

$conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
    $anio = isset($_GET['anio']) ? intval($_GET['anio']) : null;

    if ($mes !== null && $anio !== null) {
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM reservations WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado = 'confirmada'");
        $stmt->bind_param('ii', $mes, $anio);
    } else {
        $stmt = $conn->prepare("SELECT fecha, nombre, email FROM reservations WHERE estado = 'confirmada'");
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

    $check = $conn->prepare("SELECT id FROM reservations WHERE fecha = ? AND estado = 'confirmada'");
    $check->bind_param('s', $fecha);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Esta fecha ya está reservada']);
        $check->close();
        exit;
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO reservations (fecha, nombre, email) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $fecha, $nombre, $email);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reserva realizada con éxito', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la reserva: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
