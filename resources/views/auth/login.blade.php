<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Usuarios UNPRG</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa; height: 100vh;display: flex; justify-content: center; align-items: center; overflow: hidden;background: url('images/foto8.jpg') no-repeat center center fixed;background-size: cover;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            height: auto;
            min-height: 600px;
            display: flex;
            flex-direction: row;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            position: relative;
        }

        .knowledge-wall {
            width: 55%;
            background: linear-gradient(135deg, #2b7ec6, #2873b4, #474748);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .knowledge-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
        }

        .knowledge-title {
            color: white;
            font-size: 43px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
            margin-top: 25px;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
        }

        .floating-element {
            position: absolute;
            opacity: 0.2;
            filter: blur(1px);
        }

        .user-icon,
        .users-group,
        .permission-icon,
        .role-badge,
        .user-list {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            backdrop-filter: blur(2px);
        }

        .users-group {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(2px);
        }

        .permission-icon {
            width: 55px;
            height: 55px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            backdrop-filter: blur(2px);
        }

        .role-badge {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            backdrop-filter: blur(2px);
        }

        .user-list {
            width: 55px;
            height: 55px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 5px;
            backdrop-filter: blur(2px);
        }

        /* Animaciones para elementos flotantes */
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        .user-icon {
            animation-delay: 0s;
        }

        .users-group {
            animation-delay: 1s;
        }

        .permission-icon {
            animation-delay: 2s;
        }

        .role-badge {
            animation-delay: 1.5s;
        }

        .user-list {
            animation-delay: 0.5s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .login-side {
            width: 45%;
            background: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .welcome-text {
            font-size: 30px;
            font-weight: 600;
            color: #35465f;
            margin-bottom: 50px;
            text-align: center;
        }

        .card-reader {
            width: 325px;
            height: 290px;
            border-radius: 15px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05),
                inset 0 -5px 10px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-reader:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1),
                inset 0 -5px 10px rgba(0, 0, 0, 0.03);
        }

        .scan-button {
            width: 210px;
            height: 45px;
            background: linear-gradient(57deg, #474748, #3b82f6);
            border: none;
            border-radius: 22px;
            color: white;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
            outline: none;
            position: relative;
            overflow: hidden;
            margin-top: 40px;
            text-decoration: none;
        }

        .scan-button span {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
        }

        .scan-button svg {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .scan-button:hover {
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .scan-button:hover svg {
            transform: rotate(90deg);
        }

        .scan-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right,
                    transparent,
                    rgba(255, 255, 255, 0.1),
                    transparent);
            transition: left 0.7s ease;
        }

        .scan-button:hover::before {
            left: 100%;
        }

        .card-image {
            position: absolute;
            width: 200px;
            height: 120px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background: url('/api/placeholder/200/120');
            background-size: cover;
            border-radius: 10px;
            opacity: 0;
            transition: all 0.5s ease;
        }

        .university-name {
            position: absolute;
            bottom: 15px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        .alert {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            color: #721c24;
            width: 100%;
            max-width: 300px;
        }

        .alert ul {
            list-style-type: none;
            padding-left: 10px;
        }

        .alert-list {
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Estilos responsivos para dispositivos móviles y tablets */
        @media only screen and (max-width: 1024px) {
            .container {
                width: 90%;
                height: auto;
            }

            .knowledge-title {
                font-size: 32px;
            }
        }

        @media only screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
                min-height: auto;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            }

            .knowledge-wall {
                width: 100%;
                padding: 30px 20px;
                order: 1;
                min-height: 200px;
            }

            .login-side {
                width: 100%;
                padding: 30px 20px;
                order: 2;
            }

            .knowledge-title {
                font-size: 28px;
                margin-top: 10px;
            }

            .card-reader {
                width: 100%;
                max-width: 325px;
                height: 250px;
            }

            .floating-element {
                transform: scale(0.8);
            }

            .welcome-text {
                margin-bottom: 30px;
                font-size: 26px;
            }

            .university-name {
                position: relative;
                margin-top: 30px;
                bottom: auto;
            }
        }

        @media only screen and (max-width: 480px) {
            .container {
                width: 95%;
                border-radius: 15px;
            }

            .knowledge-wall {
                padding: 20px 15px;
                min-height: 150px;
            }

            .knowledge-title {
                font-size: 22px;
                margin-bottom: 10px;
            }

            .card-reader {
                height: 220px;
            }

            .welcome-text {
                font-size: 22px;
                margin-bottom: 20px;
            }

            .scan-button {
                width: 190px;
                height: 40px;
                font-size: 14px;
            }

            .scan-button span img {
                width: 16px;
            }

            .floating-elements {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="knowledge-wall">
            <div class="knowledge-pattern"></div>
            <div class="floating-elements">
                <!-- Elementos flotantes que simulan gestión de usuarios -->
                <div class="floating-element user-icon" style="top: 10%; left: 20%;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4a4 4 0 100 8 4 4 0 000-8zm0 10c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z" />
                    </svg>
                </div>
                <div class="floating-element users-group" style="top: 70%; left: 70%;">
                    <svg width="35" height="35" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                </div>
                <div class="floating-element permission-icon" style="top: 40%; left: 60%;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                    </svg>
                </div>
                <div class="floating-element role-badge" style="top: 80%; left: 30%;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 6h-3V4c0-1.11-.89-2-2-2H9c-1.11 0-2 .89-2 2v2H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM9 4h6v2H9V4zm11 15H4v-2h16v2zm0-5H4V8h3v2h2V8h6v2h2V8h3v6z" />
                    </svg>
                </div>
                <div class="floating-element user-list" style="top: 20%; left: 40%;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                    </svg>
                </div>
            </div>

            <h1 class="knowledge-title">Consultas de Interoperabilidad Digital de Estado - UNPRG</h1>
        </div>

        <div class="login-side">
            <h2 class="welcome-text">BIENVENIDO</h2>

            <div class="card-reader" id="card-reader">
                <div class="university-logo">
                    <img width="85px" alt="Google sign-in" src="assets/escudo_act_ofic.png">
                </div>

                <!-- Formulario de autenticación -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <a href="/google-auth/redirect" class="scan-button" id="scan-button">
                        <span>
                            <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in"
                                src="assets/google-color-icon.svg">
                            Correo Institucional
                        </span>
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
                <div class="card-image" id="card-image"></div>
            </div>

            <div class="university-name">© 2025 Universidad Nacional Pedro Ruiz Gallo</div>
        </div>
    </div>

    <script>
        const scanButton = document.getElementById('scan-button');
        const cardReader = document.getElementById('card-reader');
        const cardImage = document.getElementById('card-image');

        scanButton.addEventListener('click', () => {
            window.location.href = '#dashboard';
        });
    </script>
</body>

</html>