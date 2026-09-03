<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Valgreen')
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #333;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* MENÚ */

        .sidebar {
            width: 240px;
            background: #245c35;
            color: white;
            padding: 25px 15px;
        }

        .logo {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .usuario {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .menu a:hover {
            background: rgba(255,255,255,0.15);
        }

        .logout {
            margin-top: 30px;
        }

        .logout button {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* CONTENIDO */

        .main {
            flex: 1;
        }

        .topbar {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #ddd;
        }

        .content {
            padding: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        /* BOTONES */

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #245c35;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* TABLAS */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f0f2f3;
        }

        /* FORMULARIOS */

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        textarea {
            min-height: 100px;
        }

        .alert {
            padding: 12px;
            background: #dff0d8;
            border-radius: 6px;
            margin-bottom: 20px;
        }

    </style>
</head>

<body>

<div class="layout">

    <!-- MENÚ LATERAL -->

    <aside class="sidebar">

        <div class="logo">
            VALGREEN
        </div>

        @auth

            <div class="usuario">

                <strong>
                    {{ auth()->user()->nombres }}
                </strong>

                <br>

                <small>
                    {{ auth()->user()->usuario }}
                </small>

            </div>

        @endauth

        <nav class="menu">

            <a href="/dashboard">
                🏠 Dashboard
            </a>

            <a href="/productos">
                📦 Productos
            </a>

            <a href="/stock">
                📊 Stock
            </a>

            <a href="#">
                💰 Ventas
            </a>

            <a href="#">
                📝 Pedidos
            </a>

        </nav>

        @auth

            <div class="logout">

                <form method="POST" action="/logout">

                    @csrf

                    <button type="submit">
                        Cerrar sesión
                    </button>

                </form>

            </div>

        @endauth

    </aside>


    <!-- CONTENIDO PRINCIPAL -->

    <main class="main">

        <div class="topbar">

            <strong>
                Sistema de gestión - Repostería Valgreen
            </strong>

        </div>

        <section class="content">

            @if(session('success'))

                <div class="alert">
                    {{ session('success') }}
                </div>

            @endif

            @yield('content')

        </section>

    </main>

</div>

</body>

</html>