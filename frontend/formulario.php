<?php
    include 'includes/procesar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/formulario.css">
  <title>Formulario Dinámico</title>
</head>
<body>
  <div class="form-box">
    <h2>Formulario</h2>
    <form method="post" action="includes/procesar.php">
      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" value="<?php $Nombre ?>">

      <label for="correo">Correo</label>
      <input type="email" id="correo" name="correo" value="<?php $correo ?>">

      <label for="comentario">Comentario</label>
      <input type="text" id="comentario" name="comentario" value="<?php $comentario ?>">

      <label for="edad">Edad</label>
      <input type="number" id="edad" name="edad" value="<?php $edad ?>">

      <label for="fecha_de_nacimiento">Fecha de Nacimiento</label>
      <input type="date" id="fecha_de_nacimiento" name="fecha_de_nacimiento" value="<?php $fecha_de_nacimiento ?>">

      <button type="submit" class="submit-btn">Enviar</button>
    </form>
  </div>
</body>
</html>

<?php if (isset($_GET['mensaje'])){ ?>
    <div style="color: green; font-weight: bold;">
        <?php echo $_GET['mensaje']; ?>
    </div>
<?php } 
?>