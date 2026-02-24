<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Calendario de Reservas</title>
    <link rel="stylesheet" href="./Front/css/estilos_calen.css">
    <link rel="icon" href="./img/logo de luke's huse casa tranquila.webp">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <header>
        <h1 class="calendar-title">Calendario de Reservas</h1>
    </header>
    <main>
        <details>
            <summary>&#9776;</summary>
            <li><a href="index.php" class="nav-item" title="Inicio">âªInicio</a></li>
            <li><a href="panel_de_acceso.php" class="nav-item" title="Panel de Control">ðŸŸ©Panel de Acceso</a></li>
            <li><a href="informacion.php" class="nav-item" title="InformaciÃ³n">ðŸ—„ï¸InformaciÃ³n</a></li>
            <li><a href="Galeria.php" class="nav-item" title="Galeria">ðŸ–¼ï¸Galeria</a></li>
            <li><a href="mailto:lucaszv2006@gmail.com" class="nav-item">ðŸ“§gmail</a></li>
            <li><a href="tel:+50683256836" class="nav-item" title="Contacto">ðŸ“žContacto</a></li>

        </details>
        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevMonth">&lt;</button>
                <span id="monthYear"></span>
                <button id="nextMonth">&gt;</button>
            </div>
            <div class="calendar-grid" id="calendarDays"></div>
            <div class="calendar-grid" id="calendar"></div>
            <div class="reservation-form" id="reservationForm" style="display:none;">
                <h3>Reservar noche</h3>
                <p id="selectedDate"></p>
                <input type="text" id="nombre" placeholder="Tu nombre" required>
                <input type="email" id="email" placeholder="Tu correo" required>
                <button id="confirmReservation">Confirmar Reserva</button>
                
            </div>
        </div>
    </main>
    <?php include './back/PHP/includes/footer.php'; ?>
    <script>
        let unavailableDates = [];
        let selectedCell = null;
        let selectedDate = null;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        const API_URL = './back/PHP/reservas.php';

        const monthNames = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        async function cargarReservas(month, year) {
            try {
                const response = await fetch(`${API_URL}?mes=${month + 1}&anio=${year}`);
                const data = await response.json();
                if (data.success) {
                    unavailableDates = data.reservas.map(r => r.fecha);
                } else {
                    throw new Error(data.message || 'No se pudieron cargar las reservas');
                }
            } catch (error) {
                console.error('Error al cargar reservas:', error);
            }
        }

        async function renderCalendar(month, year) {
            await cargarReservas(month, year);

            document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;
            const daysGrid = document.getElementById('calendarDays');
            const calendarGrid = document.getElementById('calendar');
            daysGrid.innerHTML = '';
            calendarGrid.innerHTML = '';

            const days = ['Dom', 'Lun', 'Mar', 'MiÃ©', 'Jue', 'Vie', 'SÃ¡b'];
            days.forEach(d => {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                dayDiv.textContent = d;
                daysGrid.appendChild(dayDiv);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'calendar-cell';
                emptyCell.style.visibility = 'hidden';
                calendarGrid.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const cell = document.createElement('div');
                cell.className = 'calendar-cell';
                cell.textContent = day;

                if (unavailableDates.includes(dateStr)) {
                    cell.classList.add('unavailable');
                } else {
                    cell.addEventListener('click', function () {
                        if (selectedCell) selectedCell.classList.remove('selected');
                        cell.classList.add('selected');
                        selectedCell = cell;
                        selectedDate = dateStr;
                        document.getElementById('reservationForm').style.display = 'block';
                        document.getElementById('selectedDate').textContent = `Fecha seleccionada: ${day} de ${monthNames[month]} de ${year}`;
                        document.getElementById('successMsg').style.display = 'none';
                    });
                }
                calendarGrid.appendChild(cell);
            }
        }

        document.getElementById('prevMonth').onclick = function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
            document.getElementById('reservationForm').style.display = 'none';
        };
        document.getElementById('nextMonth').onclick = function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
            document.getElementById('reservationForm').style.display = 'none';
        };

        document.getElementById('confirmReservation').onclick = async function () {
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            if (nombre && email && selectedDate) {
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ fecha: selectedDate, nombre, email })
                    });
                    if (!response.ok) {
                        throw new Error(HTTP );
                    }
                    const data = await response.json();
                    if (data.success) {
                        await renderCalendar(currentMonth, currentYear);
                        document.getElementById('reservationForm').style.display = 'block';
                        document.getElementById('successMsg').style.display = 'block';
                        document.getElementById('nombre').value = '';
                        document.getElementById('email').value = '';
                    } else {
                        alert(data.message || 'Error al realizar la reserva');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'Error de conexión al servidor');
                }
            }
        };

        renderCalendar(currentMonth, currentYear);
    </script>
</body>

</html>

