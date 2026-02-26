<?php
require_once __DIR__ . '/admin-common.php';
admin_require_owner();

function panel_admin_get_stats() {
    $stats = [
        'active_users' => 0,
        'pending_reports' => 0,
    ];

    $conn = admin_db_connect();

    $usersTable = admin_first_table($conn, ['users']);
    $logsTable = admin_first_table($conn, ['access_logs']);

    if ($logsTable !== null) {
        $activeQuery = "
            SELECT COUNT(DISTINCT email) AS total
            FROM `{$logsTable}`
            WHERE resultado='ok'
              AND email IS NOT NULL
              AND email <> ''
              AND created_at >= (NOW() - INTERVAL 15 MINUTE)
        ";
        $activeResult = $conn->query($activeQuery);
        if ($activeResult && ($row = $activeResult->fetch_assoc())) {
            $stats['active_users'] = (int) ($row['total'] ?? 0);
        }

        $pendingQuery = "
            SELECT COUNT(*) AS total
            FROM `{$logsTable}`
            WHERE resultado='error'
              AND created_at >= (NOW() - INTERVAL 7 DAY)
        ";
        $pendingResult = $conn->query($pendingQuery);
        if ($pendingResult && ($row = $pendingResult->fetch_assoc())) {
            $stats['pending_reports'] = (int) ($row['total'] ?? 0);
        }
    } elseif ($usersTable !== null) {
        $fallbackUsers = $conn->query("SELECT COUNT(*) AS total FROM `{$usersTable}`");
        if ($fallbackUsers && ($row = $fallbackUsers->fetch_assoc())) {
            $stats['active_users'] = (int) ($row['total'] ?? 0);
        }
    }

    $conn->close();
    return $stats;
}

if (isset($_GET['stats']) && $_GET['stats'] === '1') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $stats = panel_admin_get_stats();
        echo json_encode(['ok' => true, 'stats' => $stats]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

$statsError = null;
$stats = ['active_users' => 0, 'pending_reports' => 0];

try {
    $stats = panel_admin_get_stats();
} catch (Throwable $e) {
    $statsError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <a href="./configuracion.php">Configuracion</a>
    <a href="./reportes.php">Reportes</a>
    <a href="./logout.php">Cerrar Sesion</a>
  </nav>
  <main>
    <div class="card">
      <h2>Bienvenido</h2>
      <p><strong>Panel de Administrador</strong></p>
    </div>

    <div class="card">
      <h2>Estadisticas rapidas</h2>
      <p>Usuarios activos: <strong id="active-users"><?php echo (int) ($stats['active_users'] ?? 0); ?></strong></p>
      <p>Reportes pendientes: <strong id="pending-reports"><?php echo (int) ($stats['pending_reports'] ?? 0); ?></strong></p>
      <p id="stats-error" style="color:#b91c1c; font-size:0.95rem;">
        <?php echo $statsError !== null ? 'Error al leer datos: ' . h($statsError) : ''; ?>
      </p>
    </div>
  </main>

  <script>
    (function () {
      var activeEl = document.getElementById('active-users');
      var pendingEl = document.getElementById('pending-reports');
      var errorEl = document.getElementById('stats-error');

      async function refreshStats() {
        try {
          var response = await fetch('Panel-admin.php?stats=1', { cache: 'no-store' });
          var data = await response.json();

          if (!response.ok || !data.ok) {
            throw new Error(data.message || 'No se pudieron actualizar las estadisticas');
          }

          activeEl.textContent = Number(data.stats.active_users || 0);
          pendingEl.textContent = Number(data.stats.pending_reports || 0);
          errorEl.textContent = '';
        } catch (err) {
          errorEl.textContent = err.message;
        }
      }

      setInterval(refreshStats, 5000);
    })();
  </script>
</body>
</html>
