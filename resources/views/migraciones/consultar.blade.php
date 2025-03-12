@extends('adminlte::page')

@section('title', 'MIGRACIONES')

@section('content_header')
    <h1 class="custom-headers text-center w-100">Consultas a través de la Plataforma de Interoperabilidad del Estado - PIDE</h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .consulta-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        .input-group-dni {
            position: relative;
            display: flex;
            width: 100%;
        }

        .input-dni {
            border-radius: 25px 0 0 25px !important;
            padding-left: 20px;
            height: 40px;
        }

        .btn-consultar {
            border-radius: 0 25px 25px 0 !important;
            background-color: #2873B4;
            color: white;
            transition: all 0.3s ease;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
        }
        
        .btn-consultar:hover {
            background-color: #1e5c91;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .resultado-consulta {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }

        .datos-persona-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background-color: #f4f6f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dato-item {
            background-color: white;
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e1e6eb;
            transition: all 0.3s ease;
        }
        
        .dato-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            border-color: #2873B4;
        }

        .label-dato {
            display: block;
            margin-bottom: 5px;
            color: #2873B4;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8em;
        }

        .dato-valor {
            font-size: 1em;
            color: #333;
            font-weight: 500;
            word-break: break-word;
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @media (max-width: 768px) {
            .datos-persona-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .custom-header {
            background-color: #2873B4 !important;
            color: white !important;
        }

        .btn-custom {
            background-color: #FFC300;
            color: black;
            border-color: #FFC300;
        }

        .btn-custom:hover {
            background-color: #e6b300;
            border-color: #e6b300;
        }

        .custom-headers {
            background-color: #2873B4 !important;
            color: white !important;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 24px;
        }

        .footer-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #2873B4;
            color: white;
            padding: 15px;
            width: 100%;
            text-align: center;
            font-size: 16px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-weight: bold;
            position: relative;
        }

        .footer-custom a {
            color: #FFC300;
            text-decoration: none;
            font-weight: bold;
        }

        .footer-custom a:hover {
            color: #FFD700;
        }

        .tol {
            position: relative;
            display: inline-block;
        }

        .tol .tooltiptext {
            visibility: hidden;
            white-space: nowrap;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tol .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: rgba(0, 0, 0, 0.85) transparent transparent transparent;
        }

        .tol:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Estilos para el nuevo loader */
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .loader-spinner {
            position: relative;
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
        }

        .loader-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-radius: 50%;
        }

        .loader-circle-outer {
            border-top-color: #2873B4;
            animation: spin 1s linear infinite;
        }

        .loader-circle-middle {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border-right-color: #FFC300;
            animation: spin 1.2s linear infinite reverse;
        }

        .loader-circle-inner {
            width: 60%;
            height: 60%;
            top: 20%;
            left: 20%;
            border-bottom-color: #2873B4;
            animation: spin 1.5s linear infinite;
        }

        .loader-text {
            color: #2873B4;
            font-weight: 600;
            text-align: center;
            font-size: 18px;
            margin-top: 5px;
        }

        .loader-subtext {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-top: 5px;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.03);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Animación para los resultados */
        .dato-item {
            opacity: 0;
            transform: translateY(20px);
            animation: slideIn 0.4s forwards;
        }
        
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Hacemos que los items aparezcan uno tras otro */
        .dato-item:nth-child(1) { animation-delay: 0.1s; }
        .dato-item:nth-child(2) { animation-delay: 0.2s; }
        .dato-item:nth-child(3) { animation-delay: 0.3s; }
        .dato-item:nth-child(4) { animation-delay: 0.4s; }
        
        /* Efecto de brillo en el botón */
        .btn-consultar::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right, 
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,0.3) 50%,
                rgba(255,255,255,0) 100%
            );
            transform: rotate(30deg);
            transition: all 0.5s;
            opacity: 0;
        }
        
        .btn-consultar:hover::after {
            animation: shine 1.5s ease-in-out;
        }
        
        @keyframes shine {
            0% {
                opacity: 0;
                left: -50%;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                opacity: 0;
                left: 150%;
            }
        }
        
        /* Notificación de éxito */
        .notification-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            transform: translateX(200%);
            transition: transform 0.5s ease;
        }
        
        .notification-success.show {
            transform: translateX(0);
        }
        
        /* Cursor personalizado para la input */
        .input-dni {
            cursor: text;
        }
    </style>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="consulta-container">
                        <form id="formConsulta" class="mb-3">
                            @csrf
                            <div class="input-group-dni mb-3">
                                <input type="text" 
                                       class="form-control input-dni" 
                                       placeholder="Ingrese número (9 dígitos)" 
                                       maxlength="9" 
                                       name="docconsulta" 
                                       required>
                                <button type="submit" class="btn btn-consultar">
                                    <i class="fas fa-search mr-2"></i>Consultar
                                </button>
                            </div>
                        </form>

                        <!-- Nuevo loader personalizado -->
                        <div id="loading" style="display: none;" class="loader-container">
                            <div class="loader-spinner">
                                <div class="loader-circle loader-circle-outer"></div>
                                <div class="loader-circle loader-circle-middle"></div>
                                <div class="loader-circle loader-circle-inner"></div>
                            </div>
                            <div class="loader-text">Consultando PIDE</div>
                            <div class="loader-subtext">Obteniendo información...</div>
                        </div>
                        
                        <div id="error" style="display: none;" class="alert alert-danger"></div>
                        <div id="resultado" class="resultado-consulta" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notificación de éxito -->
