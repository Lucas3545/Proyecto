<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Términos y Condiciones</title>
  <link rel="stylesheet" href="./css/estilos_terminos.css">
</head>

<body>

  <header>
    <h1 id="titulo">Formulario de aceptación de condiciones</h1>
  </header>
  <main>
    <div>
      <?php include './Front/PHP/includes/navbar_simple.php'; ?>
    </div>

    <div class="container-xxl">
      <h1>Términos y Condiciones</h1>
      <p>Bienvenido a nuestro sitio web. Al acceder o utilizar este sitio, aceptas cumplir con los siguientes términos y
        condiciones:</p>

      <h2>1. Uso del sitio</h2>
      <ul>
        <li>El contenido es solo para fines informativos.</li>
        <li>No puedes utilizar el sitio para actividades ilegales.</li>
        <li>Nos reservamos el derecho de modificar el contenido en cualquier momento.</li>
        <li>Por su seguridad porfavor llenar el fromulario de aceptación de terminos y condiciones.</li>
      </ul>

      <h2>2. Propiedad intelectual</h2>
      <ul>
        <li>Todo el contenido es propiedad de la empresa o sus licenciantes.</li>
        <li>No está permitido copiar, distribuir o modificar el contenido sin autorización.</li>
      </ul>

      <h2>3. Limitación de responsabilidad</h2>
      <ul>
        <li>No nos hacemos responsables por daños derivados del uso del sitio.</li>
        <li>El uso del sitio es bajo tu propio riesgo.</li>
      </ul>

      <h2>4. Cambios en los términos</h2>
      <p>Podemos actualizar estos términos en cualquier momento. Te recomendamos revisarlos periódicamente.</p>

      <div class="footer">
        &copy; 2025 Luke's house casa Tranquila. Todos los derechos reservados.
      </div>
    </div>
    <section>
      <main class="contenedor">
        <form action="./PHP/enviar.php" method="post" class="formulario">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" id="nombre" placeholder="Escriba su nombre">
          <br>
          <label for="apellido">Apellido:</label>
          <input type="text" name="apellido" id="apellido" placeholder="Escriba su apellido">
          <br>
          <label for="email">Email:</label>
          <input type="email" name="email" id="email" placeholder="Escriba su email">
          <br>
          <input type="submit" value="Enviar">
          <br>
          <br>
          <label>
            <input type="radio" name="acuerdo" value="si" required>
            Estoy de acuerdo
          </label>
          <br>
          <label>
            <input type="radio" name="acuerdo" value="no">
            No estoy de acuerdo
          </label>
        </form>
        <br>
        Descargar Términos y Condiciones
        <a href="doc/Terminos_y_Condiciones.pdf" download="Terminos_y_Condiciones.pdf" class="btn">Descargar PDF</a>
      </main>

      <script src="./js/Terminos_y_condicones.js"></script>

      <?php include './Back/PHP/includes/footer.php'; ?>
    </section>
  </main>
</body>

</html>
