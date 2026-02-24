const pago = document.getElementById("slcPago");
const tarjeta = document.getElementById("blkTarjeta");
const transferencia = document.getElementById("blkTransferencia");
const efectivo = document.getElementById("blkEfectivo");
const paypal = document.getElementById("blkPaypal");

pago.addEventListener("change", () => {
    tarjeta.classList.add("oculto");
    transferencia.classList.add("oculto");
    efectivo.classList.add("oculto");
    paypal.classList.add("oculto");

    switch (pago.value) {
        case "tarjeta":
            tarjeta.classList.remove("oculto");
            break;
        case "transferencia":
            transferencia.classList.remove("oculto");
            break;
        case "efectivo":
            efectivo.classList.remove("oculto");
            break;
        case "paypal":
            paypal.classList.remove("oculto");
            break;
    }
});

const formularioTarjeta = document.getElementById("formTarjeta");

if (formularioTarjeta) {
    formularioTarjeta.addEventListener("submit", () => {
        console.log("Formulario de tarjeta enviado");
    });
}
