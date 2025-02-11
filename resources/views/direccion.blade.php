<!DOCTYPE html>
<html>
<head>
    <title>Códigos Postales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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

        <select id="localidad" class="form-control mt-2" disabled>
            <option value="">Selecciona una localidad</option>
        </select>

        <select id="codigo-postal" class="form-control mt-2" disabled>
            <option value="">Selecciona un código postal</option>
        </select>

        <select id="colonia" class="form-control mt-2" disabled>
            <option value="">Selecciona una colonia</option>
        </select>

        <!-- Botón para guardar consulta -->
        <button id="guardar-consulta" class="btn btn-primary mt-3" disabled>Guardar Consulta</button>

        <!-- Historial de consultas -->
        <div class="mt-5">
            <h4>Historial de Consultas</h4>
            <ul id="historial-consultas" class="list-group">
                @foreach($consultas as $consulta)
                    <li class="list-group-item">
                        {{ $consulta->estado }} - {{ $consulta->municipio }} - {{ $consulta->localidad }} ({{ $consulta->codigo_postal }}) - {{ $consulta->colonia }}
                        <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Lógica para selects dinámicos
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
                });
        });

        document.getElementById('municipio').addEventListener('change', function() {
            const municipio = this.value;
            fetch(`/localidades/${municipio}`)
                .then(response => response.json())
                .then(data => {
                    const localidadSelect = document.getElementById('localidad');
                    localidadSelect.innerHTML = '<option value="">Selecciona una localidad</option>';
                    data.forEach(localidad => {
                        localidadSelect.innerHTML += `<option value="${localidad}">${localidad}</option>`;
                    });
                    localidadSelect.disabled = false;
                });
        });

        document.getElementById('localidad').addEventListener('change', function() {
            const localidad = this.value;
            fetch(`/codigos-postales/${localidad}`)
                .then(response => response.json())
                .then(data => {
                    const codigoPostalSelect = document.getElementById('codigo-postal');
                    codigoPostalSelect.innerHTML = '<option value="">Selecciona un código postal</option>';
                    data.forEach(codigoPostal => {
                        codigoPostalSelect.innerHTML += `<option value="${codigoPostal}">${codigoPostal}</option>`;
                    });
                    codigoPostalSelect.disabled = false;
                });
        });

        document.getElementById('codigo-postal').addEventListener('change', function() {
            const codigoPostal = this.value;
            fetch(`/colonias/${codigoPostal}`)
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

        // Habilitar botón cuando se selecciona una colonia
        document.getElementById('colonia').addEventListener('change', function() {
            document.getElementById('guardar-consulta').disabled = false;
        });

        // Guardar consulta
        document.getElementById('guardar-consulta').addEventListener('click', function() {
            const estado = document.getElementById('estado').selectedOptions[0].text;
            const municipio = document.getElementById('municipio').selectedOptions[0].text;
            const localidad = document.getElementById('localidad').selectedOptions[0].text;
            const codigoPostal = document.getElementById('codigo-postal').selectedOptions[0].text;
            const colonia = document.getElementById('colonia').selectedOptions[0].text;

            fetch('/consultas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    estado: estado,
                    municipio: municipio,
                    localidad: localidad,
                    codigo_postal: codigoPostal,
                    colonia: colonia
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recargar la página para mostrar la nueva consulta
                }
            });
        });
    </script>
</body>
</html>