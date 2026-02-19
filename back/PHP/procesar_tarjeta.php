<?php
include('./includes/config.php');

header('Content-Type: application/json');

try {
    $conn = new mysqli($DB_NUMBER, $DB_NOMBRE, $DB_CVV, $DB_VENCIMIENTO);

    if ($conn->connect_error) {
        die(json_encode(["mensaje" => "Conexión fallida: " . $conn->connect_error]));
    }

    $required_fields = ['numero', 'nombre_tarjeta', 'vencimiento', 'cvv'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode(["mensaje" => "Falta el campo: " . $field]);
            die();
        }
    }

    $numero = $conn->real_escape_string($_POST['numero']);
    $nombre_tarjeta = $conn->real_escape_string($_POST['nombre_tarjeta']);
    $vencimiento = $conn->real_escape_string($_POST['vencimiento']);
    $cvv = $conn->real_escape_string($_POST['cvv']);
    $banco = isset($_POST['banco']) ? $conn->real_escape_string($_POST['banco']) : '';
    $red_de_pago = isset($_POST['red_de_pago']) ? $conn->real_escape_string($_POST['red_de_pago']) : '';
    $email_usuario = isset($_POST['email_usuario']) ? $conn->real_escape_string($_POST['email_usuario']) : '';

    if (!preg_match('/^[0-9]{13,19}$/', $numero)) {
        echo json_encode(["mensaje" => "Número de tarjeta inválido"]);
        die();
    }

    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        echo json_encode(["mensaje" => "CVV inválido"]);
        die();
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{4}$/', $vencimiento)) {
        echo json_encode(["mensaje" => "Formato de vencimiento inválido (MM/YYYY)"]);
        die();
    }

    $stmt = $conn->prepare("INSERT INTO cards (numero, nombre_tarjeta, vencimiento, cvv, banco, red_de_pago, email_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $numero, $nombre_tarjeta, $vencimiento, $cvv, $banco, $red_de_pago, $email_usuario);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Tarjeta guardada correctamente", "id" => $stmt->insert_id]);
    } else {
        echo json_encode(["mensaje" => "Error al guardar la tarjeta: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} catch(Exception $e) {
    echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
}
?>
