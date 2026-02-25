//area de validacio de tarjeta
// este sistema va verifiar que la tarjeta sea valida y se pueda usar para pagar en linea

function validarTarjeta(numero) {
    numero = numero.replace(/\s+/g, '');
    if (!/^\d{13,19}$/.test(numero)) return false;
    return luhnCheck(numero);
}

function luhnCheck(numero) {
    let suma = 0;
    let alternar = false;
    for (let i = numero.length - 1; i >= 0; i--) {
        let digito = parseInt(numero.charAt(i), 10);
        if (alternar) {
            digito *= 2;
            if (digito > 9) digito -= 9;
        }
        suma += digito;
        alternar = !alternar;
    }
    return suma % 10 === 0;
}
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.querySelector('.formulario-tarjeta');
    formulario.addEventListener('submit', function (event) {
        event.preventDefault();
        const numero = formulario.numero.value;
        if (validarTarjeta(numero)) {
            alert('Tarjeta válida');
        } else {
            alert('Tarjeta inválida');
        }
    });
});
//enviar datos a la base de datos
function enviarDatos(numero, esValida, tipo) {
    fetch('../enviar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            numero_tarjeta: numero,
            es_valida: esValida,
            tipo_tarjeta: tipo
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Éxito:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}