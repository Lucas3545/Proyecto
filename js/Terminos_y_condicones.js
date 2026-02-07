document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.formulario').addEventListener('submit', function(event) {
            const acuerdoSi = document.querySelector('input[name="acuerdo"][value="si"]');
            if (!acuerdoSi.checked) {
                event.preventDefault();
                alert('Debes aceptar los términos y condiciones para continuar.');
            }
        });
    });