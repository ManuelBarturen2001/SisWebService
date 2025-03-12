<?php

namespace App\Http\Controllers;

use App\Models\Reniec as UserReniec;
use App\Models\Consulta;
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

    public function index()
    {
        try {
            $usuarios = UserReniec::all();

            $usuarios->transform(function ($user) {
                if (!empty($user->nuDniUsuario)) {
                    $user->nuDniUsuario = $this->decrypt($user->nuDniUsuario);
                }

                if (!empty($user->nuRucUsuario)) {
                    $user->nuRucUsuario = $this->decrypt($user->nuRucUsuario);
                }

                return $user; // Retorna el objeto en lugar de un array
            });

            return view('reniec.index', ['usuarios' => $usuarios]);
        } catch (\Exception $error) {
            Log::error('Error al listar usuarios: ' . $error->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los usuarios RENIEC.');
        }
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
    
    public function showConsultarForm()
    {
        return view('reniec.consultar');
    }

    

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
            $usuarioData['password'] = $this->decrypt($usuarioData['password']);

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
            'id' => 'required|exists:reniec,id'
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
            
            // Only update password if a new password is provided
            if ($request->filled('password')) {
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
                'message' => 'Error al actualizar el usuario RENIEC: ' . $error->getMessage()
            ], 500);
        }
    }

    public function consultarReniec(Request $request)
    {
        $request->validate([
            'nuDniConsulta' => 'required|digits:8'
        ]);

        try {
            // 1️⃣ OBTENER EL PROVEEDOR RENIEC
            $proveedores = Proveedor::all();
            $proveedorReniec = $proveedores->first(fn($prov) => $this->decrypt($prov->nombre) === "reniec");

            if (!$proveedorReniec) {
                Log::error('Proveedor RENIEC no encontrado en la base de datos');
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor de RENIEC no encontrado.'
                ], 404);
            }

            $urlReniec = $this->decrypt($proveedorReniec->url);

            // 2️⃣ OBTENER UN USUARIO HABILITADO
            $usuariosHabilitados = UserReniec::where('estado', 1)->get();
            if ($usuariosHabilitados->isEmpty()) {
                Log::error('No se encontraron usuarios RENIEC habilitados');
                return response()->json([
                    'success' => false,
                    'message' => 'No hay usuarios habilitados.'
                ], 404);
            }

            $usuarioReniec = $usuariosHabilitados->random();

            // 3️⃣ DESENCRIPTAR CREDENCIALES
            $nuDniUsuario = $this->decrypt($usuarioReniec->nuDniUsuario);
            $nuRucUsuario = $this->decrypt($usuarioReniec->nuRucUsuario);
            $password = $this->decrypt($usuarioReniec->password);

            $data = [
                'PIDE' => [
                    'nuDniConsulta' => $request->nuDniConsulta,
                    'nuDniUsuario' => $nuDniUsuario,
                    'nuRucUsuario' => $nuRucUsuario,
                    'password' => $password
                ]
            ];

            Log::info("Realizando consulta RENIEC para DNI: " . $request->nuDniConsulta, [
                'url' => $urlReniec,
                'usuario_dni' => $nuDniUsuario
            ]);

            // 4️⃣ HACER LA SOLICITUD A RENIEC
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/xml'
                ])
                ->post($urlReniec, $data);

            if (!$response->successful()) {
                Log::error("Error en la respuesta HTTP de RENIEC", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la conexión con RENIEC',
                    'status' => $response->status()
                ], 500);
            }

            // 5️⃣ PROCESAR RESPUESTA XML
            $xmlString = trim($response->body());
            Log::info("Respuesta cruda de RENIEC: " . substr($xmlString, 0, 500) . (strlen($xmlString) > 500 ? '...' : ''));

            // Método mejorado para manejar XML SOAP con namespaces
            $codigoResultado = '';
            $datosPersona = [];

            // Desactivar informes de errores libxml para gestionar el manejo personalizado de errores
            libxml_use_internal_errors(true);
            
            try {
                // Crear un nuevo DOMDocument para trabajar con el XML
                $doc = new \DOMDocument();
                $doc->loadXML($xmlString);
                
                // Crear un nuevo DOMXPath para consultas XPath
                $xpath = new \DOMXPath($doc);
                
                // Registrar los namespaces necesarios
                $xpath->registerNamespace('S', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xpath->registerNamespace('w', 'http://ws.reniec.gob.pe/');
                
                // Extraer el código de resultado
                $coResultadoNodes = $xpath->query('//w:consultarResponse/return/coResultado');
                if ($coResultadoNodes->length > 0) {
                    $codigoResultado = $coResultadoNodes->item(0)->nodeValue;
                }
                
                // Si el código es exitoso, extraer los datos de la persona
                if ($codigoResultado === '0000') {
                    // Mapear los nodos XML a un array asociativo
                    $nodosAExtraer = [
                        'apPrimer' => 'paterno',
                        'apSegundo' => 'materno',
                        'prenombres' => 'nombre',
                        'direccion' => 'direccion_pide',
                        'estadoCivil' => 'estadoCivil_pide',
                        'ubigeo' => 'ubigeo_pide',
                        'restriccion' => 'restriccion_pide',
                        'foto' => 'foto_pide'
                    ];
                    
                    foreach ($nodosAExtraer as $nodoXml => $campoJson) {
                        $xpath_query = "//w:consultarResponse/return/datosPersona/{$nodoXml}";
                        $nodes = $xpath->query($xpath_query);
                        
                        if ($nodes->length > 0) {
                            $datosPersona[$campoJson] = $nodes->item(0)->nodeValue;
                        } else {
                            $datosPersona[$campoJson] = '';
                        }
                    }
                    
                    // Agregar el DNI consultado a los datos
                    $datosPersona['dni'] = $request->nuDniConsulta;
                    
                    Log::info("Consulta RENIEC exitosa para DNI: " . $request->nuDniConsulta);
                    Consulta::create([
                        'proveedor' => 'reniec',
                        'credencial_id' => $usuarioReniec->id,
                        'documento_consultado' => $request->nuDniConsulta,
                        'exitoso' => true,
                        'codigo_respuesta' => $codigoResultado
                    ]);

                    // ✅ Actualizar la cantidad de consultas en `reniec`
                    $usuarioReniec->increment('n_consult');

                    return response()->json([
                        'success' => true,
                        'codigoResultado' => $codigoResultado,
                        'persona' => $datosPersona,
                        'message' => 'Consulta realizada con éxito.'
                    ]);
                } else {
                    // Obtener mensaje de error, si existe
                    $deResultadoNodes = $xpath->query('//w:consultarResponse/return/deResultado');
                    $mensajeError = ($deResultadoNodes->length > 0) 
                        ? $deResultadoNodes->item(0)->nodeValue 
                        : 'Error desconocido en la consulta RENIEC';
                    
                    Log::warning("RENIEC devolvió código de error", [
                        'codigo' => $codigoResultado,
                        'mensaje' => $mensajeError,
                        'dni' => $request->nuDniConsulta
                    ]);
                    Consulta::create([
                        'proveedor' => 'reniec',
                        'credencial_id' => $usuarioReniec->id,
                        'documento_consultado' => $request->nuDniConsulta,
                        'exitoso' => false,
                        'codigo_respuesta' => $codigoResultado
                    ]);

                    // ✅ Actualizar la cantidad de consultas en `reniec`
                    $usuarioReniec->increment('n_consult');

                    return response()->json([
                        'success' => false,
                        'message' => $mensajeError,
                        'codigoResultado' => $codigoResultado
                    ], 400);

                    
                }
            } catch (\Exception $e) {
                $libxmlErrors = libxml_get_errors();
                libxml_clear_errors();
                
                Log::error("Error al procesar XML SOAP: " . $e->getMessage(), [
                    'xml_errors' => array_map(function($error) {
                        return [
                            'code' => $error->code,
                            'message' => $error->message,
                            'line' => $error->line
                        ];
                    }, $libxmlErrors)
                ]);
                
                // Intento alternativo con SimpleXML
                return $this->intentoAlternativoSimpleXML($xmlString, $request->nuDniConsulta);
            }
        } catch (\Exception $error) {
            Log::error('Error en consulta RENIEC: ' . $error->getMessage(), [
                'trace' => $error->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la solicitud',
                'error' => $error->getMessage()
            ], 500);
        }
    }
    
    private function intentoAlternativoSimpleXML($xmlString, $dni)
    {
        try {
            // Crear un nuevo objeto SimpleXMLElement
            $xml = new \SimpleXMLElement($xmlString);
            
            // Registrar los namespaces
            $xml->registerXPathNamespace('S', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xml->registerXPathNamespace('w', 'http://ws.reniec.gob.pe/');
            
            // Acceder al cuerpo SOAP y extraer el nodo de respuesta
            $bodyNode = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
            $responseNode = $bodyNode->children('http://ws.reniec.gob.pe/')->consultarResponse;
            
            if (!$responseNode || !isset($responseNode->return)) {
                throw new \Exception('Estructura de respuesta RENIEC inválida');
            }
            
            $returnNode = $responseNode->return;
            $codigoResultado = (string)$returnNode->coResultado;
            
            if ($codigoResultado === '0000') {
                $datosPersonaNode = $returnNode->datosPersona;
                
                $persona = [
                    'dni' => $dni,
                    'paterno' => (string)$datosPersonaNode->apPrimer,
                    'materno' => (string)$datosPersonaNode->apSegundo,
                    'nombre' => (string)$datosPersonaNode->prenombres,
                    'direccion_pide' => (string)$datosPersonaNode->direccion,
                    'estadoCivil_pide' => (string)$datosPersonaNode->estadoCivil,
                    'ubigeo_pide' => (string)$datosPersonaNode->ubigeo,
                    'restriccion_pide' => (string)$datosPersonaNode->restriccion,
                    'foto_pide' => (string)$datosPersonaNode->foto
                ];
                
                Log::info("Consulta RENIEC exitosa (método alternativo) para DNI: " . $dni);
                
                return response()->json([
                    'success' => true,
                    'codigoResultado' => $codigoResultado,
                    'persona' => $persona
                ]);
            } else {
                $mensajeError = (string)$returnNode->deResultado ?? 'Error desconocido en la consulta RENIEC';
                
                Log::warning("RENIEC devolvió código de error (método alternativo)", [
                    'codigo' => $codigoResultado,
                    'mensaje' => $mensajeError,
                    'dni' => $dni
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $mensajeError,
                    'codigoResultado' => $codigoResultado
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error en el método alternativo de procesamiento XML: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la respuesta de RENIEC',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}