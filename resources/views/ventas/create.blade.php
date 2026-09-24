@extends('layouts.app')

@section('content')

<style>
    .ventas-container {
        padding: 10px 5px;
    }

    .ventas-header {
        margin-bottom: 20px;
    }

    .ventas-header h2 {
        color: #245c35;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .ventas-header p {
        color: #6c757d;
        margin: 0;
    }

    /* =========================
       LAYOUT PRINCIPAL
    ========================= */

    .venta-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 20px;
        align-items: start;
    }

    .productos-section,
    .carrito-section {
        background: white;
        border-radius: 15px;
        border: 1px solid #e1e7e3;
        padding: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #245c35;
        margin-bottom: 15px;
    }

    /* =========================
       CATEGORÍAS
    ========================= */

    .categorias-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .categoria-btn {
        border: 1px solid #d5e4d9;
        background: white;
        color: #245c35;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .categoria-btn:hover {
        background: #eaf3ed;
    }

    .categoria-btn.active {
        background: #245c35;
        color: white;
        border-color: #245c35;
    }

    /* =========================
       BUSCADOR
    ========================= */

    .buscador-container {
        position: relative;
        margin-bottom: 20px;
    }

    .buscador-input {
        width: 100%;
        border: 1px solid #d5e4d9;
        border-radius: 10px;
        padding: 12px 15px 12px 42px;
        outline: none;
        transition: 0.2s;
    }

    .buscador-input:focus {
        border-color: #245c35;
        box-shadow: 0 0 0 3px rgba(36, 92, 53, 0.08);
    }

    .buscador-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #245c35;
    }

    /* =========================
       PRODUCTOS
    ========================= */

    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .producto-card {
        background: white;
        border: 1px solid #e1e7e3;
        border-radius: 14px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .producto-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(0,0,0,0.08);
        border-color: #245c35;
    }

    .producto-card.agotado {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .producto-card.oculto {
        display: none;
    }

    .producto-imagen,
    .producto-placeholder {
        width: 100%;
        height: 140px;
    }

    .producto-imagen {
        object-fit: cover;
    }

    .producto-placeholder {
        background: #eaf3ed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 42px;
        color: #245c35;
    }

    .producto-info {
        padding: 12px;
    }

    .producto-nombre {
        font-weight: 700;
        color: #26352b;
        margin-bottom: 5px;
    }

    .producto-precio {
        color: #245c35;
        font-size: 18px;
        font-weight: 700;
    }

    .producto-stock {
        font-size: 13px;
        color: #6c757d;
        margin-top: 5px;
    }

    /* =========================
       SIN RESULTADOS
    ========================= */

    .sin-resultados {
        display: none;
        text-align: center;
        padding: 35px;
        color: #8a938d;
    }

    .sin-resultados.visible {
        display: block;
    }

    /* =========================
       CARRITO
    ========================= */

    .carrito-section {
        position: sticky;
        top: 20px;
    }

    .carrito-vacio {
        text-align: center;
        padding: 35px 10px;
        color: #8a938d;
    }

    .carrito-vacio-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .carrito-item {
        border-bottom: 1px solid #e9eeeb;
        padding: 12px 0;
    }

    .carrito-item:last-child {
        border-bottom: none;
    }

    .carrito-item-header {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .carrito-item-nombre {
        font-weight: 600;
        color: #26352b;
        font-size: 14px;
    }

    .carrito-item-precio {
        color: #245c35;
        font-weight: 700;
        white-space: nowrap;
    }

    .cantidad-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }

    .cantidad-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: 1px solid #d5e4d9;
        background: #f4f8f5;
        color: #245c35;
        font-weight: 700;
        cursor: pointer;
    }

    .cantidad-btn:hover {
        background: #eaf3ed;
    }

    .cantidad-numero {
        min-width: 25px;
        text-align: center;
        font-weight: 700;
    }

    .eliminar-btn {
        border: none;
        background: transparent;
        color: #dc3545;
        font-size: 13px;
        cursor: pointer;
        margin-left: auto;
    }

    .carrito-total {
        border-top: 2px solid #e9eeeb;
        margin-top: 15px;
        padding-top: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .carrito-total-label {
        font-size: 17px;
        font-weight: 600;
    }

    .carrito-total-valor {
        font-size: 24px;
        font-weight: 800;
        color: #245c35;
    }

    /* =========================
       BOTONES
    ========================= */

    .btn-registrar {
        width: 100%;
        margin-top: 15px;
        background: #245c35;
        border: none;
        color: white;
        padding: 12px;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .btn-registrar:hover:not(:disabled) {
        background: #1b4729;
    }

    .btn-registrar:disabled {
        background: #adb5b0;
        cursor: not-allowed;
    }

    .btn-limpiar {
        width: 100%;
        margin-top: 8px;
        border: 1px solid #dc3545;
        color: #dc3545;
        background: white;
        padding: 9px;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-limpiar:hover {
        background: #fff5f5;
    }

    /* =========================
       MODAL CONFIRMACIÓN
    ========================= */

    .modal-confirmacion .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    }

    .modal-confirmacion .modal-header {
        background: linear-gradient(135deg, #245c35, #1b4729);
        color: white;
        border: none;
        padding: 20px 24px;
    }

    .modal-confirmacion .modal-title {
        font-weight: 700;
        font-size: 20px;
    }

    .confirmacion-icono {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .confirmacion-subtitulo {
        font-size: 13px;
        opacity: 0.8;
        margin-top: 2px;
    }

    .modal-confirmacion .modal-body {
        padding: 24px;
    }

    .confirmacion-info {
        background: #f4f8f5;
        border: 1px solid #e1e7e3;
        border-radius: 10px;
        padding: 12px 15px;
        margin-bottom: 20px;
        color: #526057;
        font-size: 14px;
    }

    .confirmacion-info-icono {
        color: #245c35;
        font-size: 18px;
    }

    .confirmacion-tabla {
        width: 100%;
        border-collapse: collapse;
    }

    .confirmacion-tabla thead th {
        font-size: 12px;
        text-transform: uppercase;
        color: #7a847d;
        font-weight: 700;
        border-bottom: 1px solid #e1e7e3;
        padding: 10px 8px;
    }

    .confirmacion-tabla tbody td {
        padding: 13px 8px;
        border-bottom: 1px solid #edf1ee;
        vertical-align: middle;
        font-size: 14px;
    }

    .confirmacion-producto {
        font-weight: 600;
        color: #26352b;
    }

    .confirmacion-cantidad {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        padding: 5px 9px;
        border-radius: 20px;
        background: #eaf3ed;
        color: #245c35;
        font-weight: 700;
    }

    .confirmacion-precio {
        color: #68736c;
    }

    .confirmacion-subtotal {
        font-weight: 700;
        color: #26352b;
    }

    .confirmacion-total-box {
        margin-top: 20px;
        padding: 16px 20px;
        border-radius: 12px;
        background: #eaf3ed;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .confirmacion-total-label {
        color: #526057;
        font-weight: 600;
    }

    .confirmacion-total {
        color: #245c35;
        font-size: 25px;
        font-weight: 800;
    }

    .modal-confirmacion .modal-footer {
        border: none;
        padding: 0 24px 24px;
        gap: 10px;
    }

    .btn-cancelar-confirmacion {
        border: 1px solid #d5ddd7;
        background: white;
        color: #526057;
        border-radius: 9px;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-cancelar-confirmacion:hover {
        background: #f4f8f5;
    }

    .btn-confirmar-venta {
        border: none;
        background: #245c35;
        color: white;
        border-radius: 9px;
        padding: 10px 20px;
        font-weight: 700;
    }

    .btn-confirmar-venta:hover {
        background: #1b4729;
        color: white;
    }

    .btn-confirmar-venta:disabled {
        opacity: 0.7;
        cursor: wait;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1000px) {

        .venta-layout {
            grid-template-columns: 1fr;
        }

        .carrito-section {
            position: static;
        }
    }

    @media (max-width: 600px) {

        .productos-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .producto-imagen,
        .producto-placeholder {
            height: 120px;
        }

        .producto-info {
            padding: 9px;
        }

        .producto-precio {
            font-size: 16px;
        }

        .modal-confirmacion .modal-body {
            padding: 18px;
        }

        .modal-confirmacion .modal-header {
            padding: 16px 18px;
        }

        .confirmacion-tabla thead th,
        .confirmacion-tabla tbody td {
            padding: 9px 5px;
        }

        .confirmacion-total-box {
            padding: 14px;
        }

        .confirmacion-total {
            font-size: 21px;
        }
    }
</style>


<div class="ventas-container">

    {{-- =========================
         ENCABEZADO
    ========================= --}}

    <div class="ventas-header">

        <h2>Registrar venta</h2>

        <p>
            Selecciona una categoría, elige los productos y confirma la venta.
        </p>

    </div>


    {{-- =========================
         ERRORES
    ========================= --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>No se pudo registrar la venta:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="venta-layout">


        {{-- =========================
             PRODUCTOS
        ========================= --}}

        <div class="productos-section">

            <div class="section-title">
                Categorías
            </div>


            <div class="categorias-container">

                @forelse ($categorias as $categoria)

                    <button
                        type="button"
                        class="categoria-btn {{ $loop->first ? 'active' : '' }}"
                        data-categoria="{{ $categoria->id }}"
                    >
                        {{ $categoria->nombre }}
                    </button>

                @empty

                    <p class="text-muted">
                        No existen categorías activas.
                    </p>

                @endforelse

            </div>


            {{-- BUSCADOR --}}

            <div class="buscador-container">

                <span class="buscador-icon">
                    🔎
                </span>

                <input
                    type="text"
                    id="buscadorProducto"
                    class="buscador-input"
                    placeholder="Buscar producto..."
                    autocomplete="off"
                >

            </div>


            <div class="section-title">
                Productos
            </div>


            <div class="productos-grid" id="productosGrid">

                @forelse ($productos as $producto)

                    @php
                        $stock = $producto->stock
                            ? $producto->stock->cantidad
                            : 0;
                    @endphp


                    <div
                        class="producto-card {{ $stock <= 0 ? 'agotado' : '' }}"
                        data-categoria="{{ $producto->categoria_id }}"
                        data-id="{{ $producto->id }}"
                        data-nombre="{{ strtolower($producto->nombre) }}"
                        data-nombre-original="{{ $producto->nombre }}"
                        data-precio="{{ $producto->precio }}"
                        data-stock="{{ $stock }}"
                    >

                        @if ($producto->imagen)

                            <img
                                src="{{ asset('storage/' . $producto->imagen) }}"
                                alt="{{ $producto->nombre }}"
                                class="producto-imagen"
                            >

                        @else

                            <div class="producto-placeholder">
                                🍰
                            </div>

                        @endif


                        <div class="producto-info">

                            <div class="producto-nombre">
                                {{ $producto->nombre }}
                            </div>


                            <div class="producto-precio">
                                Bs {{ number_format($producto->precio, 2) }}
                            </div>


                            <div class="producto-stock">

                                @if ($stock > 0)

                                    Disponible: {{ $stock }}

                                @else

                                    <span class="text-danger fw-semibold">
                                        Agotado
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-muted">
                        No existen productos disponibles.
                    </div>

                @endforelse

            </div>


            <div
                id="sinResultados"
                class="sin-resultados"
            >

                🔎

                <div class="mt-2">
                    No se encontraron productos.
                </div>

            </div>

        </div>


        {{-- =========================
             CARRITO
        ========================= --}}

        <div class="carrito-section">

            <div class="section-title">
                🛒 Venta actual
            </div>


            <form
                method="POST"
                action="/ventas"
                id="ventaForm"
            >

                @csrf


                <div id="carrito">

                    <div
                        class="carrito-vacio"
                        id="carritoVacio"
                    >

                        <div class="carrito-vacio-icon">
                            🛒
                        </div>

                        <div>
                            No hay productos agregados.
                        </div>

                        <small>
                            Haz clic sobre un producto para agregarlo.
                        </small>

                    </div>

                </div>


                <div class="carrito-total">

                    <span class="carrito-total-label">
                        Total
                    </span>

                    <span
                        class="carrito-total-valor"
                        id="totalVenta"
                    >
                        Bs 0.00
                    </span>

                </div>


                <button
                    type="button"
                    class="btn-registrar"
                    id="btnRegistrar"
                    disabled
                >
                    Registrar venta
                </button>


                <button
                    type="button"
                    class="btn-limpiar"
                    id="btnLimpiar"
                >
                    Vaciar venta
                </button>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL DE CONFIRMACIÓN
========================================================= --}}

<div
    class="modal fade modal-confirmacion"
    id="modalConfirmacion"
    tabindex="-1"
    aria-labelledby="modalConfirmacionLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">


            {{-- ENCABEZADO --}}

            <div class="modal-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="confirmacion-icono">
                        ✓
                    </div>

                    <div>

                        <h5
                            class="modal-title mb-0"
                            id="modalConfirmacionLabel"
                        >
                            Confirmar venta
                        </h5>

                        <div class="confirmacion-subtitulo">
                            Revisa los productos antes de registrar
                        </div>

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>


            {{-- CUERPO --}}

            <div class="modal-body">

                <div class="confirmacion-info">

                    <div class="d-flex align-items-center gap-2">

                        <span class="confirmacion-info-icono">
                            ℹ
                        </span>

                        <span>
                            Verifica las cantidades y el total de la venta.
                            Una vez confirmada, la venta será registrada.
                        </span>

                    </div>

                </div>


                <div class="table-responsive">

                    <table class="confirmacion-tabla">

                        <thead>

                            <tr>

                                <th>
                                    Producto
                                </th>

                                <th class="text-center">
                                    Cant.
                                </th>

                                <th class="text-end">
                                    Precio
                                </th>

                                <th class="text-end">
                                    Subtotal
                                </th>

                            </tr>

                        </thead>


                        <tbody id="resumenConfirmacion">

                        </tbody>

                    </table>

                </div>


                <div class="confirmacion-total-box">

                    <span class="confirmacion-total-label">
                        Total de la venta
                    </span>

                    <span
                        class="confirmacion-total"
                        id="confirmacionTotal"
                    >
                        Bs 0.00
                    </span>

                </div>

            </div>


            {{-- BOTONES --}}

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-cancelar-confirmacion"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="button"
                    class="btn-confirmar-venta"
                    id="confirmarVenta"
                >
                    ✓ Confirmar venta
                </button>

            </div>

        </div>

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | VARIABLES
    |--------------------------------------------------------------------------
    */

    const carrito = {};

    const productoCards =
        document.querySelectorAll('.producto-card');

    const categoriaButtons =
        document.querySelectorAll('.categoria-btn');

    const buscador =
        document.getElementById('buscadorProducto');

    const carritoContainer =
        document.getElementById('carrito');

    const totalVenta =
        document.getElementById('totalVenta');

    const btnRegistrar =
        document.getElementById('btnRegistrar');

    const btnLimpiar =
        document.getElementById('btnLimpiar');

    const sinResultados =
        document.getElementById('sinResultados');

    const modalElement =
        document.getElementById('modalConfirmacion');

    const resumenConfirmacion =
        document.getElementById('resumenConfirmacion');

    const confirmacionTotal =
        document.getElementById('confirmacionTotal');

    const confirmarVenta =
        document.getElementById('confirmarVenta');


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍA ACTUAL
    |--------------------------------------------------------------------------
    */

    let categoriaActual =
        categoriaButtons.length > 0
            ? categoriaButtons[0].dataset.categoria
            : null;


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    categoriaButtons.forEach(button => {

        button.addEventListener('click', function () {

            categoriaButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            this.classList.add('active');

            categoriaActual =
                this.dataset.categoria;

            buscador.value = '';

            filtrarProductos();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | BUSCADOR
    |--------------------------------------------------------------------------
    */

    buscador.addEventListener('input', function () {

        filtrarProductos();

    });


    /*
    |--------------------------------------------------------------------------
    | FILTRAR PRODUCTOS
    |--------------------------------------------------------------------------
    */

    function filtrarProductos() {

        const texto =
            buscador.value
                .trim()
                .toLowerCase();

        let encontrados = 0;


        productoCards.forEach(card => {

            const categoria =
                card.dataset.categoria;

            const nombre =
                card.dataset.nombre;


            /*
             * Si no hay texto de búsqueda,
             * mostramos solamente la categoría seleccionada.
             *
             * Si el usuario está buscando algo,
             * buscamos en todas las categorías.
             */

            const perteneceCategoria =
                categoria === categoriaActual;

            const coincideBusqueda =
                nombre.includes(texto);


            let mostrar = false;


            if (texto === '') {

                mostrar =
                    perteneceCategoria;

            } else {

                mostrar =
                    coincideBusqueda;

            }


            if (mostrar) {

                card.classList.remove('oculto');

                encontrados++;

            } else {

                card.classList.add('oculto');

            }

        });


        if (encontrados === 0) {

            sinResultados.classList.add('visible');

        } else {

            sinResultados.classList.remove('visible');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR PRIMERA CATEGORÍA
    |--------------------------------------------------------------------------
    */

    filtrarProductos();


    /*
    |--------------------------------------------------------------------------
    | AGREGAR PRODUCTO
    |--------------------------------------------------------------------------
    */

    productoCards.forEach(card => {

        card.addEventListener('click', function () {

            const id =
                this.dataset.id;

            const nombre =
                this.dataset.nombreOriginal;

            const precio =
                parseFloat(this.dataset.precio);

            const stock =
                parseInt(this.dataset.stock);


            if (stock <= 0) {

                alert(
                    'Este producto no tiene stock disponible.'
                );

                return;

            }


            if (carrito[id]) {

                if (
                    carrito[id].cantidad >= stock
                ) {

                    alert(
                        'No puedes agregar más unidades. ' +
                        'Stock disponible: ' +
                        stock
                    );

                    return;

                }


                carrito[id].cantidad++;

            } else {

                carrito[id] = {

                    id: id,

                    nombre: nombre,

                    precio: precio,

                    stock: stock,

                    cantidad: 1

                };

            }


            actualizarCarrito();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR CARRITO
    |--------------------------------------------------------------------------
    */

    function actualizarCarrito() {

        carritoContainer.innerHTML = '';

        const productos =
            Object.values(carrito);

        let total = 0;


        if (productos.length === 0) {

            carritoContainer.innerHTML = `

                <div class="carrito-vacio">

                    <div class="carrito-vacio-icon">
                        🛒
                    </div>

                    <div>
                        No hay productos agregados.
                    </div>

                    <small>
                        Haz clic sobre un producto
                        para agregarlo.
                    </small>

                </div>

            `;

            btnRegistrar.disabled = true;

        } else {

            btnRegistrar.disabled = false;


            productos.forEach(producto => {

                const subtotal =
                    producto.precio *
                    producto.cantidad;

                total += subtotal;


                const item =
                    document.createElement('div');

                item.className =
                    'carrito-item';


                item.innerHTML = `

                    <div class="carrito-item-header">

                        <div class="carrito-item-nombre">
                            ${producto.nombre}
                        </div>

                        <div class="carrito-item-precio">
                            Bs ${subtotal.toFixed(2)}
                        </div>

                    </div>


                    <div class="cantidad-controls">

                        <button
                            type="button"
                            class="cantidad-btn"
                            onclick="disminuirCantidad(${producto.id})"
                        >
                            −
                        </button>


                        <span class="cantidad-numero">
                            ${producto.cantidad}
                        </span>


                        <button
                            type="button"
                            class="cantidad-btn"
                            onclick="aumentarCantidad(${producto.id})"
                        >
                            +
                        </button>


                        <button
                            type="button"
                            class="eliminar-btn"
                            onclick="eliminarProducto(${producto.id})"
                        >
                            Eliminar
                        </button>

                    </div>


                    <input
                        type="hidden"
                        name="productos[${producto.id}]"
                        value="${producto.cantidad}"
                    >

                `;


                carritoContainer.appendChild(item);

            });

        }


        totalVenta.textContent =
            'Bs ' + total.toFixed(2);

    }


    /*
    |--------------------------------------------------------------------------
    | AUMENTAR CANTIDAD
    |--------------------------------------------------------------------------
    */

    function aumentarCantidad(id) {

        if (!carrito[id]) {
            return;
        }


        if (
            carrito[id].cantidad >=
            carrito[id].stock
        ) {

            alert(
                'No puedes agregar más unidades. ' +
                'Stock disponible: ' +
                carrito[id].stock
            );

            return;

        }


        carrito[id].cantidad++;

        actualizarCarrito();

    }


    /*
    |--------------------------------------------------------------------------
    | DISMINUIR CANTIDAD
    |--------------------------------------------------------------------------
    */

    function disminuirCantidad(id) {

        if (!carrito[id]) {
            return;
        }


        carrito[id].cantidad--;


        if (
            carrito[id].cantidad <= 0
        ) {

            delete carrito[id];

        }


        actualizarCarrito();

    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR PRODUCTO
    |--------------------------------------------------------------------------
    */

    function eliminarProducto(id) {

        delete carrito[id];

        actualizarCarrito();

    }


    /*
    |--------------------------------------------------------------------------
    | VACIAR VENTA
    |--------------------------------------------------------------------------
    */

    btnLimpiar.addEventListener(
        'click',
        function () {

            if (
                Object.keys(carrito).length === 0
            ) {

                return;

            }


            if (
                !confirm(
                    '¿Deseas quitar todos los productos de la venta?'
                )
            ) {

                return;

            }


            Object.keys(carrito).forEach(id => {

                delete carrito[id];

            });


            actualizarCarrito();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ABRIR CONFIRMACIÓN
    |--------------------------------------------------------------------------
    */

    btnRegistrar.addEventListener(
        'click',
        function () {

            if (
                Object.keys(carrito).length === 0
            ) {

                return;

            }


            generarResumen();


            /*
             * Bootstrap se carga desde layouts.app.
             * Creamos el modal solamente cuando
             * el usuario realmente va a abrirlo.
             */

            if (
                typeof bootstrap !== 'undefined' &&
                modalElement
            ) {

                const modal =
                    bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                modal.show();

            } else {

                alert(
                    'No se pudo cargar el sistema de confirmación.'
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | GENERAR RESUMEN
    |--------------------------------------------------------------------------
    */

    function generarResumen() {

        resumenConfirmacion.innerHTML = '';

        let total = 0;


        Object.values(carrito).forEach(producto => {

            const subtotal =
                producto.precio *
                producto.cantidad;

            total += subtotal;


            const fila =
                document.createElement('tr');


            fila.innerHTML = `

                <td>

                    <div class="confirmacion-producto">
                        ${producto.nombre}
                    </div>

                </td>


                <td class="text-center">

                    <span class="confirmacion-cantidad">
                        ${producto.cantidad}
                    </span>

                </td>


                <td class="text-end">

                    <span class="confirmacion-precio">
                        Bs ${producto.precio.toFixed(2)}
                    </span>

                </td>


                <td class="text-end">

                    <span class="confirmacion-subtotal">
                        Bs ${subtotal.toFixed(2)}
                    </span>

                </td>

            `;


            resumenConfirmacion.appendChild(fila);

        });


        confirmacionTotal.textContent =
            'Bs ' + total.toFixed(2);

    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR VENTA
    |--------------------------------------------------------------------------
    */

    confirmarVenta.addEventListener(
        'click',
        function () {

            const form =
                document.getElementById('ventaForm');


            if (!form) {
                return;
            }


            /*
             * Evitar doble clic
             */

            confirmarVenta.disabled = true;


            confirmarVenta.innerHTML = `

                <span
                    class="spinner-border spinner-border-sm me-2"
                    aria-hidden="true"
                ></span>

                Registrando...

            `;


            form.submit();

        }
    );

</script>

@endsection