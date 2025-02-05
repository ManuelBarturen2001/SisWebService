<?php

namespace App\Http\Controllers;

use App\Models\Reniec as UserReniec;
use App\Models\Proveedores as Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReniecController extends Controller
{

    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
    
    private const IV_LENGTH = 16;
    private const ENCRYPTION_KEY = 'my32characterlongencryptionkey12';

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
            $data = json_decode($encryptedString, true);
            $iv = hex2bin($data['iv']);
            $encrypted = hex2bin($data['content']);
            $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', self::ENCRYPTION_KEY, 0, $iv);
            return $decrypted;
        } catch (\Exception $error) {
            Log::error('Decryption error: ' . $error->getMessage());
            throw new \Exception('Error decrypting data');
        }
    }

    public function crearUsuarioReniec(Request $request)
    {
        $request->validate([
            'nuDniUsuario' => 'required',
            'nuRucUsuario' => 'required',
            'password' => 'required'
        ]);

        try {
            $usuarioReniec = UserReniec::create([
                'nuDniUsuario' => $this->encrypt($request->nuDniUsuario),
                'nuRucUsuario' => $this->encrypt($request->nuRucUsuario),
                'password' => $this->encrypt($request->password),
                'created_at' => Carbon::now('America/Lima'),
                'updated_at' => Carbon::now('America/Lima')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario RENIEC creado exitosamente.',
                'data' => ['id' => $usuarioReniec->id]
            ], 201);
        } catch (\Exception $error) {
            Log::error('Error al crear usuario: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario RENIEC.'
            ], 500);
        }
    }

    public function listarUsuariosReniec()
    {
        try {
            $usuarios = UserReniec::all();

            $usuariosData = $usuarios->map(function($user) {
                $userData = $user->toArray();

                if (!empty($userData['nuDniUsuario'])) {
                    $userData['nuDniUsuario'] = $this->decrypt($userData['nuDniUsuario']);
                }

                if (!empty($userData['nuRucUsuario'])) {
                    $userData['nuRucUsuario'] = $this->decrypt($userData['nuRucUsuario']);
                }

                return $userData;
            });

            return response()->json([
                'success' => true,
                'data' => $usuariosData
            ]);
        } catch (\Exception $error) {
            Log::error('Error al listar usuarios: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al listar los usuarios RENIEC.'
            ], 500);
        }
    }

    public function obtenerUsuarioReniecPorId($id)
    {
        try {
            $usuario = UserReniec::findOrFail($id);

            $usuarioData = $usuario->toArray();
            $usuarioData['nuDniUsuario'] = $this->decrypt($usuarioData['nuDniUsuario']);
            $usuarioData['nuRucUsuario'] = $this->decrypt($usuarioData['nuRucUsuario']);

            return response()->json([
                'success' => true,
                'user' => $usuarioData
            ]);
        } catch (\Exception $error) {
            Log::error('Error al obtener usuario: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del usuario RENIEC.'
            ], 500);
        }
    }

    public function editarUsuarioReniec(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|exists:user_reniec,id'
        ]);

        try {
            $usuario = UserReniec::findOrFail($id);

            $updates = [];
            if ($request->has('nuDniUsuario')) {
                $updates['nuDniUsuario'] = $this->encrypt($request->nuDniUsuario);
            }
            if ($request->has('nuRucUsuario')) {
                $updates['nuRucUsuario'] = $this->encrypt($request->nuRucUsuario);
            }
            if ($request->has('password')) {
                $updates['password'] = $this->encrypt($request->password);
            }
            if ($request->has('estado')) {
                $updates['estado'] = $request->estado;
            }

            $updates['updated_at'] = Carbon::now('America/Lima');

            $usuario->update($updates);

            return response()->json([
                'success' => true,
                'message' => 'Usuario RENIEC actualizado exitosamente.',
                'data' => ['id' => $usuario->id]
            ]);
        } catch (\Exception $error) {
            Log::error('Error al actualizar usuario: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario RENIEC.'
            ], 500);
        }
    }

    public function consultarReniec(Request $request)
    {
        $request->validate([
            'nuDniConsulta' => 'required|digits:8'
        ]);

        try {
            // Find RENIEC provider
            $proveedores = Proveedor::all();
            $proveedorReniec = $proveedores->first(function ($prov) {
                return $this->decrypt($prov->nombre) === "reniec";
            });

            if (!$proveedorReniec) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor de RENIEC no encontrado.'
                ], 404);
            }

            $urlReniec = $this->decrypt($proveedorReniec->url);

            // Find enabled users
            $usuariosHabilitados = UserReniec::where('estado', 1)->get();

            if ($usuariosHabilitados->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay usuarios habilitados.'
                ], 404);
            }

            // Select random enabled user
            $usuarioReniec = $usuariosHabilitados->random();

            $data = [
                'PIDE' => [
                    'nuDniConsulta' => $request->nuDniConsulta,
                    'nuDniUsuario' => $this->decrypt($usuarioReniec->nuDniUsuario),
                    'nuRucUsuario' => $this->decrypt($usuarioReniec->nuRucUsuario),
                    'password' => $this->decrypt($usuarioReniec->password)
                ]
            ];

            // Make HTTP request to RENIEC
            $response = Http::timeout(10)->post($urlReniec, $data);

            // Parse XML response (you might need to install additional XML parsing library)
            $xml = simplexml_load_string($response->body());
            $consultarResponse = $xml->{'S:Body'}->{'w:consultarResponse'}->{'return'};
            $codigoRespuesta = (string)$consultarResponse->coResultado;

            if ($codigoRespuesta === '0000') {
                $datosPersona = $consultarResponse->datosPersona;
                
                $persona = [
                    'dni' => $request->nuDniConsulta,
                    'paterno' => (string)$datosPersona->apPrimer ?? '',
                    'materno' => (string)$datosPersona->apSegundo ?? '',
                    'nombre' => (string)$datosPersona->prenombres ?? '',
                    'apPrimer_pide' => (string)$datosPersona->apPrimer ?? '',
                    'apSegundo_pide' => (string)$datosPersona->apSegundo ?? '',
                    'direccion_pide' => (string)$datosPersona->direccion ?? '',
                    'estadoCivil_pide' => (string)$datosPersona->estadoCivil ?? '',
                    'ubigeo_pide' => (string)$datosPersona->ubigeo ?? '',
                    'restriccion_pide' => (string)$datosPersona->restriccion ?? '',
                    'foto_pide' => (string)$datosPersona->foto ?? '',
                ];

                return response()->json([
                    'success' => true,
                    'codigoResultado' => $codigoRespuesta,
                    'persona' => $persona
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la consulta',
                    'codigoRespuesta' => $codigoRespuesta
                ], 400);
            }
        } catch (\Exception $error) {
            Log::error('Error en consulta RENIEC: ' . $error->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}