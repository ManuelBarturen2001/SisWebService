<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedores as Proveedor;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class ProveedoresController extends Controller
{
    public function index()
    {
        try {
            $proveedores = Proveedor::all()->map(function ($proveedor) {
                return (object)[
                    'id' => $proveedor->id,
                    'nombre' => $this->decrypt($proveedor->nombre),
                    'url' => $this->decrypt($proveedor->url),
                    'created_at' => $proveedor->created_at,
                    'updated_at' => $proveedor->updated_at
                ];
            });

            return view('proveedores.index', ['proveedores' => $proveedores]);
        } catch (\Exception $error) {
            Log::error('Error al listar proveedores: ' . $error->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los proveedores.');
        }
    }
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    private const ENCRYPTION_KEY = 'oFiC1n@Tcn0%lA$b@rtvr3Nu$pRg%njv'; // Debe ser de 32 caracteres
    private const IV_LENGTH = 16;

    // Función para encriptar
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

    // Agregar un proveedor
    public function agregarProveedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'url' => 'required|string',
        ]);

        try {
            $proveedor = Proveedor::create([
                'nombre' => $this->encrypt($request->nombre),
                'url' => $this->encrypt($request->url),
                'created_at' => Carbon::now('America/Lima'),
                'updated_at' => Carbon::now('America/Lima'),
            ]);

            return response()->json(['success' => true, 'message' => 'Proveedor agregado con éxito.', 'proveedor' => $proveedor], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al agregar el proveedor.', 'error' => $e->getMessage()], 500);
        }
    }

    // Listar todos los proveedores
    public function listarProveedores()
    {
        $proveedores = Proveedor::all()->map(function ($proveedor) {
            return [
                'id' => $proveedor->id,
                'nombre' => $this->decrypt($proveedor->nombre),
                'url' => $this->decrypt($proveedor->url),
                'created_at' => $proveedor->created_at,
                'updated_at' => $proveedor->updated_at,
            ];
        });

        return response()->json(['success' => true, 'proveedores' => $proveedores]);
    }

    // Listar proveedor por ID
    public function listarProveedorPorId($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['success' => false, 'message' => 'Proveedor no encontrado.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $proveedor->id,
                'nombre' => $this->decrypt($proveedor->nombre),
                'url' => $this->decrypt($proveedor->url),
            ],
        ]);
    }

    // Editar proveedor
    public function editarProveedor(Request $request, $id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['success' => false, 'message' => 'Proveedor no encontrado.'], 404);
        }

        $proveedor->nombre = $request->nombre ? $this->encrypt($request->nombre) : $proveedor->nombre;
        $proveedor->url = $request->url ? $this->encrypt($request->url) : $proveedor->url;
        $proveedor->updated_at = Carbon::now('America/Lima');
        $proveedor->save();

        return response()->json(['success' => true, 'message' => 'Proveedor actualizado exitosamente.', 'data' => $proveedor]);
    }

    // Eliminar proveedor
    public function eliminarProveedor($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['success' => false, 'message' => 'Proveedor no encontrado.'], 404);
        }

        $proveedor->delete();

        return response()->json(['success' => true, 'message' => 'Proveedor eliminado con éxito.']);
    }

}
