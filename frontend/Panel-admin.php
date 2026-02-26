<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administrador</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }
    header {
      background-color: #222;
      color: #fff;
      padding: 1rem;
      text-align: center;
    }
    nav {
      width: 200px;
      background-color: #fff;
      border-right: 1px solid #ddd;
      position: fixed;
      top: 60px;
      bottom: 0;
      padding: 1rem;
    }
    nav a {
      display: block;
      padding: 0.5rem;
      margin-bottom: 0.5rem;
      text-decoration: none;
      color: #333;
      border-radius: 4px;
      transition: background 0.3s;
    }
    nav a:hover {
      background-color: #eee;
    }
    main {
      margin-left: 220px;
      padding: 2rem;
    }
    .card {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .card h2 {
      margin-top: 0;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>
  <header>
    <h1>Panel de Administrador</h1>
  </header>
  <nav>
    <a href="./dashboard.php">Dashboard</a>
    <a href="./usuarios.php">Usuarios</a>
    <a href="./configuracion.php">Configuración</a>
    <a href="./reportes.php">Reportes</a>
    <a href="./logout.php">Cerrar Sesión</a>
  </nav>
  <main>
    <div class="card">
      <h2>Bienvenido</h2>
      <p>Este es tu panel de administración minimalista.</p>
    </div>
    <div class="card">
      <h2>Estadísticas rápidas</h2>
      <p>Usuarios activos: 120</p>
      <p>Reportes pendientes: 5</p>
    </div>
  </main>
</body>
</html>