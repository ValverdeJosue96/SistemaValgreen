<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nueva contraseña - Valgreen</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h3 class="text-center mb-3">
                        Nueva contraseña
                    </h3>

                    <p class="text-muted text-center">
                        Ingresa tu nueva contraseña.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input
                            type="hidden"
                            name="token"
                            value="{{ $token }}"
                        >

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $email) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            Restablecer contraseña
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}">
                            Volver al inicio de sesión
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>