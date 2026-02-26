<?php
require_once __DIR__ . '/admin-common.php';
admin_require_owner();

$error = null;
$summary = [
    'login_ok' => 0,
    'login_error' => 0,
    'registro_ok' => 0,
    'registro_error' => 0,
];
$recent = [];
$reservasByEstado = [];

try {
    $conn = admin_db_connect();

    $logsTable = admin_first_table($conn, ['access_logs']);
    $resTable = admin_first_table($conn, ['reservations', 'reservas']);

    if ($logsTable) {
        $q = "
            SELECT
                SUM(CASE WHEN evento='login' AND resultado='ok' THEN 1 ELSE 0 END) AS login_ok,
                SUM(CASE WHEN evento='login' AND resultado='error' THEN 1 ELSE 0 END) AS login_error,
                SUM(CASE WHEN evento='registro' AND resultado='ok' THEN 1 ELSE 0 END) AS registro_ok,
                SUM(CASE WHEN evento='registro' AND resultado='error' THEN 1 ELSE 0 END) AS registro_error
            FROM `{$logsTable}`
        ";
        $r = $conn->query($q);
        if ($r && $row = $r->fetch_assoc()) {
            $summary['login_ok'] = (int) ($row['login_ok'] ?? 0);
            $summary['login_error'] = (int) ($row['login_error'] ?? 0);
            $summary['registro_ok'] = (int) ($row['registro_ok'] ?? 0);
            $summary['registro_error'] = (int) ($row['registro_error'] ?? 0);
        }

        $recentQ = "SELECT evento, email, resultado, mensaje, created_at FROM `{$logsTable}` ORDER BY id DESC LIMIT 30";
        $rs = $conn->query($recentQ);
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $recent[] = $row;
            }
        }
    }

    if ($resTable) {
        if (admin_has_column($conn, $resTable, 'estado')) {
            $rs = $conn->query("SELECT estado, COUNT(*) total FROM `{$resTable}` GROUP BY estado");
            if ($rs) {
                while ($row = $rs->fetch_assoc()) {
                    $reservasByEstado[] = $row;
                }
            }
        } else {
            $rs = $conn->query("SELECT COUNT(*) total FROM `{$resTable}`");
            if ($rs && $row = $rs->fetch_assoc()) {
                $reservasByEstado[] = ['estado' => 'confirmada', 'total' => (int) $row['total']];
            }
        }
    }

    $conn->close();
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_layout_start('Reportes', 'reportes');
?>
<div class="card"><h2>Reportes de actividad</h2><p>Resumen de accesos y estado de reservas.</p></div>
<?php if ($error !== null): ?><div class="card"><strong>Error DB:</strong> <?php echo h($error); ?></div><?php endif; ?>

<div class="grid">
  <div class="card"><div class="k">Login ok</div><div class="v"><?php echo (int) $summary['login_ok']; ?></div></div>
  <div class="card"><div class="k">Login error</div><div class="v"><?php echo (int) $summary['login_error']; ?></div></div>
  <div class="card"><div class="k">Registro ok</div><div class="v"><?php echo (int) $summary['registro_ok']; ?></div></div>
  <div class="card"><div class="k">Registro error</div><div class="v"><?php echo (int) $summary['registro_error']; ?></div></div>
</div>

<div class="card">
  <h2>Reservas por estado</h2>
  <?php if (count($reservasByEstado) === 0): ?>
    <p>Sin datos de reservas.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Estado</th><th>Total</th></tr></thead>
      <tbody>
      <?php foreach ($reservasByEstado as $row): ?>
        <tr><td><?php echo h($row['estado'] ?? ''); ?></td><td><?php echo (int) ($row['total'] ?? 0); ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="card">
  <h2>Ultimos eventos</h2>
  <?php if (count($recent) === 0): ?>
    <p>Sin eventos.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Evento</th><th>Email</th><th>Resultado</th><th>Mensaje</th><th>Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($recent as $row): ?>
        <tr>
          <td><?php echo h($row['evento'] ?? ''); ?></td>
          <td><?php echo h($row['email'] ?? ''); ?></td>
          <td><span class="badge <?php echo ($row['resultado'] ?? '') === 'ok' ? 'ok' : 'err'; ?>"><?php echo h($row['resultado'] ?? ''); ?></span></td>
          <td><?php echo h($row['mensaje'] ?? ''); ?></td>
          <td><?php echo h($row['created_at'] ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php admin_layout_end(); ?>
