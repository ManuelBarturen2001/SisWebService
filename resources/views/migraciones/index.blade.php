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
    <link rel="stylesheet" href="{{ asset('css/migraciones.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                            <th>Num Consultas</th>
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
                            <td>{{ $usuario->n_consult}}</td>
                            <td>
                                <button class="btn btn-sm btn-info editar-usuario" 
                                        data-id="{{ $usuario->id }}"
                                        data-username="{{ $usuario->username}}"
                                        data-ip="{{ $usuario->ip }}"
                                        data-nivelacceso="{{ $usuario->nivelacceso }}"
                                        data-estado="{{ $usuario->estado }}"
                                        data-n_consult="{{ $usuario->n_consult }}"
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
            Copyright © 2025 Oficina de Tecnologias de la Informacion UNPRG<span class="tooltiptext">Desarrollado por
                <a href="https://linkedin.com/in/mbarturen" target="_blank">Manuel Barturen</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            Swal.fire('¡Éxito!', 'Usuario Migraciones creado exitosamente', 'success')
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Error', 'Error al crear el usuario', 'error');
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
                            Swal.fire('¡Éxito!', 'Usuario Migraciones actualizado exitosamente', 'success')
                            .then(() => location.reload());
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error al actualizar el usuario', 'error');
                    }
                });
            });
        });
    </script>
@stop