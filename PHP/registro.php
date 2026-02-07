// enviar a la base de datos los datos de los javascript a luke.sql
<?php

echo "<h1>Hola</h1>";
die();


include('./includes/config.php');

$conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
echo $data;
$stmt = $conn->prepare("INSERT INTO users (email, password, fullname) VALUES (?, ?, ?)"); //TODO: change fullname to username
$stmt->bind_param("sis", $data['register-email'], $data['register-password'], $data['register-name']);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Datos guardados correctamente"]);
} else {
    echo json_encode(["mensaje" => "Error al guardar datos"]);
}

$stmt->close();
$conn->close();