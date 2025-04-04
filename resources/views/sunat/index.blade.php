@extends('adminlte::page')

@section('title', 'SUNAT')

@section('content_header')
    <h1 class="custom-headers text-center w-100">Consultas a través de la Plataforma de Interoperabilidad del Estado - PIDE</h1>
@stop

@section('css')
    <link type="img/ico" rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
    <style>
        .input-group-ruc {
            position: relative;
            display: flex;
            width: 100%;
        }
        .input-ruc {
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
            grid-template-columns: repeat(4, 1fr);
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
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .small-box {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .small-box:hover {
            transform: scale(1.05);
        }

        .small-box .icon {
            color: rgba(255, 255, 255, 0.3);
            z-index: 0;
        }

        .card-body canvas {
            width: 100% !important;
            height: auto !important;
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

        .loading-container {
            text-align: center;
            padding: 25px;
            margin: 20px auto;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .loading-text {
            margin-top: 15px;
            color: #2873B4;
            font-weight: 500;
            font-size: 16px;
            animation: pulse-text 1.5s infinite;
        }

        /* Nueva animación de búsqueda de documentos */
        .document-search {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .document-icon {
            position: relative;
            width: 60px;
            height: 75px;
            background-color: #fff;
            border: 2px solid #2873B4;
            border-radius: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .document-icon:before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            height: 4px;
            background-color: #2873B4;
            border-radius: 2px;
        }

        .document-icon:after {
            content: "";
            position: absolute;
            top: 20px;
            left: 10px;
            right: 10px;
            height: 4px;
            background-color: #2873B4;
            border-radius: 2px;
        }

        .document-lines {
            position: absolute;
            top: 30px;
            left: 10px;
            right: 10px;
            height: 4px;
            background-color: #2873B4;
            border-radius: 2px;
        }

        .document-lines:before {
            content: "";
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #2873B4;
            border-radius: 2px;
        }

        .document-lines:after {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 15px;
            height: 4px;
            background-color: #2873B4;
            border-radius: 2px;
        }

        .magnifier {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #FFC300;
            border-radius: 50%;
            top: 20px;
            right: -15px;
            animation: searching 2s infinite;
        }

        .magnifier:before {
            content: "";
            position: absolute;
            width: 3px;
            height: 15px;
            background-color: #FFC300;
            bottom: -13px;
            right: 5px;
            transform: rotate(45deg);
            border-radius: 3px;
        }

        /* Animación lateral de búsqueda */
        @keyframes searching {
            0% {
                transform: translateX(0px) rotate(0deg);
            }
            25% {
                transform: translateX(-70px) rotate(-5deg);
            }
            50% {
                transform: translateX(-70px) rotate(5deg);
            }
            75% {
                transform: translateX(0px) rotate(0deg);
            }
            100% {
                transform: translateX(0px) rotate(0deg);
            }
        }

        /* Animación de pulso para texto */
        @keyframes pulse-text {
            0% {
                opacity: 0.7;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0.7;
            }
        }

        /* Animación para barras de progreso */
        .progress-container {
            margin-top: 15px;
            height: 5px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0;
            background-color: #2873B4;
            animation: progress 2s infinite;
        }

        @keyframes progress {
            0% {
                width: 0%;
            }
            50% {
                width: 70%;
            }
            100% {
                width: 100%;
            }
        }

        /* Estilo para el mensaje de error */
        #error {
            padding: 15px;
            border-radius: 8px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            margin-top: 15px;
            font-weight: 500;
            animation: fadeIn 0.5s ease;
        }

        /* Ajuste para pantallas pequeñas */
        @media (max-width: 768px) {
            .datos-persona-grid {
                grid-template-columns: 1fr !important;
            }
            .loading-container {
                padding: 15px;
            }
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
                            <div class="input-group-ruc mb-3">
                                <input type="text" 
                                       class="form-control input-ruc" 
                                       placeholder="Ingrese número de RUC (11 dígitos)" 
                                       maxlength="11" 
                                       name="numRuc" 
                                       required>
                                <button type="submit" class="btn btn-consultar">
                                    <i class="fas fa-search mr-2"></i>Consultar
                                </button>
                            </div>
                        </form>

                         <!-- Animación de carga para SUNAT -->
                         <div id="loading" class="loading-container" style="display: none;">
                            <div class="document-search">
                                <div class="document-icon">
                                    <div class="document-lines"></div>
                                    <div class="magnifier"></div>
                                </div>
                            </div>
                            <p class="loading-text">Consultando información del contribuyente...</p>
                            <div class="progress-container">
                                <div class="progress-bar"></div>
                            </div>
                        </div>

                        <div id="error" style="display: none;"></div>
                        <div id="resultado" class="resultado-consulta" style="display: none;"></div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('footer')
    <footer class="footer-custom">
        <span class="tol">
            Copyright © 2025 Oficina de Tecnologias de la Informacion UNPRG<span class="tooltiptext">Desarrollado por
                <a href="https://linkedin.com/in/mbarturen" target="_blank">Manuel Barturen</a>
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
    
    loading.style.display = 'block';
    error.style.display = 'none';
    resultado.style.display = 'none';
    
    try {
        const [response] = await Promise.all([
            fetch('{{ route("sunat.consultar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    numRuc: this.querySelector('input[name="numRuc"]').value
                })
            }),
            new Promise(resolve => setTimeout(resolve, 1000))
        ]);
        
        const data = await response.json();
        
        if (data.success) {
            const formatBoolean = (value) => value ? 'Activo' : 'Inactivo';
            
            const campos = [
                { label: 'Código de ubigeo', value: data.datos.ddp_ubigeo_pide },
                { label: 'Código de departamento', value: data.datos.cod_dep_pide },
                { label: 'Descripción de departamento', value: data.datos.desc_dep_pide },
                { label: 'Código de provincia', value: data.datos.cod_prov_pide },
                { label: 'Descripción de provincia', value: data.datos.desc_prov_pide },
                { label: 'Código de distrito', value: data.datos.cod_dist_pide },
                { label: 'Descripción de distrito', value: data.datos.desc_dist_pide },
                { label: 'Código de actividad económica', value: data.datos.ddp_ciiu_pide },
                { label: 'Descripción de actividad económica', value: data.datos.desc_ciiu_pide },
                { label: 'Estado del contribuyente', value: data.datos.ddp_estado_pide },
                { label: 'Descripción del estado del contribuyente', value: data.datos.desc_estado_pide },
                { label: 'Fecha y hora de actualización', value: data.datos.ddp_fecact_pide },
                { label: 'Fecha de alta', value: data.datos.ddp_fecalt_pide },
                { label: 'Fecha de baja', value: data.datos.ddp_fecbaj_pide },
                { label: 'Tipo de persona', value: data.datos.ddp_identi_pide },
                { label: 'Descripción de tipo de persona', value: data.datos.desc_identi_pide },
                { label: 'Librería Tributaria', value: data.datos.ddp_lllttt_pide },
                { label: 'Nombre o Razón Social', value: data.datos.ddp_nombre_pide },
                { label: 'Nombre de la vía', value: data.datos.ddp_nomvia_pide },
                { label: 'Número', value: data.datos.ddp_numer1_pide },
                { label: 'Interior', value: data.datos.ddp_inter1_pide },
                { label: 'Nombre de la zona', value: data.datos.ddp_nomzon_pide },
                { label: 'Referencia de ubicación', value: data.datos.ddp_refer1_pide },
                { label: 'Condición del domicilio', value: data.datos.ddp_flag22_pide },
                { label: 'Descripción de la condición del domicilio', value: data.datos.desc_flag22_pide },
                { label: 'Código de dependencia', value: data.datos.ddp_numreg_pide },
                { label: 'Descripción de la dependencia', value: data.datos.desc_numreg_pide },
                { label: 'Número de Ruc', value: data.datos.ddp_numruc_pide },
                { label: 'Código de tipo de vía', value: data.datos.dpp_tipvia_pide },
                { label: 'Descripción de tipo de vía', value: data.datos.desc_tipvia_pide },
                { label: 'Código de tipo de zona', value: data.datos.dpp_tipzon_pide },
                { label: 'Descripción de tipo de zona', value: data.datos.desc_tipzon_pide },
                { label: 'Tipo de contribuyente', value: data.datos.dpp_tpoemp_pide },
                { label: 'Descripción de contribuyente', value: data.datos.desc_tpoemp_pide },
                { label: 'Código de secuencia', value: data.datos.ddp_secuen_pide },
                { label: 'Estado Activo', value: formatBoolean(data.datos.esActivo_pide) },
                { label: 'Estado Habido', value: formatBoolean(data.datos.esHabido_pide) }
            ];
            
            resultado.innerHTML = `
                <div class="datos-persona-grid">
                    ${campos.map(campo => `
                        <div class="dato-item">
                            <span class="label-dato">${campo.label}</span>
                            <span class="dato-valor">${campo.value || '-'}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            resultado.style.display = 'block';
        } else {
            error.textContent = data.message;
            error.style.display = 'block';
        }
    } catch (err) {
        error.textContent = 'Hubo un problema con la consulta';
        error.style.display = 'block';
    } finally {
        loading.style.display = 'none';
    }
});
</script>
@stop