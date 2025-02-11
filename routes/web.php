<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReniecController;
use App\Http\Controllers\MigracionesController;
use App\Models\User;


Route::get('/', function () {
    return view('auth.login');
});

// Rutas para la autenticación con Google
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-auth/callback', function () {
    $user_google = Socialite::driver('google')->stateless()->user();

    // Buscar si el usuario ya está registrado en la base de datos
    $user = User::where('email', $user_google->email)->first();

    if ($user) {
        // Si el usuario existe, actualizar su google_id y loguearlo
        $user->update([
            'google_id' => $user_google->id,
        ]);

        Auth::login($user);

        // Redirigir al dashboard
        return redirect('/dash');
    } else {
        // Si no está registrado, redirigir con un mensaje de error
        return redirect('/')->withErrors(['msg' => '¡NO ESTÁS REGISTRADO EN EL SISTEMA!']);
    }
});

/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/


Route::middleware(['auth', 'verified'])->group(function () {

    //ROUTE DASHBOARD
    Route::get('/dash', [DashboardController::class, 'index'])->name('dash');

    //ROUTE RENIEC
    Route::get('/reniec/listar', [ReniecController::class, 'index'])->name('reniec.index');
    Route::post('/reniec/crear', [ReniecController::class, 'crearUsuarioReniec'])->name('reniec.crear');
    Route::get('/reniec/{id}', [ReniecController::class, 'obtenerUsuarioReniecPorId'])->name('reniec.obtener');
    Route::put('/reniec/{id}/editar', [ReniecController::class, 'editarUsuarioReniec'])->name('reniec.editar'); 

    //ROUTE MIGRACIONES
    Route::get('/migraciones/listar', [MigracionesController::class, 'index'])->name('migraciones.index');
    Route::post('/migraciones/crear', [MigracionesController::class, 'crearUsuarioMigraciones'])->name('migraciones.crear');
    Route::get('/migraciones/{id}', [MigracionesController::class, 'listarUsuarioMigracionesPorId'])->name('migraciones.obtener');
    Route::put('/migraciones/{id}/editar', [MigracionesController::class, 'editarUsuarioMigraciones'])->name('migraciones.editar'); 

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
