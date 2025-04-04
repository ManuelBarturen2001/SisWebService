<?php

namespace App\Http\Controllers;

use App\Models\Migraciones as UserMigraciones;
use App\Models\Proveedores as Proveedor;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class MigracionesController extends Controller
{

    public function index()
    {
        try {
            $usuarios = UserMigraciones::all();

            $usuarios->transform(function ($user) {
                if (!empty($user->username)) {
                    $user->username = $this->decrypt($user->username);
                }

                if (!empty($user->ip)) {
                    $user->ip = $this->decrypt($user->ip);
                }

                if (!empty($user->nivelacceso)) {
                    $user->nivelacceso = $this->decrypt($user->nivelacceso);
                }
                
                return $user; // Retorna el objeto en lugar de un array
            });

            return view('migraciones.index', ['usuarios' => $usuarios]);
        } catch (\Exception $error) {
            Log::error('Error al listar usuarios: ' . $error->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los usuarios MIGRACIONES.');
        }
    }

    public function showConsultarForm()
    {
        return view('migraciones.consultar');
    }


    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}


    private const IV_LENGTH = 16;
    private const ENCRYPTION_KEY = 'oFiC1n@Tcn0%lA$b@rtvr3Nu$pRg%njv';

    private $errores = [
        "400" => "Error al consumir servicio de consultas",
        "403" => "Acceso prohibido",
        "0010" => "No se encontraron datos personales",
        "0011" => "No se encontraron datos de historial de movimiento migratorio",
        "0012" => "No se encontraron datos de historial de pasaporte",
        "0013" => "No se encontraron datos de historial de documento",
        "0014" => "No se encontraron datos de historial de carné de extranjeria",
        "0015" => "No se encontraron datos de historial de ptp",
        "0016" => "No se encontraron datos de imagen",
        "0201" => "Tipo de consulta no existe",
        "0202" => "Nivel de acceso no ingresado",
        "0303" => "No existe persona(consulta)",
        "0304" => "No existe Usuario registrado para datos ingresados",
        "0305" => "El token no existe o ya ha sido utilizado",
        "1000" => "Nivel de acceso no ingresado",
        "1001" => "Longitud errónea en Nivel de acceso",
        "1002" => "DNI registrado, no ingresado",
        "1003" => "Longitud errónea en DNI registrado",
        "1004" => "Nombre en consulta, no ingresado",
        "1005" => "Longitud errónea en DNI registrado",
        "1006" => "Apellido Paterno en consulta, no ingresado",
        "1007" => "Longitud errónea en Apellido Paterno en consulta",
        "1008" => "Apellido Materno en consulta, no ingresado",
        "1009" => "Longitud errónea en Apellido Materno en consulta",
        "1010" => "País de nacionalidad en consulta, no ingresado/erróneo",
        "1011" => "Longitud errónea en País de nacionalidad en consulta",
        "1012" => "ID de persona en consulta, no ingresado/erróneo",
        "1013" => "Longitud errónea en ID de persona en consulta",
        "3001" => "Usuario dado de baja",
        "3003" => "Fecha de convenio vencida",
        "3004" => "El token ha expirado",
        "3005" => "El enlace no existe o ya ha sido utilizado",
        "3006" => "Longitud errónea de contraseña",
        "3007" => "Longitud errónea de correo",
        "3008" => "Correo registrado, no ingresado",
        "3009" => "Formato erróneo de correo",
        "9000" => "Error al procesar información",
        "9001" => "Se presentó un problema al intentar enviar el correo electrónico",
        "Error" => "El proveedor de los Datos no esta devolviendo respuesta",
    ];

    private function encrypt($text)
    {
        $iv = openssl_random_pseudo_bytes(self::IV_LENGTH);
        $encrypted = openssl_encrypt($text, 'aes-256-cbc', self::ENCRYPTION_KEY, 0, $iv);
        return json_encode([
            'iv' => bin2hex($iv),
            'content' => bin2hex($encrypted)
        ]);
    }

    private function decrypt($encryptedString)
    {
        try {
            if (is_string($encryptedString) && strpos($encryptedString, '{') === 0) {
                $data = json_decode($encryptedString, true);
                $iv = hex2bin($data['iv']);
                $encrypted = hex2bin($data['content']);
                $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', self::ENCRYPTION_KEY, 0, $iv);
                return $decrypted;
            } else {
                return $encryptedString;
            }
        } catch (\Exception $error) {
            Log::error('Decryption error: ' . $error->getMessage());
            throw new \Exception('Error al desencriptar los datos.');
        }
    }

    public function crearUsuarioMigraciones(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required',
                'password' => 'required',
                'ip' => 'required',
                'nivelacceso' => 'required'
            ]);

            $usuarioMigraciones = UserMigraciones::create([
                'username' => $this->encrypt($validated['username']),
                'password' => $this->encrypt($validated['password']),
                'ip' => $this->encrypt($validated['ip']),
                'nivelacceso' => $this->encrypt($validated['nivelacceso']),
                'estado' => 1, // Asumiendo que nuevo usuario es activo por defecto
                'n_consult' => 0,
                'created_at' => Carbon::now('America/Lima'),
                'updated_at' => Carbon::now('America/Lima'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario MIGRACIONES creado exitosamente.',
                'data' => $usuarioMigraciones
            ], 201);
        } catch (\Exception $error) {
            Log::error('Error al crear usuario MIGRACIONES: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario MIGRACIONES: ' . $error->getMessage(),
            ], 500);
        }
    }

    public function listarUsuariosMigraciones()
    {
        try {
            $usuarios = UserMigraciones::all();

            $usuariosData = $usuarios->map(function($user) {
                $userData = $user->toArray();

                if (!empty($userData['username'])) {
                    $userData['username'] = $this->decrypt($userData['username']);
                }
                if (!empty($userData['ip'])) {
                    $userData['ip'] = $this->decrypt($userData['ip']);
                }
                if (!empty($userData['nivelacceso'])) {
                    $userData['nivelacceso'] = $this->decrypt($userData['nivelacceso']);
                }

                return $userData;
            });

            return response()->json([
                'success' => true,
                'data' => $usuariosData
            ]);
        } catch (\Exception $error) {
            Log::error('Error al listar usuarios MIGRACIONES: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al listar los usuarios MIGRACIONES.',
            ], 500);
        }
    }

    public function listarUsuarioMigracionesPorId($id)
    {
        try {
            $usuario = UserMigraciones::findOrFail($id);

            $userData = $usuario->toArray();
            $userData['username'] = $this->decrypt($userData['username']);
            $userData['ip'] = $this->decrypt($userData['ip']);
            $userData['nivelacceso'] = $this->decrypt($userData['nivelacceso']);

            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        } catch (\Exception $error) {
            Log::error('Error al listar usuario MIGRACIONES por ID: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al listar el usuario MIGRACIONES.',
            ], 500);
        }
    }

    public function editarUsuarioMigraciones(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|exists:migraciones,id'
        ]);

        try {
            $usuario = UserMigraciones::findOrFail($id);

            $updates = [];
            if ($request->has('password')) {
                $updates['password'] = $this->encrypt($request->password);
            }
            if ($request->has('username')) {
                $updates['username'] = $this->encrypt($request->username);
            }
            if ($request->has('ip')) {
                $updates['ip'] = $this->encrypt($request->ip);
            }
            if ($request->has('nivelacceso')) {
                $updates['nivelacceso'] = $this->encrypt($request->nivelacceso);
            }
            if ($request->has('estado')) {
                $updates['estado'] = $request->estado;
            }

            $updates['updated_at'] = Carbon::now('America/Lima');

            $usuario->update($updates);

            return response()->json([
                'success' => true,
                'message' => 'Usuario MIGRACIONES actualizado exitosamente.',
                'data' => $usuario
            ]);
        } catch (\Exception $error) {
            Log::error('Error al actualizar usuario MIGRACIONES: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario MIGRACIONES.',
            ], 500);
        }
    }

    public function consultarMigraciones(Request $request)
    {
        $request->validate([
            'docconsulta' => 'required|digits:9'
        ]);

        try {
            $proveedores = Proveedor::all();
            $proveedorMigraciones = $proveedores->first(function ($prov) {
                return $this->decrypt($prov->nombre) === "migraciones";
            });

            if (!$proveedorMigraciones) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor de Migraciones no encontrado en la base de datos.',
                ], 404);
            }

            $urlMigraciones = $this->decrypt($proveedorMigraciones->url);

            $usuariosHabilitados = UserMigraciones::where('estado', 1)->get();

            if ($usuariosHabilitados->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay usuarios habilitados.',
                ], 404);
            }

            $usuarioMigraciones = $usuariosHabilitados->random();

            $username = $this->decrypt($usuarioMigraciones->username);
            $password = $this->decrypt($usuarioMigraciones->password);
            $ip = $this->decrypt($usuarioMigraciones->ip);
            $nivelacceso = $this->decrypt($usuarioMigraciones->nivelacceso);

            $data = [
                'PIDE' => [
                    'username' => $username,
                    'password' => $password,
                    'ip' => $ip,
                    'nivelacceso' => $nivelacceso,
                    'docconsulta' => $request->docconsulta,
                ],
            ];

            $response = Http::timeout(10)->post($urlMigraciones, $data);
            $datosMigraciones = $response->json();

            if (!$datosMigraciones || !isset($datosMigraciones['codRespuesta'])) {
                Log::error("Estructura de respuesta inesperada: " . json_encode($response->json()));
                return response()->json([
                    'success' => false,
                    'message' => 'Estructura de respuesta inesperada',
                    'debug' => $response->json(),
                ], 500);
            }

            $mensajeError = $this->errores[$datosMigraciones['codRespuesta']] ?? "Error desconocido";

            if ($datosMigraciones['codRespuesta'] === "0000") {
                $datosPersonales = $datosMigraciones['datosPersonales'] ?? [];
                $historialCarne = $datosMigraciones['historialCarneExtranjeria'][0] ?? [];
                $imagenes = $datosMigraciones['imagenes'] ?? [];
                $huellas = $datosMigraciones['huellas'][0] ?? [];

                $persona = [
                    'codRespuesta_pide' => $datosMigraciones['codRespuesta'] ?? '',
                    'desRespuesta_pide' => $datosMigraciones['desRespuesta'] ?? '',
                    'painacionalidad_pide' => $datosPersonales['painacionalidad'] ?? '',
                    'numce_pide' => $datosPersonales['numce'] ?? '',
                    'fecinscripcion_pide' => $datosPersonales['fecinscripcion'] ?? '',
                    'fecemision_pide' => $datosPersonales['fecemision'] ?? '',
                    'feccaducidad_pide' => $datosPersonales['feccaducidad'] ?? '',
                    'fecvenresidencia_pide' => $datosPersonales['fecvenresidencia'] ?? '',
                    'apepaterno_pide' => $datosPersonales['apepaterno'] ?? '',
                    'apematerno_pide' => $datosPersonales['apematerno'] ?? '',
                    'nombres_pide' => $datosPersonales['nombres'] ?? '',
                    'ofimigratoria_pide' => $datosPersonales['ofimigratoria'] ?? '',
                    'fecnacimiento_pide' => $datosPersonales['fecnacimiento'] ?? '',
                    'painacimiento_pide' => $datosPersonales['painacimiento'] ?? '',
                    'ubiactual_pide' => $datosPersonales['ubiactual'] ?? '',
                    'domactual_pide' => $datosPersonales['domactual'] ?? '',
                    'numpasaporte_pide' => $datosPersonales['numpasaporte'] ?? '',
                    'estcivil_pide' => $datosPersonales['estcivil'] ?? '',
                    'genero_pide' => $datosPersonales['genero'] ?? '',
                    'calmigratoria_pide' => $datosPersonales['calmigratoria'] ?? '',
                    'dependencia_pide' => $datosPersonales['dependencia'] ?? '',
                    'numcarne_pide' => $datosPersonales['numcarne'] ?? '',
                    'feccaducidad_pide' => $datosPersonales['feccaducidad'] ?? '',
                    'fecemision_pide' => $datosPersonales['fecemision'] ?? '',
                    'estado_pide' => $datosPersonales['estado'] ?? '',
                    'foto_pide' => $imagenes['foto'] ?? '',
                    'firma_pide' => $imagenes['firma'] ?? '',
                    'idDedo_pide' => $huellas['idDedo'] ?? '',
                    'imagen_pide' => $huellas['imagen'] ?? '',
                ];
                $clientIP = null;
                $clientIP = 
                    $request->getClientIp() ??  // Método recomendado para entornos con proxies
                    $request->header('X-Real-IP') ?? 
                    $request->header('X-Forwarded-For') ?? 
                    $request->ip();

                Log::info('Detalles de IP Detallados', [
                    'getClientIp()' => $request->getClientIp(),
                    'X-Real-IP' => $request->header('X-Real-IP'),
                    'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                    'Laravel ip()' => $request->ip(),
                    'Servidor' => gethostname(),
                    'IP Final' => $clientIP
                ]);

                Consulta::create([
                    'proveedor' => 'migraciones',
                    'credencial_id' => $usuarioMigraciones->id,
                    'documento_consultado' => $request->docconsulta,
                    'exitoso' => true,
                    'codigo_respuesta' => $datosMigraciones['codRespuesta'],
                    'ip' => $clientIP
                ]);


                // ✅ Actualizar la cantidad de consultas en `migracionesc`
                $usuarioMigraciones->increment('n_consult');

                return response()->json([
                    'success' => true,
                    'message' => 'Consulta exitosa',
                    'persona' => $persona
                ]);
            } else {
                Log::info($request->header('X-Forwarded-For') ?? $request->ip());
                Consulta::create([
                    'proveedor' => 'migraciones',
                    'credencial_id' => $usuarioMigraciones->id,
                    'documento_consultado' => $request->docconsulta,
                    'exitoso' => false,
                    'codigo_respuesta' => $datosMigraciones['codRespuesta'],
                    'ip' => $request->header('X-Forwarded-For') ?? $request->ip()
                ]);

                $usuarioMigraciones->increment('n_consult');

                return response()->json([
                    'success' => false,
                    'message' => "Error en la consulta: $mensajeError",
                    'error' => $datosMigraciones['codRespuesta'],
                ], 400);
            }
        } catch (\Exception $error) {
            Log::error('Error en la consulta de Migraciones: ' . $error->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}