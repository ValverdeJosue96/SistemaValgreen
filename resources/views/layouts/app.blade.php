<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Valgreen')
    </title>

    <!-- Bootstrap 5.3 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        :root {
            --valgreen: #245c35;
            --valgreen-dark: #1b4729;
            --valgreen-light: #eaf3ed;
            --valgreen-soft: #f4f8f5;
            --text-dark: #26352b;
        }

        body {
            background-color: #f5f6f8;
            color: var(--text-dark);
        }


        /* ==========================================
           SIDEBAR
        ========================================== */

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(
                180deg,
                var(--valgreen),
                var(--valgreen-dark)
            );
            position: sticky;
            top: 0;
        }


        .logo {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
        }


        .sidebar-user {
            background-color: rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 12px;
        }


        .sidebar .nav-link {
            color: rgba(255,255,255,0.90);
            padding: 12px 15px;
            border-radius: 9px;
            margin-bottom: 5px;
            transition: 0.2s;
            font-weight: 500;
        }


        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.13);
            color: white;
            transform: translateX(3px);
        }


        /* ==========================================
           CONTENIDO
        ========================================== */

        .main-content {
            min-height: 100vh;
        }


        .topbar {
            background-color: white;
            border-bottom: 1px solid #e4e7e5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        }


        .page-title {
            font-weight: 700;
            color: var(--text-dark);
        }


        /* ==========================================
           TARJETAS
        ========================================== */

        .dashboard-card {
            border: none;
            border-radius: 16px;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.2s;
        }


        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0,0,0,0.08);
        }


        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            background-color: var(--valgreen-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
        }


        .stat-title {
            font-size: 14px;
            color: #718078;
            margin-bottom: 5px;
        }


        .stat-number {
            font-size: 27px;
            font-weight: 700;
            color: var(--text-dark);
        }


        /* ==========================================
           TARJETAS DE ACCIONES
        ========================================== */

        .quick-action {
            text-decoration: none;
            color: var(--text-dark);
            background-color: white;
            border: 1px solid #e4e9e5;
            border-radius: 14px;
            padding: 20px;
            display: block;
            height: 100%;
            transition: 0.2s;
        }


        .quick-action:hover {
            color: var(--valgreen-dark);
            border-color: #b8d0bf;
            background-color: var(--valgreen-soft);
            transform: translateY(-2px);
        }


        .quick-action-icon {
            font-size: 30px;
            margin-bottom: 10px;
        }


        .quick-action-title {
            font-weight: 700;
            margin-bottom: 3px;
        }


        .quick-action-text {
            color: #718078;
            font-size: 13px;
        }


        /* ==========================================
           SECCIONES
        ========================================== */

        .section-card {
            border: none;
            border-radius: 16px;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }


        .section-header {
            padding: 18px 20px;
            border-bottom: 1px solid #edf0ee;
        }


        .section-header h5 {
            font-weight: 700;
            margin: 0;
        }


        /* ==========================================
           BOTONES
        ========================================== */

        .btn-valgreen {
            background-color: var(--valgreen);
            color: white;
            border: none;
        }


        .btn-valgreen:hover {
            background-color: var(--valgreen-dark);
            color: white;
        }


        /* ==========================================
           TABLAS
        ========================================== */

        .table thead th {
            color: #718078;
            font-size: 13px;
            font-weight: 600;
            border-bottom: 1px solid #e8ece9;
        }


        .table tbody td {
            vertical-align: middle;
            padding-top: 13px;
            padding-bottom: 13px;
        }


        /* ==========================================
           BADGES DE ESTADO
        ========================================== */

        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }


        .estado-preparacion {
            background-color: #cfe2ff;
            color: #084298;
        }


        .estado-listo {
            background-color: #d1e7dd;
            color: #0f5132;
        }


        .estado-entregado {
            background-color: #e2e3e5;
            color: #41464b;
        }


        .estado-cancelado {
            background-color: #f8d7da;
            color: #842029;
        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 768px) {

            .sidebar {
                width: 210px;
            }

            .logo {
                font-size: 21px;
            }

            .container-fluid.p-4 {
                padding: 20px !important;
            }

        }

    </style>

</head>


<body>

<div class="d-flex">


    <!-- ==========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar p-3">


        <div class="logo text-center text-white mb-4">

            🍰 VALGREEN

        </div>


        @auth

            <div class="sidebar-user text-center text-white mb-4">

                <strong>
                    {{ auth()->user()->nombres }}
                </strong>

                <br>

                <small class="opacity-75">

                    {{ auth()->user()->usuario }}

                </small>

                <br>

                @if(auth()->user()->rol)

                    <span class="badge bg-light text-success mt-2">

                        {{ auth()->user()->rol->nombre }}

                    </span>

                @endif

            </div>

        @endauth


        <!-- MENÚ -->

        <nav class="nav flex-column">


            <a href="/dashboard"
               class="nav-link">

                🏠 Dashboard

            </a>


            @if(auth()->user()->rol &&
                auth()->user()->rol->nombre === 'Administrador')


                <a href="/productos"
                   class="nav-link">

                    📦 Productos

                </a>


                <a href="/stock"
                   class="nav-link">

                    📊 Stock

                </a>


                <a href="/ventas"
                   class="nav-link">

                    💰 Ventas

                </a>


                <a href="/pedidos"
                   class="nav-link">

                    📝 Pedidos

                </a>


                <a href="/clientes"
                   class="nav-link">

                    👤 Clientes

                </a>


                <a href="/usuarios"
                   class="nav-link">

                    👥 Usuarios

                </a>


            @else


                <a href="/ventas"
                   class="nav-link">

                    💰 Ventas

                </a>


                <a href="/pedidos"
                   class="nav-link">

                    📝 Pedidos

                </a>


                <a href="/clientes"
                   class="nav-link">

                    👤 Clientes

                </a>


                <a href="/stock"
                   class="nav-link">

                    📊 Consultar stock

                </a>


            @endif


        </nav>


        <!-- CERRAR SESIÓN -->

        @auth

            <div class="mt-4 pt-3 border-top">

                <form method="POST"
                      action="/logout">

                    @csrf

                    <button type="submit"
                            class="btn btn-light w-100">

                        🚪 Cerrar sesión

                    </button>

                </form>

            </div>

        @endauth


    </aside>


    <!-- ==========================================
         CONTENIDO PRINCIPAL
    ========================================== -->

    <main class="main-content flex-grow-1">


        <!-- TOPBAR -->

        <div class="topbar p-3">

            <div class="container-fluid">

                <strong>

                    Sistema de gestión de Repostería Valgreen

                </strong>

            </div>

        </div>


        <!-- CONTENIDO -->

        <div class="container-fluid p-4">


            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Se encontraron errores:
                    </strong>

                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')


        </div>


    </main>


</div>


<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>