<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones Personalizadas - Luke's House</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./Front/css/estilos_index.css">
    <link rel="stylesheet" href="./Front/css/ai-chatbot.css">
    <link rel="stylesheet" href="./Front/css/ai-recommendations.css">
    <link type="image/webp" rel="icon" href="./img/logo de luke's huse casa tranquila.webp">
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
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Luke's House</div>
            <ul class="nav-links">
                <li><a class="navbar-link" href="index.php">Inicio</a></li>
                <li><a class="navbar-link" href="informacion.php">InformaciÃ³n</a></li>
                <li><a class="navbar-link" href="Galeria.php">GalerÃ­a</a></li>
                <li><a class="navbar-link" href="Calendario.php">Reservar</a></li>
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
                <p>Planifica tu viaje completo con recomendaciones dÃ­a por dÃ­a</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"></div>
                <h3>Consejos Locales</h3>
                <p>InformaciÃ³n actualizada sobre las mejores atracciones de La Fortuna</p>
            </div>
        </div>

        <div id="recommendations-widget"></div>
    </div>

    <?php include './Back/PHP/includes/footer.php'; ?>

    <script src="./back/PHP/js/ai-chatbot.js"></script>
    <script src="./back/PHP/js/ai-recommendations.js"></script>
    <script src="./back/PHP/js/ai-config.js"></script>

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

