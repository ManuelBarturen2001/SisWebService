@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
    <h1 class="custom-headers text-center w-100">BIENVENIDOS AL SISTEMA</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            HOLA, {{ Auth::user()->name }}! 👋
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalConsultas }}</h3>
                        <p>Consultas Realizadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalUsuariosReniec }}</h3>
                        <p>Usuarios RENIEC</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalUsuariosMigraciones }}</h3>
                        <p>Usuarios Migraciones</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $totalProveedores }}</h3>
                        <p>Proveedores Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Gráficos -->
        <div class="row">
            <!-- Consultas por Proveedor -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-database"></i> Consultas por Proveedor
                    </div>
                    <div class="card-body">
                        <canvas id="graficoConsultasProveedor"></canvas>
                    </div>
                </div>
            </div>

            <!-- Consultas por Credencial -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-key"></i> Consultas por Credencial
                    </div>
                    <div class="card-body">
                        <canvas id="graficoConsultasCredencial"></canvas>
                    </div>
                </div>
            </div>

            <!-- Consultas en los Últimos 30 Días -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-calendar-alt"></i> Consultas en los Últimos 30 Días
                    </div>
                    <div class="card-body">
                        <canvas id="graficoConsultasDia"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos desde Laravel
        var consultasProveedor = @json($consultasPorProveedor);
        var consultasReniec = @json($consultasReniec);
        var consultasMigraciones = @json($consultasMigraciones);
        var consultasPorDia = @json($consultasPorDia);

        // Extraer datos para gráfico de proveedores
        var labelsProveedores = consultasProveedor.map(item => item.proveedor);
        var dataProveedores = consultasProveedor.map(item => item.total);

        // Extraer datos para gráfico de credenciales (RENIEC + Migraciones)
        var labelsCredenciales = [...consultasReniec.map(item => "RENIEC ID " + item.credencial_id), 
                                ...consultasMigraciones.map(item => "MIGRACIONES ID " + item.credencial_id)];
        var dataCredenciales = [...consultasReniec.map(item => item.total), 
                                ...consultasMigraciones.map(item => item.total)];

        // Extraer datos para gráfico de consultas por día
        var labelsDias = consultasPorDia.map(item => item.fecha);
        var dataDias = consultasPorDia.map(item => item.total);

        // Crear gráfico de consultas por proveedor
        new Chart(document.getElementById('graficoConsultasProveedor'), {
            type: 'pie',
            data: {
                labels: labelsProveedores,
                datasets: [{
                    label: 'Total Consultas',
                    data: dataProveedores,
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            }
        });

        // Crear gráfico de consultas por credencial
        new Chart(document.getElementById('graficoConsultasCredencial'), {
            type: 'bar',
            data: {
                labels: labelsCredenciales,
                datasets: [{
                    label: 'Total Consultas',
                    data: dataCredenciales,
                    backgroundColor: '#42A5F5'
                }]
            }
        });

        // Crear gráfico de consultas por día
        new Chart(document.getElementById('graficoConsultasDia'), {
            type: 'line',
            data: {
                labels: labelsDias,
                datasets: [{
                    label: 'Consultas por Día',
                    data: dataDias,
                    borderColor: '#FFA726',
                    fill: false
                }]
            }
        });

        // Función para crear un modal dinámico para gráficos
        function createChartModal(chartId, chartTitle, chartType) {
            // Crear un nuevo ID para el canvas del modal
            const modalCanvasId = `modal-${chartId}`;
            
            // Crear el HTML del modal
            const modalHTML = `
            <div class="modal fade" id="modal-${chartId}" tabindex="-1" aria-labelledby="modal-${chartId}-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-${chartId}-label">${chartTitle}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <canvas id="${modalCanvasId}-canvas" width="800" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>`;
            
            // Agregar el modal al DOM
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            return modalCanvasId;
        }

        // Función para clonar un gráfico de Chart.js a un canvas diferente
        function cloneChart(sourceChartId, targetCanvasId) {
            // Obtener el gráfico original
            const sourceChart = Chart.getChart(sourceChartId);
            
            // Si el gráfico existe, clonar sus datos y configuración
            if (sourceChart) {
                const targetCanvas = document.getElementById(targetCanvasId);
                if (targetCanvas) {
                    // Crear un nuevo gráfico con los mismos datos y configuración
                    new Chart(targetCanvas, {
                        type: sourceChart.config.type,
                        data: JSON.parse(JSON.stringify(sourceChart.data)),
                        options: {
                            ...JSON.parse(JSON.stringify(sourceChart.config.options)),
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                tooltip: {
                                    bodyFont: {
                                        size: 14
                                    },
                                    titleFont: {
                                        size: 16
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }

        // Configuración de los modales para cada gráfico
        document.addEventListener('DOMContentLoaded', function() {
            // Lista de gráficos para generar modales
            const charts = [
                { id: 'graficoConsultasProveedor', title: 'Consultas por Proveedor', type: 'pie' },
                { id: 'graficoConsultasCredencial', title: 'Consultas por Credencial', type: 'bar' },
                { id: 'graficoConsultasDia', title: 'Consultas en los Últimos 30 Días', type: 'line' }
            ];
            
            // Generar modal para cada gráfico
            charts.forEach(chart => {
                const chartElement = document.getElementById(chart.id);
                if (chartElement) {
                    // Crear el modal para este gráfico
                    // Crear el modal para este gráfico
                    const modalCanvasId = createChartModal(chart.id, chart.title, chart.type);
                    
                    // Hacer el contenedor del gráfico clickeable
                    const cardContainer = chartElement.closest('.card');
                    if (cardContainer) {
                        cardContainer.style.cursor = 'pointer';
                        
                        // Agregar un evento de clic al contenedor
                        cardContainer.addEventListener('click', function() {
                            // Mostrar el modal
                            const modal = new bootstrap.Modal(document.getElementById(`modal-${chart.id}`));
                            modal.show();
                            
                            // Clonar el gráfico al canvas del modal
                            setTimeout(() => {
                                cloneChart(chart.id, `${modalCanvasId}-canvas`);
                            }, 150); // Pequeño retraso para asegurar que el modal esté visible
                        });
                    }
                }
            });
            
            // Agregar tooltip a los cards para indicar que son clickeables
            document.querySelectorAll('.card').forEach(card => {
                if (card.querySelector('canvas')) {
                    // Agregar overlay con texto de ayuda
                    const overlay = document.createElement('div');
                    overlay.className = 'chart-overlay';
                    overlay.innerHTML = '<span class="overlay-text"><i class="fas fa-search-plus"></i> Click para ampliar</span>';
                    card.querySelector('.card-body').appendChild(overlay);
                }
            });
        });
    </script>
@stop

@section('css')
    <link type="img/ico" rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card-header {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }

        .custom-headers {
            background-color: #2873B4 !important;
            color: white !important;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 22px;
            text-transform: uppercase;
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

        /*  */

        .small-box {
            border-radius: 10px;
            padding: 20px;
            color: white;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .small-box .inner {
            font-size: 18px;
        }

        .small-box .icon {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 40px;
            opacity: 0.5;
        }

        .bg-info { background-color: #17a2b8 !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; color: black !important; }
        .bg-danger { background-color: #dc3545 !important; }

        /*  */

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

        .custom-table {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #ddd;
            padding: 8px;
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
        
        /* Estilos para los modales de gráficos */
        .card-body {
            position: relative;
        }

        .chart-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0 0 10px 10px;
        }

        .card:hover .chart-overlay {
            opacity: 1;
        }

        .overlay-text {
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        /* Estilo para el modal */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background-color: #2873B4;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }

        .modal-body {
            padding: 20px;
            height: 500px; /* Altura fija para el modal */
        }

        /* Estilo para el botón de cerrar */
        .modal-header .btn-close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }
    </style>
@stop