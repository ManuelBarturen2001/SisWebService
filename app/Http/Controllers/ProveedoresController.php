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
    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    private const ENCRYPTION_KEY = "my32characterlongencryptionkey12"; // Debe ser de 32 caracteres
    private const IV_LENGTH = 16;

    // Función para encriptar
    private function encrypt($text)
    {
        $iv = random_bytes(self::IV_LENGTH);
        $cipher = openssl_encrypt($text, 'aes-256-cbc', self::ENCRYPTION_KEY, 0, $iv);
        return json_encode(['iv' => bin2hex($iv), 'content' => $cipher]);
    }

    // Función para desencriptar
    private function decrypt($encryptedString)
    {
        try {
            $data = json_decode($encryptedString, true);
            $iv = hex2bin($data['iv']);
            return openssl_decrypt($data['content'], 'aes-256-cbc', self::ENCRYPTION_KEY, 0, $iv);
        } catch (Exception $e) {
            return null;
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
