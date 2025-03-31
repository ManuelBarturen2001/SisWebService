@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
    <h1 class="custom-headers text-center w-100">BIENVENIDO AL SISTEMA</h1>
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
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%; height: 100%; position: relative;">
                            <canvas id="graficoConsultasProveedor"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultas por Credencial -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-key"></i> Consultas por Credencial
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%; height: 100%; position: relative;">
                            <canvas id="graficoConsultasCredencial"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultas en los Últimos 30 Días -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-calendar-alt"></i> Consultas en los Últimos 7 Días
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%; height: 100%; position: relative;">
                            <canvas id="graficoConsultasDia"></canvas>
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

    // Función para establecer opciones responsivas comunes para los gráficos
    function getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    enabled: true
                }
            }
        };
    }

    // Función para crear o actualizar gráfico de consultas por día
    function crearGraficoDia(labels, data, canvasId) {
        const ctx = document.getElementById(canvasId);
        
        // Destruir gráfico existente si hay uno
        const existingChart = Chart.getChart(canvasId);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Consultas por Día',
                    data: data,
                    borderColor: '#FFA726',
                    backgroundColor: 'rgba(255, 167, 38, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...getChartOptions(),
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Función para actualizar los gráficos de consultas por día
    function actualizarGraficosDia(datos) {
        // Extraer labels y data
        const labels = datos.map(item => item.fecha);
        const data = datos.map(item => item.total);

        // Actualizar gráfico pequeño
        crearGraficoDia(labels, data, 'graficoConsultasDia');

        // Actualizar gráfico en el modal si existe
        const modalCanvasId = 'modal-graficoConsultasDia-canvas';
        const modalCanvas = document.getElementById(modalCanvasId);
        if (modalCanvas) {
            crearGraficoDia(labels, data, modalCanvasId);
        }
    }

    // Crear gráfico de consultas por proveedor
    new Chart(document.getElementById('graficoConsultasProveedor'), {
        type: 'pie',
        data: {
            labels: labelsProveedores,
            datasets: [{
                label: 'Total Consultas',
                data: dataProveedores,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        },
        options: getChartOptions()
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
        },
        options: {
            ...getChartOptions(),
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Crear gráfico de consultas por día inicial
    crearGraficoDia(labelsDias, dataDias, 'graficoConsultasDia');

    // Función para crear un modal dinámico para gráficos
    function createChartModal(chartId, chartTitle, chartType) {
        // Crear un nuevo ID para el canvas del modal
        const modalCanvasId = `modal-${chartId}`;
        
        // Crear el HTML del modal
        const modalHTML = `
        <div class="modal fade" id="modal-${chartId}"
        ${chartId === 'graficoConsultasDia' ? 'class="modal-consultas-dia"' : ''}
        tabindex="-1" aria-labelledby="modal-${chartId}-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-${chartId}-label">${chartTitle}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${chartId === 'graficoConsultasDia' ? `
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Fecha Inicio</label>
                                    <input type="date" id="fecha-inicio" class="form-control">
                                </div>
                                <div class="col-md-5">
                                    <label>Fecha Fin</label>
                                    <input type="date" id="fecha-fin" class="form-control">
                                </div>
                                <div class="col-md-2 align-self-end">
                                    <button id="buscar-fechas" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </div>` : ''}
                        <canvas id="${modalCanvasId}-canvas" width="800" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Agregar el modal al DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        return modalCanvasId;
    }

    // Configuración de los modales para cada gráfico
    document.addEventListener('DOMContentLoaded', function() {
        // Lista de gráficos para generar modales
        const charts = [
            { id: 'graficoConsultasProveedor', title: 'Consultas por Proveedor', type: 'pie' },
            { id: 'graficoConsultasCredencial', title: 'Consultas por Credencial', type: 'bar' },
            { id: 'graficoConsultasDia', title: 'Consultas en los Últimos 7 Días', type: 'line' }
        ];
        
        // Establecer altura fija para los contenedores de gráficos
        document.querySelectorAll('.card.h-100 .card-body').forEach(cardBody => {
            cardBody.style.height = '250px';
        });
        
        // Generar modal para cada gráfico
        charts.forEach(chart => {
            const chartElement = document.getElementById(chart.id);
            if (chartElement) {
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
                            // Si es el gráfico de consultas por día, configurar búsqueda
                            if (chart.id === 'graficoConsultasDia') {
                                const searchButton = document.getElementById('buscar-fechas');
                                if (searchButton) {
                                    searchButton.addEventListener('click', function() {
                                        const fechaInicio = document.getElementById('fecha-inicio').value;
                                        const fechaFin = document.getElementById('fecha-fin').value;

                                        if (fechaInicio && fechaFin) {
                                            fetch(`/consultas-por-fecha?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
                                                .then(response => response.json())
                                                .then(datos => {
                                                    actualizarGraficosDia(datos);
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Hubo un problema al buscar los datos');
                                                });
                                        } else {
                                            alert('Por favor selecciona ambas fechas');
                                        }
                                    });
                                }
                            }

                            // Clonar el gráfico
                            const sourceChart = Chart.getChart(chart.id);
                            const targetCanvas = document.getElementById(`${modalCanvasId}-canvas`);
                            
                            if (sourceChart && targetCanvas) {
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

        /* Ajustes para los gráficos */
        .card-body {
            position: relative;
            padding: 1.25rem;
            display: flex;
            justify-content: center;
            align-items: center;
            
        }

        /* Asegura que los canvas ocupen todo el espacio disponible */
        .card-body > div {
            width: 100%;
            height: 100%;
        }

        .card-body canvas {
            max-width: 100%;
            max-height: 100%;
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


        /*aqui*/
        .modal-body {
            padding: 20px;
            height: 500px; /* Altura fija para el modal */
        }
        /*hasta aqui*/

    

        /* Estilo para el botón de cerrar */
        .modal-header .btn-close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }
        /* Estilos específicos para el modal de Consultas por Día */
        #modal-graficoConsultasDia .modal-body {
            position: relative;
            padding: 20px;
            height: 500px; /* Altura fija que se ajuste bien */
        }

        #modal-graficoConsultasDia .modal-body .container {
            margin-bottom: 20px;
        }

        #modal-graficoConsultasDia .modal-body .row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        #modal-graficoConsultasDia .modal-body canvas {
            width: 100% !important;
            height: calc(100% - 100px) !important; /* Ajusta el espacio para los inputs */
            max-height: 400px;
        }

        #modal-graficoConsultasDia .modal-body .form-control {
            border-radius: 5px;
            border: 1px solid #2873B4;
        }

        #modal-graficoConsultasDia .modal-body #buscar-fechas {
            background-color: #2873B4;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            margin-top: 22px;
        }

        @media (max-width: 768px) {
            #modal-graficoConsultasDia .modal-body {
                height: 450px;
            }
            
            #modal-graficoConsultasDia .modal-body .row {
                flex-direction: column;
            }
            
            #modal-graficoConsultasDia .modal-body canvas {
                height: calc(100% - 150px) !important;
            }
        }
    </style>
@stop