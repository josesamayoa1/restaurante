<?php
use Illuminate\Support\Facades\Route;
use App\Livewire\Roles\Index as RolesIndex;
use App\Livewire\Cajas\Index as CajasIndex;
use App\Livewire\Cortes\Index as CortesIndex;
use App\Livewire\Ordenes\Index as OrdenesIndex;
use App\Livewire\Productos\Index as ProductosIndex;
use App\Livewire\Mesas\Index as MesasIndex;
use App\Livewire\Facturas\Index as FacturasIndex;
use App\Livewire\Pagos\Index as PagosIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

Route::redirect('/', '/dashboard')->name('home');

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard principal
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');




    // Configuración de usuario

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');



    // Rutas de administración (solo para admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/roles', RolesIndex::class)->name('roles.index');
        Route::get('/cajas', CajasIndex::class)->name('cajas.index');
        Route::get('/productos', ProductosIndex::class)->name('productos.index');
        Route::get('/mesas', MesasIndex::class)->name('mesas.index');
    });

    // Rutas para cajeros
    Route::middleware(['role:cajero|admin'])->group(function () {
        Route::get('/cortes', CortesIndex::class)->name('cortes.index');
        Route::get('/facturas', FacturasIndex::class)->name('facturas.index');
        Route::get('/pagos', PagosIndex::class)->name('pagos.index');

    });

    // Rutas para meseros
    Route::middleware(['role:mesero|admin'])->group(function () {
        Route::get('/ordenes', OrdenesIndex::class)->name('ordenes.index');
    });

    // Rutas para cocineros
    Route::middleware(['role:cocinero|admin|mesero'])->group(function () {
        Route::get('/articulos', \App\Livewire\Articulos\Index::class)->name('articulos.index');
    });
});

