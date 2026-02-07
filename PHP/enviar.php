// enviar a la base de datos los datos de los javascript a luke.sql
<?php
$servername = "localhost";
$username = "root";
$password = "...";
$dbname = "luke";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("INSERT INTO ValidacionTarjetas (numero_tarjeta, es_valida, tipo_tarjeta) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $data['numero_tarjeta'], $data['es_valida'], $data['tipo_tarjeta']);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Datos guardados correctamente"]);
} else {
    echo json_encode(["mensaje" => "Error al guardar datos"]);
}

$stmt->close();
$conn->close();