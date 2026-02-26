<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/includes/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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

function current_user_email(): string {
    $sessionEmail = strtolower(trim((string) ($_SESSION['user_email'] ?? '')));
    $cookieEmail = strtolower(trim((string) ($_COOKIE['lh_email'] ?? '')));
    return $sessionEmail !== '' ? $sessionEmail : $cookieEmail;
}

function owner_email(): string {
    global $OWNER_EMAIL;
    return strtolower(trim((string) ($OWNER_EMAIL ?: 'lucaszv2006@gmail.com')));
}

function is_owner_email(string $email): bool {
    $normalized = strtolower(trim($email));
    return $normalized !== '' && strcasecmp($normalized, owner_email()) === 0;
}

function dates_for_nights(string $startDate, int $nights): array {
    $date = DateTime::createFromFormat('Y-m-d', $startDate);
    if (!$date || $date->format('Y-m-d') !== $startDate || $nights < 1) {
        return [];
    }

    $result = [];
    for ($i = 0; $i < $nights; $i++) {
        $result[] = $date->format('Y-m-d');
        $date->modify('+1 day');
    }

    return $result;
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
        $userEmail = current_user_email();

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

        if ($userEmail === '') {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesion para cancelar una reserva']);
            $conn->close();
            exit;
        }

        $owner = is_owner_email($userEmail);
        $selectWhere = $hasEstadoColumn ? " WHERE fecha = ? AND estado = 'confirmada'" : " WHERE fecha = ?";
        $findStmt = $conn->prepare("SELECT email FROM {$reservationsTable}{$selectWhere} LIMIT 1");
        if (!$findStmt) {
            echo json_encode(['success' => false, 'message' => 'Error al validar reserva: ' . $conn->error]);
            $conn->close();
            exit;
        }

        $findStmt->bind_param('s', $fecha);
        $findStmt->execute();
        $reservation = $findStmt->get_result()->fetch_assoc();
        $findStmt->close();

        if (!$reservation) {
            echo json_encode(['success' => false, 'message' => 'No se encontro una reserva activa en esa fecha']);
            $conn->close();
            exit;
        }

        $reservationEmail = strtolower(trim((string) ($reservation['email'] ?? '')));
        if (!$owner && strcasecmp($reservationEmail, $userEmail) !== 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Solo el usuario que hizo la reserva o el admin puede cancelarla'
            ]);
            $conn->close();
            exit;
        }

        if ($hasEstadoColumn) {
            $stmt = $owner
                ? $conn->prepare("UPDATE {$reservationsTable} SET estado = 'cancelada' WHERE fecha = ? AND estado = 'confirmada'")
                : $conn->prepare("UPDATE {$reservationsTable} SET estado = 'cancelada' WHERE fecha = ? AND estado = 'confirmada' AND email = ?");
        } else {
            $stmt = $owner
                ? $conn->prepare("DELETE FROM {$reservationsTable} WHERE fecha = ?")
                : $conn->prepare("DELETE FROM {$reservationsTable} WHERE fecha = ? AND email = ?");
        }

        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error al preparar cancelacion: ' . $conn->error]);
            $conn->close();
            exit;
        }

        if ($owner) {
            $stmt->bind_param('s', $fecha);
        } else {
            $stmt->bind_param('ss', $fecha, $userEmail);
        }
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
    $noches = isset($data['noches']) ? intval($data['noches']) : 1;

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

    if ($noches < 1 || $noches > 30) {
        echo json_encode(['success' => false, 'message' => 'La cantidad de noches debe estar entre 1 y 30']);
        $conn->close();
        exit;
    }

    $fechasReserva = dates_for_nights($fecha, $noches);
    if (count($fechasReserva) !== $noches) {
        echo json_encode(['success' => false, 'message' => 'Rango de fechas invalido']);
        $conn->close();
        exit;
    }

    $dateCheckQuery = $hasEstadoColumn
        ? "SELECT 1 FROM {$reservationsTable} WHERE fecha = ? AND estado = 'confirmada' LIMIT 1"
        : "SELECT 1 FROM {$reservationsTable} WHERE fecha = ? LIMIT 1";

    foreach ($fechasReserva as $fechaReserva) {
        $check = $conn->prepare($dateCheckQuery);
        if (!$check) {
            echo json_encode(['success' => false, 'message' => 'Error al validar disponibilidad: ' . $conn->error]);
            $conn->close();
            exit;
        }

        $check->bind_param('s', $fechaReserva);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            echo json_encode(['success' => false, 'message' => "La fecha {$fechaReserva} ya esta reservada"]);
            $conn->close();
            exit;
        }
        $check->close();
    }

    try {
        $conn->begin_transaction();

        if ($hasEstadoColumn) {
            $reactivateStmt = $conn->prepare(
                "UPDATE {$reservationsTable} SET nombre = ?, email = ?, estado = 'confirmada' WHERE fecha = ? AND estado <> 'confirmada'"
            );
            if (!$reactivateStmt) {
                throw new RuntimeException('Error al preparar reactivacion: ' . $conn->error);
            }

            $insertStmt = $conn->prepare(
                "INSERT INTO {$reservationsTable} (fecha, nombre, email, estado) VALUES (?, ?, ?, 'confirmada')"
            );
            if (!$insertStmt) {
                $reactivateStmt->close();
                throw new RuntimeException('Error al preparar insercion: ' . $conn->error);
            }

            foreach ($fechasReserva as $fechaReserva) {
                $reactivateStmt->bind_param('sss', $nombre, $email, $fechaReserva);
                if (!$reactivateStmt->execute()) {
                    throw new RuntimeException($reactivateStmt->error);
                }

                if ($reactivateStmt->affected_rows === 0) {
                    $insertStmt->bind_param('sss', $fechaReserva, $nombre, $email);
                    if (!$insertStmt->execute()) {
                        throw new RuntimeException($insertStmt->error);
                    }
                }
            }

            $reactivateStmt->close();
            $insertStmt->close();
        } else {
            $insertStmt = $conn->prepare("INSERT INTO {$reservationsTable} (fecha, nombre, email) VALUES (?, ?, ?)");
            if (!$insertStmt) {
                throw new RuntimeException('Error al preparar insercion: ' . $conn->error);
            }

            foreach ($fechasReserva as $fechaReserva) {
                $insertStmt->bind_param('sss', $fechaReserva, $nombre, $email);
                if (!$insertStmt->execute()) {
                    throw new RuntimeException($insertStmt->error);
                }
            }

            $insertStmt->close();
        }

        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => $noches > 1 ? 'Reservas realizadas con exito' : 'Reserva realizada con exito',
            'noches' => $noches
        ]);
    } catch (Throwable $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al guardar la reserva: ' . $e->getMessage()]);
    }
}

$conn->close();
