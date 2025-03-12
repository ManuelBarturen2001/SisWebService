<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SunatController extends Controller
{
    public function index()
    {
        return view('sunat.index');
    }
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    public function consultarSunat(Request $request)
    {
        $numRuc = $request->input('numRuc');

        // Validar que el RUC tenga 11 dígitos
        if (!preg_match('/^\d{11}$/', $numRuc)) {
            return response()->json([
                'success' => false,
                'message' => 'RUC inválido. Debe contener 11 dígitos.'
            ], 400);
        }

        try {
            // URL de la API de SUNAT
            $url = "https://ws3.pide.gob.pe/Rest/Sunat/DatosPrincipales?numruc={$numRuc}&out=json";

            // Realizar la consulta a SUNAT
            $response = Http::timeout(10)->get($url);

            // Verificar respuesta
            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la API externa de SUNAT',
                    'details' => $response->body()
                ], $response->status());
            }

            $datosSunat = $response->json();

            // Verificar si la respuesta contiene los datos esperados
            if (!isset($datosSunat['list']['multiRef'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para el RUC proporcionado'
                ], 400);
            }

            $multiRef = $datosSunat['list']['multiRef'];

            // Obtener datos
            $ddp_nombre = $multiRef['ddp_nombre']['$'] ?? '';
            $ruc = $multiRef['ddp_numruc']['$'] ?? '';
            $razon_social = $ddp_nombre;

            // Desglosar el nombre
            $partesNombre = explode(' ', $ddp_nombre);
            $paterno = $partesNombre[0] ?? '';
            $materno = $partesNombre[1] ?? '';
            $nombres = implode(' ', array_slice($partesNombre, 2));

            $datos = [
                'ruc' => $ruc,
                'razon_social' => $razon_social,
                'paterno' => $paterno,
                'materno' => $materno,
                'nombres' => $nombres,
                'ruc_pide' => $multiRef['ddp_numruc']['$'] ?? '',
                'ddp_ubigeo_pide' => $multiRef['ddp_ubigeo']['$'] ?? '',
                'cod_dep_pide' => $multiRef['cod_dep']['$'] ?? '',
                'desc_dep_pide' => $multiRef['desc_dep']['$'] ?? '',
                'cod_prov_pide' => $multiRef['cod_prov']['$'] ?? '',
                'desc_prov_pide' => $multiRef['desc_prov']['$'] ?? '',
                'cod_dist_pide' => $multiRef['cod_dist']['$'] ?? '',
                'desc_dist_pide' => $multiRef['desc_dist']['$'] ?? '',
                'ddp_ciiu_pide' => $multiRef['ddp_ciiu']['$'] ?? '',
                'desc_ciiu_pide' => $multiRef['desc_ciiu']['$'] ?? '',
                'ddp_estado_pide' => $multiRef['ddp_estado']['$'] ?? '',
                'desc_estado_pide' => $multiRef['desc_estado']['$'] ?? '',
                'ddp_fecact_pide' => $multiRef['ddp_fecact']['$'] ?? '',
                'ddp_fecalt_pide' => $multiRef['ddp_fecalt']['$'] ?? '',
                'ddp_fecbaj_pide' => $multiRef['ddp_fecbaj']['$'] ?? '',
                'ddp_identi_pide' => $multiRef['ddp_identi']['$'] ?? '',
                'desc_identi_pide' => $multiRef['desc_identi']['$'] ?? '',
                'ddp_lllttt_pide' => $multiRef['ddp_lllttt']['$'] ?? '',
                'ddp_nombre_pide' => $multiRef['ddp_nombre']['$'] ?? '',
                'ddp_nomvia_pide' => $multiRef['ddp_nomvia']['$'] ?? '',
                'ddp_numer1_pide' => $multiRef['ddp_numer1']['$'] ?? '',
                'ddp_inter1_pide' => $multiRef['ddp_inter1']['$'] ?? '',
                'ddp_nomzon_pide' => $multiRef['ddp_nomzon']['$'] ?? '',
                'ddp_refer1_pide' => $multiRef['ddp_refer1']['$'] ?? '',
                'ddp_flag22_pide' => $multiRef['ddp_flag22']['$'] ?? '',
                'desc_flag22_pide' => $multiRef['desc_flag22']['$'] ?? '',
                'ddp_numreg_pide' => $multiRef['ddp_numreg']['$'] ?? '',
                'desc_numreg_pide' => $multiRef['desc_numreg']['$'] ?? '',
                'ddp_tipvia_pide' => $multiRef['ddp_tipvia']['$'] ?? '',
                'desc_tipvia_pide' => $multiRef['desc_tipvia']['$'] ?? '',
                'ddp_tipzon_pide' => $multiRef['ddp_tipzon']['$'] ?? '',
                'desc_tipzon_pide' => $multiRef['desc_tipzon']['$'] ?? '',
                'ddp_tpoemp_pide' => $multiRef['ddp_tpoemp']['$'] ?? '',
                'desc_tpoemp_pide' => $multiRef['desc_tpoemp']['$'] ?? '',
                'ddp_secuen_pide' => $multiRef['ddp_secuen']['$'] ?? '',
                'esActivo_pide' => $multiRef['esActivo']['$'] ?? '',
                'esHabido_pide' => $multiRef['esHabido']['$'] ?? '',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Consulta exitosa',
                'datos' => $datos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
