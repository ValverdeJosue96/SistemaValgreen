<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Valgreen</title>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión - Valgreen</title>

    <!-- Bootstrap -->
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
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #eaf3ed,
                #f8f9fa
            );
        }

        .login-container {
            min-height: 100vh;
        }

        .login-card {
            max-width: 430px;
            width: 100%;
            border: none;
            border-radius: 18px;
        }

        .logo {
            width: 75px;
            height: 75px;
            margin: 0 auto 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background-color: var(--valgreen-light);

            font-size: 38px;
        }

        .brand-name {
            color: var(--valgreen);
            font-weight: 700;
            letter-spacing: 1px;
        }

        .btn-valgreen {
            background-color: var(--valgreen);
            color: white;
            border: none;
            padding: 11px;
            font-weight: 600;
        }

        .btn-valgreen:hover {
            background-color: var(--valgreen-dark);
            color: white;
        }

        .form-control {
            padding: 11px 13px;
        }

        .form-control:focus {
            border-color: var(--valgreen);
            box-shadow: 0 0 0 0.2rem rgba(36, 92, 53, 0.15);
        }

        .login-footer {
            font-size: 13px;
            color: #777;
        }

    </style>

</head>


<body>

<div class="container login-container
            d-flex align-items-center
            justify-content-center">

    <div class="card login-card shadow-lg">

        <div class="card-body p-4 p-md-5">


            <!-- LOGO -->

            <div class="text-center mb-4">

                <div class="logo">
                    🍰
                </div>

                <h2 class="brand-name mb-1">
                    VALGREEN
                </h2>

                <p class="text-muted mb-0">
                    Sistema de gestión de repostería
                </p>

            </div>


            <!-- MENSAJE DE ERROR -->

            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        No se pudo iniciar sesión.
                    </strong>

                    <div class="mt-1">

                        {{ $errors->first() }}

                    </div>

                </div>

            @endif


            <!-- LOGIN -->

            <form method="POST"
                  action="/login">

                @csrf


                <!-- USUARIO -->

                <div class="mb-3">

                    <label for="usuario"
                           class="form-label fw-semibold">

                        Usuario

                    </label>

                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        class="form-control"
                        value="{{ old('usuario') }}"
                        placeholder="Ingrese su usuario"
                        required
                        autofocus
                    >

                </div>


                <!-- CONTRASEÑA -->

                <div class="mb-4">

                    <label for="password"
                           class="form-label fw-semibold">

                        Contraseña

                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Ingrese su contraseña"
                        required
                    >

                </div>


                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="btn btn-valgreen w-100">

                    Iniciar sesión

                </button>

            </form>


            <!-- FOOTER -->

            <div class="text-center mt-4 login-footer">

                <div>
                    🍰 Valgreen
                </div>

                <div>
                    Sistema de gestión de repostería
                </div>

            </div>


        </div>

    </div>

</div>


</body>

</html>