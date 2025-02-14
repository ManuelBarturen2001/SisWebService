@extends('adminlte::page')

@section('title', 'RENIEC')

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
        .imagen-container {
            margin-top: 20px;
            max-width: 150px;
            margin: 20px auto 0;
            display: block;
            visibility: visible;
            opacity: 1;
            transition: opacity 0.5s ease;
            pointer-events: none;
            position: relative;
        }
        .imagen {
            width: 100%;
            height: auto;
            border-radius: 10px;
            pointer-events: none;
        }
        .imagen-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
            pointer-events: none;
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
                                       placeholder="Ingrese número de DNI (8 dígitos)" 
                                       maxlength="8" 
                                       name="nuDniConsulta" 
                                       required>
                                <button type="submit" class="btn btn-consultar">
                                    <i class="fas fa-search mr-2"></i>Consultar
                                </button>
                            </div>
                        </form>

                        <div id="loading" style="display: none;">Cargando...</div>
                        <div id="error" style="display: none;" class="alert alert-danger"></div>
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
            Copyright © 2024 Oficina de Tecnologias de la Informacion UNPRG.<span class="tooltiptext">Developed by
                <a href="https://linkedin.com/in/mbarturen" target="_blank">J.M.B.CH</a>
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
        const response = await fetch('{{ route("reniec.consultar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                nuDniConsulta: this.querySelector('input[name="nuDniConsulta"]').value
            })
        });
        
        const data = await response.json();
        
        if (data.persona) {
            const campos = [
                { label: 'Nombres', value: data.persona.nombre },
                { label: 'Primer Apellido', value: data.persona.paterno },
                { label: 'Segundo Apellido', value: data.persona.materno },
                { label: 'Dirección', value: data.persona.direccion_pide },
                { label: 'Estado Civil', value: data.persona.estadoCivil_pide },
                { label: 'Ubigeo', value: data.persona.ubigeo_pide },
                { label: 'Restricción', value: data.persona.restriccion_pide }
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
            
            if (data.persona.foto_pide) {
                html += `
                    <div class="imagen-container">
                        <img src="data:image/jpeg;base64,${data.persona.foto_pide}" 
                             alt="Foto de la persona" 
                             class="imagen">
                        <div class="imagen-overlay"></div>
                    </div>
                `;
            }
            
            resultado.innerHTML = html;
            resultado.style.display = 'block';
        } else {
            throw new Error('No se encontraron datos');
        }
    } catch (err) {
        error.textContent = 'No se encontraron datos para el DNI ingresado o hubo un error en la consulta.';
        error.style.display = 'block';
    } finally {
        loading.style.display = 'none';
    }
});

// Prevenir clic derecho y selección en la imagen
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.imagen-container')) {
            e.preventDefault();
        }
    });
    
    document.addEventListener('selectstart', function(e) {
        if (e.target.closest('.imagen-container')) {
            e.preventDefault();
        }
    });
});
</script>
@stop