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
        }

        body {
            background-color: #f5f6f8;
        }

        /* Sidebar */

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: var(--valgreen);
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--valgreen-dark);
            color: white;
        }

        /* Contenido */

        .main-content {
            min-height: 100vh;
        }

        .topbar {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        /* Tarjetas */

        .dashboard-card {
            border: none;
            border-radius: 12px;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        /* Botón Valgreen */

        .btn-valgreen {
            background-color: var(--valgreen);
            color: white;
            border: none;
        }

        .btn-valgreen:hover {
            background-color: var(--valgreen-dark);
            color: white;
        }

    </style>

</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->

    <aside class="sidebar p-3">

        <div class="logo text-center text-white mb-4">
            🍰 VALGREEN
        </div>

        @auth

            <div class="text-center text-white mb-4 pb-3 border-bottom">

                <strong>
                    {{ auth()->user()->nombres }}
                </strong>

                <br>

                <small>
                    {{ auth()->user()->usuario }}
                </small>

            </div>

        @endauth


        <nav class="nav flex-column">

            <a href="/dashboard"
               class="nav-link">
                🏠 Dashboard
            </a>

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

            <a href="#"
               class="nav-link">
                📝 Pedidos
            </a>

            <a href="#"
               class="nav-link">
                👥 Usuarios
            </a>

        </nav>


        @auth

            <div class="mt-4">

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


    <!-- CONTENIDO -->

    <main class="main-content flex-grow-1">

        <!-- BARRA SUPERIOR -->

        <div class="topbar p-3">

            <div class="container-fluid">

                <strong>
                    Sistema de gestión de Repostería Valgreen
                </strong>

            </div>

        </div>


        <!-- CONTENIDO DE CADA PÁGINA -->

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