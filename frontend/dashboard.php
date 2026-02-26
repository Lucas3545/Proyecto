<?php
require_once __DIR__ . '/admin-common.php';
admin_require_owner();

$error = null;
$stats = ['users' => 0, 'reservas' => 0, 'cards' => 0, 'logs' => 0];
$recentLogs = [];

try {
    $conn = admin_db_connect();

    $usersTable = admin_first_table($conn, ['users']);
    $resTable = admin_first_table($conn, ['reservations', 'reservas']);
    $cardsTable = admin_first_table($conn, ['cards', 'ValidacionTarjetas', 'tarjetas']);
    $logsTable = admin_first_table($conn, ['access_logs']);

    if ($usersTable) {
        $r = $conn->query("SELECT COUNT(*) total FROM `{$usersTable}`");
        if ($r && $row = $r->fetch_assoc()) { $stats['users'] = (int) $row['total']; }
    }

    if ($resTable) {
        $r = $conn->query("SELECT COUNT(*) total FROM `{$resTable}`");
        if ($r && $row = $r->fetch_assoc()) { $stats['reservas'] = (int) $row['total']; }
    }

    if ($cardsTable) {
        $r = $conn->query("SELECT COUNT(*) total FROM `{$cardsTable}`");
        if ($r && $row = $r->fetch_assoc()) { $stats['cards'] = (int) $row['total']; }
    }

    if ($logsTable) {
        $r = $conn->query("SELECT COUNT(*) total FROM `{$logsTable}`");
        if ($r && $row = $r->fetch_assoc()) { $stats['logs'] = (int) $row['total']; }

        $rs = $conn->query("SELECT evento, email, resultado, created_at FROM `{$logsTable}` ORDER BY id DESC LIMIT 10");
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $recentLogs[] = $row;
            }
        }
    }

    $conn->close();
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_layout_start('Dashboard', 'dashboard');
?>
<div class="card"><h2>Resumen general</h2><p>Panel con datos de la base de datos en tiempo real.</p></div>
<?php if ($error !== null): ?><div class="card"><strong>Error DB:</strong> <?php echo h($error); ?></div><?php endif; ?>

<div class="grid">
  <div class="card"><div class="k">Usuarios</div><div class="v"><?php echo (int) $stats['users']; ?></div></div>
  <div class="card"><div class="k">Reservas</div><div class="v"><?php echo (int) $stats['reservas']; ?></div></div>
  <div class="card"><div class="k">Tarjetas</div><div class="v"><?php echo (int) $stats['cards']; ?></div></div>
  <div class="card"><div class="k">Logs</div><div class="v"><?php echo (int) $stats['logs']; ?></div></div>
</div>

<div class="card">
  <h2>Ultimos eventos de acceso</h2>
  <?php if (count($recentLogs) === 0): ?>
    <p>Sin eventos.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Evento</th><th>Email</th><th>Resultado</th><th>Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($recentLogs as $log): ?>
        <tr>
          <td><?php echo h($log['evento'] ?? ''); ?></td>
          <td><?php echo h($log['email'] ?? ''); ?></td>
          <td><span class="badge <?php echo ($log['resultado'] ?? '') === 'ok' ? 'ok' : 'err'; ?>"><?php echo h($log['resultado'] ?? ''); ?></span></td>
          <td><?php echo h($log['created_at'] ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php admin_layout_end(); ?>
