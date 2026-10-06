@extends('layouts.app')

@section('title', 'Nuevo pedido - Valgreen')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold mb-1">
        Nuevo pedido
    </h1>

    <p class="text-muted">
        Registra los productos que el cliente desea recibir.
    </p>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <strong>Hay un problema:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<form method="POST"
      action="/pedidos"
      id="formPedido">

    @csrf


    {{-- ============================================================= --}}
    {{-- INFORMACIÓN DEL CLIENTE --}}
    {{-- ============================================================= --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-1">
                Información del cliente
            </h5>

            <p class="text-muted small mb-4">
                Busca al cliente por su carnet. Si ya está registrado,
                se utilizarán sus datos. Si no existe, puedes registrarlo
                desde este mismo formulario.
            </p>


            <div class="row g-3">


                {{-- CARNET --}}

                <div class="col-md-8">

                    <label class="form-label fw-semibold">
                        Carnet de identidad
                    </label>

                    <div class="input-group">

                        <input type="text"
                               name="cliente[carnet]"
                               id="clienteCarnet"
                               class="form-control"
                               value="{{ old('cliente.carnet') }}"
                               maxlength="20"
                               placeholder="Ingrese el carnet"
                               required>

                        <button type="button"
                                class="btn btn-outline-success"
                                id="btnBuscarCliente">

                            🔍 Buscar

                        </button>

                    </div>

                    <small class="text-muted">
                        El carnet se utiliza para evitar registrar
                        dos veces al mismo cliente.
                    </small>

                </div>


                {{-- FECHA DE ENTREGA --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">
                        Fecha y hora de entrega
                    </label>

                    <input type="datetime-local"
                           name="fecha_entrega"
                           class="form-control"
                           value="{{ old('fecha_entrega') }}"
                           required>

                </div>


                {{-- MENSAJE DE ESTADO --}}

                <div class="col-12">

                    <div id="mensajeCliente"
                         class="alert alert-secondary py-2 mb-0">

                        Ingresa un carnet y presiona
                        <strong>Buscar</strong>.

                    </div>

                </div>


                {{-- NOMBRES --}}

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Nombres
                    </label>

                    <input type="text"
                           name="cliente[nombres]"
                           id="clienteNombres"
                           class="form-control"
                           value="{{ old('cliente.nombres') }}"
                           maxlength="100"
                           placeholder="Nombres">

                </div>


                {{-- PRIMER APELLIDO --}}

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Primer apellido
                    </label>

                    <input type="text"
                           name="cliente[primer_apellido]"
                           id="clientePrimerApellido"
                           class="form-control"
                           value="{{ old('cliente.primer_apellido') }}"
                           maxlength="50"
                           placeholder="Primer apellido">

                </div>


                {{-- SEGUNDO APELLIDO --}}

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Segundo apellido
                        <span class="text-muted fw-normal">
                            (opcional)
                        </span>
                    </label>

                    <input type="text"
                           name="cliente[segundo_apellido]"
                           id="clienteSegundoApellido"
                           class="form-control"
                           value="{{ old('cliente.segundo_apellido') }}"
                           maxlength="50"
                           placeholder="Segundo apellido">

                </div>


                {{-- TELÉFONO --}}

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Teléfono
                    </label>

                    <input type="text"
                           name="cliente[telefono]"
                           id="clienteTelefono"
                           class="form-control"
                           value="{{ old('cliente.telefono') }}"
                           maxlength="20"
                           placeholder="Número de teléfono">

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- PRODUCTOS --}}
    {{-- ============================================================= --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-1">
                Productos del pedido
            </h5>

            <p class="text-muted small mb-3">

                Estos productos serán preparados para el pedido.
                El stock actual no limita la cantidad solicitada.

            </p>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>
                                Producto
                            </th>

                            <th>
                                Precio
                            </th>

                            <th style="width: 150px;">
                                Cantidad
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($productos as $producto)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $producto->nombre }}
                                    </strong>

                                    @if($producto->descripcion)

                                        <br>

                                        <small class="text-muted">

                                            {{ $producto->descripcion }}

                                        </small>

                                    @endif

                                </td>


                                <td>

                                    Bs
                                    {{ number_format($producto->precio, 2) }}

                                </td>


                                <td>

                                    <input type="number"
                                           name="productos[{{ $producto->id }}]"
                                           class="form-control"
                                           min="0"
                                           value="{{ old('productos.' . $producto->id, 0) }}">

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center text-muted">

                                    No hay productos disponibles.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- TORTA PERSONALIZADA --}}
    {{-- ============================================================= --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-1">
                Torta personalizada
            </h5>

            <p class="text-muted small mb-3">

                Completa esta sección únicamente si el cliente
                solicita una torta personalizada.

            </p>


            <div class="row g-3">


                <div class="col-md-4">

                    <label class="form-label">
                        Porciones
                    </label>

                    <input type="number"
                           name="torta[porciones]"
                           class="form-control"
                           min="1"
                           value="{{ old('torta.porciones') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Sabor
                    </label>

                    <input type="text"
                           name="torta[sabor]"
                           class="form-control"
                           value="{{ old('torta.sabor') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Precio
                    </label>

                    <input type="number"
                           name="torta[precio]"
                           class="form-control"
                           min="0"
                           step="0.01"
                           value="{{ old('torta.precio') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Relleno
                    </label>

                    <input type="text"
                           name="torta[relleno]"
                           class="form-control"
                           value="{{ old('torta.relleno') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Cobertura
                    </label>

                    <input type="text"
                           name="torta[cobertura]"
                           class="form-control"
                           value="{{ old('torta.cobertura') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Decoración
                    </label>

                    <input type="text"
                           name="torta[decoracion]"
                           class="form-control"
                           value="{{ old('torta.decoracion') }}">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Mensaje
                    </label>

                    <input type="text"
                           name="torta[mensaje]"
                           class="form-control"
                           value="{{ old('torta.mensaje') }}">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Observaciones de la torta
                    </label>

                    <input type="text"
                           name="torta[observaciones]"
                           class="form-control"
                           value="{{ old('torta.observaciones') }}">

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- PAGO --}}
    {{-- ============================================================= --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Pago
            </h5>


            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">
                        Anticipo
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Bs
                        </span>

                        <input type="number"
                               name="anticipo"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="{{ old('anticipo', 0) }}"
                               required>

                    </div>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Observaciones del pedido
                    </label>

                    <textarea name="observaciones"
                              class="form-control"
                              rows="2">{{ old('observaciones') }}</textarea>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- BOTONES --}}
    {{-- ============================================================= --}}

    <div class="d-flex justify-content-end gap-2">

        <a href="/pedidos"
           class="btn btn-secondary">

            Cancelar

        </a>


        <button type="submit"
                class="btn btn-valgreen">

            Registrar pedido

        </button>

    </div>

