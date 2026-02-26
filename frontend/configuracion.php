<?php
require_once __DIR__ . '/admin-common.php';
admin_require_owner();

$error = null;
$configData = [];
$existingTables = [];

try {
    $conn = admin_db_connect();

    $configData = [
        'host' => $DB_CONSUSLT ?: ($DB_CONSULT ?: $DB_HOSTNAME),
        'database' => $DB_TEXT ?: $DB_NAME,
        'username' => $DB_USERNAME,
        'password_configurada' => (($D_ANSWER ?: ($DB_ANSWER ?: $DB_PASSWORD)) !== '') ? 'si' : 'no',
        'owner_email' => admin_owner_email(),
        'openai_configurada' => (trim((string) ($OPENAI_API_KEY ?? '')) !== '') ? 'si' : 'no',
        'estado_conexion' => 'conectado',
    ];

    $tablesToCheck = ['users', 'access_logs', 'reservations', 'reservas', 'cards', 'ValidacionTarjetas'];
    foreach ($tablesToCheck as $table) {
        if (admin_table_exists($conn, $table)) {
            $existingTables[] = $table;
        }
    }

    $conn->close();
} catch (Throwable $e) {
    $error = $e->getMessage();
    $configData = [
        'host' => $DB_CONSUSLT ?: ($DB_CONSULT ?: $DB_HOSTNAME),
        'database' => $DB_TEXT ?: $DB_NAME,
        'username' => $DB_USERNAME,
        'password_configurada' => (($D_ANSWER ?: ($DB_ANSWER ?: $DB_PASSWORD)) !== '') ? 'si' : 'no',
        'owner_email' => admin_owner_email(),
        'openai_configurada' => (trim((string) ($OPENAI_API_KEY ?? '')) !== '') ? 'si' : 'no',
        'estado_conexion' => 'error',
    ];
}

admin_layout_start('Configuracion', 'configuracion');
?>
<div class="card"><h2>Configuracion de sistema</h2><p>Datos de entorno y estado de servicios.</p></div>
<?php if ($error !== null): ?><div class="card"><strong>Error DB:</strong> <?php echo h($error); ?></div><?php endif; ?>

<div class="card">
  <table>
    <tbody>
      <tr><th>Host</th><td><?php echo h($configData['host'] ?? ''); ?></td></tr>
      <tr><th>Base de datos</th><td><?php echo h($configData['database'] ?? ''); ?></td></tr>
      <tr><th>Usuario DB</th><td><?php echo h($configData['username'] ?? ''); ?></td></tr>
      <tr><th>Password configurada</th><td><?php echo h($configData['password_configurada'] ?? 'no'); ?></td></tr>
      <tr><th>Owner email</th><td><?php echo h($configData['owner_email'] ?? ''); ?></td></tr>
      <tr><th>OpenAI API</th><td><?php echo h($configData['openai_configurada'] ?? 'no'); ?></td></tr>
      <tr><th>Estado conexion DB</th><td><?php echo h($configData['estado_conexion'] ?? 'desconocido'); ?></td></tr>
    </tbody>
  </table>
</div>

<div class="card">
  <h2>Tablas detectadas</h2>
  <?php if (count($existingTables) === 0): ?>
    <p>No se detectaron tablas (o no hay conexion).</p>
  <?php else: ?>
    <ul>
      <?php foreach ($existingTables as $table): ?>
        <li><?php echo h($table); ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php admin_layout_end(); ?>
