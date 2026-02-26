<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_owner_email() {
    global $OWNER_EMAIL;
    return strtolower(trim((string) ($OWNER_EMAIL ?: 'lucaszv2006@gmail.com')));
}

function admin_current_email() {
    $sessionEmail = strtolower(trim((string) ($_SESSION['user_email'] ?? '')));
    $cookieEmail = strtolower(trim((string) ($_COOKIE['lh_email'] ?? '')));
    return $sessionEmail !== '' ? $sessionEmail : $cookieEmail;
}

function admin_is_owner() {
    return admin_current_email() !== '' && strcasecmp(admin_current_email(), admin_owner_email()) === 0;
}

function admin_require_owner() {
    if (admin_is_owner()) {
        return;
    }

    http_response_code(403);
    echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Acceso denegado</title><style>body{font-family:Arial,sans-serif;background:#f5f7fb;margin:0;display:grid;place-items:center;min-height:100vh}.card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;max-width:460px}a{display:inline-block;margin-top:10px;background:#111827;color:#fff;text-decoration:none;padding:8px 12px;border-radius:8px}</style></head><body><div class="card"><h1>Acceso denegado</h1><p>Solo el propietario puede entrar al panel admin.</p><a href="panel-de-acceso.php">Ir a panel de acceso</a></div></body></html>';
    exit;
}

function admin_db_connect() {
    global $DB_CONSUSLT, $DB_CONSULT, $DB_HOSTNAME, $DB_TEXT, $DB_NAME, $D_ANSWER, $DB_ANSWER, $DB_PASSWORD, $DB_USERNAME;

    $dbHost = $DB_CONSUSLT ?: ($DB_CONSULT ?: $DB_HOSTNAME);
    $dbName = $DB_TEXT ?: $DB_NAME;
    $dbPass = $D_ANSWER ?: ($DB_ANSWER ?: $DB_PASSWORD);
    $dbUser = $DB_USERNAME ?: 'root';

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        throw new RuntimeException('Error de conexion: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function admin_table_exists($conn, $tableName) {
    $safeName = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW TABLES LIKE '{$safeName}'");
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function admin_has_column($conn, $tableName, $columnName) {
    $safeTable = str_replace('`', '``', $tableName);
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM `{$safeTable}` LIKE '{$safeColumn}'");
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function admin_first_table($conn, $tableNames) {
    foreach ($tableNames as $tableName) {
        if (admin_table_exists($conn, $tableName)) {
            return $tableName;
        }
    }
    return null;
}

function admin_layout_start($title, $active) {
    $currentEmail = admin_current_email();
    $ownerEmail = admin_owner_email();
    $items = [
        'panel' => ['Panel', 'Panel-admin.php'],
        'dashboard' => ['Dashboard', 'dashboard.php'],
        'usuarios' => ['Usuarios', 'usuarios.php'],
        'configuracion' => ['Configuracion', 'configuracion.php'],
        'reportes' => ['Reportes', 'reportes.php'],
    ];

    echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>' . h($title) . '</title>';
    echo '<style>';
    echo 'body{margin:0;font-family:Arial,sans-serif;background:#f5f5f5;color:#333}header{background:#111827;color:#fff;padding:1rem}header h1{margin:0;font-size:1.2rem}.meta{font-size:.85rem;opacity:.85;margin-top:.3rem}';
    echo '.sidebar{position:fixed;top:74px;left:0;bottom:0;width:220px;background:#fff;border-right:1px solid #ddd;padding:1rem;overflow:auto}.menu{display:flex;flex-direction:column;gap:8px}.menu a{display:block;padding:.55rem .65rem;border-radius:6px;text-decoration:none;color:#333}.menu a:hover{background:#eceff1}.menu a.active{background:#dbeafe;color:#1e40af;font-weight:700}';
    echo 'main{margin-left:240px;padding:1.2rem}.card{background:#fff;border:1px solid #ddd;border-radius:8px;padding:1rem;margin-bottom:1rem}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:10px}.k{font-size:.85rem;color:#6b7280}.v{font-size:1.3rem;font-weight:700}table{width:100%;border-collapse:collapse;font-size:.92rem}th,td{padding:8px;border-bottom:1px solid #e5e7eb;text-align:left}th{color:#6b7280}.badge{display:inline-block;padding:2px 8px;border-radius:999px;border:1px solid #ddd;font-size:.75rem}.ok{background:#f0fdf4;border-color:#86efac}.err{background:#fef2f2;border-color:#fca5a5}';
    echo '@media(max-width:820px){.sidebar{position:static;width:auto;border-right:none;border-bottom:1px solid #ddd}.sidebar{top:0}main{margin-left:0;padding:1rem}}';
    echo '</style></head><body>';

    echo '<header><h1>' . h($title) . '</h1><div class="meta">Sesion: ' . h($currentEmail !== '' ? $currentEmail : 'sin sesion') . ' | Owner: ' . h($ownerEmail) . '</div></header>';

    echo '<aside class="sidebar"><nav class="menu">';
    foreach ($items as $key => $item) {
        $class = $active === $key ? 'active' : '';
        echo '<a class="' . $class . '" href="' . h($item[1]) . '">' . h($item[0]) . '</a>';
    }
    echo '<a href="logout.php">Cerrar Sesion</a>';
    echo '</nav></aside><main>';
}

function admin_layout_end() {
    echo '</main></body></html>';
}
?>
