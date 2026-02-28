<?php
$pageLang = $pageLang ?? 'es';
$pageTitle = $pageTitle ?? "Luke's House Casa Tranquila";
$pageStyles = $pageStyles ?? [];
$pageExtraHead = $pageExtraHead ?? '';
$pageBodyAttrs = $pageBodyAttrs ?? '';

if (!isset($pageFavicon)) {
    $pageFavicon = './img/logo-de-lukes-house-casa-tranquila.webp';
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($pageLang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php foreach ($pageStyles as $href): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endforeach; ?>
    <?php if (!empty($pageFavicon)): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($pageFavicon, ENT_QUOTES, 'UTF-8'); ?>" type="image/webp">
    <?php endif; ?>
    <?php if (!empty($pageExtraHead)) { echo $pageExtraHead; } ?>
</head>
<body<?php echo $pageBodyAttrs !== '' ? ' ' . $pageBodyAttrs : ''; ?>>