</form>


{{-- ============================================================= --}}
{{-- JAVASCRIPT PARA BUSCAR CLIENTES --}}
{{-- ============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
     * Clientes existentes enviados desde Laravel.
     *
     * Esto solamente sirve para facilitar la búsqueda
     * desde el formulario.
     *
     * El controlador vuelve a comprobar el carnet
     * antes de guardar.
     */
    const clientes = @json($clientes);


    const carnetInput =
        document.getElementById('clienteCarnet');

    const nombresInput =
        document.getElementById('clienteNombres');

    const primerApellidoInput =
        document.getElementById('clientePrimerApellido');

    const segundoApellidoInput =
        document.getElementById('clienteSegundoApellido');

    const telefonoInput =
        document.getElementById('clienteTelefono');

    const buscarButton =
        document.getElementById('btnBuscarCliente');

    const mensaje =
        document.getElementById('mensajeCliente');


    /*
     * Campos personales del cliente.
     */
    const camposCliente = [

        nombresInput,
        primerApellidoInput,
        segundoApellidoInput,
        telefonoInput

    ];


    /*
     * Configurar campos cuando encontramos
     * un cliente existente.
     */
    function mostrarClienteExistente(cliente) {

        nombresInput.value =
            cliente.nombres ?? '';

        primerApellidoInput.value =
            cliente.primer_apellido ?? '';

        segundoApellidoInput.value =
            cliente.segundo_apellido ?? '';

        telefonoInput.value =
            cliente.telefono ?? '';


        /*
         * Como el cliente ya existe,
         * no queremos modificar sus datos
         * desde el pedido.
         */
        camposCliente.forEach(function (campo) {

            campo.readOnly = true;
            campo.required = false;

        });


        mensaje.className =
            'alert alert-success py-2 mb-0';

        mensaje.innerHTML =
            '✓ <strong>Cliente encontrado.</strong> ' +
            'Se utilizarán los datos registrados para este carnet. ' +
            'No se creará un cliente nuevo.';

    }


    /*
     * Configurar campos para un cliente nuevo.
     */
    function prepararClienteNuevo() {

        camposCliente.forEach(function (campo) {

            campo.readOnly = false;
            campo.required = true;

        });


        mensaje.className =
            'alert alert-info py-2 mb-0';

        mensaje.innerHTML =
            'ℹ <strong>Cliente nuevo.</strong> ' +
            'Completa sus datos para registrarlo junto con el pedido.';

    }


    /*
     * Buscar cliente por carnet.
     */
    function buscarCliente() {

        const carnet =
            carnetInput.value.trim();


        if (!carnet) {

            mensaje.className =
                'alert alert-warning py-2 mb-0';

            mensaje.innerHTML =
                'Debes ingresar un carnet para buscar al cliente.';

            return;

        }


        const clienteEncontrado =
            clientes.find(function (cliente) {

                return String(cliente.carnet).trim() === carnet;

            });


        if (clienteEncontrado) {

            mostrarClienteExistente(
                clienteEncontrado
            );

        } else {

            prepararClienteNuevo();

        }

    }


    /*
     * Botón Buscar.
     */
    buscarButton.addEventListener(
        'click',
        buscarCliente
    );


    /*
     * Si el usuario cambia el carnet después
     * de haber encontrado un cliente,
     * volvemos a habilitar los campos.
     */
    carnetInput.addEventListener(
        'input',
        function () {

            camposCliente.forEach(function (campo) {

                campo.readOnly = false;
                campo.required = false;

            });


            mensaje.className =
                'alert alert-secondary py-2 mb-0';

            mensaje.innerHTML =
                'Presiona <strong>Buscar</strong> ' +
                'para comprobar si el carnet ya está registrado.';

        }
    );


    /*
     * Si Laravel devolvió el formulario por un error,
     * intentamos buscar automáticamente el carnet.
     */
    if (carnetInput.value.trim() !== '') {

        buscarCliente();

    }

});

</script>

@endsection