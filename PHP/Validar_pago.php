<?php 
  include 'PHP/config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/pago.css">
  <title>Tarjeta de Crédito</title>
</head>
<body>
  <div class="card-form">
    <h2>Datos de Tarjeta</h2>
    <form method="post" action="procesar_tarjeta.php">
      <label for="numero">Número de Tarjeta</label>
      <input type="text" id="numero" name="numero" value="<?php $numero ?>">

      <label for="nombre">Nombre del Titular</label>
      <input type="text" id="nombre" name="nombre" value="<?php $Nombre_tarjeta ?>">

      <label for="vencimiento">Fecha de Vencimiento</label>
      <input type="text" id="vencimiento" name="Vencimiento" value="<?php $Vencimiento ?>">

      <label for="cvv">CVV</label>
      <input type="text" id="cvv" name="cvv" value="<?php $cvv ?>">

      <label for="banco">Banco Emisor</label>
      <input type="text" id="banco" name="banco" value="<?php $banco ?>">

      <label for="red_de_pago">Red de Pago</label>
      <input type="text" id="red_de_pago" name="red_de_pago" value="<?php $red_de_pago ?>">

      <button type="submit" class="submit-btn">Enviar</button>
    </form>
  </div>
</body>
</html>
