<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/config.php';

$response = [
    'success' => false,
    'stats' => [
        'users' => 0,
        'reservations' => 0,
        'cards' => 0,
    ],
    'nextReservations' => [],
    'error' => null,
];

try {
    $dbHost = $DB_CONSUSLT ?? $DB_CONSULT ?? $DB_HOSTNAME;
    $dbName = $DB_TEXT ?? $DB_NAME;
    $dbPass = $D_ANSWER ?? $DB_ANSWER ?? $DB_PASSWORD;
    $dbUser = $DB_USERNAME ?? 'root';

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        throw new RuntimeException('Error de conexion: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');

    $usersResult = $conn->query('SELECT COUNT(*) AS total FROM users');
    if ($usersResult && $row = $usersResult->fetch_assoc()) {
        $response['stats']['users'] = (int) $row['total'];
    }

    $reservationsResult = $conn->query('SELECT COUNT(*) AS total FROM reservations');
    if ($reservationsResult && $row = $reservationsResult->fetch_assoc()) {
        $response['stats']['reservations'] = (int) $row['total'];
    }

    $cardsResult = $conn->query('SELECT COUNT(*) AS total FROM cards');
    if ($cardsResult && $row = $cardsResult->fetch_assoc()) {
        $response['stats']['cards'] = (int) $row['total'];
    }

    $nextReservationsQuery = "
        SELECT nombre, fecha, estado
        FROM reservations
        WHERE fecha >= CURDATE()
        ORDER BY fecha ASC
        LIMIT 5
    ";

    $nextReservationsResult = $conn->query($nextReservationsQuery);
    if ($nextReservationsResult) {
        while ($reservation = $nextReservationsResult->fetch_assoc()) {
            $response['nextReservations'][] = [
                'nombre' => $reservation['nombre'],
                'fecha' => $reservation['fecha'],
                'estado' => $reservation['estado'],
            ];
        }
    }

    $conn->close();
    $response['success'] = true;
} catch (Throwable $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
