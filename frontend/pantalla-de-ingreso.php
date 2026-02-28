<?php
$pageTitle = 'Pantalla de Ingreso';
$pageStyles = ['./css/estilos-pantalla.css'];
$pageExtraHead = <<<'HTML'
<link rel="preconnect" href="https://fonts.googleapis.com">
HTML;
include __DIR__ . '/includes/page-start.php';
?>
    <div class="splash" id="splashScreen">
        <div class="welcome-title">Welcome to Luke&#39;s house</div>
        <div class="subtitle">Casa Tranquila</div>
    </div>
    <script src="./js/pantalla-de-ingreso.js"></script>
<?php include __DIR__ . '/includes/page-end.php'; ?>





