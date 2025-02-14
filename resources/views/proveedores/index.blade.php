@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')

@section('content_header')
    <h1 class="custom-headers text-center w-100">Gestión de Proveedores</h1>
@stop

@section('css')
    <link type="img/ico" rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">

    <style>
        table.dataTable tbody td,
        table.dataTable thead th {
            text-align: center;
        }

        textarea {
            resize: none;
        }

        label p {
            display: inline;
        }

        .custom-header {
            background-color: #2873B4 !important;
            color: white !important;
        }

        .btn-custom {
            background-color: transparent;
            color: black;
            border-color: transparent;
        }

        .celda-nombre {
            text-align: justify !important;
            width: 35% !important;
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

        /* Estilos para el modal */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.5rem;
            border: none;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Estilos para los inputs */
        .input-wrapper {
            position: relative;
        }

        .custom-input {
            height: 45px;
            padding-left: 15px;
            padding-right: 40px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            border-color: #2873b5;
            box-shadow: 0 0 0 0.2rem rgba(40, 115, 181, 0.15);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .input-icon:hover {
            color: #2873b5 !important;
        }

        /* Estilos para los botones */
        .custom-btn-submit {
            background: linear-gradient(135deg, #2873b5 0%, #7bd4e8 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .custom-btn-submit:hover {
            background: linear-gradient(135deg, #2365a0 0%, #6ac3d7 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 115, 181, 0.2);
            color: white;
        }

        .custom-btn-cancel {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 25px;
            transition: all 0.3s ease;
            color: #737373;
        }

        .custom-btn-cancel:hover {
            background-color: #f8f9fa;
            border-color: #737373;
            color: #737373;
        }

        /* Animaciones */
        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* -- */
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
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearProveedorModal">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </button>
            </div>
            <div class="card-body">
                <table id="proveedoresTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>URL</th>
                            <th>Fecha Creación</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->id }}</td>
                            <td>{{ $proveedor->nombre }}</td>
                            <td>{{ $proveedor->url }}</td>
                            <td>{{ $proveedor->created_at }}</td>
                            <td>{{ $proveedor->updated_at }}</td>
                            <td>
                                <button class="btn btn-sm btn-info editar-proveedor" 
                                        data-id="{{ $proveedor->id }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editarProveedorModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger eliminar-proveedor" 
                                        data-id="{{ $proveedor->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Crear Proveedor -->
        <div class="modal fade" id="crearProveedorModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearProveedor" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input type="text" class="form-control" name="url" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Editar Proveedor -->
        <div class="modal fade" id="editarProveedorModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formEditarProveedor" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input type="text" class="form-control" name="url" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
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
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#proveedoresTable').DataTable({
                language: {
                    url: "../json/mx.json"
                }
            });

            // Crear proveedor
            $('#formCrearProveedor').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/proveedores/agregar',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.success) {
                            $('#crearProveedorModal').modal('hide');
                            Swal.fire('¡Éxito!', 'Proveedor creado exitosamente', 'success')
                            .then(() => location.reload());
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error al crear el proveedor', 'error');
                    }
                });
            });

            // Editar proveedor
            $('.editar-proveedor').on('click', function() {
                let id = $(this).data('id');
                $.get(`/proveedores/${id}`, function(response) {
                    if(response.success) {
                        let proveedor = response.data;
                        $('#formEditarProveedor input[name="id"]').val(id);
                        $('#formEditarProveedor').attr('action', `/proveedores/${id}/editar`);
                        $('#formEditarProveedor input[name="nombre"]').val(proveedor.nombre);
                        $('#formEditarProveedor input[name="url"]').val(proveedor.url);
                    }
                });
            });

            // Submit editar
            $('#formEditarProveedor').on('submit', function(e) {
                e.preventDefault();
                let id = $(this).find('input[name="id"]').val();
                $.ajax({
                    url: `/proveedores/${id}/editar`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.success) {
                            $('#editarProveedorModal').modal('hide');
                            Swal.fire('¡Éxito!', 'Proveedor actualizado exitosamente', 'success')
                            .then(() => location.reload());
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error al actualizar el proveedor', 'error');
                    }
                });
            });

            // Eliminar proveedor
            $('.eliminar-proveedor').on('click', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede revertir",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/proveedores/${id}/eliminar`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire('¡Eliminado!', 'Proveedor eliminado exitosamente', 'success')
                                    .then(() => location.reload());
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Error al eliminar el proveedor', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop