<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./img/logo de luke's huse casa tranquila.webp">
    <link rel="stylesheet" href="./Front/css/Seleccion_de_metodo_de_pago.css">
    <title>Metodo de Pago</title>
</head>

<body class="contenedor">

    <div class="header">
        <?php include './Front/PHP/includes/navbar_simple.php'; ?>
    </div>

    <h3 class="titulo_principal">Nueva reserva</h3>

    <label for="slcPago">Método de pago:</label>
    <select id="slcPago">
        <option value="0">-- Elegí una opción --</option>
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
            <li>Comisión 3.5%.</li>
            <li>Confirmación inmediata.</li>
        </ul>
    </div>

    <div id="blkTransferencia" class="panel oculto">
        <h4>Transferencia bancaria</h4>
        <ul>
            <li>Banco: CR-123456-001</li>
            <li>Adjuntar comprobante.</li>
            <li>Acreditación 24–48 h.</li>
        </ul>
    </div>

    <div id="blkEfectivo" class="panel oculto">
        <h4>Efectivo en oficina</h4>
        <ul>
            <li>Horarios: Lun–Vie 8:00–18:00</li>
            <li>Dirección: San Pedro de la Tigra, Alajuela, Costa Rica</li>
            <li>Reserva válida 24 h.</li>
        </ul>
    </div>

    <div id="blkPaypal" class="panel oculto">
        <h4>Pago con PayPal</h4>
        <ul>
            <li>Comisión 4%.</li>
            <li>Confirmación inmediata.</li>
        </ul>
    </div>

    <div class="panel_interectivo_tarjeta" id="blkTarjeta">
        <h4>Ingresá los datos de tu tarjeta</h4>
        <form id="formTarjeta" action="./PHP/procesar_tarjeta.php" method="POST">
            <br>
            <label for="txtNombre">Nombre en la tarjeta:</label>
            <input type="text" id="txtNombre" placeholder="Nombre completo" name="register-nombre">
            <br>
            <label for="txtNumero">Número de tarjeta:</label>
            <input type="text" id="txtNumero" placeholder="XXXX-XXXX-XXXX-XXXX" name="register-numero">
            <br>
            <label for="txtVencimiento">Fecha de vencimiento:</label>
            <input type="text" id="txtVencimiento" placeholder="MM/AA" name="register-vencimiento">
            <br>
            <label for="txtCVV">CVV:</label>
            <input type="text" id="txtCVV" placeholder="XXX" name="register-cvv">
            <br><br>
            <button type="submit">Pagar</button>
        </form>
    </div>

    <div class="panel_interectivo_transferencia" id="blkTransferencia">
        <h4>Instrucciones para transferencia bancaria</h4>
        <p>Por favor, realiza la transferencia a la cuenta indicada y envía el comprobante a nuestro correo
            electrónico.<strong> (lukeshouse@gmail.com)</strong>, y hacer el simpe al
            <strong>8678-7471</strong>
        </p>
    </div>

    <div class="panel_interectivo_efectivo" id="blkEfectivo">
        <h4>Instrucciones para pago en efectivo</h4>
        <p>Visita nuestra oficina en el horario indicado para completar tu pago en efectivo.</p>
    </div>

    <div class="panel_interectivo_paypal" id="blkPaypal">
        <h4>Instrucciones para pago con PayPal</h4>
        <p>Serás redirigido a la plataforma de PayPal para completar tu pago de manera segura.</p>
        <button onclick="window.location.href='https://www.paypal.com/cr/home';">
            Ir a PayPal
        </button>

    </div>
    <script src="./js/Seleccion_de_metodo_de_pago.js"></script>

    <?php include './Front/PHP/includes/footer.php'; ?>
</body>

</html>
