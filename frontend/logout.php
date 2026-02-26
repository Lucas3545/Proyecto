<?php
session_start();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();

setcookie('lh_user', '', time() - 3600, '/');
setcookie('lh_email', '', time() - 3600, '/');

header('Location: panel-de-acceso.php');
exit;
