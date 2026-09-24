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

    /* CATEGORÍAS */

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

    /* PRODUCTOS */

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
        position: relative;
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

    .producto-imagen {
        width: 100%;
        height: 140px;
        object-fit: cover;
        background: #eaf3ed;
    }

    .producto-placeholder {
        width: 100%;
        height: 140px;
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

    /* CONTENEDOR CARRITO */

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

    /* CARRITO */

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

    .alert-stock {
        margin-top: 10px;
    }

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
    }
</style>

<div class="ventas-container">

    <div class="ventas-header">
        <h2>Registrar venta</h2>
        <p>Selecciona una categoría y agrega los productos a la venta.</p>
    </div>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-stock">
            <strong>No se pudo registrar la venta:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="venta-layout">

        {{-- ============================= --}}
        {{-- PRODUCTOS --}}
        {{-- ============================= --}}

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

            <div class="section-title mt-4">
                Productos
            </div>

            <div class="productos-grid">

                @forelse ($productos as $producto)

                    @php
                        $stock = $producto->stock ? $producto->stock->cantidad : 0;
                    @endphp

                    <div
                        class="producto-card {{ $stock <= 0 ? 'agotado' : '' }}"
                        data-categoria="{{ $producto->categoria_id }}"
                        data-id="{{ $producto->id }}"
                        data-nombre="{{ $producto->nombre }}"
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

        </div>


        {{-- ============================= --}}
        {{-- CARRITO --}}
        {{-- ============================= --}}

        <div class="carrito-section">

            <div class="section-title">
                🛒 Venta actual
            </div>

            <form method="POST" action="/ventas" id="ventaForm">

                @csrf

                <div id="carrito">

                    <div class="carrito-vacio" id="carritoVacio">

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

                    <span class="carrito-total-valor" id="totalVenta">
                        Bs 0.00
                    </span>

                </div>

                <button
                    type="submit"
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


<script>

    /*
    |--------------------------------------------------------------------------
    | CARRITO
    |--------------------------------------------------------------------------
    */

    const carrito = {};

    const productoCards = document.querySelectorAll('.producto-card');

    const carritoContainer = document.getElementById('carrito');

    const carritoVacio = document.getElementById('carritoVacio');

    const totalVenta = document.getElementById('totalVenta');

    const btnRegistrar = document.getElementById('btnRegistrar');

    const btnLimpiar = document.getElementById('btnLimpiar');


    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    const categoriaButtons = document.querySelectorAll('.categoria-btn');

    categoriaButtons.forEach(button => {

        button.addEventListener('click', function () {

            categoriaButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            this.classList.add('active');

            const categoriaId = this.dataset.categoria;

            productoCards.forEach(card => {

                if (card.dataset.categoria === categoriaId) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR PRIMERA CATEGORÍA
    |--------------------------------------------------------------------------
    */

    if (categoriaButtons.length > 0) {

        const primeraCategoria = categoriaButtons[0].dataset.categoria;

        productoCards.forEach(card => {

            if (card.dataset.categoria === primeraCategoria) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | AGREGAR PRODUCTO
    |--------------------------------------------------------------------------
    */

    productoCards.forEach(card => {

        card.addEventListener('click', function () {

            const id = this.dataset.id;

            const nombre = this.dataset.nombre;

            const precio = parseFloat(this.dataset.precio);

            const stock = parseInt(this.dataset.stock);


            // No permitir productos agotados

            if (stock <= 0) {
                return;
            }


            // Si ya existe, aumentar cantidad

            if (carrito[id]) {

                if (carrito[id].cantidad >= stock) {

                    alert('No puedes agregar más unidades de este producto. Stock disponible: ' + stock);

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

        const productos = Object.values(carrito);

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
                        Haz clic sobre un producto para agregarlo.
                    </small>

                </div>
            `;

            btnRegistrar.disabled = true;

        } else {

            btnRegistrar.disabled = false;


            productos.forEach(producto => {

                const subtotal = producto.precio * producto.cantidad;

                total += subtotal;


                const item = document.createElement('div');

                item.className = 'carrito-item';

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


        totalVenta.textContent = 'Bs ' + total.toFixed(2);

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

        if (carrito[id].cantidad >= carrito[id].stock) {

            alert(
                'No puedes agregar más unidades. Stock disponible: ' +
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

        if (carrito[id].cantidad <= 0) {
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

    btnLimpiar.addEventListener('click', function () {

        if (Object.keys(carrito).length === 0) {
            return;
        }

        if (!confirm('¿Deseas quitar todos los productos de la venta?')) {
            return;
        }

        Object.keys(carrito).forEach(id => {
            delete carrito[id];
        });

        actualizarCarrito();

    });

</script>

@endsection