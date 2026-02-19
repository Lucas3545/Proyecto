<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Luke's House Casa Tranquila</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./Front/css/estilos_index.css">
    <link rel="stylesheet" href="./Front/css/ai-chatbot.css">
    <link rel="stylesheet" href="./Front/css/ai-recommendations.css">
    <link type="image/webp" rel="icon" href="./img/logo de luke's huse casa tranquila.webp">
</head>

<body>
    <header>
        <div>
            <h1>Bienvenido a Luke's House</h1>
            <p class="description">Casa Tranquila - Tu refugio en la naturaleza</p>
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
        </div>
        <section>
            <button class="btn-reserve" onclick="location.href='Calendario.php'"><strong>Reservar
                    Ahora</strong></button>
        </section>
        <?php include './Front/PHP/includes/navbar_main.php'; ?>
    </header>
    <main class="container">
        <div class="principal-card">
            <div class="card-image" style="background-image: url(./img/CabaÃ±ita/_DSC1273.jpg);"></div>
            <div class="card-content">
                <h2 class="card-title">Welcome to Luke's Tiny House</h2>
                <p class="card-description">Disfruta una estancia Ãºnica; patio de 1 acre visitado por perezosos,
                    tucanes, ranas, colibrÃ­es, polinizadores, etc. En las tardes y maÃ±anas talvez escuches monos
                    aulladores.
                    Si caminas un poco, es posible que veas perezosos y algunos animales silvestres, Â¡nunca alimentes a
                    ninguno! <br>
                    La casa es ideal para compartir, pasear, hacer ejercicio, leer, pintar, acampar en verano ya que
                    cuenta con amplio terreno y darte unos dÃ­as de descanso en este espacio rural.
                    Necesitas 30 minutos para ir a Fortuna.<br></p>
            </div>
        </div>

        <section>
            <div class="banner"
                style="background-image: url(./img/Fortuna/aldea-la-fortuna-costa-rica-vista-aÃ©rea-de-ciudad-y-iglesia-en-plaza-parque-central-167023541.webp);">
            </div>
            <h2>Lugares a Visitar en La Fortuna</h2>
            <p>Descubre la belleza natural y las actividades emocionantes que La Fortuna tiene para ofrecer.</p>
        </section>
        <section class="info-section">
            <div class="info-card">
                <div class="info-title">Mistico Park</div>
                <div class="info-desc">Explora los famosos puentes colgantes y la biodiversidad de Costa Rica en un
                    entorno natural Ãºnico.</div>
            </div>
            <div class="info-card">
                <div class="info-title">RÃ­o Celeste</div>
                <article class="info-desc">Descubre el rÃ­o de color turquesa y disfruta de senderismo en el Parque
                    Nacional VolcÃ¡n Tenorio.</article>
            </div>
            <div class="info-card">
                <div class="info-title">Guanacaste</div>
                <article class="info-desc">Playas, atardeceres y naturaleza en la provincia mÃ¡s extensa y menos poblada
                    de Costa Rica.</article>
            </div>
            <div class="info-card">
                <div class="info-title">La Fortuna</div>
                <article class="info-desc">Pueblo tranquilo, volcÃ¡n Arenal y actividades turÃ­sticas para todos los
                    gustos.</article>
            </div>
            <div class="info-card">
                <div class="info-title">Cerro Chato</div>
                <article class="info-desc">SumÃ©rgete en la diversidad de flora y fauna explorando el volcÃ¡n y sus
                    tesoros ocultos.</article>
            </div>
            <div class="info-card">
                <div class="info-title">Catarata de la Fortuna</div>
                <article class="info-desc">Admira la impresionante cascada y disfruta de un baÃ±o refrescante en aguas
                    cristalinas.</article>
            </div>
            <div class="info-card">
                <div class="info-title">Parque Nacional Manuel Antonio</div>
                <article class="info-desc">Disfruta de playas paradisÃ­acas y una rica biodiversidad en uno de los
                    parques mÃ¡s visitados.</article>
            </div>
        </section>

        <seccion>
            </div>
        </seccion>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
        </section>
    </main>
    <?php include './Back/PHP/includes/footer.php'; ?>
    
    <script src="./back/PHP/js/ai-chatbot.js"></script>
    <script src="./back/PHP/js/ai-recommendations.js"></script>
    <script src="./back/PHP/js/ai-config.js"></script>
    <script src="./js/index.js"></script>
</body>

</html>

