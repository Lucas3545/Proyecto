<?php
$pageLang = 'en';
$pageTitle = 'TÃ©rminos y Condiciones';
$pageStyles = ['./css/estilos-terminos.css'];
include __DIR__ . '/includes/page-start.php';
?>

  <header>
    <h1 id="titulo">Formulario de aceptaciÃ³n de condiciones</h1>
  </header>
  <main>
    <div>
      <?php include './includes/navbar-simple.php'; ?>
    </div>

    <div class="container-xxl">
      <h1>TÃ©rminos y Condiciones</h1>
      <p>Bienvenido a nuestro sitio web. Al acceder o utilizar este sitio, aceptas cumplir con los siguientes tÃ©rminos y
        condiciones:</p>

      <h2>1. Uso del sitio</h2>
      <ul>
        <li>El contenido es solo para fines informativos.</li>
        <li>No puedes utilizar el sitio para actividades ilegales.</li>
        <li>Nos reservamos el derecho de modificar el contenido en cualquier momento.</li>
        <li>Por su seguridad porfavor llenar el fromulario de aceptaciÃ³n de terminos y condiciones.</li>
      </ul>

      <h2>2. Propiedad intelectual</h2>
      <ul>
        <li>Todo el contenido es propiedad de la empresa o sus licenciantes.</li>
        <li>No estÃ¡ permitido copiar, distribuir o modificar el contenido sin autorizaciÃ³n.</li>
      </ul>

      <h2>3. LimitaciÃ³n de responsabilidad</h2>
      <ul>
        <li>No nos hacemos responsables por daÃ±os derivados del uso del sitio.</li>
        <li>El uso del sitio es bajo tu propio riesgo.</li>
      </ul>

      <h2>4. Cambios en los tÃ©rminos</h2>
      <p>Podemos actualizar estos tÃ©rminos en cualquier momento. Te recomendamos revisarlos periÃ³dicamente.</p>

      <div class="footer">
        &copy; 2025 Luke's house casa Tranquila. Todos los derechos reservados.
      </div>
    </div>
    <section>
      <main class="contenedor">
        <form action="./enviar.php" method="post" class="formulario">
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
        Descargar TÃ©rminos y Condiciones
        <a href="./doc/terminos-y-condiciones.pdf" download="terminos-y-condiciones.pdf" class="btn">Descargar PDF</a>
      </main>

      <script src="./js/terminos-y-condicones.js"></script>

      <?php include './includes/footer.php'; ?>
    </section>
  </main>
<?php include __DIR__ . '/includes/page-end.php'; ?>





