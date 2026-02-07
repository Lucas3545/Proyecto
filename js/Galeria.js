const formulario = document.getElementById('formulario-envio');
        const envios = document.getElementById('envios');
        let listaEnvios = [];

        formulario.addEventListener('submit', function(e) {
            e.preventDefault();
            const fotoInput = document.getElementById('foto');
            const comentarioInput = document.getElementById('comentario');
            const file = fotoInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                listaEnvios.push({
                    imagen: event.target.result,
                    comentario: comentarioInput.value
                });
                renderEnvios();
                formulario.reset();
            };
            reader.readAsDataURL(file);
        });

        function renderEnvios() {
            envios.innerHTML = '';
            listaEnvios.forEach((envio, idx) => {
                const div = document.createElement('div');
                div.className = 'envio';
                div.innerHTML = `
                    <img src="${envio.imagen}" alt="foto subida">
                    <div class="comentario" contenteditable="false">${envio.comentario}</div>
                    <button onclick="editarEnvio(${idx}, this)">Editar</button>
                    <button onclick="borrarEnvio(${idx})">Borrar</button>
                `;
                envios.appendChild(div);
            });
        }

        window.borrarEnvio = function(idx) {
            listaEnvios.splice(idx, 1);
            renderEnvios();
        };

        window.editarEnvio = function(idx, btn) {
            const envioDiv = btn.parentElement;
            const comentarioDiv = envioDiv.querySelector('.comentario');
            if (btn.textContent === 'Editar') {
                comentarioDiv.contentEditable = "true";
                comentarioDiv.focus();
                btn.textContent = 'Guardar';
            } else {
                comentarioDiv.contentEditable = "false";
                listaEnvios[idx].comentario = comentarioDiv.textContent;
                btn.textContent = 'Editar';
            }
        };