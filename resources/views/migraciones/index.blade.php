@extends('adminlte::page')

@section('title', 'Gestión de Usuarios Migraciones')

@section('content_header')
    <h1 class="custom-headers text-center w-100">Gestión de Usuarios Migraciones</h1>
@stop

@section('css')
    <link type="img/ico" rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .custom-headers {
            background-color: #2873B4 !important;
            color: white !important;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 24px;
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
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                    <i class="fas fa-plus"></i> Nuevo Usuario Migraciones
                </button>
            </div>
            <div class="card-body">
                <table id="usuariosMigracionesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>IP</th>
                            <th>Nivel Acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->username }}</td>
                            <td>{{ $usuario->ip }}</td>
                            <td>{{ $usuario->nivelacceso }}</td>
                            <td>
                                <span class="badge {{ $usuario->estado ? 'bg-success' : 'bg-danger' }}">
                                    {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info editar-usuario" 
                                        data-id="{{ $usuario->id }}"
                                        data-username="{{ $usuario->username}}"
                                        data-ip="{{ $usuario->ip }}"
                                        data-nivelacceso="{{ $usuario->nivelacceso }}"
                                        data-estado="{{ $usuario->estado }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editarUsuarioModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Crear Usuario -->
        <div class="modal fade" id="crearUsuarioModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Usuario Migraciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearUsuario" method="POST" action="{{ route('migraciones.crear') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">IP</label>
                                <input type="text" class="form-control" name="ip" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nivel de Acceso</label>
                                <input type="text" class="form-control" name="nivelacceso" required>
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

        <!-- Modal Editar Usuario -->
        <div class="modal fade" id="editarUsuarioModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario Migraciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formEditarUsuario" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña (Opcional)</label>
                                <input type="password" class="form-control" name="password" placeholder="Cambiar contraseña si es necesario">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">IP</label>
                                <input type="text" class="form-control" name="ip">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nivel de Acceso</label>
                                <input type="text" class="form-control" name="nivelacceso">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-control" name="estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#usuariosMigracionesTable').DataTable({
                language: {
                    url: "../json/mx.json"
                }
            });

            $('#formCrearUsuario').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#crearUsuarioModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message || 'Error al crear usuario');
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al crear usuario');
                        console.log('Error:', xhr);
                    }
                });
            });
            
            // Editar usuario
            $('.editar-usuario').on('click', function() {
                let id = $(this).data('id');
                let username = $(this).data('username');
                let ip = $(this).data('ip');
                let nivelacceso = $(this).data('nivelacceso');
                let estado = $(this).data('estado');
                        
                $('#formEditarUsuario input[name="id"]').val(id);
                $('#formEditarUsuario input[name="username"]').val(username);
                $('#formEditarUsuario input[name="ip"]').val(ip);
                $('#formEditarUsuario input[name="nivelacceso"]').val(nivelacceso);
                $('#formEditarUsuario select[name="estado"]').val(estado);

                $('#formEditarUsuario').attr('action', `/migraciones/${id}/editar`);        
                
            });

            // Submit editar
            $('#formEditarUsuario').on('submit', function(e) {
                e.preventDefault();
                let id = $(this).find('input[name="id"]').val();
                $.ajax({
                    url: `/migraciones/${id}/editar`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.success) {
                            $('#editarUsuarioModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error al actualizar usuario: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@stop