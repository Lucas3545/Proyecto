<?php
require_once __DIR__ . '/includes/config.php';

$dbError = null;
$dbStats = [
    'users' => 0,
    'reservations' => 0,
    'cards' => 0,
];
$nextReservations = [];

try {
    $dbHost = $DB_CONSUSLT ?? $DB_CONSULT ?? $DB_HOSTNAME;
    $dbName = $DB_TEXT ?? $DB_NAME;
    $dbPass = $D_ANSWER ?? $DB_ANSWER ?? $DB_PASSWORD;
    $dbUser = $DB_USERNAME ?? 'root';

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        throw new RuntimeException('Error de conexion: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');

    $usersResult = $conn->query('SELECT COUNT(*) AS total FROM users');
    if ($usersResult && $row = $usersResult->fetch_assoc()) {
        $dbStats['users'] = (int) $row['total'];
    }

    $reservationsResult = $conn->query('SELECT COUNT(*) AS total FROM reservations');
    if ($reservationsResult && $row = $reservationsResult->fetch_assoc()) {
        $dbStats['reservations'] = (int) $row['total'];
    }

    $cardsResult = $conn->query('SELECT COUNT(*) AS total FROM cards');
    if ($cardsResult && $row = $cardsResult->fetch_assoc()) {
        $dbStats['cards'] = (int) $row['total'];
    }

    $nextReservationsQuery = "
        SELECT nombre, fecha, estado
        FROM reservations
        WHERE fecha >= CURDATE()
        ORDER BY fecha ASC
        LIMIT 5
    ";

    $nextReservationsResult = $conn->query($nextReservationsQuery);
    if ($nextReservationsResult) {
        while ($reservation = $nextReservationsResult->fetch_assoc()) {
            $nextReservations[] = $reservation;
        }
    }

    $conn->close();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

?>
<!DOCTYPE html>

<script>
  window.RECOMMENDATIONS_API_ENDPOINT = './recomendaciones-data.php';
</script>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones Personalizadas - Luke's House</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./css/estilos-index.css">
    <link rel="stylesheet" href="./css/ai-chatbot.css">
    <link rel="stylesheet" href="./css/ai-recommendations.css">
    <link type="image/webp" rel="icon" href="./img/logo-de-lukes-house-casa-tranquila.webp">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .hero-section p {
            font-size: 20px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-bottom: 32px;
        }

        .back-button:hover {
            background: #667eea;
            color: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 40px 0;
        }

        .feature-card {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .feature-card h3 {
            color: #2d3748;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #718096;
            line-height: 1.6;
        }

        .db-panel {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
        }

        .db-panel h2 {
            color: #2d3748;
            margin-top: 0;
            margin-bottom: 16px;
        }

        .db-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .db-stat {
            background: white;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #edf2f7;
        }

        .db-stat strong {
            color: #4a5568;
            display: block;
            margin-bottom: 6px;
        }

        .db-stat span {
            font-size: 26px;
            font-weight: 700;
            color: #667eea;
        }

        .reservation-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 10px;
        }

        .reservation-list li {
            background: white;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            padding: 12px;
            color: #4a5568;
        }

        .db-error {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 10px;
            color: #c53030;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .db-context-inline {
            background: #edf2f7;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #2d3748;
            font-size: 14px;
            margin: 0 0 16px 0;
            padding: 10px 12px;
        }

        .db-context-inline p {
            margin: 4px 0;
        }

        .db-context-error {
            color: #c53030;
            margin: 0;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Luke's House</div>
            <ul class="nav-links">
                <li><a class="navbar-link" href="index.php">Inicio</a></li>
                <li><a class="navbar-link" href="informacion.php">Informacion</a></li>
                <li><a class="navbar-link" href="galeria.php">Galeri­a</a></li>
                <li><a class="navbar-link" href="calendario.php">Reservar</a></li>
                <li><a class="navbar-link" href="recomendaciones.php" id="chatbot-shortcut" title="Chat de Ayuda"><i class="fas fa-comments"></i></a></li>
            </ul>
        </nav>
    </header>

    <div class="hero-section">
        <h1>Encuentra tu Aventura Perfecta</h1>
        <p>Recomendaciones personalizadas con Inteligencia Artificial</p>
    </div>

    <div class="container">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Volver al Inicio
        </a>

        <?php if ($dbError !== null): ?>
            <div class="db-error">
                No se pudo conectar a la base de datos <strong><?= htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8'); ?></strong>.
                Detalle: <?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php else: ?>
            <section class="db-panel">
                <h2>Datos en tiempo real desde MySQL</h2>
                <div class="db-stats">
                    <article class="db-stat">
                        <strong>Usuarios registrados</strong>
                        <span><?= $dbStats['users']; ?></span>
                    </article>
                    <article class="db-stat">
                        <strong>Reservas totales</strong>
                        <span><?= $dbStats['reservations']; ?></span>
                    </article>
                    <article class="db-stat">
                        <strong>Tarjetas guardadas</strong>
                        <span><?= $dbStats['cards']; ?></span>
                    </article>
                </div>

                <h3>Próximas reservas</h3>
                <?php if (count($nextReservations) > 0): ?>
                    <ul class="reservation-list">
                        <?php foreach ($nextReservations as $reservation): ?>
                            <li>
                                <strong><?= htmlspecialchars($reservation['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                - <?= htmlspecialchars($reservation['fecha'], ENT_QUOTES, 'UTF-8'); ?>
                                (<?= htmlspecialchars($reservation['estado'], ENT_QUOTES, 'UTF-8'); ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay reservas futuras registradas.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"></div>
                <h3>IA Personalizada</h3>
                <p>Nuestro sistema de IA analiza tus preferencias para sugerirte las mejores actividades</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"></div>
                <h3>Actividades Variadas</h3>
                <p>Desde aventuras extremas hasta experiencias relajantes en la naturaleza</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"></div>
                <h3>Itinerarios Completos</h3>
                <p>Planifica tu viaje completo con recomendaciones dia por dia</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"></div>
                <h3>Consejos Locales</h3>
                <p>Informacion actualizada sobre las mejores atracciones de La Fortuna</p>
            </div>
        </div>

        <div id="recommendations-widget"></div>
    </div>

    <?php include './includes/footer.php'; ?>

    <script>
        window.RECOMMENDATIONS_API_ENDPOINT = './recomendaciones-data.php';
    </script>
    <script src="./js/ai-chatbot.js"></script>
    <script src="./js/ai-recommendations.js"></script>
    <script src="./js/ai-config.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.aiRecommendations) {
                window.aiRecommendations.createRecommendationWidget('recommendations-widget');
            } else {
                console.error('Sistema de recomendaciones no disponible');
            }

            const chatbotShortcut = document.getElementById('chatbot-shortcut');
            if (chatbotShortcut) {
                chatbotShortcut.addEventListener('click', function(e) {
                    e.preventDefault();
                    const chatbotToggle = document.getElementById('chatbot-toggle');
                    if (chatbotToggle) {
                        chatbotToggle.click();
                    }
                });
            }
        });
    </script>
</body>

</html>
