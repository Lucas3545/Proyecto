<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./img/logo de luke's huse casa tranquila.webp">
    <link rel="stylesheet" href="./Front/css/Seleccion_de_metodo_de_pago.css">
    <title>Metodo de Pago</title>
</head>

<body class="contenedor">

    <div class="header">
        <?php include './back/PHP/includes/navbar_simple.php'; ?>
    </div>

    <h3 class="titulo_principal">Nueva reserva</h3>

    <label for="slcPago">MÃ©todo de pago:</label>
    <select id="slcPago">
        <option value="0">-- Elegi­ una opcion --</option>
        <option value="tarjeta">Tarjeta</option>
        <option value="transferencia">Transferencia</option>
        <option value="efectivo">Efectivo</option>
        <option value="paypal">PayPal</option>
    </select>

    <hr>

    <div id="blkTarjeta" class="panel oculto">
        <h4>Pago con tarjeta</h4>
        <ul>
            <li>Se acepta VISA/Mastercard.</li>
            <li>ComisiÃ³n 3.5%.</li>
            <li>ConfirmaciÃ³n inmediata.</li>
        </ul>
    </div>

    <div id="blkTransferencia" class="panel oculto">
        <h4>Transferencia bancaria</h4>
        <ul>
            <li>Banco: CR-123456-001</li>
            <li>Adjuntar comprobante.</li>
            <li>AcreditaciÃ³n 24â€“48 h.</li>
        </ul>
    </div>

    <div id="blkEfectivo" class="panel oculto">
        <h4>Efectivo en oficina</h4>
        <ul>
            <li>Horarios: Lunâ€“Vie 8:00â€“18:00</li>
            <li>DirecciÃ³n: San Pedro de la Tigra, Alajuela, Costa Rica</li>
            <li>Reserva vÃ¡lida 24 h.</li>
        </ul>
    </div>

    <div id="blkPaypal" class="panel oculto">
        <h4>Pago con PayPal</h4>
        <ul>
            <li>ComisiÃ³n 4%.</li>
            <li>ConfirmaciÃ³n inmediata.</li>
        </ul>
    </div>

    <div class="panel_interectivo_tarjeta" id="blkTarjetaPanel">
        <h4>IngresÃ¡ los datos de tu tarjeta</h4>
        <form id="formTarjeta" action="./back/PHP/procesar_tarjeta.php" method="POST">
            <br>
            <label for="txtNombre">Nombre en la tarjeta:</label>
            <input type="text" id="txtNombre" placeholder="Nombre completo" name="nombre_tarjeta" required>
            <br>
            <label for="txtNumero">NÃºmero de tarjeta:</label>
            <input type="text" id="txtNumero" placeholder="XXXX-XXXX-XXXX-XXXX" name="numero" required>
            <br>
            <label for="txtVencimiento">Fecha de vencimiento:</label>
            <input type="text" id="txtVencimiento" placeholder="MM/YYYY" name="vencimiento" required>
            <br>
            <label for="txtCVV">CVV:</label>
            <input type="text" id="txtCVV" placeholder="XXX" name="cvv" required>
            <br><br>
            <button type="submit">Pagar</button>
        </form>
    </div>

    <div class="panel_interectivo_transferencia" id="blkTransferenciaPanel">
        <h4>Instrucciones para transferencia bancaria</h4>
        <p>Por favor, realiza la transferencia a la cuenta indicada y envi­a el comprobante a nuestro correo
            electrÃ³nico.<strong> (lukeshouse@gmail.com)</strong>, y hacer el simpe al
            <strong>8678-7471</strong>
        </p>
    </div>

    <div class="panel_interectivo_efectivo" id="blkEfectivoPanel">
        <h4>Instrucciones para pago en efectivo</h4>
        <p>Visita nuestra oficina en el horario indicado para completar tu pago en efectivo.</p>
    </div>

    <div class="panel_interectivo_paypal" id="blkPaypalPanel">
        <h4>Instrucciones para pago con PayPal</h4>
        <p>SerÃ¡s redirigido a la plataforma de PayPal para completar tu pago de manera segura.</p>
        <button onclick="window.location.href='https://www.paypal.com/us/home';">
            Ir a PayPal
        </button>

    </div>
    <script src="./back/PHP/js/Seleccion_de_metodo_de_pago.js"></script>

    <?php include './back/PHP/includes/footer.php'; ?>
</body>

</html>