<div id="notification-success" class="notification-success">
    <i class="fas fa-check-circle mr-2"></i> Consulta exitosa
</div>
@stop

@section('footer')
    <footer class="footer-custom">
        <span class="tol">
            Copyright © 2025 Oficina de Tecnologias de la Informacion UNPRG<span class="tooltiptext">Desarrollado por
                <a href="https://linkedin.com/in/mbarturen" target="_blank">Manuel Barturen</a>
            </span>
        </span>
    </footer>
@stop

@section('js')
<script>
document.getElementById('formConsulta').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const loading = document.getElementById('loading');
    const error = document.getElementById('error');
    const resultado = document.getElementById('resultado');
    const notificationSuccess = document.getElementById('notification-success');
    const dniInput = this.querySelector('input[name="docconsulta"]');
    
    // Validar que el DNI tenga exactamente 9 dígitos
    if (dniInput.value.length !== 9 || isNaN(dniInput.value)) {
        error.textContent = 'Por favor ingrese un número válido de 9 dígitos.';
        error.style.display = 'block';
        return;
    }
    
    loading.style.display = 'flex';
    error.style.display = 'none';
    resultado.style.display = 'none';
    
    try {
        // Agregamos un pequeño retraso mínimo para asegurar que la animación se vea
        // incluso si la respuesta es muy rápida
        const fetchPromise = fetch('{{ route("migraciones.consultar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                docconsulta: dniInput.value
            })
        });
        
        // Aseguramos que el loader se muestre por al menos 700ms para apreciar la animación
        const [response] = await Promise.all([
            fetchPromise,
            new Promise(resolve => setTimeout(resolve, 700))
        ]);
        
        const data = await response.json();
        
        if (data.persona) {
            const campos = [
                { label: 'Nombres', value: data.persona.nombres_pide },
                { label: 'Primer Apellido', value: data.persona.apepaterno_pide },
                { label: 'Segundo Apellido', value: data.persona.apematerno_pide },
                { label: 'Calidad Migratoria', value: data.persona.calmigratoria_pide }
            ];
            
            let html = `
                <div class="datos-persona-grid">
                    ${campos.map(campo => `
                        <div class="dato-item">
                            <span class="label-dato">${campo.label}</span>
                            <div class="dato-valor">${campo.value || 'No disponible'}</div>
                        </div>
                    `).join('')}
                </div>
            `;
            
            resultado.innerHTML = html;
            resultado.style.display = 'block';
            
            // Mostrar notificación de éxito
            notificationSuccess.classList.add('show');
            setTimeout(() => {
                notificationSuccess.classList.remove('show');
            }, 3000);
        } else {
            throw new Error('No se encontraron datos');
        }
    } catch (err) {
        error.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> No se encontraron datos para el carnet ingresado o hubo un error en la consulta.';
        error.style.display = 'block';
    } finally {
        loading.style.display = 'none';
    }
});

// Validación en tiempo real del campo de entrada
document.querySelector('input[name="docconsulta"]').addEventListener('input', function(e) {
    // Permitir solo dígitos
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@stop