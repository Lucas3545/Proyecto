<?php
require_once __DIR__ . '/admin-common.php';
admin_require_owner();

$error = null;
$users = [];
$totalUsers = 0;

try {
    $conn = admin_db_connect();
    $usersTable = admin_first_table($conn, ['users']);

    if ($usersTable) {
        $countResult = $conn->query("SELECT COUNT(*) total FROM `{$usersTable}`");
        if ($countResult && $row = $countResult->fetch_assoc()) {
            $totalUsers = (int) $row['total'];
        }

        $hasId = admin_has_column($conn, $usersTable, 'id');
        $hasUsername = admin_has_column($conn, $usersTable, 'username');
        $hasFullname = admin_has_column($conn, $usersTable, 'fullname');
        $hasEmail = admin_has_column($conn, $usersTable, 'email');
        $hasCreatedAt = admin_has_column($conn, $usersTable, 'created_at');

        $selectParts = [];
        $selectParts[] = $hasId ? 'id' : '0 AS id';
        $selectParts[] = $hasUsername ? 'username' : "'' AS username";
        $selectParts[] = $hasFullname ? 'fullname' : "'' AS fullname";
        $selectParts[] = $hasEmail ? 'email' : "'' AS email";
        $selectParts[] = $hasCreatedAt ? 'created_at' : "'' AS created_at";

        if ($hasId) {
            $orderBy = 'id DESC';
        } elseif ($hasCreatedAt) {
            $orderBy = 'created_at DESC';
        } elseif ($hasEmail) {
            $orderBy = 'email ASC';
        } else {
            $orderBy = '1';
        }

        $query = "SELECT " . implode(', ', $selectParts) . " FROM `{$usersTable}` ORDER BY {$orderBy} LIMIT 100";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
    }

    $conn->close();
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_layout_start('Usuarios', 'usuarios');
?>
<div class="card"><h2>Gestion de usuarios</h2><p>Total registrados: <strong><?php echo (int) $totalUsers; ?></strong></p></div>
<?php if ($error !== null): ?><div class="card"><strong>Error DB:</strong> <?php echo h($error); ?></div><?php endif; ?>

<div class="card">
  <?php if (count($users) === 0): ?>
    <p>No hay usuarios registrados.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Registro</th></tr></thead>
      <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo (int) ($user['id'] ?? 0); ?></td>
          <td><?php echo h($user['username'] ?? ''); ?></td>
          <td><?php echo h($user['fullname'] ?? ''); ?></td>
          <td><?php echo h($user['email'] ?? ''); ?></td>
          <td><?php echo h($user['created_at'] ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php admin_layout_end(); ?>
