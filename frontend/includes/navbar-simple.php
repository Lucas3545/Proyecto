<?php
$displayName = '';
$email = '';

if (!empty($_COOKIE['lh_user'])) {
    $displayName = trim((string) $_COOKIE['lh_user']);
} elseif (!empty($_SESSION['username'])) {
    $displayName = trim((string) $_SESSION['username']);
}

if (!empty($_COOKIE['lh_email'])) {
    $email = trim((string) $_COOKIE['lh_email']);
} elseif (!empty($_SESSION['user_email'])) {
    $email = trim((string) $_SESSION['user_email']);
}

$isLoggedIn = $displayName !== '';
$ownerEmail = 'lucaszv2006@gmail.com';
$isOwner = $isLoggedIn && strcasecmp($email, $ownerEmail) === 0;
?>

<nav class="nav-menu" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
    <a href="index.php" class="nav-item" title="Inicio">Inicio</a>
    <a href="informacion.php" class="nav-item" title="Informacion">Informacion</a>

    <?php if (!$isLoggedIn): ?>
        <a href="galeria.php" class="nav-item" title="Galeria">Galeria</a>
        <a href="panel-de-acceso.php" class="nav-item" title="Panel de acceso">Panel de acceso</a>
    <?php else: ?>
        <a href="galeria.php" class="nav-item" title="Galeria">Galeria</a>
        <a href="seleccion-de-metodo-de-pago.php" class="nav-item" title="Metodo de pago">Metodo de pago</a>
        <a href="recomendaciones.php" class="nav-item" title="Recomendaciones">Recomendaciones</a>
        <?php if ($isOwner): ?>
            <a href="Panel-admin.php" class="nav-item" title="Panel admin">Panel admin</a>
        <?php endif; ?>
        <a href="terminos-y-condiciones.php" class="nav-item" title="Terminos y condiciones">Terminos</a>
    <?php endif; ?>
</nav>

<?php if ($isLoggedIn): ?>
    <div style="position:fixed;top:10px;right:12px;z-index:2000;background:#ffffffcc;border:1px solid #ccc;border-radius:999px;padding:8px 12px;font-size:13px;display:flex;gap:10px;align-items:center;">
        <strong>Perfil:</strong>
        <span title="<?php echo htmlspecialchars($email !== '' ? $email : $displayName, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></span>
        <a href="logout.php" style="text-decoration:none;">Salir</a>
    </div>
<?php endif; ?>

<?php if ($isOwner): ?>
    <a href="Panel-admin.php" style="position:fixed;top:10px;right:220px;z-index:2100;background:#111827;color:#fff;text-decoration:none;padding:8px 12px;border-radius:999px;font-size:13px;">Admin</a>
<?php endif; ?>
