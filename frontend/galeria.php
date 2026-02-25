<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/estilos-galeria.css">
    <title>Galería</title>
    <link rel="icon" href="./img/logo-de-lukes-house-casa-tranquila.webp" type="image/webp">
</head>

<body>
    <header>
        <h1>Galería</h1>
    </header>
    <main>
        <div class="header">
            <?php include './includes/navbar-simple.php'; ?>
        </div>
        <div class="galeria">
            <img src="./img/cabanita/dsc1270.jpg" alt="img1">
            <img src="./img/cabanita/dsc1273.jpg" alt="img2">
            <img src="./img/cabanita/dsc1275.jpg" alt="img3">
            <img src="./img/cabanita/dsc1277.jpg" alt="img4">
        </div>

        <div class="galeria">
            <img src="./img/cabanita/dsc1278.jpg" alt="img1">
            <img src="./img/cabanita/dsc1279.jpg" alt="img2">
            <img src="./img/cabanita/dsc1280.jpg" alt="img3">
            <img src="./img/cabanita/dsc1281.jpg" alt="img4">
        </div>
        <form class="formulario" id="formulario-envio">
            <h2>Sube tu foto y deja tu comentario</h2>
            <input type="file" id="foto" accept="image/*">
            <textarea id="comentario" rows="2" placeholder="Escribe un comentario" required></textarea>
            <button type="submit">Enviar</button>
        </form>

        <div class="envios" id="envios"></div>
    </main>

    <?php include './includes/footer.php'; ?>
    <script src="./js/galeria.js"></script>
</body>

</html>
