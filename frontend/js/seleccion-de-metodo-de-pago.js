const pago = document.getElementById("slcPago");
const tarjeta = document.getElementById("blkTarjeta");
const paypal = document.getElementById("blkPaypal");

pago.addEventListener("change", () => {
    tarjeta.classList.add("oculto");
    paypal.classList.add("oculto");

    switch (pago.value) {
        case "tarjeta":
            tarjeta.classList.remove("oculto");
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
