<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Reniec;
use App\Models\Migraciones;
use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Consultas totales por proveedor
        $consultasPorProveedor = Consulta::select('proveedor', DB::raw('count(*) as total'))
            ->groupBy('proveedor')
            ->get();
        
        // Consultas por credencial de Reniec
        $consultasReniec = Consulta::where('proveedor', 'reniec')
            ->select('credencial_id', DB::raw('count(*) as total'))
            ->groupBy('credencial_id')
            ->get();
            
        // Obtener información de los usuarios de Reniec para los IDs
        $usuariosReniec = Reniec::whereIn('id', $consultasReniec->pluck('credencial_id'))->get();
        
        // Consultas por credencial de Migraciones
        $consultasMigraciones = Consulta::where('proveedor', 'migraciones')
            ->select('credencial_id', DB::raw('count(*) as total'))
            ->groupBy('credencial_id')
            ->get();
            
        // Obtener información de los usuarios de Migraciones para los IDs
        $usuariosMigraciones = Migraciones::whereIn('id', $consultasMigraciones->pluck('credencial_id'))->get();
        
        // Consultas por día (últimos 30 días)
        $consultasPorDia = Consulta::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        
        // Tasa de éxito por proveedor
        $tasaExitoPorProveedor = Consulta::select('proveedor', 
            DB::raw('SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) as exitosas'),
            DB::raw('COUNT(*) as total'),
            DB::raw('(SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as porcentaje_exito')
        )
        ->groupBy('proveedor')
        ->get();

        $totalConsultas = Consulta::count();
        $totalUsuariosReniec = Reniec::count();
        $totalUsuariosMigraciones = Migraciones::count();
        $totalProveedores = Proveedores::count();
        
        return view('dash.index', [
            'consultasPorProveedor' => $consultasPorProveedor,
            'consultasReniec' => $consultasReniec,
            'usuariosReniec' => $usuariosReniec,
            'consultasMigraciones' => $consultasMigraciones,
            'usuariosMigraciones' => $usuariosMigraciones,
            'consultasPorDia' => $consultasPorDia,
            'tasaExitoPorProveedor' => $tasaExitoPorProveedor,
            'totalConsultas' => $totalConsultas, 
            'totalUsuariosReniec'=> $totalUsuariosReniec, 
            'totalUsuariosMigraciones'=> $totalUsuariosMigraciones, 
            'totalProveedores'=> $totalProveedores,
        ]);
    }
}