<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Caja;
use App\Models\TipoPago;
use App\Models\Mesa;
use App\Models\Producto;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Roles
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'cajero', 'guard_name' => 'web'],
            ['name' => 'mesero', 'guard_name' => 'web'],
            ['name' => 'cocinero', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Admin user
        $admin = User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Restaurante',
            'edad' => 30,
            'email' => 'admin@restaurante.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        // Cajero
        $cajero = User::create([
            'nombre' => 'Juan',
            'apellidos' => 'Perez',
            'edad' => 25,
            'email' => 'cajero@restaurante.com',
            'password' => bcrypt('password'),
        ]);
        $cajero->assignRole('cajero');

        // Mesero
        $mesero = User::create([
            'nombre' => 'Maria',
            'apellidos' => 'Gomez',
            'edad' => 28,
            'email' => 'mesero@restaurante.com',
            'password' => bcrypt('password'),
        ]);
        $mesero->assignRole('mesero');

        // Cocinero
        $cocinero = User::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Lopez',
            'edad' => 35,
            'email' => 'cocinero@restaurante.com',
            'password' => bcrypt('password'),
        ]);
        $cocinero->assignRole('cocinero');

        // Cajas
        Caja::create(['nombre' => 'Caja Principal']);
        Caja::create(['nombre' => 'Caja Secundaria']);

        // Tipo de pagos
        TipoPago::create(['nombre' => 'Efectivo']);
        TipoPago::create(['nombre' => 'Tarjeta']);
        TipoPago::create(['nombre' => 'Transferencia']);

        // Mesas
        for ($i = 1; $i <= 10; $i++) {
            Mesa::create(['nombre' => 'Mesa ' . $i]);
        }

        // Productos
        $productos = [
            ['nombre' => 'Pizza Margarita', 'descripcion' => 'Pizza con queso y tomate', 'precio' => 120],
            ['nombre' => 'Hamburguesa', 'descripcion' => 'Hamburguesa con queso y papas', 'precio' => 80],
            ['nombre' => 'Ensalada César', 'descripcion' => 'Ensalada con pollo y aderezo', 'precio' => 60],
            ['nombre' => 'Sopa del día', 'descripcion' => 'Sopa variada según temporada', 'precio' => 45],
            ['nombre' => 'Refresco', 'descripcion' => 'Refresco de 500ml', 'precio' => 20],
            ['nombre' => 'Agua mineral', 'descripcion' => 'Agua mineral 500ml', 'precio' => 15],
            ['nombre' => 'Café', 'descripcion' => 'Café americano', 'precio' => 25],
            ['nombre' => 'Postre del día', 'descripcion' => 'Postre variado según temporada', 'precio' => 40],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
