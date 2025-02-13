<!DOCTYPE html>
<html>
<head>
    <title>Códigos Postales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Posiciona la hora y la fecha en la esquina superior derecha */
        .fecha-hora {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 14px;
            font-weight: bold;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="fecha-hora">
        <p id="fecha-actual"></p>
        <p id="hora-actual"></p>
    </div>

    <div class="container mt-5">
        <!-- Selects dinámicos -->
        <select id="estado" class="form-control">
            <option value="">Selecciona un estado</option>
            @foreach($estados as $estado)
                <option value="{{ $estado }}">{{ $estado }}</option>
            @endforeach
        </select>

        <select id="municipio" class="form-control mt-2" disabled>
            <option value="">Selecciona un municipio</option>
        </select>

        <select id="colonia" class="form-control mt-2" disabled>
            <option value="">Selecciona una colonia</option>
        </select>

        <p id="codigo-postal" class="mt-3 fw-bold text-primary" style="display: none;">
            Código Postal: <span id="cp-text"></span>
        </p>

        <!-- Botón para guardar consulta -->
        <button id="guardar-consulta" class="btn btn-primary mt-3" disabled>Guardar Consulta</button>

        <!-- Historial de consultas -->
        <div class="mt-5">
            <h4>Historial de Consultas</h4>
            <ul id="historial-consultas" class="list-group">
                @foreach($consultas as $consulta)
                    <li class="list-group-item">
                        {{ $consulta->estado }} - {{ $consulta->municipio }}  - {{ $consulta->colonia }} - {{ $consulta->codigo_postal }}
                        <button class="btn btn-danger btn-sm eliminar-consulta" data-id="{{ $consulta->id }}">Eliminar</button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

<script>

    // Mostrar fecha y hora actual
    function actualizarFecha() {
            const ahora = new Date();
            const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const fechaFormateada = ahora.toLocaleDateString('es-ES', opciones);
            document.getElementById('fecha-actual').innerText = fechaFormateada;
        }

        function actualizarHora() {
            const ahora = new Date();
            const horas = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            const segundos = ahora.getSeconds().toString().padStart(2, '0');
            document.getElementById('hora-actual').innerText = `${horas}:${minutos}:${segundos}`;
        }

        actualizarFecha();
        actualizarHora();
        setInterval(actualizarHora, 1000);

    // Cargar municipios
    document.getElementById('estado').addEventListener('change', function() {
        const estado = this.value;
        fetch(`/municipios/${estado}`)
            .then(response => response.json())
            .then(data => {
                const municipioSelect = document.getElementById('municipio');
                municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
                data.forEach(municipio => {
                    municipioSelect.innerHTML += `<option value="${municipio}">${municipio}</option>`;
                });
                municipioSelect.disabled = false;
                document.getElementById('colonia').disabled = true;
            });
        validarBoton();
    });

    // Cargar colonias
    document.getElementById('municipio').addEventListener('change', function() {
        const municipio = this.value;
        fetch(`/colonias/${municipio}`)
            .then(response => response.json())
            .then(data => {
                const coloniaSelect = document.getElementById('colonia');
                coloniaSelect.innerHTML = '<option value="">Selecciona una colonia</option>';
                data.forEach(colonia => {
                    coloniaSelect.innerHTML += `<option value="${colonia}">${colonia}</option>`;
                });
                coloniaSelect.disabled = false;
            });
    });

    // Mostrar código postal
    document.getElementById('colonia').addEventListener('change', function() {
        const estado = document.getElementById('estado').value;
        const municipio = document.getElementById('municipio').value;
        const colonia = this.value;
       
        fetch(`/codigo-postal/${estado}/${municipio}/${colonia}`)
            .then(response => response.json())
            .then(data => {
                if (data.codigo_postal) {
                    document.getElementById('cp-text').innerText = data.codigo_postal;
                    document.getElementById('codigo-postal').style.display = "block";
                } else {
                    document.getElementById('codigo-postal').style.display = "none";
                }
                validarBoton();
            })
            .catch(error => console.error('Error obteniendo código postal:', error));
    });

    // Habilitar el botón solo si todos los campos están llenos
    function validarBoton() {
        const estado = document.getElementById('estado').value;
        const municipio = document.getElementById('municipio').value;
        const colonia = document.getElementById('colonia').value;
        const codigoPostal = document.getElementById('cp-text').innerText;

        document.getElementById('guardar-consulta').disabled = !(estado && municipio && colonia && codigoPostal);
    }

    // Guardar consulta en la base de datos
    document.getElementById('guardar-consulta').addEventListener('click', function() {
        const estado = document.getElementById('estado').value;
        const municipio = document.getElementById('municipio').value;
        const colonia = document.getElementById('colonia').value;
        const codigoPostal = document.getElementById('cp-text').innerText;

        fetch('/consultas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                estado: estado,
                municipio: municipio,
                colonia: colonia, 
                codigo_postal: codigoPostal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recargar la página para mostrar la nueva consulta
            }
        });
    });

    // Eliminar consulta
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.eliminar-consulta').forEach(button => {
        button.addEventListener('click', function () {
            const consultaId = this.getAttribute('data-id');
            
            if (confirm('¿Seguro que deseas eliminar esta consulta?')) {
                fetch(`/consultas/${consultaId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('li').remove(); // Eliminar visualmente de la lista
                    } else {
                        alert('Error al eliminar la consulta.');
                    }
                })
                .catch(error => console.error('Error eliminando consulta:', error));
            }
        });
    });
    });

</script>
</body>
</html>