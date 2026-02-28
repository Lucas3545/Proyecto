<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/config.php';

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
$userKeySource = $email !== '' ? $email : $displayName;
$userKey = trim((string) preg_replace('/[^a-z0-9]+/i', '_', strtolower($userKeySource)), '_');

$ownerEmail = strtolower(trim((string) ($OWNER_EMAIL ?: 'lucaszv2006@gmail.com')));
$isOwner = $isLoggedIn && $email !== '' && strcasecmp($email, $ownerEmail) === 0;
