<?php
$pageTitle = 'Panel de Acceso';
$pageStyles = ['./css/estilos-panes.css'];
include __DIR__ . '/includes/page-start.php';
?>

<header class="container-xl">
  <?php include __DIR__ . '/includes/navbar-simple.php'; ?>
</header>

<main class="auth-shell">
  <section class="auth-hero">
    <h1 class="titulo-principal">Panel de Acceso</h1>
    <p class="subtitle">Inicia sesión o regístrate para acceder a funciones exclusivas.</p>
    <button id="openPanelBtn" class="submit-btn" aria-expanded="false" aria-controls="authPanel">
      Abrir Panel de Acceso
    </button>
  </section>

  <section class="overlay" id="authPanel" role="dialog" aria-modal="true" aria-labelledby="authPanelTitle" tabindex="-1">
    <div class="container" tabindex="0">
      <div class="tabs" id="authPanelTitle" role="tablist" aria-label="Panel de acceso">
        <button class="tab active" id="tab-login" type="button" role="tab" aria-selected="true" aria-controls="form-login">
          Iniciar sesión
        </button>
        <button class="tab" id="tab-register" type="button" role="tab" aria-selected="false" aria-controls="form-register">
          Registrarse
        </button>
      </div>

      <form id="form-login" autocomplete="off" role="tabpanel" aria-hidden="false">
        <label for="login-email">Correo electrónico</label>
        <input type="email" id="login-email" name="login-email" required placeholder="tucorreo@ejemplo.com">

        <label for="login-password">Contraseña</label>
        <input type="password" id="login-password" name="login-password" required placeholder="Contraseña" minlength="6">

        <button type="submit" class="submit-btn">Entrar</button>
        <div class="form-message" id="login-message"></div>
      </form>

      <form id="form-register" style="display:none;" autocomplete="off" role="tabpanel" aria-hidden="true" method="POST" action="./registro.php">
        <label for="register-username">Usuario</label>
        <input type="text" id="register-username" name="username" required placeholder="Nombre de usuario">

        <label for="register-name">Nombre completo</label>
        <input type="text" id="register-name" name="register-name" required placeholder="Tu nombre completo">

        <label for="register-email">Correo electrónico</label>
        <input type="email" id="register-email" name="register-email" required placeholder="tucorreo@ejemplo.com">

        <label for="register-password">Contraseña</label>
        <input type="password" id="register-password" name="register-password" required placeholder="Contraseña" minlength="6">

        <label for="register-confirm-password">Confirmar contraseña</label>
        <input type="password" id="register-confirm-password" name="register-confirm-password" required placeholder="Confirma tu contraseña" minlength="6">

        <button type="submit" class="submit-btn">Registrarse</button>
        <div class="form-message" id="register-message"></div>
      </form>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="./js/panel-de-acceso.js" defer></script>
<?php include __DIR__ . '/includes/page-end.php'; ?>
