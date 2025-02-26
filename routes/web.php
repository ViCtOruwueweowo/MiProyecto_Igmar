<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------- 
| Web Routes 
|--------------------------------------------------------------------------- 
*/

Route::get('/', function () {
    return view('/auth/login');
});

// Rutas de autenticación
Auth::routes();

// Proteger las rutas de 2FA y asegurarse de que el usuario esté autenticado
Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/2fa', function () {
        return redirect(route('home'));
    })->name('2fa');
});

// Ruta para completar el registro de usuario
Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');
