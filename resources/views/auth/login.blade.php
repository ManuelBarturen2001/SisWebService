<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WS-UNPRG</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> <!-- Asegúrate de tener un archivo de estilos -->
    <link type="img/ico" rel="icon" href="{{ asset("favicon.ico")}}">

</head>

<body>
    <div class="contenedor">
        <div class="image_contenedor">
            <img src="{{ asset('assets/fondo_unprg.jpg') }}" alt="Background">
        </div>
        <div class="content-container">
            <div class="rect-contenedor_1">
                <span class="first-titulo">WS - PIDE</span>
                <div class="linea"></div>
                <span class="titulo">SISTEMA DE</span>
                <span class="titulo">GESTIÓN DE </span>
                <span class="titulo">USUARIOS UNPRG</span>
            </div>
            <div class="rect-contenedor">
                <div class="logo">
                    <img src="{{ asset('assets/escudo_act_ofic.png') }}" alt="Logo">
                </div>

                <!-- Formulario de autenticación -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <a href="/google-auth/redirect" class="button">
                        <img src="{{ asset('assets/google-color-icon.svg') }}" alt="Google">
                        <span>{{ __('Correo Institucional') }}</a>
                    </a>
                </form>

                <!-- Aquí agregamos el manejo de errores -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="alert-list">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Redes Sociales -->
                <div class="icon-container">
                    <div class="icon-svg">
                        <a href="https://www.facebook.com/unprgimageninstitucional/?locale=es_LA" target="_blank"><img
                                src="{{ asset('assets/facebook-f-brands-solid.svg') }}" alt="Facebook"></a>
                    </div>
                    <div class="icon-svg">
                        <a href="https://www.instagram.com/unprg_oficial/" target="_blank"><img
                                src="{{ asset('assets/instagram-brands-solid.svg') }}" alt="Instagram"></a>
                    </div>
                    <div class="icon-svg">
                        <a href="https://www.tiktok.com/@unprg?lang=es" target="_blank"><img
                                src="{{ asset('assets/tiktok-brands-solid.svg') }}" alt="TikTok"></a>
                    </div>
                    <div class="icon-svg">
                        <a href="https://www.youtube.com/@universidadnacionalpedroru8627" target="_blank"><img
                                src="{{ asset('assets/youtube-brands-solid.svg') }}" alt="YouTube"></a>
                    </div>
                </div>

                <div class="fecha">
                    <span>2024</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>